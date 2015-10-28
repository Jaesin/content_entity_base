<?php

/**
 * @file
 * Contains \Drupal\content_entity_base\Entity\ExpandedEntityRevisionInterface.
 */

namespace Drupal\content_entity_base\Entity;

interface ExpandedEntityRevisionInterface {

  /**
   * Gets the node revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the node revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\node\NodeInterface
   *   The called node entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the node revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionAuthor();

  /**
   * Sets the node revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\node\NodeInterface
   *   The called node entity.
   */
  public function setRevisionAuthorId($uid);

}
