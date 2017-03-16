<?php

namespace Drupal\content_entity_base\Entity;

use Drupal\content_entity_base\Entity\Revision\RevisionLogEntityTrait;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\user\UserInterface;

/**
 * Defines a base entity class to be used by custom entities.
 */
class EntityBase extends ContentEntityBase implements EntityBaseInterface {

  use EntityChangedTrait;
  use RevisionLogEntityTrait;

  /**
   * {@inheritdoc}
   */
  public function createDuplicate() {
    $duplicate = parent::createDuplicate();
    $duplicate->revision_id->value = NULL;
    $duplicate->id->value = NULL;
    return $duplicate;
  }

  /**
   * {@inheritdoc}
   */
  protected function urlRouteParameters($rel) {
    $route_parameters = parent::urlRouteParameters($rel);

    if ($rel == 'revision-revert') {
      $route_parameters[$this->getEntityTypeId() . '_revision'] = $this->getRevisionId();
    }
    elseif ($rel == 'revision-delete') {
      $route_parameters[$this->getEntityTypeId() . '_revision'] = $this->getRevisionId();
    }

    return $route_parameters;
  }

  /**
   * {@inheritdoc}
   */
  public function preSaveRevision(EntityStorageInterface $storage, \stdClass $record) {
    parent::preSaveRevision($storage, $record);

    if (!$this->isNewRevision() && isset($this->original) && (!isset($record->revision_log) || $record->revision_log === '')) {
      // If we are updating an existing entity without adding a new
      // revision and the user did not supply a revision log, keep the existing
      // one.
      $record->revision_log = $this->original->getRevisionLogMessage();
    }

    if (isset($this->original) && $this->isNewRevision() && $this->getRevisionCreationTime() === $this->original->getRevisionCreationTime()) {
      // Set the revision_timestamp if it has not been set to some new value.
      $record->revision_timestamp = REQUEST_TIME;
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = static::entityKeysBaseFieldDefinitions($entity_type);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Administrative Title'))
      ->setDescription(t('A brief description of this @entity_label entry.', ['@entity_label' => $entity_type->getLabel()]))
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE)
      ->setRequired(TRUE)
      ->setDisplayOptions('form', array(
        'type' => 'string_textfield',
        'weight' => -5,
      ))
      ->setDisplayConfigurable('form', TRUE);

    $fields['uid'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The username of the entity author.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'user')
      ->setDefaultValueCallback('Drupal\content_entity_base\Entity\EntityBase::getCurrentUserId')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', array(
        'label' => 'hidden',
        'type' => 'author',
        'weight' => 0,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'entity_reference_autocomplete',
        'weight' => 5,
        'settings' => array(
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'placeholder' => '',
        ),
      ))
      ->setDisplayConfigurable('form', TRUE);

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Publishing status'))
      ->setDescription(t('A boolean indicating whether the node is published.'))
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE)
      ->setDefaultValue(TRUE);

    $fields['type'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Entity type (Bundle)'))
      ->setDescription(t('The entity type.'))
      ->setSetting('target_type', $entity_type->getBundleEntityType());

    $fields += static::revisionLogBaseFieldDefinitions($entity_type);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'))
      ->setTranslatable(TRUE)
      ->setRevisionable(TRUE);

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the custom entity was last edited.'))
      ->setTranslatable(TRUE)
      ->setRevisionable(TRUE);

    $fields['revision_translation_affected'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Revision translation affected'))
      ->setDescription(t('Indicates if the last edit of a translation belongs to current revision.'))
      ->setReadOnly(TRUE)
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE);

    $fields += static::entityRevisionLogBaseFieldDefinitions();

    return $fields;
  }

  /**
   * Returns the base field definitions for entity keys.
   *
   * @internal Reference will be replaced with parent::baseFieldDefinitions in
   *   8.x-1.x of this module.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type.
   *
   * @return \Drupal\Core\Field\BaseFieldDefinition[]
   */
  protected static function entityKeysBaseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = [];
    if ($entity_type->hasKey('id')) {
      $fields[$entity_type->getKey('id')] = BaseFieldDefinition::create('integer')
        ->setLabel(new TranslatableMarkup('ID'))
        ->setReadOnly(TRUE)
        ->setSetting('unsigned', TRUE);
    }
    if ($entity_type->hasKey('uuid')) {
      $fields[$entity_type->getKey('uuid')] = BaseFieldDefinition::create('uuid')
        ->setLabel(new TranslatableMarkup('UUID'))
        ->setReadOnly(TRUE);
    }
    if ($entity_type->hasKey('revision')) {
      $fields[$entity_type->getKey('revision')] = BaseFieldDefinition::create('integer')
        ->setLabel(new TranslatableMarkup('Revision ID'))
        ->setReadOnly(TRUE)
        ->setSetting('unsigned', TRUE);
    }
    if ($entity_type->hasKey('langcode')) {
      $fields[$entity_type->getKey('langcode')] = BaseFieldDefinition::create('language')
        ->setLabel(new TranslatableMarkup('Language'))
        ->setDisplayOptions('view', [
          'type' => 'hidden',
        ])
        ->setDisplayOptions('form', [
          'type' => 'language_select',
          'weight' => 2,
        ]);
      if ($entity_type->isRevisionable()) {
        $fields[$entity_type->getKey('langcode')]->setRevisionable(TRUE);
      }
      if ($entity_type->isTranslatable()) {
        $fields[$entity_type->getKey('langcode')]->setTranslatable(TRUE);
      }
    }
    if ($entity_type->hasKey('bundle')) {
      if ($bundle_entity_type_id = $entity_type->getBundleEntityType()) {
        $fields[$entity_type->getKey('bundle')] = BaseFieldDefinition::create('entity_reference')
          ->setLabel($entity_type->getBundleLabel())
          ->setSetting('target_type', $bundle_entity_type_id)
          ->setRequired(TRUE)
          ->setReadOnly(TRUE);
      }
      else {
        $fields[$entity_type->getKey('bundle')] = BaseFieldDefinition::create('string')
          ->setLabel($entity_type->getBundleLabel())
          ->setRequired(TRUE)
          ->setReadOnly(TRUE);
      }
    }

    return $fields;
  }


  /**
   * {@inheritdoc}
   */
  public function isPublished() {
    return (bool) $this->getEntityKey('status');
  }

  /**
   * {@inheritdoc}
   */
  public function setPublished($published) {
    $this->set('status', $published ? NODE_PUBLISHED : NODE_NOT_PUBLISHED);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('uid')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->getEntityKey('uid');
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('uid', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('uid', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return DrupalDateTime::createFromTimestamp($this->get('created')->value);
  }

  /**
   * {@inheritdoc}
   */
  public function setInfo($info) {
    $this->set('info', $info);
    return $this;
  }

  /**
   * Default value callback for 'uid' base field definition.
   *
   * @see ::baseFieldDefinitions()
   *
   * @return array
   *   An array of default values.
   */
  public static function getCurrentUserId() {
    return array(\Drupal::currentUser()->id());
  }

  /**
   * {@inheritdoc}
   */
  public function getBundleEntity() {
    // Get the bundle key;
    $bundle_key = $this->getEntityType()->getKey('bundle');
    // Return the bundle entity if it exists.
    return (!empty($bundle_key) && isset($this->{$bundle_key}->entity))
      ? $this->{$this->getEntityType()->getKey('bundle')}->entity
      : FALSE;
  }

}
