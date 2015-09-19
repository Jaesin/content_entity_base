<?php

/**
 * @file
 * Contains \Drupal\content_entity_base\Entity\Controller\EntityBaseController.
 */

namespace Drupal\content_entity_base\Entity\Controller;

use Drupal\content_entity_base\Entity\EntityBaseInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\ContentEntityTypeInterface;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class EntityBaseController extends ControllerBase {

  /**
   * Displays add custom entity links for available types.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The current request object.
   * @param (string) $entity_type
   *   The custom entity type to add.
   *
   * @return array
   *   A render array for a list of the custom entity types that can be added or
   *   if there is only one custom entity type defined for the site, the function
   *   returns the custom entity add page for that custom entity type.
   */
  public function addPage(Request $request, $entity_type = NULL) {
    // Get the entity definition.
    /** @var ContentEntityTypeInterface $entity_definition */
    $entity_definition = !empty($entity_type)
      ? $this->entityManager()->getDefinition($entity_type)
      : NULL;
    // Get the storage controller for this entity.
    $bundle_storage = $entity_definition
      ? $this->entityManager()->getStorage($entity_definition->getBundleEntityType())
      : NULL;
    // Load all entity types for this entity definition.
    $types = $bundle_storage->loadMultiple();

    // Check for existing types.
    if ($types && count($types) == 1) {
      $type = reset($types);
      return $this->addForm($entity_type, $type->id());
    }
    if (count($types) === 0) {
      return array(
        '#markup' => $this->t('You have not created any @entity_label types yet. Go to the <a href="!url">@entity_label type creation page</a> to add a new @entity_label type.', [
          '@entity_label' => $entity_definition->label(),
          '!url' => Url::fromRoute($entity_definition->id().'.type_add')->toString(),
        ]),
      );
    }

    $build = ['add_links'=>[
      '#theme' => 'links__help',
      '#heading' => [
        'level' => 'h3',
        'text' => $this->t('@entity_label types', [
          '@entity_label' => $entity_definition->getLabel(),
        ]),
      ],
      '#links' => [],
    ]];

    $query = \Drupal::request()->query->all();
    foreach ($types as $type) {
      $build['add_links']['#links'][$type->id()] = [
        'title' => $type->label(),
        'url' => new Url('entity.'.$entity_definition->id().'.add_form', ['entity_bundle_id' => $type->id()], ['query' => $query]),
      ];
    }

    return $build;
  }

  /**
   * Gets the title for the "Entity Add" page.
   *
   * @param \Drupal\content_entity_base\Entity\EntityBaseInterface $entity_definition
   *   The custom entity type to add.
   *
   * @return string
   *   The title customized for this entity type.
   */
  public function getAddPageTitle(EntityBaseInterface $entity_definition) {
    return $this->t('Add @entity_label', [
      '@entity_label' => $entity_definition->label(),
    ]);
  }

  /**
   * Presents the custom entity creation form.
   *
   * @param \Drupal\content_entity_base\Entity\EntityBaseInterface $entity_definition
   *   The custom entity type to add.
   * @param \Drupal\content_entity_base\Entity\EntityTypeBaseInterface $entity_bundle
   *   The custom entity type bundle to use.
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The current request object.
   *
   * @return array
   *   A form array as expected by drupal_render().
   */
  public function addForm($entity_type = NULL, $entity_bundle_id = NULL) {
    // Get the entity type from the entity_type_id.
    $entity_definition = $this->entityManager()->getDefinition($entity_type);

    // Load the bundle.
    $entity_bundle = $this->entityManager->getStorage($entity_definition->getBundleEntityType())->load($entity_bundle_id);

    // Validate the bundle.
    if (!$entity_bundle) {
      \Drupal::logger('content_entity_base')->error($this->t('Cannot create a @entity_type entity with an invalid bundle type of "%entity_bundle_id".'), [
        '@entity_type' => $entity_definition->getLabel(),
        '%entity_bundle_id' => $entity_bundle_id,
      ]);
      return new RedirectResponse(\Drupal::url($entity_type.'.add_page'));
    }
    // Get the entity storage for this entity type.
    $entity_storage = $this->entityManager()->getStorage($entity_definition->id());
    $entity = $entity_storage->create([
      'type' => $entity_bundle->id(),
    ]);
    return $this->entityFormBuilder()->getForm($entity);
  }

  /**
   * Provides the page title for the entity form.
   *
   * @param \Drupal\content_entity_base\Entity\EntityTypeBaseInterface $entity_bundle
   *   The custom entity type to add.
   *
   * @return string
   *   The page title.
   */
  public function getAddFormTitle($entity_type = NULL, $entity_bundle_id = NULL) {
    $entity_definition = $this->entityManager()->getDefinition($entity_type);

    $entity_bundle = $this->entityManager->getStorage($entity_definition->getBundleEntityType())->load($entity_bundle_id);

    return $this->t('Add %type @entity_label', [
      '@entity_label' => $entity_definition->getLabel(),
      '%type' => $entity_bundle ? $entity_bundle->label() : 'Invalid Bundle',
    ]);
  }

  /**
   * Provides the page title for this controller.
   *
   * @param \Drupal\content_entity_base\Entity\EntityBaseInterface $entity_definition
   *   The custom entity type to add.
   * @param string $action
   *   The action being performed.
   *
   * @return string
   *   The page title.
   */
  public function getEntityFormTitle(EntityBaseInterface $entity_definition, $action = 'default') {
    return $this->t('@action %type @entity_label', [
      '@action' => ucwords(strtolower($action)),
      '@entity_label' => $entity_definition->label(),
      '%type' => $entity_definition->label(),
    ]);
  }

}
