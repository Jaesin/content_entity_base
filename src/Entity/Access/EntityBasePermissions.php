<?php

/**
 * @file
 * Contains \Drupal\content_entity_base\Entity\Access\EntityBasePermissions.
 */

namespace Drupal\content_entity_base\Entity\Access;

use Drupal\content_entity_base\Entity\EntityTypeBaseInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\ContentEntityTypeInterface;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Routing\UrlGeneratorTrait;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines a class containing permission callbacks.
 */
class EntityBasePermissions implements ContainerInjectionInterface {

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

  /**
   * Gets an array of entity type permissions.
   *
   * @param ContentEntityTypeInterface $entity_type
   *   The custom entity definition.
   *
   * @return array
   *   The entity type permissions.
   *
   * @see \Drupal\user\PermissionHandlerInterface::getPermissions()
   */
  public function entityPermissions(ContentEntityTypeInterface $entity_type = NULL) {
    $perms = [];

    if (!empty($entity_type)) {
      // Get the entity ID.
      $entity_type_id = $entity_type->id();
      // Build replacement data for lables and descriptions.
      $replacements = [
        '@entity_type_id' => $entity_type_id,
        '@entity_label' => $entity_type->getLabel(),
      ];
      // Add the default entity permissions.
      $perms = [
        "bypass $entity_type_id access" => [
          'title' => $this->t('Bypass @entity_label access control', $replacements),
          'description' => $this->t('View, edit and delete all @entity_label regardless of permission restrictions.', $replacements),
          'restrict access' => TRUE,
        ],
        "administer $entity_type_id types" => [
          'title' => $this->t('Administer @entity_label types', $replacements),
          'description' => $this->t('Promote, change ownership, edit revisions, and perform other tasks across all @entity_label types.', $replacements),
          'restrict access' => TRUE,
        ],
        "administer $entity_type_id" => [
          'title' => $this->t('Administer @entity_label', $replacements),
          'restrict access' => TRUE,
        ],
        "access $entity_type_id overview" => [
          'title' => $this->t('Access the @entity_label overview page', $replacements),
          'description' => $this->t('Get an overview of all @entity_label.', $replacements),
        ],
        "access $entity_type_id" => [
          'title' => $this->t('View published @entity_label', $replacements),
        ],
        "view own unpublished $entity_type_id" => [
          'title' => $this->t('View own unpublished @entity_label', $replacements),
        ],
        "view all $entity_type_id revisions" => [
          'title' => $this->t('View all @entity_label revisions', $replacements),
        ],
        "revert all $entity_type_id revisions" => [
          'title' => $this->t('Revert all @entity_label revisions', $replacements),
          'description' => $this->t('Role requires permission <em>View all @entity_label revisions</em> and <em>edit rights</em> for @entity_label in question or <em>Administer @entity_label</em>.', $replacements),
        ],
        "delete all $entity_type_id revisions" => [
          'title' => $this->t('Delete all @entity_label revisions', $replacements),
          'description' => $this->t('Role requires permission to <em>View all @entity_label revisions</em> and <em>delete rights</em> for @entity_label in question or <em>Administer @entity_label</em>.', $replacements),
        ],
      ];
      // Load bundles if any are defined.
      if (($entity_type_storage = $this->entityManager->getStorage($entity_type->getBundleEntityType()))
        && ($entity_types = $entity_type_storage->loadMultiple())) {
        // Generate entity permissions for all types for this entity.
        foreach ($entity_types as $type) {
          $perms += $this->buildPermissions($type);
        }
      }
    }

    return $perms;
  }

  /**
   * Builds a standard list of entity permissions for a given type.
   *
   * @param \Drupal\content_entity_base\Entity\EntityTypeBaseInterface $type
   *   The machine name of the entity type.
   *
   * @return array
   *   An array of permission names and descriptions.
   */
  protected function buildPermissions(EntityTypeBaseInterface $type) {

    $entity_id = $type->bundleOf();
    // Get the referring entity definition.
    $entity_definition = $this->entityManager->getDefinition($entity_id);
    $type_id = $type->id();
    $type_params = [
      '%entity_label' => $entity_definition->getLabel(),
      '%type_name' => $type->label(),
    ];

    return [
      "create $type_id $entity_id" => [
        'title' => $this->t('%type_name: Create new %entity_label', $type_params),
      ],
      "edit own $type_id $entity_id" => [
        'title' => $this->t('%type_name: Edit own %entity_label', $type_params),
      ],
      "edit any $type_id $entity_id" => [
        'title' => $this->t('%type_name: Edit any %entity_label', $type_params),
      ],
      "delete own $type_id $entity_id" => [
        'title' => $this->t('%type_name: Delete own %entity_label', $type_params),
      ],
      "delete any $type_id $entity_id" => [
        'title' => $this->t('%type_name: Delete any %entity_label', $type_params),
      ],
      "view $type_id $entity_id revisions" => [
        'title' => $this->t('%type_name: View %entity_label revisions', $type_params),
      ],
      "revert $type_id $entity_id revisions" => [
        'title' => $this->t('%type_name: Revert %entity_label revisions', $type_params),
        'description' => t('Role requires permission <em>view revisions</em> and <em>edit rights</em> for %entity_label in question, or <em>Administer %entity_label</em>.', $type_params),
      ],
      "delete $type_id $entity_id revisions" => [
        'title' => $this->t('%type_name: Delete %entity_label revisions', $type_params),
        'description' => $this->t('Role requires permission to <em>view revisions</em> and <em>delete rights</em> for %entity_label in question, or <em>Administer %entity_label</em>.', $type_params),
      ],
    ];
  }
}
