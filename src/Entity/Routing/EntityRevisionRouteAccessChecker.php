<?php

/**
 * @file
 * Contains
 *   \Drupal\content_entity_base\Entity\Routing\EntityRevisionRouteAccessChecker.
 */

namespace Drupal\content_entity_base\Entity\Routing;

use Drupal\content_entity_base\Entity\Storage\RevisionableStorageInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Routing\Access\AccessInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\Routing\Route;

/**
 * Checks access to a entity revision.
 */
class EntityRevisionRouteAccessChecker implements AccessInterface {

  use RevisionObjectExtractionTrait;

  /**
   * @var \Drupal\Core\Entity\EntityManagerInterface
   */
  protected $entityManager;

  /**
   * @var array
   */
  protected $access;

  /**
   * Creates a new EntityRevisionRouteAccessChecker instance.
   *
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   *   The entity manager.
   */
  public function __construct(EntityManagerInterface $entity_manager) {
    $this->entityManager = $entity_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function access(Route $route, AccountInterface $account, RouteMatchInterface $route_match, $entity_revision = NULL, $_entity_revision = NULL) {
    $entity = $_entity_revision ?: $this->extractEntityFromRouteMatch($route_match);
    if ($entity_revision) {
      $entity = $this->entityManager->getStorage($entity->getEntityTypeId())->loadRevision($entity_revision);
    }
    $operation = $route->getRequirement('_entity_access_revision');
    list(, $operation) = explode('.', $operation, 2);
    return AccessResult::allowedIf($entity && $this->checkAccess($entity, $account, $operation))->cachePerPermissions();
  }

  protected function checkAccess(ContentEntityInterface $entity, AccountInterface $account, $operation = 'view') {
    $entity_type_id = $entity->getEntityTypeId();
    $entity_access = $this->entityManager->getAccessControlHandler($entity_type_id);

    /** @var \Drupal\content_entity_base\Entity\Storage\RevisionableStorageInterface|\Drupal\Core\Entity\EntityStorageInterface $entity_storage */
    $entity_storage = $this->entityManager->getStorage($entity_type_id);
    if (!$entity_storage instanceof RevisionableStorageInterface) {
      throw new \InvalidArgumentException('The entity storage has to implement \Drupal\content_entity_base\Entity\Storage\RevisionableStorageInterface');
    }

    $map = [
      'view' => "view all $entity_type_id revisions",
      'update' => "revert all $entity_type_id revisions",
      'delete' => "delete all $entity_type_id revisions",
    ];
    $bundle = $entity->bundle();
    $type_map = [
      'view' => "view $entity_type_id $bundle revisions",
      'update' => "revert $entity_type_id $bundle revisions",
      'delete' => "delete $entity_type_id $bundle revisions",
    ];

    if (!$entity || !isset($map[$operation]) || !isset($type_map[$operation])) {
      // If there was no node to check against, or the $op was not one of the
      // supported ones, we return access denied.
      return FALSE;
    }

    // Statically cache access by revision ID, language code, user account ID,
    // and operation.
    $langcode = $entity->language()->getId();
    $cid = $entity->getRevisionId() . ':' . $langcode . ':' . $account->id() . ':' . $operation;

    if (!isset($this->access[$cid])) {
      // Perform basic permission checks first.
      if (!$account->hasPermission($map[$operation]) && !$account->hasPermission($type_map[$operation]) && !$account->hasPermission('administer nodes')) {
        $this->access[$cid] = FALSE;
        return FALSE;
      }

      // There should be at least two revisions. If the vid of the given node
      // and the vid of the default revision differ, then we already have two
      // different revisions so there is no need for a separate database check.
      // Also, if you try to revert to or delete the default revision, that's
      // not good.
      if ($entity->isDefaultRevision() && ($entity_storage->countDefaultLanguageRevisions($entity) == 1 || $operation == 'update' || $operation == 'delete')) {
        $this->access[$cid] = FALSE;
      }
      elseif ($account->hasPermission('administer ' . $entity_type_id)) {
        $this->access[$cid] = TRUE;
      }
      else {
        // First check the access to the default revision and finally, if the
        // node passed in is not the default revision then access to that, too.
        $this->access[$cid] = $entity_access->access($entity_storage->load($entity->id()), $operation, $account) && ($entity->isDefaultRevision() || $entity_access->access($entity, $operation, $account));
      }
    }

    return $this->access[$cid];
  }

  public function resetAccessCache() {
    $this->access = [];
    return $this;
  }

}
