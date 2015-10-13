<?php

/**
 * @file
 * Contains \Drupal\content_entity_base\Entity\Storage\RevisionableStorageInterface.
 */

namespace Drupal\content_entity_base\Entity\Storage;

use Drupal\Core\Entity\ContentEntityInterface;

/**
 * Defines some additional storage methods used for revision support.
 */
interface RevisionableStorageInterface {

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   *   The entity.
   *
   * @return int The number of revisions in the default language.
   * The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(ContentEntityInterface $entity);

}
