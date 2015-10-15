<?php

/**
 * @file
 * Contains
 *   \Drupal\content_entity_base\Entity\Routing\RevisionObjectExtractionTrait.
 */

namespace Drupal\content_entity_base\Entity\Routing;

use Drupal\Core\Routing\RouteMatchInterface;

trait RevisionObjectExtractionTrait {

  /**
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *
   * @return \Drupal\Core\Entity\ContentEntityInterface
   *
   * @throws \Exception
   *   Thrown when no entity was found.
   */
  protected function extractEntityFromRouteMatch(RouteMatchInterface $route_match) {
    $route = $route_match->getRouteObject();
    $options = $route->getOptions();
    if (isset($options['parameters'])) {
      foreach ($options['parameters'] as $name => $details) {
        if (!empty($details['type']) && strpos($details['type'], 'entity_revision:') !== FALSE) {
          return $route_match->getParameter($name);
        }
      }
      foreach ($options['parameters'] as $name => $details) {
        if (!empty($details['type']) && strpos($details['type'], 'entity:') !== FALSE) {
          return $route_match->getParameter($name);
        }
      }
    }

    throw new \Exception('No entity found');
  }

}
