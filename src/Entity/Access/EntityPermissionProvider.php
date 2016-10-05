<?php

namespace Drupal\content_entity_base\Entity\Access;

use Drupal\Core\Entity\EntityHandlerInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides base permissions for use as a `permission_provider` handler in
 * entity definitions.
 *
 * @see tests/modules/ceb_test/src/Entity/CebTestContent.php
 */
class EntityPermissionProvider implements EntityPermissionProviderInterface, EntityHandlerInterface {

  use StringTranslationTrait;

  /**
   * Information about the entity type.
   *
   * @var \Drupal\Core\Entity\EntityTypeInterface
   */
  protected $entity_type;

  /**
   * The entity manager.
   *
   * @var \Drupal\Core\Entity\EntityManagerInterface
   */
  protected $entity_manager;

  /**
   * The original permission base that was proxied by content entity modules.
   *
   * @var \Drupal\content_entity_base\Entity\Access\EntityPermissionProviderInterface
   */
  protected $entity_base_permissions;

  /**
   * Constructs an EntityPermissionProvider object.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type to provide views integration for.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_manager
   *   The entity manager.
   */
  function __construct(EntityTypeInterface $entity_type, EntityTypeManagerInterface $entity_manager) {
    $this->entity_type = $entity_type;
    $this->entity_manager = $entity_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    return new static(
      $entity_type,
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function entityPermissions() {
    // Use the legacy permission provider until it is removed.
    $legacy_permission_provider = EntityBasePermissions::create(\Drupal::getContainer());

    $perms = $legacy_permission_provider->entityPermissions($this->entity_type);

    // Set the provider for the permissions page (@see http://drupal.org/node/2673726).
    array_walk($perms, function (&$perm) {
      $perm['provider'] = $this->entity_type->getProvider();
    });

    return $perms;
  }

}
