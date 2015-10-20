<?php

/**
 * @file
 * Contains \Drupal\content_entity_base\Entity\Routing\RevisionHtmlRouteProvider.
 */

namespace Drupal\content_entity_base\Entity\Routing;

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
    $collection->add('entity.' . $entity_type->id() . '.revision_revert', $route);

    $route = $this->revisionDeleteRoute($entity_type);
    $collection->add('entity.' . $entity_type->id() . '.revision_delete', $route);
    return $collection;
  }

  protected function revisionHistoryRoute(EntityTypeInterface $entity_type) {
    $route = new Route($entity_type->getLinkTemplate('version-history'));
    $route->setDefault('_title', 'Revisions');
    $route->setDefault('_controller', '\Drupal\content_entity_base\Entity\Controller\RevisionController::revisionOverviewController');
    $route->setRequirement('_entity_access_revision', $entity_type->id() . '.view');
    $route->setOption('parameters', [
      $entity_type->id() => [
        'type' => 'entity:' . $entity_type->id(),
      ],
    ]);
    return $route;
  }

  protected function revisionViewRoute(EntityTypeInterface $entity_type) {
    $route = new Route($entity_type->getLinkTemplate('revision'));
    $route->setDefault('_title_callback', '\Drupal\content_entity_base\Entity\Controller\RevisionController::revisionTitle');
    $route->setDefault('_controller', '\Drupal\content_entity_base\Entity\Controller\RevisionController::showRevision');
    $route->setRequirement('_entity_access_revision', $entity_type->id() . '.view');

    $route->setOption('parameters', [
      $entity_type->id() => [
        'type' => 'entity:' . $entity_type->id(),
      ],
      $entity_type->id() . '_revision' => [
        'type' => 'entity_revision:' . $entity_type->id(),
      ],
    ]);
    return $route;
  }

  protected function revisionRevertRoute(EntityTypeInterface $entity_type) {
    $route = new Route($entity_type->getLinkTemplate('revision-revert'));
    $route->setDefault('_form', 'Drupal\content_entity_base\Entity\Form\EntityRevisionRevertForm');
    $route->setDefault('_title', 'Revert to earlier revision');
    $route->setRequirement('_entity_access_revision', $entity_type->id() . '.update');
    $route->setOption('parameters', [
      $entity_type->id() => [
        'type' => 'entity:' . $entity_type->id(),
      ],
      $entity_type->id() . '_revision' => [
        'type' => 'entity_revision:' . $entity_type->id(),
      ],
    ]);
    return $route;
  }

  protected function revisionDeleteRoute(EntityTypeInterface $entity_type) {
    $route = new Route($entity_type->getLinkTemplate('revision-delete'));
    $route->setDefault('_form', 'Drupal\content_entity_base\Entity\Form\EntityRevisionDeleteForm');
    $route->setDefault('_title', 'Delete earlier revision');
    $route->setRequirement('_entity_access_revision', $entity_type->id() . '.delete');
    $route->setOption('parameters', [
      $entity_type->id() => [
        'type' => 'entity:' . $entity_type->id(),
      ],
      $entity_type->id() . '_revision' => [
        'type' => 'entity_revision:' . $entity_type->id(),
      ],
    ]);
    return $route;
  }

}
