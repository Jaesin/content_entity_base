<?php
/**
 * @file
 *   Contains \Drupal\content_entity_base\Entity\Access\EntityBasePermissions.
 */

namespace Drupal\content_entity_base\Entity\Access;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Routing\UrlGeneratorTrait;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Defines a class containing permission callbacks.
 *
 * @deprecated in 8.0.x, will be removed before Drupal 8.1.x.
 *   Use the permission_provider entity handler to specify a permission handler.
 */
class EntityBasePermissions implements ContainerInjectionInterface {

  use StringTranslationTrait;
  use UrlGeneratorTrait;

  /**
   * @var \Drupal\Core\Entity\EntityManagerInterface
   */
  protected $entity_manager;

  /**
   * Creates Drupal\content_entity_base\Entity\Access\EntityBasePermissions.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_manager
   */
  public function __construct(EntityTypeManagerInterface $entity_manager) {
    $this->entity_manager = $entity_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  /**
   * Gets an array of entity permissions.
   *
   * @param EntityTypeInterface $entity_type
   *   The custom entity definition.
   *
   * @return array
   *   The permissions for the provided entity type.
   */
  public function entityPermissions(EntityTypeInterface $entity_type) {
    // Create a permission provider for the provided entity type.
    $permission_provider = EntityPermissionProvider::createInstance($entity_type, $this->entity_manager);

    // Get entity permissions from the default permission_provider.
    return $permission_provider->entityPermissions();
  }
}
