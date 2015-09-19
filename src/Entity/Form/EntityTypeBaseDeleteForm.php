<?php

/**
 * @file
 * Contains \Drupal\content_entity_base\Entity\Form\EntityTypeBaseDeleteForm.
 */

namespace Drupal\content_entity_base\Entity\Form;

use Drupal\Core\Entity\EntityDeleteForm;
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
   * Constructs a query factory object.
   *
   * @param \Drupal\Core\Entity\Query\QueryFactory $query_factory
   *   The entity query object.
   */
  public function __construct(QueryFactory $query_factory) {
    $this->queryFactory = $query_factory;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.query')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Get the custom entity ID.
    $custom_entity_id = $this->entity->getEntityType()->getBundleOf();
    $entities = $this->queryFactory->get($custom_entity_id)->condition('type', $this->entity->id())->execute();
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
