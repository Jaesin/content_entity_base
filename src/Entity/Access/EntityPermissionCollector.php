<?php
/**
 * @file
 *   Contains \Drupal\content_entity_base\Entity\Access\EntityPermissionCollector.
 */

namespace Drupal\content_entity_base\Entity\Access;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Routing\UrlGeneratorTrait;
use Drupal\Core\StringTranslation\StringTranslationTrait;


/**
 * Iterates content entities and aggregates permissions provided by their
 * permission provider handler classes.
 */
class EntityPermissionCollector implements ContainerInjectionInterface {

  use StringTranslationTrait;
  use UrlGeneratorTrait;

  /**
   * @var \Drupal\Core\Entity\EntityManagerInterface
   */
  protected $entityManager;

  /**
   * Creates Drupal\content_entity_base\Entity\Access\EntityBasePermissions.
   *
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   */
  public function __construct(EntityManagerInterface $entity_manager) {
    $this->entityManager = $entity_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.manager')
    );
  }

  public function providedPermissions() {
    // Get entities which have a permission_provider defined.
    $content_entity_collection = array_filter($this->entityManager->getDefinitions(), function ($entity_definition) {
      return ($this->entityManager->hasHandler($entity_definition->id(), 'permission_provider'));
    });

    // Get all permission  provider handlers.
    $providers = array_map(function ($entity_definition) {
      $handler = $this->entityManager->getHandler($entity_definition->id(), 'permission_provider');
      return $handler;
    }, $content_entity_collection);

    $permissions = [];
    foreach ($providers as $entity_type_id => $handler) {
      $permissions +=  $handler->entityPermissions();
    }
    return $permissions;
  }

}
