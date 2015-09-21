<?php

/**
 * @file
 *   Contains Drupal\foo_entity\Entity\Views\FooContentViewsData.
 */

namespace Drupal\foo_entity\Entity\Views;

use Drupal\content_entity_base\Entity\Views\EntityBaseViewsData;

/**
 * Provides the views data for the Foo content entity type.
 */
class FooContentViewsData extends EntityBaseViewsData {
  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    return parent::getViewsData($this->entityManager->getDefinition('foo_content'));
  }
}
