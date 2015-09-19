<?php

/**
 * @file
 * Contains \Drupal\content_entity_base\Entity\Listing\EntityTypeBaseListBuilder.
 */

namespace Drupal\content_entity_base\Entity\Listing;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;

/**
 * Defines a class to build a listing of custom block type entities.
 *
 * @see \Drupal\block_content\Entity\BlockContentType
 */
class EntityTypeBaseListBuilder extends ConfigEntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function getDefaultOperations(EntityInterface $entity) {
    $operations = parent::getDefaultOperations($entity);
    // Place the edit operation after the operations added by field_ui.module
    // which have the weights 15, 20, 25.
    if (isset($operations['edit'])) {
      $operations['edit']['weight'] = 30;
    }
    return $operations;
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['type'] = t('Entity type');
    $header['description'] = t('Description');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row['type'] = $entity->link();
    $row['description']['data']['#markup'] = $entity->getDescription();
    return $row + parent::buildRow($entity);
  }

  /**
   * {@inheritdoc}
   */
  protected function getTitle() {
    // Get the definition of the referring entity.
    $bundle_of_definition = $this->getBundleOfDefinition();

    return $this->t('Custom @label entity types', ['@label' => $bundle_of_definition->getLabel()]);
  }

  /**
   * {@inheritdoc}
   */
  public function render() {
    $build = parent::render();

    // Get the definition of the referring entity.
    $bundle_of_definition = $this->getBundleOfDefinition();
    // Override the empty text.
    $build['table']['#empty'] = $this->t('There are no @label types yet.', ['@label' => $bundle_of_definition->getLabel()]);

    return $build;
  }

  protected function getBundleOfDefinition() {
    return \Drupal::entityManager()->getDefinition($this->entityType->getBundleOf());
  }
}
