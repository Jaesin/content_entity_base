<?php

/**
 * @file
 * Contains \Drupal\ceb_test\Entity\Access\CebTestContentPermissions.
 */

namespace Drupal\ceb_test\Entity\Access;

use Drupal\content_entity_base\Entity\Access\EntityBasePermissions;
use Drupal\Core\Entity\ContentEntityTypeInterface;

/**
 * Defines a class containing permission callbacks.
 */
class CebTestContentPermissions extends EntityBasePermissions {

  /**
   * {@inheritdoc}
   *
   * @todo Leverage https://www.drupal.org/node/2652684
   */
  public function entityPermissions(ContentEntityTypeInterface $entity_type = NULL) {
    return parent::entityPermissions(\Drupal::entityManager()->getDefinition('ceb_test_content'));
  }

}
