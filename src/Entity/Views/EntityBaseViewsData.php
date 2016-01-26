<?php

/**
 * @file
 * Contains \Drupal\content_entity_base\Entity\Views\EntityBaseViewsData.
 */

namespace Drupal\content_entity_base\Entity\Views;

use Drupal\Core\Entity\ContentEntityTypeInterface;
use Drupal\views\EntityViewsData;

/**
 * Provides the views data for a custom entity type.
 */
class EntityBaseViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {

    $data = parent::getViewsData();

    // Get some info to work off.
    $entity_table = $this->entityType->getBaseTable();
    $entity_data_table = $this->entityType->getDataTable();
    $entity_revision_table = $this->entityType->getRevisionTable();
    $entity_revision_data_table = $this->entityType->getRevisionDataTable();
    // Replacements for all strings.
    $replacements = [
      '@entity_label' => $this->entityType->getLabel(),
      '@entity_id' => $this->entityType->id(),
    ];

    if (!empty($entity_data_table)) {
      // Add the entity ID field.
      $data[$entity_data_table]['id']['field']['id'] = 'field';
      // Add entity info.
      $data[$entity_data_table]['info']['field']['id'] = 'field';
      $data[$entity_data_table]['info']['field']['link_to_entity default'] = TRUE;
      // Add the bundle (type).
      $data[$entity_data_table]['type']['field']['id'] = 'field';

    }
    if (!empty($entity_table) && !empty($entity_revision_table)) {
      $revision_base_table = $entity_revision_data_table ?: $entity_revision_table;

      // Advertise this table as a possible base table.
      $data[$revision_base_table]['table']['base']['help'] = $this->t('@entity_label revision is a history of changes to a "@entity_id" entity.', $replacements);
      $data[$revision_base_table]['table']['base']['defaults']['title'] = 'info';

      // @todo EntityViewsData should add these relationships by default.
      //   https://www.drupal.org/node/2652652
      $data[$revision_base_table]['id']['relationship']['id'] = 'standard';
      $data[$revision_base_table]['id']['relationship']['base'] = $entity_table;
      $data[$revision_base_table]['id']['relationship']['base field'] = 'id';
      $data[$revision_base_table]['id']['relationship']['title'] = $this->t('@entity_label', $replacements);
      $data[$revision_base_table]['id']['relationship']['label'] = $this->t('Get the actual @entity_label from a @entity_label revision.', $replacements);

      $data[$revision_base_table]['revision_id']['relationship']['id'] = 'standard';
      $data[$revision_base_table]['revision_id']['relationship']['base'] = $entity_table;
      $data[$revision_base_table]['revision_id']['relationship']['base field'] = 'revision_id';
      $data[$revision_base_table]['revision_id']['relationship']['title'] = $this->t('@entity_label', $replacements);
      $data[$revision_base_table]['revision_id']['relationship']['label'] = $this->t('Get the actual @entity_label from a @entity_label revision.', $replacements);
    }

    return $data;
  }
}
