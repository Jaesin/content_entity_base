<?php
/**
 * @file
 *   Contains \Drupal\content_entity_base\Entity\EntityTypeBaseInterface.
 */

namespace Drupal\content_entity_base\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface defining a custom entity type entity.
 */
interface EntityTypeBaseInterface extends ConfigEntityInterface {

  /**
   * Returns the id of the referring entity type.
   *
   * @return string
   *   The entity type id of the referring entity.
   */
  public function bundleOf();

  /**
   * Returns the description of the entity type.
   *
   * @return string
   *   The description of the type of this entity.
   */
  public function getDescription();

  /**
   * Returns whether a new revision should be created by default.
   *
   * @return bool
   *   TRUE if a new revision should be created by default.
   */
  public function shouldCreateNewRevision();

}
