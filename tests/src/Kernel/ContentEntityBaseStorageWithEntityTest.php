<?php

namespace Drupal\Tests\content_entity_base\Kernel;

/**
 * @coversDefaultClass \Drupal\content_entity_base\Entity\Storage\ContentEntityBaseStorage
 * @group content_entity_base
 */
class ContentEntityBaseStorageWithEntityTest extends ContentEntityBaseStorageTest {

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'content_entity_base',
    'ceb_test',
    'system',
    'user',
    'entity',
  ];

}
