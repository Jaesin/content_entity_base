<?php

/**
 * @file
 * Contains \Drupal\content_entity_base\Entity\Routing\CrudUiRouteProvider.
 */

namespace Drupal\content_entity_base\Entity\Routing;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\Routing\EntityRouteProviderInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * Additional common routes needed for a CRUD UI.
 *
 * - add bundle page
 * - a collection page.
 */
class CrudUiRouteProvider implements EntityRouteProviderInterface {

  /**
   * {@inheritdoc}
   */
  public function getRoutes(EntityTypeInterface $entity_type) {
    $routes = new RouteCollection();

    $routes->add('entity.' . $entity_type->id() . '.collection', $this->collectionRoute($entity_type));
    $routes->add('entity.' . $entity_type->id() . '.add_page', $this->addPageRoute($entity_type));
    $routes->add('entity.' . $entity_type->id() . '.add_form', $this->addFormRoute($entity_type));

    return $routes;
  }

  protected function collectionRoute(EntityTypeInterface $entity_type) {
    $route = new Route($entity_type->getLinkTemplate('collection'));
    $route->setDefault('_title', $entity_type->getLabel() . ' content');
    $route->setDefault('_entity_list', $entity_type->id());
    $route->setRequirement('_permission', 'view ' . $entity_type->id() . ' entity');
    return $route;
  }

  protected function addPageRoute(EntityTypeInterface $entity_type) {
    $route = new Route($entity_type->getLinkTemplate('add-page'));
    $route->setDefault('_controller', '\Drupal\content_entity_base\Entity\Controller\EntityBaseController::addPage');
    $route->setDefault('_title_callback', '\Drupal\content_entity_base\Entity\Controller\EntityBaseController::getAddPageTitle');
    $route->setDefault('entity_definition', $entity_type->id());
    $route->setOption('parameters', ['entity_definition' => ['type' => 'entity_definition']]);
    $route->setRequirement('_entity_create_access', $entity_type->id());
    return $route;
  }

  protected function addFormRoute(EntityTypeInterface $entity_type) {
    $route = new Route('entity.' . $entity_type->id() . '.add-form');
    $route->setDefault('_controller', '\Drupal\content_entity_base\Entity\Controller\EntityBaseController::addForm');
    $route->setDefault('_title_callback', '\Drupal\content_entity_base\Entity\Controller\EntityBaseController::getAddFormTitle');
    $route->setDefault('entity_type', $entity_type->id());
    $route->setRequirement('_entity_create_access', $entity_type->id());
    return $route;
  }

}
