<?php
/**
 * @file
 *   Contains \Drupal\content_entity_base\EntityBaseCallbackProxy
 */

namespace Drupal\content_entity_base;


use Drupal\content_entity_base\Entity\Access\EntityBasePermissionsInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class EntityBaseCallbackProxy implements EntityBaseCallbackProxyInterface, ContainerInjectionInterface {

  public static $entity_type;

  public static $bundle_type;

  /**
   * The content entity definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeInterface|null
   */
  protected $entity_type_definition;

  /**
   * The bundle definition for the content entity.
   *
   * @var \Drupal\Core\Entity\EntityTypeInterface|null
   */
  protected $bundle_type_definition;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entity_type_manager;

  /**
   * The CEB permission handler.
   *
   * @var \Drupal\content_entity_base\Entity\Access\EntityBasePermissionsInterface
   */
  protected $permission_handler;

  /**
   * EntityBaseCallbackResolver constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *  The entity type manager service.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, EntityBasePermissionsInterface $permission_handler) {
    $this->entity_type_manager = $entity_type_manager;
    $this->permission_handler = $permission_handler;

    $this->entity_type_definition = $this->entity_type_manager->getDefinition(static::$entity_type);
    $this->bundle_type_definition = $this->entity_type_manager->getDefinition(static::$bundle_type);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('content_entity_base.permission_handler')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function entityPermissions() {
    return $this->permission_handler->entityPermissions($this->entity_type_definition);
  }
}
