<?php

/**
 * @file
 * Contains \Drupal\content_entity_base\Entity\EntityBaseInterface.
 */

namespace Drupal\content_entity_base\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface defining a custom entity base entity.
 */
interface EntityBaseInterface extends ContentEntityInterface, EntityChangedInterface, EntityRevisionLogInterface {

  /**
   * Sets the entity description.
   *
   * @param string $info
   *   The entity description.
   *
   * @return \Drupal\content_entity_base\Entity\EntityBaseInterface
   *   The class instance that this method is called on.
   */
  public function setInfo($info);

}
