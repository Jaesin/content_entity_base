<?php

/**
 * @file
 * Contains \Drupal\content_entity_base\Routing\RevisionHtmlRouteProvider.
 */

namespace Drupal\content_entity_base\Routing;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\Routing\EntityRouteProviderInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class RevisionHtmlRouteProvider implements EntityRouteProviderInterface {

  public function getRoutes(EntityTypeInterface $entity_type) {
    $collection = new RouteCollection();

    $route = $this->revisionHistoryRoute($entity_type);
    $collection->add('entity.' . $entity_type->id() . '.version_history', $route);

    $route = $this->revisionViewRoute($entity_type);
    $collection->add('entity.' . $entity_type->id() . '.revision', $route);

    $route = $this->revisionRevertRoute($entity_type);
    $collection->add('entity.' . $entity_type->id() . '.revision_revert_confirm', $route);

    $route = $this->revisionDeleteRoute($entity_type);
    $collection->add('entity.' . $entity_type->id() . '.revision_delete_confirm', $route);
  }

  protected function revisionHistoryRoute(EntityTypeInterface $entity_type) {
    $route = new Route($entity_type->getLinkTemplate('canonical') . '/revisions');
    $route->setDefault('_title', 'Revisions');
    $route->setDefault('_controller', '');
    $route->setRequirement('_access_entity_revision', 'view');
  }

  protected function revisionViewRoute(EntityTypeInterface $entity_type) {
    $route = new Route($entity_type->getLinkTemplate('canonical') . '/revisions/{entity_revision}/view');
    $route->setDefault('_title', 'Revisions');
    $route->setDefault('_controller', '');
    $route->setRequirement('_access_entity_revision', 'view');
  }

  protected function revisionRevertRoute(EntityTypeInterface $entity_type) {
  }

  protected function revisionDeleteRoute(EntityTypeInterface $entity_type) {
  }

entity.node.revision:
  path: '/node/{node}/revisions/{node_revision}/view'
  defaults:
    _controller: '\Drupal\node\Controller\NodeController::revisionShow'
    _title_callback: '\Drupal\node\Controller\NodeController::revisionPageTitle'
  requirements:
    _access_node_revision: 'view'

node.revision_revert_confirm:
  path: '/node/{node}/revisions/{node_revision}/revert'
  defaults:
    _form: '\Drupal\node\Form\NodeRevisionRevertForm'
    _title: 'Revert to earlier revision'
  requirements:
    _access_node_revision: 'update'
  options:
    _node_operation_route: TRUE

node.revision_delete_confirm:
  path: '/node/{node}/revisions/{node_revision}/delete'
  defaults:
    _form: '\Drupal\node\Form\NodeRevisionDeleteForm'
    _title: 'Delete earlier revision'
  requirements:
    _access_node_revision: 'delete'
  options:
    _node_operation_route: TRUE

}
