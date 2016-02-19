<?php
/**
 * @file
 * Contains \Drupal\content_entity_base\Entity\Access\EntityBasePermissionsInterface.
 */

namespace Drupal\content_entity_base\Entity\Access;

use Drupal\content_entity_base\Entity\EntityTypeBaseInterface;
use Drupal\Core\Entity\ContentEntityTypeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines a class containing permission callbacks.
 */
interface EntityBasePermissionsInterface {

  /**
   * Gets an array of entity type permissions.
   *
   * @param ContentEntityTypeInterface $entity_type
   *   The custom entity definition.
   *
   * @return array
   *   The entity type permissions.
   *
   * @see \Drupal\user\PermissionHandlerInterface::getPermissions()
   */
  function entityPermissions(ContentEntityTypeInterface $entity_type = NULL);

}
