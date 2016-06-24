<?php

/**
 * @file
 * Contains \Drupal\content_entity_base\Entity\Form\EntityTypeBaseForm.
 */

namespace Drupal\content_entity_base\Entity\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\language\Entity\ContentLanguageSettings;

/**
 * Base form for category edit forms.
 */
class EntityTypeBaseForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    // Get the content entity type.
    $content_entity_type = $this->getContentEntityTypeID();
    // Get the content entity type.
    $content_entity_label = $this->getContentEntityTypeLabel();

    /* @var \Drupal\content_entity_base\Entity\EntityTypeBaseInterface $entity_type */
    $entity_type = $this->entity;

    // Get the bundle entity type definition and the exportable entries.
    $exportable_config = $entity_type->getEntityType()->get('config_export');

    // Get the default field definitions or the overridden settings if editing.
    if ($this->operation == 'add') {
      $fields = $this->entityManager->getBaseFieldDefinitions($content_entity_type);
    }
    else {
      $fields = $this->entityManager->getFieldDefinitions($content_entity_type, $entity_type->id());
    }

    $form['label'] = array(
      '#type' => 'textfield',
      '#title' => t('Label'),
      '#maxlength' => 255,
      '#default_value' => $entity_type->label(),
      '#description' => t('Provide a label for this "%content_label" type to help identify it in the administration pages.', ['%content_label' => $content_entity_label]),
      '#required' => TRUE,
    );
    $form['id'] = array(
      '#type' => 'machine_name',
      '#default_value' => $entity_type->id(),
      '#machine_name' => array(
        'exists' => get_class($entity_type) . '::load',
      ),
      '#maxlength' => EntityTypeInterface::BUNDLE_MAX_LENGTH,
      '#disabled' => !$entity_type->isNew(),
    );

    $form['description'] = array(
      '#type' => 'textarea',
      '#default_value' => $entity_type->getDescription(),
      '#description' => t('Enter a description for this "%content_label" type.', ['%content_label' => $content_entity_label]),
      '#title' => t('Description'),
    );

    $form['additional_settings'] = array(
      '#type' => 'vertical_tabs',
      '#attached' => array(
        'library' => array('node/drupal.content_types'),
      ),
    );

    if (in_array('name_label', $exportable_config)) {
      $form['submission'] = array(
        '#type' => 'details',
        '#title' => t('Submission form settings'),
        '#group' => 'additional_settings',
        '#open' => TRUE,
      );

      $form['submission']['name_label'] = array(
        '#title' => t('Title label'),
        '#type' => 'textfield',
        '#default_value' => $fields['name']->getLabel(),
        '#required' => TRUE,
        '#description' => t(' default all entities have a name field. This field 
        is used to identify the entity but may or may not be displayed. By value 
        set here will be used in the entity creation and edit forms.'),
      );
    }

    $form['workflow'] = array(
      '#type' => 'details',
      '#title' => t('Publishing options'),
      '#group' => 'additional_settings',
    );

    $form['revision'] = array(
      '#type' => 'checkbox',
      '#title' => t('Create new revision'),
      '#default_value' => $entity_type->shouldCreateNewRevision(),
      '#group' => 'workflow',
      '#description' => t('Create a new revision by default for this "%content_label" type.', ['%content_label' => $content_entity_label])
    );

    if ($this->moduleHandler->moduleExists('language')) {
      $form['language'] = array(
        '#type' => 'details',
        '#title' => t('Language settings'),
        '#group' => 'additional_settings',
      );

      $language_configuration = ContentLanguageSettings::loadByEntityTypeBundle($content_entity_type, $entity_type->id());
      $form['language']['language_configuration'] = array(
        '#type' => 'language_configuration',
        '#entity_information' => array(
          'entity_type' => $content_entity_type,
          'bundle' => $entity_type->id(),
        ),
        '#default_value' => $language_configuration,
      );

      $form['#submit'][] = 'language_configuration_element_submit';
    }

    $form['actions'] = array('#type' => 'actions');
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => t('Save'),
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    // Get the content entity type.
    $content_entity_type = $this->getContentEntityTypeID();
    // Get the content entity type.
    $content_entity_label = $this->getContentEntityTypeLabel();

    $entity_type = $this->entity;
    $status = $entity_type->save();

    $edit_link = $this->entity->link($this->t('Edit'));
    $logger = $this->logger($content_entity_type);

    $update_verb = 'updated';
    if ($status != SAVED_UPDATED) {
      $this->contentAddBodyField($entity_type->id());
      $update_verb = 'added';
    }

    drupal_set_message(t('%content_label entity type %label has been @verb.', [
      '%content_label' => $content_entity_label,
      '%label' => $entity_type->label(),
      '@verb' => $update_verb,
    ]));
    $logger->notice('%content_label entity type %label has been @verb.', [
      '%content_label' => $content_entity_label,
      '%label' => $entity_type->label(),
      '@verb' => $update_verb,
      'link' => $edit_link,
    ]);

    $form_state->setRedirectUrl($this->entity->urlInfo('collection'));
  }

  protected function getContentEntityTypeID() {
    return $this->entity->getEntityType()->getBundleOf();
  }
  protected function getContentEntityTypeLabel() {
    return $this->entityManager->getDefinition($this->getContentEntityTypeID())->getLabel();
  }

  protected function contentAddBodyField($entity_type_id, $label = 'Body') {
    // Get the content entity type.
    $content_entity_type = $this->getContentEntityTypeID();

    // Make the creation of a default body field optional. Do not create if
    // there is no field storage configuration.
    if (!FieldStorageConfig::loadByName($content_entity_type, 'body')) {
      return;
    }

    // Add or remove the body field, as needed.
    $field = FieldConfig::loadByName($content_entity_type, $entity_type_id, 'body');
    if (empty($field)) {
      $field = entity_create('field_config', array(
        'field_storage' =>  FieldStorageConfig::loadByName($content_entity_type, 'body'),
        'bundle' => $entity_type_id,
        'label' => $label,
        'settings' => array('display_summary' => FALSE),
      ));
      $field->save();

      // Assign widget settings for the 'default' form mode.
      entity_get_form_display($content_entity_type, $entity_type_id, 'default')
        ->setComponent('body', array(
          'type' => 'text_textarea_with_summary',
        ))
        ->save();

      // Assign display settings for 'default' view mode.
      entity_get_display($content_entity_type, $entity_type_id, 'default')
        ->setComponent('body', array(
          'label' => 'hidden',
          'type' => 'text_default',
        ))
        ->save();
    }

    return $field;
  }

}
