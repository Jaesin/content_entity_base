<?php
/**
 * @file
 *   Contains \Drupal\content_entity_base\EntityBaseCallbackProxyInterface.
 */

namespace Drupal\content_entity_base;

interface EntityBaseCallbackProxyInterface {

  /**
   * Proxies the entity permissions callback.
   *
   * @return array
   *   The entity type permissions.
   */
  public function entityPermissions();
}
