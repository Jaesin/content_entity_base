<?php

namespace Drupal\content_entity_base\Entity\Access;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Routing\UrlGeneratorTrait;
use Drupal\Core\StringTranslation\StringTranslationTrait;


/**
 * Iterates content entities and aggregates permissions provided by their
 * permission provider handler classes.
 */
class EntityPermissionBuilder implements ContainerInjectionInterface {

  use StringTranslationTrait;

  /**
   * @var \Drupal\Core\Entity\EntityManagerInterface
   */
  protected $entity_type_manager;

  /**
   * Creates Drupal\content_entity_base\Entity\Access\EntityBasePermissions.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_manager
   */
  public function __construct(EntityTypeManagerInterface $entity_manager) {
    $this->entity_type_manager = $entity_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  public function providedPermissions() {
    // Get entities which have a permission_provider defined.
    $content_entity_collection = array_filter($this->entity_type_manager->getDefinitions(), function ($entity_definition) {
      return ($this->entity_type_manager->hasHandler($entity_definition->id(), 'permission_provider'));
    });

    // Get all permission  provider handlers.
    $providers = array_map(function ($entity_definition) {
      $handler = $this->entity_type_manager->getHandler($entity_definition->id(), 'permission_provider');
      return $handler;
    }, $content_entity_collection);

    $permissions = [];
    foreach ($providers as $entity_type_id => $handler) {
      $permissions +=  $handler->entityPermissions();
    }
    return $permissions;
  }

}
