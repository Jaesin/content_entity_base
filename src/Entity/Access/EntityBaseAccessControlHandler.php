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

    if ($operation == 'view') {
      $access = $access->orIf(AccessResult::allowedIfHasPermission($account, 'access ' . $entity->getEntityTypeId()));
    }
    return $access;
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    $access = parent::checkCreateAccess($account, $context, $entity_bundle);
    $entity_type_id = $this->entityTypeId;
    if (!$entity_bundle) {
      $access = $access->orIf(AccessResult::allowedIf($account->hasPermission("access $entity_type_id overview")))->cachePerPermissions();
    }
    else {
      $access = $access->orIf(AccessResult::allowedIf($account->hasPermission("create $entity_bundle $entity_type_id")))->cachePerPermissions();
    }

    return $access;
  }

}
