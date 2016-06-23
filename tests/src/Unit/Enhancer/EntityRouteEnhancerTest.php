<?php

namespace Drupal\Tests\content_entity_base\Unit\Enhancer;

use Drupal\content_entity_base\Entity\Enhancer\EntityRouteEnhancer;
use Drupal\Core\Entity\EntityInterface;
use Symfony\Cmf\Component\Routing\RouteObjectInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;

/**
 * @group content_entity_base
 * @coversDefaultClass \Drupal\content_entity_base\Entity\Enhancer\EntityRouteEnhancer
 */
class EntityRouteEnhancerTest extends \PHPUnit_Framework_TestCase {

  /**
   * @var \Drupal\content_entity_base\Entity\Enhancer\EntityRouteEnhancer
   */
  protected $routeEnhancer;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->routeEnhancer = new EntityRouteEnhancer();
  }

  /**
   * @covers ::applies
   */
  public function testAppliesWithNoParameters() {
    $route = new Route('/test-path');

    $this->assertFalse($this->routeEnhancer->applies($route));
  }

  /**
   * @covers ::applies
   */
  public function testAppliesWithEntityParameters() {
    $route = new Route('/test-path/{entity_test}', [], [], [
      'parameters' => [
        'entity_test' => [
          'type' => 'entity:entity_test',
        ]
      ]
    ]);

    $this->assertTrue($this->routeEnhancer->applies($route), 'Route enhancer applies to route.');
  }

  /**
   * @covers ::enhance
   */
  public function testEnhanceWithoutEntity() {
    $route = new Route('/test-path/{entity_test}');
    $request = Request::create('/test-path/123');

    $defaults = [];
    $defaults['entity_test'] = 123;
    $defaults[RouteObjectInterface::ROUTE_OBJECT] = $route;
    $this->assertEquals($defaults, $this->routeEnhancer->enhance($defaults, $request));
  }

  /**
   * @covers ::enhance
   */
  public function testEnhanceWithEntity() {
    $route = new Route('/test-path/{entity_test}', [], [], ['parameters' => ['entity_test' => ['type' => 'entity:entity_test']]]);
    $request = Request::create('/test-path/123');
    $entity = $this->prophesize(EntityInterface::class);

    $defaults = [];
    $defaults['entity_test'] = $entity->reveal();
    $defaults[RouteObjectInterface::ROUTE_OBJECT] = $route;

    $expected = $defaults;
    $expected['_entity'] = $defaults['entity_test'];
    $this->assertEquals($expected, $this->routeEnhancer->enhance($defaults, $request));
  }

}
