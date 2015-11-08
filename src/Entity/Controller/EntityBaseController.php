<?php

/**
 * @file
 * Contains \Drupal\content_entity_base\Entity\Controller\EntityBaseController.
 */

namespace Drupal\content_entity_base\Entity\Controller;

use Drupal\content_entity_base\Entity\EntityBaseInterface;
use Drupal\content_entity_base\Entity\EntityTypeBaseInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\ContentEntityTypeInterface;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;

class EntityBaseController extends ControllerBase {

  /**
   * Displays add custom entity links for available types.
   *
   * @param \Drupal\Core\Entity\ContentEntityTypeInterface $entity_definition
   *   The custom entity definition.
   *
   * @return array
   *   A render array for a list of the custom entity types that can be added or
   *   if there is only one custom entity type defined for the site, the function
   *   returns the custom entity add page for that custom entity type.
   */
  public function addPage(ContentEntityTypeInterface $entity_definition = NULL) {
    // Get the storage controller for this entity.
    $bundle_storage = $entity_definition
      ? $this->entityManager()->getStorage($entity_definition->getBundleEntityType())
      : NULL;
    // Load all entity types for this entity definition.
    $types = $bundle_storage->loadMultiple();

    // Check for existing types.
    if ($types && count($types) == 1) {
      $type = reset($types);
      return $this->addForm($entity_definition, $type);
    }
    if (count($types) === 0) {
      return [
        '#markup' => $this->t('You have not created any @entity_label types yet. Go to the <a href=":url">@entity_label type creation page</a> to add a new @entity_label type.', [
          '@entity_label' => $entity_definition->getLabel(),
          ':url' => Url::fromRoute('entity.' . $entity_definition->getBundleEntityType() . '.add_form')->toString(),
        ]),
      ];
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
        'url' => new Url('entity.'.$entity_definition->id().'.add_form', ['entity_bundle' => $type->id()], ['query' => $query]),
      ];
    }

    return $build;
  }

  /**
   * Gets the title for the "Entity Add" page.
   *
   * @param \Drupal\Core\Entity\ContentEntityTypeInterface $entity_definition
   *   The custom entity definition.
   *
   * @return string
   *   The title customized for this entity type.
   */
  public function getAddPageTitle(ContentEntityTypeInterface $entity_definition = NULL) {
    // Get the storage controller for this entity.
    $bundle_storage = $entity_definition
      ? $this->entityManager()->getStorage($entity_definition->getBundleEntityType())
      : NULL;
    // Load all entity types for this entity definition.
    $types = $bundle_storage->loadMultiple();

    // Check for existing types.
    $entity_bundle  = $types && count($types) == 1 ? reset($types)  : FALSE;

    return $this->t($entity_bundle ? 'Add %type @entity_label content' : 'Add @entity_label', [
      '@entity_label' => $entity_definition->getLabel(),
      '%type' => $entity_bundle ? $entity_bundle->label() : '',
    ]);
  }

  /**
   * Presents the custom entity creation form.
   *
   * @param \Drupal\Core\Entity\ContentEntityTypeInterface $entity_definition
   *   The custom entity definition.
   * @param \Drupal\content_entity_base\Entity\EntityTypeBaseInterface $entity_bundle
   *   The custom entity type bundle to use.
   *
   * @return array
   *   A form array as expected by drupal_render().
   */
  public function addForm(ContentEntityTypeInterface $entity_definition = NULL, EntityTypeBaseInterface $entity_bundle = NULL) {
    // Validate the bundle.
    if (!$entity_bundle || !$entity_definition) {
      // @todo Replace it with https://www.drupal.org/node/2571521.
      \Drupal::logger('content_entity_base')->error($this->t('Cannot create a @entity_type entity with an invalid bundle type of "%entity_bundle_id".'), [
        '@entity_type' => (string) $entity_definition,
        '%entity_bundle_id' => (string) $entity_bundle,
      ]);
      return new RedirectResponse(Url::fromRoute($entity_definition . '.add_page')->toString());
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
   * @param \Drupal\Core\Entity\ContentEntityTypeInterface $entity_definition
   *   The custom entity definition.
   * @param \Drupal\content_entity_base\Entity\EntityTypeBaseInterface $entity_bundle
   *   The custom entity type bundle to use.
   *
   * @return string
   *   The page title.
   */
  public function getAddFormTitle(ContentEntityTypeInterface $entity_definition = NULL, EntityTypeBaseInterface $entity_bundle = NULL) {
    // Build the form page title using the type.
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
