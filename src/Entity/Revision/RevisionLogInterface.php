<?php

namespace Drupal\content_entity_base\Entity\Revision;

use Drupal\Core\Entity\RevisionableInterface;
use Drupal\user\UserInterface;

/**
 * Defines methods for an entity that supports revision logging and ownership.
 *
 * @internal This class is a backport of \Drupal\Core\Entity\RevisionLogInterface
 *   introduced in 8.1.x. This class will be removed in a future version of
 *   content entity base in favor of the core interface.
 */
interface RevisionLogInterface extends RevisionableInterface {

  /**
   * Gets the entity revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the entity revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return $this
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the entity revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the entity revision author.
   *
   * @param \Drupal\user\UserInterface $account
   *   The user account of the revision author.
   *
   * @return $this
   */
  public function setRevisionUser(UserInterface $account);

  /**
   * Gets the entity revision author ID.
   *
   * @return int
   *   The user ID.
   */
  public function getRevisionUserId();

  /**
   * Sets the entity revision author by ID.
   *
   * @param int $user_id
   *   The user ID of the revision author.
   *
   * @return $this
   */
  public function setRevisionUserId($user_id);

  /**
   * Returns the entity revision log message.
   *
   * @return string
   *   The revision log message.
   */
  public function getRevisionLogMessage();

  /**
   * Sets the entity revision log message.
   *
   * @param string $revision_log_message
   *   The revision log message.
   *
   * @return $this
   */
  public function setRevisionLogMessage($revision_log_message);

}
