<?php

/**
 * @file
 * Contains
 *   \Drupal\Tests\content_entity_base\Kernel\ContentEntityBaseStorageTest.
 */

namespace Drupal\Tests\content_entity_base\Kernel;

use Drupal\ceb_test\Entity\CebTestContent;
use Drupal\KernelTests\KernelTestBase;

/**
 * @coversDefaultClass \Drupal\content_entity_base\Entity\Storage\ContentEntityBaseStorage
 * @group content_entity_base
 */
class ContentEntityBaseStorageTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = ['content_entity_base', 'ceb_test', 'system', 'user'];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->installEntitySchema('ceb_test_content');
    $this->installEntitySchema('user');
    $this->installSchema('system', ['sequences']);
  }

  /**
   * @return \Drupal\content_entity_base\Entity\Storage\RevisionableStorageInterface
   */
  protected function getStorage() {
    return \Drupal::entityManager()->getStorage('ceb_test_content');
  }

  public function testCountDefaultLanguageRevisions() {
    $storage = $this->getStorage();

    $entity = CebTestContent::create([
      'type' => 'ceb_test_content',
    ]);
    $entity->save();

    $this->assertEquals(1, $storage->countDefaultLanguageRevisions($entity));

    $entity->setNewRevision(TRUE);
    $entity->save();

    $this->assertEquals(2, $storage->countDefaultLanguageRevisions($entity));
  }

  /**
   * @covers ::revisionIds
   */
  public function testRevisionIds() {
    $entity = CebTestContent::create([
      'type' => 'ceb_test_content',
    ]);
    $entity->save();

    $revision_ids = $this->getStorage()->revisionIds($entity);
    $this->assertCount(1, $revision_ids);
    $this->assertEquals([$entity->getRevisionId()], $revision_ids);

    $old_revision = clone $entity;
    $entity->setNewRevision(TRUE);
    $entity->save();

    $expected_ids = [$old_revision->getRevisionId(), $entity->getRevisionId()];
    $revision_ids = $this->getStorage()->revisionIds($entity);
    $this->assertCount(2, $revision_ids);
    $this->assertEquals($expected_ids, $revision_ids);
  }

}
