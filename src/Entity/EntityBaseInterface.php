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
interface EntityBaseInterface extends ContentEntityInterface, EntityChangedInterface, EntityRevisionLogInterface, EntityOwnerInterface {

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

  /**
   * Returns the node published status indicator.
   *
   * Unpublished nodes are only visible to their authors and to administrators.
   *
   * @return bool
   *   TRUE if the node is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a node..
   *
   * @param bool $published
   *   TRUE to set this node to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\node\NodeInterface
   *   The called node entity.
   */
  public function setPublished($published);

  /**
   * Gets the config entity that serves as the content entities' bundle.
   *
   * @return \Drupal\Core\Config\Entity\ConfigEntityInterface|false
   */
  public function getBundleEntity();

}
