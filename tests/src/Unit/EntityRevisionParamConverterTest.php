<?php

/**
 * @file
 * Contains
 *   \Drupal\Tests\content_entity_base\Unit\EntityRevisionParamConverterTest.
 */

namespace Drupal\Tests\content_entity_base\Unit;

use Drupal\content_entity_base\ParamConverter\EntityRevisionParamConverter;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Symfony\Component\Routing\Route;

/**
 * @coversDefaultClass \Drupal\content_entity_base\ParamConverter\EntityRevisionParamConverter
 * @group content_entity_base
 */
class EntityRevisionParamConverterTest extends \PHPUnit_Framework_TestCase {

  protected $entityManager;

  /**
   * @var \Drupal\content_entity_base\ParamConverter\EntityRevisionParamConverter
   */
  protected $converter;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->converter = new EntityRevisionParamConverter($this->prophesize(EntityManagerInterface::class)->reveal());
  }

  protected function getTestRoute() {
    $route = new Route('/test/{test_revision}');
    $route->setOption('parameters', [
      'test_revision' => [
        'type' => 'entity_revision:test',
      ],
    ]);
    return $route;
  }

  /**
   * @covers ::applies
   */
  public function testNonApplyingRoute() {
    $route = new Route('/test');
    $this->assertFalse($this->converter->applies([], 'test_revision', $route));
  }

  /**
   * @covers ::applies
   */
  public function testApplyingRoute() {
    $route = $this->getTestRoute();
    $this->assertTrue($this->converter->applies($route->getOption('parameters')['test_revision'], 'test_revision', $route));
  }

  /**
   * @covers ::convert
   */
  public function testConvert() {
    $entity = $this->prophesize(EntityInterface::class)->reveal();
    $storage = $this->prophesize(EntityStorageInterface::class);
    $storage->loadRevision(1)->willReturn($entity);

    $entity_manager = $this->prophesize(EntityManagerInterface::class);
    $entity_manager->getStorage('test')->willReturn($storage->reveal());
    $converter = new EntityRevisionParamConverter($entity_manager->reveal());

    $route = $this->getTestRoute();
    $converter->convert(1, $route->getOption('parameters')['test_revision'], 'test_revision', ['test_revision' => 1]);
  }

}
