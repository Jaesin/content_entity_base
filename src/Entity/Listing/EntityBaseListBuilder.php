<?php

/**
 * @file
 *   Contains \Drupal\content_entity_base\Entity\Listing\EntityBaseListBuilder.
 */

namespace Drupal\content_entity_base\Entity\Listing;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;

/**
 * Defines a class to build a listing of custom entities.
 */
class EntityBaseListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    return [
      'label' => $this->t('')
    ] + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row['label'] = $entity->link();
    return $row + parent::buildRow($entity);
  }

  /**
   * {@inheritdoc}
   */
  public function getDefaultOperations(EntityInterface $entity) {
    $operations = parent::getDefaultOperations($entity);
    if (isset($operations['edit'])) {
      $operations['edit']['query']['destination'] = $entity->url('collection');
    }
    return $operations;
  }

  /**
   * {@inheritdoc}
   */
  public function render() {
    $build = parent::render();

    // Override the empty text.
    $build['table']['#empty'] = $this->t('There are no @label entities yet.', [
      '@label' => $this->entityType->getLabel(),
    ]);

    return $build;
  }
}
