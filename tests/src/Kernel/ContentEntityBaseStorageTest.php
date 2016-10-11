<?php

namespace Drupal\Tests\content_entity_base\Kernel;

use Drupal\ceb_test\Entity\CebTestContent;

/**
 * @coversDefaultClass \Drupal\content_entity_base\Entity\Storage\ContentEntityBaseStorage
 * @group content_entity_base
 */
class ContentEntityBaseStorageTest extends CEBKernelTestBase {

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
