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
   * @var \Drupal\ceb_test\Entity\CebTestContentType
   */
  protected $bundle;

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

    $this->bundle = CebTestContentType::create([
      'id' => 'test_bundle',
      'revision' => TRUE,
    ]);
    $this->bundle->save();

    $root_user = User::create([
      'name' => 'admin',
    ]);
    $root_user->save();
  }

  /**
   * @return \Drupal\user\UserInterface
   */
  protected function drupalCreateUser(array $permissions = []) {
    $role = Role::create([
      'id' => 'test_role__' . $this->randomMachineName(),
    ]);
    foreach ($permissions as $permission) {
      $role->grantPermission($permission);
    }
    $role->save();
    $user = User::create([
      'name' => 'test name  ' . $this->randomMachineName(),
    ]);
    $user->addRole($role->id());
    $user->save();

    return $user;
  }

  public function testPages() {
    /** @var \Drupal\Core\Session\AccountSwitcherInterface $account_switcher */
    $account_switcher = \Drupal::service('account_switcher');

    $entity = CebTestContent::create([
      'type' => 'test_bundle',
    ]);
    $entity->save();

    $user = $this->drupalCreateUser(['administer ceb_test_content']);
    $account_switcher->switchTo($user);

    $response = $this->httpKernel->handle(Request::create($entity->url('canonical')));
    $this->assertEquals(200, $response->getStatusCode());

    $response = $this->httpKernel->handle(Request::create($entity->url('add-page')));
    $this->assertEquals(200, $response->getStatusCode());

    $response = $this->httpKernel->handle(Request::create($entity->url('edit-form')));
    $this->assertEquals(200, $response->getStatusCode());
  }

  public function testRevisionViewPage() {
    /** @var \Drupal\Core\Session\AccountSwitcherInterface $account_switcher */
    $account_switcher = \Drupal::service('account_switcher');

    $entity = CebTestContent::create([
      'type' => 'test_bundle',
      'name' => 'original name',
    ]);
    $entity->save();
    $old_revision = clone $entity;
    $old_revision->isDefaultRevision(FALSE);

    $entity->setNewRevision(TRUE);
    $entity->isDefaultRevision(TRUE);
    $entity->name->value = 'revision name';
    $entity->save();

    $response = $this->httpKernel->handle(Request::create($old_revision->url('revision')));
    $this->assertEquals(403, $response->getStatusCode());

    $user = $this->drupalCreateUser(['access ceb_test_content', "view all ceb_test_content revisions"]);
    $account_switcher->switchTo($user);

    $response = $this->httpKernel->handle(Request::create($old_revision->url('revision')));
    $this->assertEquals(200, $response->getStatusCode());

    $this->setRawContent($response->getContent());
    $this->assertTitle('Revision of original name | ');
    $this->assertRaw('<h1>Revision of <em class="placeholder">original name</em>');
  }

  public function testRevisionHistoryPagesWithMoreThanOneRevision() {
    /** @var \Drupal\Core\Session\AccountSwitcherInterface $account_switcher */
    $account_switcher = \Drupal::service('account_switcher');
    /** @var \Drupal\content_entity_base\Entity\Routing\EntityRevisionRouteAccessChecker $revision_access_check */
    $revision_access_check = \Drupal::service('content_entity_base.entity_revision_access_checker');

    $entity = CebTestContent::create([
      'type' => 'test_bundle',
    ]);
    $entity->save();

    $entity->setNewRevision(TRUE);
    $entity->save();

    $user = $this->drupalCreateUser(['access ceb_test_content']);
    $account_switcher->switchTo($user);

    $response = $this->httpKernel->handle(Request::create($entity->url('version-history')));
    $this->assertEquals(403, $response->getStatusCode());

    $revision_access_check->resetAccessCache();
    $user = $this->drupalCreateUser(['access ceb_test_content', 'view all ceb_test_content revisions']);
    $account_switcher->switchTo($user);

    $response = $this->httpKernel->handle(Request::create($entity->url('version-history')));
    $this->assertEquals(200, $response->getStatusCode());
    $this->setRawContent($response->getContent());

    $this->assertText('Current revision');
    // Ensure that we have a link to the current and prevision revision.
    $this->assertLinkByHref($entity->url('canonical'));
    $this->assertLinkByHref($entity->url('revision'));
  }

}
