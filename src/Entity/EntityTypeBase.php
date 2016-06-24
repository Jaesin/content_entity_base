<?php

/**
 * @file
 * Contains Drupal\content_entity_base\Entity\EntityTypeBase.
 *
 * @todo Can we use a generic bundle across multiple custom entity types?
 */

namespace Drupal\content_entity_base\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;
use Drupal\Core\Entity\EntityStorageInterface;

/**
 * Defines the entity type configuration entity.
 */
class EntityTypeBase extends ConfigEntityBundleBase implements EntityTypeBaseInterface {

  /**
   * The custom entity type ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The custom entity type label.
   *
   * @var string
   */
  protected $label;

  /**
   * The default revision setting for custom entities of this type.
   *
   * @var bool
   */
  protected $revision;

  /**
   * The description of the entity type.
   *
   * @var string
   */
  protected $description;

  /**
   * {@inheritdoc}
   */
  public function bundleOf() {
    return $this->getEntityType()->getBundleOf();
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->description;
  }

  /**
   * {@inheritdoc}
   */
  public function shouldCreateNewRevision() {
    return $this->revision;
  }

  /**
   * {@inheritdoc}
   */
  public function postSave(EntityStorageInterface $storage, $update = TRUE) {
    parent::postSave($storage, $update);

    // Update the label for the name field if the name_label has been set.
    if ($name_label = $this->get('name_label')) {
      // Get overridden field settings for this bundle.
      $fields = $this->entityManager()->getFieldDefinitions($this->getEntityType()->getBundleOf(), $this->id());
      $name_field = $fields['name'];
      // Set the name label if it has been updated.
      if ($name_field->getLabel() != $name_label) {
        $name_field->getConfig($this->id())->setLabel($name_label)->save();
      }
    }
  }

}
