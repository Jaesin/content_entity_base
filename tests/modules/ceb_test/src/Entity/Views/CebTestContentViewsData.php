<?php

/**
 * @file
 *   Contains Drupal\ceb_test\Entity\Views\CebTestContentViewsData.
 */

namespace Drupal\ceb_test\Entity\Views;

use Drupal\content_entity_base\Entity\Views\EntityBaseViewsData;

/**
 * Provides the views data for the CEB test content entity type.
 */
class CebTestContentViewsData extends EntityBaseViewsData {
  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    return parent::getViewsData($this->entityManager->getDefinition('ceb_test_content'));
  }
}
