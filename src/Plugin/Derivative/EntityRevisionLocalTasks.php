<?php

/**
 * @file
 * Contains
 *   \Drupal\content_entity_base\Plugin\Derivative\EntityRevisionLocalTasks.
 */

namespace Drupal\content_entity_base\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\content_entity_base\Entity\Routing\RevisionHtmlRouteProvider;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Symfony\Component\DependencyInjection\ContainerInterface;

class EntityRevisionLocalTasks extends DeriverBase implements ContainerDeriverInterface {

  /** @var \Drupal\Core\Entity\EntityManagerInterface */
  protected $entityManager;

  /**
   * {@inheritdoc}
   */
  protected $derivatives = NULL;

  /**
   * Creates a new EntityRevisionLocalTasks instance.
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
  public static function create(ContainerInterface $container, $base_plugin_id) {
    return new static(
      $container->get('entity.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    if (!isset($this->derivatives)) {
      $this->derivatives = [];
      foreach ($this->entityManager->getDefinitions() as $entity_type_id => $entity_type) {
        foreach ($entity_type->getRouteProviderClasses() as $route_provider_type => $route_provider) {
          if ($route_provider == '\\' . RevisionHtmlRouteProvider::class || is_subclass_of($route_provider, RevisionHtmlRouteProvider::class)) {
            $this->derivatives["version_history_$entity_type_id"] = [
              'route_name' => "entity.$entity_type_id.version_history",
              'weight' => 1,
              'title' => new TranslatableMarkup('Version history'),
              'base_route' => "entity.$entity_type_id.canonical",
            ];
          }
        }
      }
    }
    return $this->derivatives;
  }

}
