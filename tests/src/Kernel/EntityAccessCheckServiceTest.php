<?php

namespace Drupal\Tests\content_entity_base\Kernel;

use Drupal\KernelTests\KernelTestBase;

/**
 * @coversDefaultClass \Drupal\content_entity_base\ContentEntityBaseServiceProvider
 * @group content_entity_base
 */
class EntityAccessCheckServiceTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'content_entity_base',
  ];

  /**
   * Tests that entity access check providers are present.
   */
  function testServiceProviderRegistration() {
    $this->assertTrue(\Drupal::hasService('access_check.entity_create_any'), 'An access check exists for creating an entity of any bundle.');
    $this->assertTrue(\Drupal::hasService('access_check.entity.revision'), 'An access check exists for viewing entity revisions.');
    $this->assertTrue(\Drupal::hasService('access_checker.entity_revision'), 'An legacy access check exists for viewing entity revisions.');
    $this->assertTrue(\Drupal::hasService('paramconverter.entity_revision'), 'A param converter for entity revisions exists.');
    $this->assertTrue(\Drupal::hasService('route_enhancer.entity_revision'), 'A route enhancer for entity revisions exists.');
  }

}
