<?php

/**
 * @file
 * Contains \Drupal\content_entity_base\Entity\EntityRevisionLogInterface.
 */

namespace Drupal\content_entity_base\Entity;

interface EntityRevisionLogInterface {

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
