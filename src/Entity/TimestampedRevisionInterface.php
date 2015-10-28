<?php

/**
 * @file
 * Contains \Drupal\content_entity_base\Entity\TimestampedRevisionInterface.
 */

namespace Drupal\content_entity_base\Entity;

/**
 * Defines an interface with timestamped revisions.
 */
interface TimestampedRevisionInterface {

  /**
   * Returns the revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return $this
   *   The called object.
   */
  public function setRevisionCreationTime($timestamp);

}
