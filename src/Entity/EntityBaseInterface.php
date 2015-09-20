<?php

/**
 * @file
 * Contains \Drupal\content_entity_base\Entity\EntityBaseInterface.
 */

namespace Drupal\content_entity_base\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;

/**
 * Provides an interface defining a custom entity base entity.
 */
interface EntityBaseInterface extends ContentEntityInterface, EntityChangedInterface {

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
   * @todo Ideally this would be its own interface?
   *
   * Returns the entity revision log message.
   *
   * @return string
   *   The revision log message.
   */
  public function getRevisionLog();

  /**
   * Sets the entity revision log message.
   *
   * @param string $revision_log
   *   The revision log message.
   *
   * @return \Drupal\content_entity_base\Entity\EntityBaseInterface
   *   The class instance that this method is called on.
   */
  public function setRevisionLog($revision_log);

}
