<?php

/**
 * @file
 * Contains
 *   \Drupal\Tests\content_entity_base\Kernel\ContentEntityBaseStorageTest.
 */

namespace Drupal\Tests\content_entity_base\Kernel;

use Drupal\ceb_test\Entity\CebTestContent;
use Drupal\KernelTests\KernelTestBase;

class ContentEntityBaseStorageTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = ['content_entity_base', 'ceb_test', 'system'];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->installEntitySchema('ceb_test_content');
    $this->installSchema('system', ['sequences']);
  }

  public function testCountDefaultLanguageRevisions() {

    /** @var \Drupal\content_entity_base\Entity\Storage\RevisionableStorageInterface $storage */
    $storage = \Drupal::entityManager()->getStorage('ceb_test_content');

    $entity = CebTestContent::create([
      'type' => 'ceb_test_content',
    ]);
    $entity->save();

    $this->assertEquals(1, $storage->countDefaultLanguageRevisions($entity));

    $entity->setNewRevision(TRUE);
    $entity->save();

    $this->assertEquals(2, $storage->countDefaultLanguageRevisions($entity));
  }

}
