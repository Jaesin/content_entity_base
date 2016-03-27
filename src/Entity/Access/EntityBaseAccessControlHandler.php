<?php

/**
 * @file
 * Contains \Drupal\content_entity_base\Entity\Access\EntityBaseAccessControlHandler.
 */

namespace Drupal\content_entity_base\Entity\Access;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Defines the access control handler for a custom entity type.
 */
class EntityBaseAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    $access = parent::checkAccess($entity, $operation, $account);
    $entity_type_id = $entity->getEntityTypeId();
    $bundle = $entity->bundle();

    switch ($operation) {
      case 'view':
        if (!$entity->isPublished() && $account->hasPermission("view own unpublished $entity_type_id") && $account->isAuthenticated() && $account->id() == $entity->getOwnerId()) {
          $access = $access->orIf(AccessResult::allowed())->cachePerPermissions()->cachePerUser()->cacheUntilEntityChanges($entity);
        }
        else {
          $access = $access->orIf(AccessResult::allowedIfHasPermission($account, "access $entity_type_id"));
        }
        break;

      case 'create':
        $access = AccessResult::allowedIf($account->hasPermission("create $entity_type_id $bundle"))->cachePerPermissions();
        break;

      case 'update':
        if ($account->hasPermission("edit any $entity_type_id $bundle", $account)) {
          $access = $access->orIf(AccessResult::allowed())->cachePerPermissions();
        }
        else {
          $access = $access->orIf(AccessResult::allowedIf($account->hasPermission("edit own $entity_type_id $bundle", $account) && ($account->id() == $entity->getOwnerId())))->cachePerPermissions()->cachePerUser()->cacheUntilEntityChanges($entity);
        }
        break;

      case 'delete':
        if ($account->hasPermission("delete any $entity_type_id $bundle", $account)) {
          $access = $access->orIf(AccessResult::allowed())->cachePerPermissions();
        }
        else {
          $access = $access->orIf(AccessResult::allowedIf($account->hasPermission("delete own $entity_type_id $bundle", $account) && ($account->id() == $entity->getOwnerId())))->cachePerPermissions()->cachePerUser()->cacheUntilEntityChanges($entity);
        }
        break;

      default:
        // No opinion.
        return AccessResult::neutral();
    }

    return $access;
  }

  /**
   * {@inheritdoc}
   */
  public function createAccess($entity_bundle = NULL, AccountInterface $account = NULL, array $context = array(), $return_as_object = FALSE) {
    $account = $this->prepareUser($account);

    $entity_type_id = $this->entityTypeId;
    if ($account->hasPermission("bypass $entity_type_id access")) {
      $access = AccessResult::allowedIf($account->hasPermission("bypass $entity_type_id access"))->cachePerPermissions();
    }
    elseif (!$account->hasPermission("access $entity_type_id")) {
      $access = AccessResult::forbidden()->cachePerPermissions();
    }
    elseif (!$entity_bundle) {
      $access = AccessResult::allowedIf($account->hasPermission("access $entity_type_id overview"))->cachePerPermissions();
    }
    else {
      $access = AccessResult::allowedIf($account->hasPermission("create $entity_bundle $entity_type_id"))->cachePerPermissions();
    }

    $result = $access->orIf(parent::createAccess($entity_bundle, $account, $context, TRUE))->cachePerPermissions();
    return $return_as_object ? $result : $result->isAllowed();
  }

}
