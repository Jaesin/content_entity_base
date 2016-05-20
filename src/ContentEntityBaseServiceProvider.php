<?php

namespace Drupal\content_entity_base;

use Drupal\content_entity_base\Entity\Access\EntityCreateAnyAccessCheck;
use Drupal\content_entity_base\Entity\Access\EntityRevisionRouteAccessCheck;
use Drupal\content_entity_base\Entity\Enhancer\EntityRevisionRouteEnhancer;
use Drupal\content_entity_base\ParamConverter\EntityRevisionParamConverter;
use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderBase;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Allows dynamic configuration of services.
 */
class ContentEntityBaseServiceProvider extends ServiceProviderBase {

  /**
   * {@inheritdoc}
   */
  public function alter(ContainerBuilder $container) {
    // Get the entity_create_any access check if it exists.
    if ( ! $container->has('access_check.entity_create_any') ) {
      // Add a back port of the core 8.1.x access_check.entity_create_any.
      $container
        ->register('access_check.entity_create_any', EntityCreateAnyAccessCheck::class)
        ->addArgument(new Reference('entity_type.manager'))
        ->addArgument(new Reference('entity_type.bundle.info'))
        ->addTag('access_check', ['applies_to' => '_entity_create_any_access']);
    }

    // Get the entity_revision param converter if it exists.
    if ( ! $container->has('paramconverter.entity_revision') ) {
      // Add a back port of the core 8.1.x paramconverter.entity_revision.
      $container
        ->register('paramconverter.entity_revision', EntityRevisionParamConverter::class)
        ->addArgument(new Reference('entity_type.manager'))
        ->addTag('paramconverter');
    }

    // Get the entity_revision route enhancer if it exists.
    if ( ! $container->has('route_enhancer.entity_revision') ) {
      // Add a back port of the core 8.1.x route_enhancer.entity_revision.
      $container
        ->register('route_enhancer.entity_revision', EntityRevisionRouteEnhancer::class)
        ->addTag('route_enhancer');
    }

    // Get the entity_revision access check if it exists.
    if ( ! $container->has('access_check.entity.revision') ) {
      // Add a access check for entity revisions.
      $container
        ->register('access_check.entity.revision', EntityRevisionRouteAccessCheck::class)
        ->setArguments([new Reference('entity_type.manager'), new Reference('request_stack')])
        ->addTag('access_check', [
          'applies_to' => '_entity_access_revision',
        ]);
    }

    // Get the entity_revision access check if it exists.
    $revision_access_checker = $container->has('access_checker.entity_revision')
      ? $container->getDefinition('access_checker.entity_revision')
      : $container
        ->register('access_checker.entity_revision')
        ->addArgument(new Reference('entity_type.manager'))
        ->addTag('access_check', [
          'applies_to' => '_entity_access_revision',
        ]);
    // Use the content entity base class for checking access.
    $revision_access_checker
      ->setClass(EntityRevisionRouteAccessCheck::class)
      ->setArguments([new Reference('entity_type.manager'), new Reference('request_stack')]);
  }
}
