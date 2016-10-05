<?php

namespace Drupal\content_entity_base\Entity\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing\RouteMatchInterface;

/**
 * Form controller for the custom entity edit forms.
 */
class EntityBaseForm extends ContentEntityForm {

  /**
 * @var \Drupal\content_entity_base\Entity\EntityBaseInterface */
  protected $entity;

  /**
   * Overrides \Drupal\Core\Entity\EntityForm::prepareEntity().
   *
   * Prepares the custom entity object.
   */
  protected function prepareEntity() {
    parent::prepareEntity();

    /** @var \Drupal\content_entity_base\Entity\EntityTypeBaseInterface $bundle */
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
      $form_state->setRedirectUrl($this->entity->toUrl('collection'));
    }
    else {
      // In the unlikely case something went wrong on save, the entity will be
      // rebuilt and entity form redisplayed.
      drupal_set_message($this->t('The entity could not be saved.'), 'error');
      $form_state->setRebuild();
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getEntityFromRouteMatch(RouteMatchInterface $route_match, $entity_type_id) {
    if ($route_match->getRawParameter($entity_type_id) !== NULL) {
      $entity = $route_match->getParameter($entity_type_id);
    }
    else {
      $values = [];
      // If the entity has bundles, fetch it from the route match.
      $entity_type = $this->entityTypeManager->getDefinition($entity_type_id);
      if ($bundle_key = $entity_type->getKey('bundle')) {
        if (($bundle_entity_type_id = $entity_type->getBundleEntityType()) && $route_match->getRawParameter($bundle_entity_type_id)) {
          $values[$bundle_key] = $route_match->getParameter($bundle_entity_type_id)->id();
        }
        elseif ($route_match->getRawParameter($bundle_key)) {
          $values[$bundle_key] = $route_match->getParameter($bundle_key);
        }
      }

      $entity = $this->entityTypeManager->getStorage($entity_type_id)->create($values);
    }

    return $entity;
  }

}
