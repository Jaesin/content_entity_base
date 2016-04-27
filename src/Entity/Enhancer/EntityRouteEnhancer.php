<?php

namespace Drupal\content_entity_base\Entity\Enhancer;

use Drupal\Core\Routing\Enhancer\RouteEnhancerInterface;
use Symfony\Cmf\Component\Routing\RouteObjectInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;

/**
 * Enhances routes with upcasted entities.
 */
class EntityRouteEnhancer implements RouteEnhancerInterface {

  /**
   * {@inheritdoc}
   */
  public function applies(Route $route) {
    // Check whether there is any entity parameter.
    $parameters = $route->getOption('parameters') ?: [];
    foreach ($parameters as $info) {
      if (isset($info['type']) && strpos($info['type'], 'entity:') === 0) {
        return TRUE;
      }
    }
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function enhance(array $defaults, Request $request) {
    /** @var \Symfony\Component\Routing\Route $route */
    $route = $defaults[RouteObjectInterface::ROUTE_OBJECT];
    $options = $route->getOptions();
    if (isset($options['parameters'])) {
      foreach ($options['parameters'] as $name => $details) {
        if (!empty($details['type']) && strpos($details['type'], 'entity:') !== FALSE) {
          $defaults['_entity'] = $defaults[$name];
          break;
        }
      }
    }

    return $defaults;
  }

}
