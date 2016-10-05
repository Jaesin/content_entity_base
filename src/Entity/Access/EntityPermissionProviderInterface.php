<?php

namespace Drupal\content_entity_base\Entity\Access;


/**
 * Provides a callback for getting entity permissions.
 */
interface EntityPermissionProviderInterface {

  /**
   * Gets an array of entity permissions.
   *
   * @return array
   *   The permissions for the provided entity type.
   */
  public function entityPermissions();

}
