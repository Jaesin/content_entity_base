<?php

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

    // List is here for revision access. You cannot have a "list" all entities
    // _entity_access check because _entity_access requires a single entity.
    if (in_array($operation, ['view', 'list'])) {
      $access = $access
        ->orIf(AccessResult::allowedIfHasPermission($account, 'access ' . $entity->getEntityTypeId()))
        ->orIf(AccessResult::allowedIfHasPermission($account, 'administer ' . $entity->getEntityTypeId()));
    }
    return $access;
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    $allowed_permissions = [];
    if ($admin_permission = $this->entityType->getAdminPermission()) {
      $allowed_permissions[] = $admin_permission;
    }

    if ($entity_bundle) {
      $allowed_permissions[] = "create {$entity_bundle} {$this->entityTypeId}";
    }

    foreach ($allowed_permissions as $allowed_permission) {
      if ($account->hasPermission($allowed_permission)) {
        return AccessResult::allowed()->addCacheContexts(['user.permissions']);
      }
    }
    return AccessResult::neutral();
  }

}
