<?php

namespace Drupal\content_entity_base\Entity\Routing;

use Drupal\content_entity_base\Entity\Controller\EntityBaseController;
use Drupal\Core\Config\Entity\ConfigEntityTypeInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityHandlerInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\FieldableEntityInterface;
use Drupal\Core\Entity\Routing\EntityRouteProviderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * Provides HTML routes for creating entities.
 *
 * This class provides the following routes for entities, with title callbacks:
 * - add-page
 * - add-form
 *
 * @internal This will be removed in a future version as getAddPageRoute and
 *   getAddFormRoute are being provided by
 *   \Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider in drupal 8.1.x.
 */
class CreateHtmlRouteProvider implements EntityRouteProviderInterface, EntityHandlerInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The entity field manager.
   *
   * @var \Drupal\Core\Entity\EntityFieldManagerInterface
   */
  protected $entityFieldManager;

  /**
   * Constructs a new DefaultHtmlRouteProvider.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Entity\EntityFieldManagerInterface $entity_field_manager
   *   The entity field manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, EntityFieldManagerInterface $entity_field_manager) {
    $this->entityTypeManager = $entity_type_manager;
    $this->entityFieldManager = $entity_field_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('entity_field.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getRoutes(EntityTypeInterface $entity_type) {
    $collection = new RouteCollection();

    $entity_type_id = $entity_type->id();

    if ($add_page_route = $this->getAddPageRoute($entity_type)) {
      $collection->add("entity.{$entity_type_id}.add_page", $add_page_route);
    }

    if ($add_form_route = $this->getAddFormRoute($entity_type)) {
      $collection->add("entity.{$entity_type_id}.add_form", $add_form_route);
    }

    return $collection;
  }

  /**
   * Gets the add page route.
   *
   * Built only for entity types that have bundles.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type.
   *
   * @return \Symfony\Component\Routing\Route|null
   *   The generated route, if available.
   */
  protected function getAddPageRoute(EntityTypeInterface $entity_type) {
    if ($entity_type->hasLinkTemplate('add-page') && $entity_type->getKey('bundle')) {
      $route = new Route($entity_type->getLinkTemplate('add-page'));
      $route->setDefault('_controller', EntityBaseController::class . '::addPage');
      $route->setDefault('_title_callback', EntityBaseController::class . '::addTitle');
      $route->setDefault('entity_type_id', $entity_type->id());
      $route->setRequirement('_entity_create_any_access', $entity_type->id());

      return $route;
    }
  }

  /**
   * Gets the add-form route.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type.
   *
   * @return \Symfony\Component\Routing\Route|null
   *   The generated route, if available.
   */
  protected function getAddFormRoute(EntityTypeInterface $entity_type) {
    if ($entity_type->hasLinkTemplate('add-form')) {
      $entity_type_id = $entity_type->id();
      $route = new Route($entity_type->getLinkTemplate('add-form'));
      // Use the add form handler, if available, otherwise default.
      $operation = 'default';
      if ($entity_type->getFormClass('add')) {
        $operation = 'add';
      }
      $route->setDefaults([
        '_entity_form' => "{$entity_type_id}.{$operation}",
        'entity_type_id' => $entity_type_id,
      ]);

      // If the entity has bundles, we can provide a bundle-specific title
      // and access requirements.
      $expected_parameter = $entity_type->getBundleEntityType() ?: $entity_type->getKey('bundle');
      // @todo: We have to check if a route contains a bundle in its path as
      //   test entities have inconsistent usage of "add-form" link templates.
      //   Fix it in https://www.drupal.org/node/2699959.
      if (($bundle_key = $entity_type->getKey('bundle')) && strpos($route->getPath(), '{' . $expected_parameter . '}') !== FALSE) {
        $route->setDefault('_title_callback', EntityBaseController::class . '::addBundleTitle');
        // If the bundles are entities themselves, we can add parameter
        // information to the route options.
        if ($bundle_entity_type_id = $entity_type->getBundleEntityType()) {
          $bundle_entity_type = $this->entityTypeManager->getDefinition($bundle_entity_type_id);

          $route
            // The title callback uses the value of the bundle parameter to
            // fetch the respective bundle at runtime.
            ->setDefault('bundle_parameter', $bundle_entity_type_id)
            ->setRequirement('_entity_create_access', $entity_type_id . ':{' . $bundle_entity_type_id . '}');

          // Entity types with serial IDs can specify this in their route
          // requirements, improving the matching process.
          if ($this->getEntityTypeIdKeyType($bundle_entity_type) === 'integer') {
            $route->setRequirement($entity_type_id, '\d+');
          }

          $bundle_entity_parameter = ['type' => 'entity:' . $bundle_entity_type_id];
          if ($bundle_entity_type instanceof ConfigEntityTypeInterface) {
            // The add page might be displayed on an admin path. Even then, we
            // need to load configuration overrides so that, for example, the
            // bundle label gets translated correctly.
            // @see \Drupal\Core\ParamConverter\AdminPathConfigEntityConverter
            $bundle_entity_parameter['with_config_overrides'] = TRUE;
          }
          $route->setOption('parameters', [$bundle_entity_type_id => $bundle_entity_parameter]);
        }
        else {
          // If the bundles are not entities, the bundle key is used as the
          // route parameter name directly.
          $route
            ->setDefault('bundle_parameter', $bundle_key)
            ->setRequirement('_entity_create_access', $entity_type_id . ':{' . $bundle_key . '}');
        }
      }
      else {
        $route
          ->setDefault('_title_callback', EntityBaseController::class . '::addTitle')
          ->setRequirement('_entity_create_access', $entity_type_id);
      }

      return $route;
    }
  }

  /**
   * Gets the type of the ID key for a given entity type.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   An entity type.
   *
   * @return string|null
   *   The type of the ID key for a given entity type, or NULL if the entity
   *   type does not support fields.
   */
  protected function getEntityTypeIdKeyType(EntityTypeInterface $entity_type) {
    if (!$entity_type->isSubclassOf(FieldableEntityInterface::class)) {
      return NULL;
    }

    $field_storage_definitions = $this->entityFieldManager->getFieldStorageDefinitions($entity_type->id());
    return $field_storage_definitions[$entity_type->getKey('id')]->getType();
  }

}
