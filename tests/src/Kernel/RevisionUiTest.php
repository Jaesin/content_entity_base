<?php

/**
 * @file
 * Contains \Drupal\Tests\content_entity_base\Kernel\RevisionUiTest.
 */

namespace Drupal\Tests\content_entity_base\Kernel;

use Drupal\ceb_test\Entity\CebTestContent;
use Drupal\ceb_test\Entity\CebTestContentType;
use Drupal\KernelTests\KernelTestBase;
use Drupal\user\Entity\Role;
use Drupal\user\Entity\User;
use Symfony\Component\HttpFoundation\Request;

/**
 * Tests the revision UI support of content_entity_base.
 *
 * @group content_entity_base
 */
class RevisionUiTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = ['content_entity_base', 'ceb_test', 'system', 'user'];

  /**
   * @var \Symfony\Component\HttpKernel\HttpKernelInterface
   */
  protected $httpKernel;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->installEntitySchema('ceb_test_content');
    $this->installEntitySchema('user');
    $this->installSchema('system', ['router', 'sequences']);
    \Drupal::service('router.builder')->rebuild();

    $this->httpKernel = \Drupal::service('http_kernel');
  }


  public function testPages() {
    $bundle = CebTestContentType::create([
      'id' => 'test_bundle',
      'revision' => TRUE,
    ]);
    $bundle->save();
    $entity = CebTestContent::create([
      'type' => 'test_bundle',
    ]);
    $entity->save();

    $role = Role::create([
      'id' => 'test_role',
    ]);
    $role->grantPermission('administer ceb_test_content');
    $role->save();
    $user = User::create([
      'name' => 'test name',
    ]);
    $user->addRole('test_role');
    $user->save();

    /** @var \Drupal\Core\Session\AccountSwitcherInterface $account_switcher */
    $account_switcher = \Drupal::service('account_switcher');
    $account_switcher->switchTo($user);

//    $response = $this->httpKernel->handle(Request::create($entity->url('canonical')));
//    $this->assertEquals(200, $response->getStatusCode());

//    $response = $this->httpKernel->handle(Request::create($entity->url('add-page')));
//    $this->assertEquals(200, $response->getStatusCode());

//    $response = $this->httpKernel->handle(Request::create($entity->url('edit-form')));
//    $this->assertEquals(200, $response->getStatusCode());

    $response = $this->httpKernel->handle(Request::create($entity->url('version-history')));
    $this->assertEquals(200, $response->getStatusCode());
  }

}
