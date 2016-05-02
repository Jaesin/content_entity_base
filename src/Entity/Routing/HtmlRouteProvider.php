<?php

namespace Drupal\content_entity_base\Entity\Routing;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider;
use Symfony\Component\Routing\Route;

class HtmlRouteProvider extends DefaultHtmlRouteProvider {

  /**
   * {@inheritdoc}
   */
  public function getRoutes(EntityTypeInterface $entity_type) {
    $routes = parent::getRoutes($entity_type);

    $routes->add('entity.' . $entity_type->id() . '.collection', $this->collectionRoute($entity_type));

    return $routes;
  }

  protected function collectionRoute(EntityTypeInterface $entity_type) {
    $route = new Route($entity_type->getLinkTemplate('collection'));
    $route->setDefault('_title', $entity_type->getLabel() . ' content');
    $route->setDefault('_entity_list', $entity_type->id());
    $route->setRequirement('_permission', 'view ' . $entity_type->id() . ' entity');
    return $route;
  }

}
