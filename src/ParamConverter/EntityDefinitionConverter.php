<?php
/**
 * @file
 *   Contains \Drupal\content_entity_base\ParamConverter\EntityDefinitionConverter.
 */

namespace Drupal\content_entity_base\ParamConverter;


use Drupal\Core\ParamConverter\EntityConverter;
use Symfony\Component\Routing\Route;


class EntityDefinitionConverter extends EntityConverter {

  /**
   * {@inheritdoc}
   */
  public function convert($value, $definition, $name, array $defaults) {
    if (!empty($value)) {
      return $this->entityManager->getDefinition($value);
    }
    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function applies($definition, $name, Route $route) {
    return (!empty($definition['type']) && $definition['type'] == 'entity_definition');
  }
}
