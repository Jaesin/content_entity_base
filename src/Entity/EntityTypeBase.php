<?php

/**
 * @file
 * Contains Drupal\content_entity_base\Entity\EntityTypeBase.
 *
 * @todo Can we use a generic bundle across multiple custom entity types?
 */

namespace Drupal\content_entity_base\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

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

}
