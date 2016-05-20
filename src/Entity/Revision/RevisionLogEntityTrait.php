<?php

namespace Drupal\content_entity_base\Entity\Revision;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\user\UserInterface;

/**
 * Provides a trait for accessing revision logging and ownership information.
 *
 * @internal This trait is a back port of `\Drupal\Core\Entity\RevisionLogEntityTrait`
 *   introduced in 8.1.x. This class will be removed in version 8.x-2.x of
 *   content entity base in favor of the core interface which uses different
 *   field id's than this trait.
 */
trait RevisionLogEntityTrait {

  /**
   * Provides revision-related base field definitions for an entity type.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type definition.
   *
   * @return \Drupal\Core\Field\FieldDefinitionInterface[]
   *   An array of base field definitions for the entity type, keyed by field
   *   name.
   *
   * @see \Drupal\Core\Entity\FieldableEntityInterface::baseFieldDefinitions()
   */
  public static function revisionLogBaseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields['revision_timestamp'] = BaseFieldDefinition::create('created')
      ->setLabel(new TranslatableMarkup('Revision creation time'))
      ->setDescription(new TranslatableMarkup('The time that the current revision was created.'))
      ->setRevisionable(TRUE);

    $fields['revision_uid'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(new TranslatableMarkup('Revision user'))
      ->setDescription(new TranslatableMarkup('The user ID of the author of the current revision.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'user');

    $fields['revision_log'] = BaseFieldDefinition::create('string_long')
      ->setLabel(new TranslatableMarkup('Revision log message'))
      ->setDescription(new TranslatableMarkup('Briefly describe the changes you have made.'))
      ->setRevisionable(TRUE)
      ->setDefaultValue('')
      ->setDisplayOptions('form', [
        'type' => 'string_textarea',
        'weight' => 25,
        'settings' => [
          'rows' => 4,
        ],
      ]);

    return $fields;
  }

  /**
   * Implements \Drupal\Core\Entity\RevisionLogInterface::getRevisionCreationTime().
   */
  public function getRevisionCreationTime() {
    return $this->revision_timestamp->value;
  }

  /**
   * Implements \Drupal\Core\Entity\RevisionLogInterface::setRevisionCreationTime().
   */
  public function setRevisionCreationTime($timestamp) {
    $this->revision_timestamp->value = $timestamp;
    return $this;
  }

  /**
   * Implements \Drupal\Core\Entity\RevisionLogInterface::getRevisionUser().
   */
  public function getRevisionUser() {
    return $this->revision_uid->entity;
  }

  /**
   * Implements \Drupal\Core\Entity\RevisionLogInterface::setRevisionUser().
   */
  public function setRevisionUser(UserInterface $account) {
    $this->revision_uid->entity = $account;
    return $this;
  }

  /**
   * Implements \Drupal\Core\Entity\RevisionLogInterface::getRevisionUserId().
   */
  public function getRevisionUserId() {
    return $this->revision_uid->target_id;
  }

  /**
   * Implements \Drupal\Core\Entity\RevisionLogInterface::setRevisionUserId().
   */
  public function setRevisionUserId($user_id) {
    $this->revision_uid->target_id = $user_id;
    return $this;
  }

  /**
   * Implements \Drupal\Core\Entity\RevisionLogInterface::getRevisionLogMessage().
   */
  public function getRevisionLogMessage() {
    return $this->revision_log->value;
  }

  /**
   * Implements \Drupal\Core\Entity\RevisionLogInterface::setRevisionLogMessage().
   */
  public function setRevisionLogMessage($revision_log_message) {
    $this->revision_log->value = $revision_log_message;
    return $this;
  }

}
