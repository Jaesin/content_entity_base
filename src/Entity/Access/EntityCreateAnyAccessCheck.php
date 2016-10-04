<?php

namespace Drupal\content_entity_base\Entity\Access;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Routing\Access\AccessInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\Routing\Route;

/**
 * Defines an access checker for creating an entity of any bundle.
 */
class EntityCreateAnyAccessCheck implements AccessInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The entity type bundle info.
   *
   * @var \Drupal\Core\Entity\EntityTypeBundleInfoInterface
   */
  protected $entityTypeBundleInfo;

  /**
   * The key used by the routing requirement.
   *
   * @var string
   */
  protected $requirementsKey = '_entity_create_any_access';

  /**
   * Constructs a EntityCreateAnyAccessCheck object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Entity\EntityTypeBundleInfoInterface $entity_type_bundle_info
   *   The entity type bundle info.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, EntityTypeBundleInfoInterface $entity_type_bundle_info) {
    $this->entityTypeManager = $entity_type_manager;
    $this->entityTypeBundleInfo = $entity_type_bundle_info;
  }

  /**
   * Checks access to create an entity of any bundle for the given route.
   *
   * @param \Symfony\Component\Routing\Route $route
   *   The route to check against.
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The parameterized route.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The currently logged in account.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
   */
  public function access(Route $route, RouteMatchInterface $route_match, AccountInterface $account) {
    $entity_type_id = $route->getRequirement($this->requirementsKey);
    $entity_type = $this->entityTypeManager->getDefinition($entity_type_id);
    $access_control_handler = $this->entityTypeManager->getAccessControlHandler($entity_type_id);

    // In case there is no "bundle" entity key, check create access with no
    // bundle specified.
    if (!$entity_type->hasKey('bundle')) {
      return $access_control_handler->createAccess(NULL, $account, [], TRUE);
    }

    list($entity_type, $bundle) = explode(':', $route->getRequirement($this->requirementsKey) . ':');

    // The bundle argument can contain request argument placeholders like
    // {name}, loop over the raw variables and attempt to replace them in the
    // bundle name. If a placeholder does not exist, it won't get replaced.
    if ($bundle && strpos($bundle, '{') !== FALSE) {
      foreach ($route_match->getRawParameters()->all() as $name => $value) {
        $bundle = str_replace('{' . $name . '}', $value, $bundle);
      }
      // If we were unable to replace all placeholders, deny access.
      if (strpos($bundle, '{') !== FALSE) {
        return AccessResult::neutral();
      }
    }
    return $this->entityTypeManager->getAccessControlHandler($entity_type)->createAccess($bundle, $account, [], TRUE);
  }

}
