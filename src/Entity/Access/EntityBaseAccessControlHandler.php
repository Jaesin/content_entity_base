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

    if ($operation === 'view') {
      $access = $access->orIf(AccessResult::allowedIfHasPermission($account, 'access ' . $entity->getEntityTypeId()));
    }
    return $access;
  }

}
