<?php

/**
 * @file
 * Contains \Drupal\content_entity_base\Entity\Storage\ContentEntityBaseStorage.
 */

namespace Drupal\content_entity_base\Entity\Storage;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\Sql\SqlContentEntityStorage;

class ContentEntityBaseStorage extends SqlContentEntityStorage implements RevisionableStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(ContentEntityInterface $entity) {
    $entity_type = $entity->getEntityType();
    $count = $this->getQuery()
      ->allRevisions()
      ->condition($entity_type->getKey('id'), $entity->id())
      ->condition('default_langcode', 1)
      ->count()
      ->execute();
    return $count;
  }

}
