<?php

namespace Drupal\Tests\content_entity_base\Kernel;

/**
 * Tests the revision UI support of content_entity_base with the entity module
 * installed.
 *
 * @group content_entity_base
 */
class RevisionUiWithEntityTest extends RevisionUiTest {

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
