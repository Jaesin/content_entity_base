<?php

/**
 * @file
 * Contains \Drupal\content_entity_base\Entity\Form\EntityTypeBaseDeleteForm.
 */

namespace Drupal\content_entity_base\Entity\Form;

use Drupal\Core\Entity\EntityDeleteForm;
use Drupal\Core\Entity\EntityManager;
use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a confirmation form for deleting a custom entity type entity.
 */
class EntityTypeBaseDeleteForm extends EntityDeleteForm {

  /**
   * The entity being used by this form.
   *
   * @var \Drupal\content_entity_base\Entity\EntityTypeBaseInterface
   */
  protected $entity;

  /**
   * The query factory to create entity queries.
   *
   * @var \Drupal\Core\Entity\Query\QueryFactory
   */
  public $queryFactory;

  /**
   * The entity manager.
   *
   * @var \Drupal\Core\Entity\EntityManager
   */
  public $entityManager;

  /**
   * Constructs a query factory object.
   *
   * @param \Drupal\Core\Entity\Query\QueryFactory $query_factory
   *   The entity query object.
   * @param \Drupal\Core\Entity\EntityManager $entity_manager
   *   The entity manager.
   */
  public function __construct(QueryFactory $query_factory, EntityManager $entity_manager) {
    $this->queryFactory = $query_factory;
    $this->entityManager = $entity_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.query'),
      $container->get('entity.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Get the custom entity ID.
    $custom_entity_id = $this->entity->getEntityType()->getBundleOf();
    // Get the bundle key for the custom entity type.
    $custom_entity_bundle_key = $this->entityManager->getDefinition($custom_entity_id)->getKey('bundle');

    $entities = $this->queryFactory->get($custom_entity_id)->condition($custom_entity_bundle_key, $this->entity->id())->execute();
    if (!empty($entities)) {
      $caption = '<p>' . $this->formatPlural(count($entities), '%label is used by 1 entity on your site. You can not remove this entity type until you have removed all of the %label entities.', '%label is used by @count entities on your site. You may not remove %label until you have removed all of the %label entities.', ['%label' => $this->entity->label()]) . '</p>';
      $form['description'] = array('#markup' => $caption);
      return $form;
    }
    else {
      return parent::buildForm($form, $form_state);
    }
  }

}
