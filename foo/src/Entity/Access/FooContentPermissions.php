<?php

/**
 * @file
 *   Contains \Drupal\foo\Entity\Access\FooContentPermissions.
 */

namespace Drupal\foo\Entity\Access;

use Drupal\content_entity_base\Entity\Access\EntityBasePermissions;
use Drupal\Core\Entity\ContentEntityTypeInterface;

/**
 * Defines a class containing permission callbacks.
 */
class FooContentPermissions extends EntityBasePermissions {

  /**
   * @inheritdoc{}
   */
  public function entityPermissions(ContentEntityTypeInterface $entity = NULL) {
    return parent::entityPermissions(\Drupal::entityManager()->getDefinition('foo_content'));
  }
}
