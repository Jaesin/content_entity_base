<?php

namespace Drupal\Tests\content_entity_base\Kernel;

use Drupal\ceb_test\Entity\CebTestContentType;
use Drupal\user\Entity\User;
use Symfony\Component\HttpFoundation\Request;

/**
 * Ensures access checking works as expected.
 *
 * @group content_entity_base
 */
class EntityAccessTest extends CEBKernelTestBase {

  /**
   * @var \Symfony\Component\HttpKernel\HttpKernelInterface
   */
  protected $httpKernel;

  /**
   * @var \Drupal\ceb_test\Entity\CebTestContentType
   */
  protected $bundle;

  /**
   * @var \Drupal\ceb_test\Entity\CebTestContentType
   */
  protected $bundle2;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->installConfig('system');

    \Drupal::service('router.builder')->rebuild();

    $this->httpKernel = \Drupal::service('http_kernel');

    $this->bundle = CebTestContentType::create([
      'id' => 'test_bundle',
      'label' => 'Test Bundle',
      'revision' => TRUE,
    ]);
    $this->bundle->save();
    $this->bundle2 = CebTestContentType::create([
      'id' => 'test_bundle2',
      'label' => 'Test Bundle2',
      'revision' => TRUE,
    ]);
    $this->bundle2->save();

    $root_user = User::create([
      'name' => 'admin',
    ]);
    $root_user->save();
  }

  public function testCreateAccess() {
    $account = $this->drupalCreateUser(["create test_bundle ceb_test_content"]);
    \Drupal::currentUser()->setAccount($account);

    $response = $this->httpKernel->handle(Request::create('/admin/ceb_test_content/add/test_bundle'));
    $this->assertEquals(200, $response->getStatusCode());

    $response = $this->httpKernel->handle(Request::create('/admin/ceb_test_content/add/test_bundle2'));
    $this->assertEquals(403, $response->getStatusCode());

    $account2 = $this->drupalCreateUser(["create test_bundle2 ceb_test_content"]);
    \Drupal::currentUser()->setAccount($account2);

    $response = $this->httpKernel->handle(Request::create('/admin/ceb_test_content/add/test_bundle'));
    $this->assertEquals(403, $response->getStatusCode());

    $response = $this->httpKernel->handle(Request::create('/admin/ceb_test_content/add/test_bundle2'));
    $this->assertEquals(200, $response->getStatusCode());
  }

}
