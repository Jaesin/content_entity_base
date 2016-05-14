<?php

/**
 * @file
 * Contains \Drupal\content_entity_base\Entity\Form\EntityBaseForm.
 */

namespace Drupal\content_entity_base\Entity\Form;

use Drupal\content_entity_base\Entity\EntityTypeBaseInterface;
use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for the custom entity edit forms.
 */
class EntityBaseForm extends ContentEntityForm {

  /** @var \Drupal\content_entity_base\Entity\EntityBaseInterface */
  protected $entity;

  /**
   * Overrides \Drupal\Core\Entity\EntityForm::prepareEntity().
   *
   * Prepares the custom entity object.
   */
  protected function prepareEntity() {
    parent::prepareEntity();

    $bundle = $this->entity->getBundleEntity();

    // Set up default values, if required.
    if (!$this->entity->isNew()) {
      $this->entity->setRevisionLogMessage(NULL);
    }
    // Always use the default revision setting.
    $this->entity->setNewRevision($bundle && $bundle->shouldCreateNewRevision());
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {

    $form = parent::form($form, $form_state);

    $entity_type = $this->entity->getEntityType();

    $bundle = $this->entity->getBundleEntity();

    $account = $this->currentUser();

    if ($this->operation == 'edit') {
      $form['#title'] = $this->t('Edit %bundle_label @label', [
        '%bundle_label' => $bundle ? $bundle->label() : '',
        '@label' => $this->entity->label(),
      ]);
    }

    $form['advanced'] = [
      '#type' => 'vertical_tabs',
      '#weight' => 99,
    ];

    // Add a log field if the "Create new revision" option is checked, or if the
    // current user has the ability to check that option.
    // @todo Could we autogenerate this form by using some widget on the
    //   revision info field.
    $form['revision_information'] = [
      '#type' => 'details',
      '#title' => $this->t('Revision information'),
      // Open by default when "Create new revision" is checked.
      '#open' => $this->entity->isNewRevision(),
      '#group' => 'advanced',
      '#weight' => 20,
      '#access' => $this->entity->isNewRevision() || $account->hasPermission($entity_type->get('admin_permission')),
    ];

    $form['revision'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Create new revision'),
      '#default_value' => $this->entity->isNewRevision(),
      '#group' => 'revision_information',
      '#access' => $account->hasPermission($entity_type->get('admin_permission')),
    ];

    $form['revision_log'] += [
      '#group' => 'revision_information',
      '#states' => [
        'visible' => [
          'input[name="revision"]' => ['checked' => TRUE],
        ],
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {

    // Save as a new revision if requested to do so.
    if (!$form_state->isValueEmpty('revision')) {
      $this->entity->setNewRevision();
    }

    $insert = $this->entity->isNew();
    $this->entity->save();
    $context = ['@type' => $this->entity->bundle(), '%info' => $this->entity->label()];
    $logger = $this->logger($this->entity->id());
    $bundle = $this->entity->getBundleEntity();
    $t_args = ['@type' => $bundle ? $bundle->label() : 'None', '%info' => $this->entity->label()];

    if ($insert) {
      $logger->notice('@type: added %info.', $context);
      drupal_set_message($this->t('@type %info has been created.', $t_args));
    }
    else {
      $logger->notice('@type: updated %info.', $context);
      drupal_set_message($this->t('@type %info has been updated.', $t_args));
    }

    if ($this->entity->id()) {
      $form_state->setValue('id', $this->entity->id());
      $form_state->set('id', $this->entity->id());
      $form_state->setRedirectUrl($this->entity->urlInfo('collection'));
    }
    else {
      // In the unlikely case something went wrong on save, the entity will be
      // rebuilt and entity form redisplayed.
      drupal_set_message($this->t('The entity could not be saved.'), 'error');
      $form_state->setRebuild();
    }
  }
}
