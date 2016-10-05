<?php

namespace Drupal\content_entity_base\Entity\Controller;

use Drupal\content_entity_base\Entity\EntityTypeBaseInterface;
use Drupal\Core\Controller\ControllerBase;

class EntityTypeBaseController extends ControllerBase {

  /**
   * Provides the page title for this controller.
   *
   * @param string $entity_type
   *   The entity type id for the custom entity type.
   * @param string $title_format
   *   The title  to use for substitutions..
   *
   * @return string
   *   The page title.
   */
  public function getEntityTypeFormTitle($entity_type = NULL, $title_format = 'default') {
    $entity = $this->entityTypeManager()->getStorage($entity_type)->getEntityType();
    $bundle_of_label = $entity instanceof EntityTypeBaseInterface
      ? $this->entityTypeManager()->getDefinition($entity->bundleOf())->getLabel()
      : '';
    // @todo Add indefinite articles for the labels like: https://github.com/Kaivosukeltaja/php-indefinite-article
    return $this->t($title_format, [
      '@entity_label' => $entity->label(),
      '@bundle_of_label' => $bundle_of_label,
      '%type' => $entity->label(),
    ]);
  }

}
