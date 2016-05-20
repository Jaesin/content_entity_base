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
class PermissionUiTest extends CEBKernelTestBase {

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

    $this->installConfig('system');

    \Drupal::service('router.builder')->rebuild();

    $this->httpKernel = \Drupal::service('http_kernel');

    $this->bundle = CebTestContentType::create([
      'id' => 'test_bundle',
      'label' => 'Test Bundle',
      'revision' => TRUE,
    ]);
    $this->bundle->save();

    $root_user = User::create([
      'name' => 'admin',
    ]);
    $root_user->save();
  }

  public function testPermissionsPage() {
    /** @var \Drupal\Core\Session\AccountSwitcherInterface $account_switcher */
    $account_switcher = \Drupal::service('account_switcher');

    $account_switcher->switchTo(user_load(1));

    $response = $this->httpKernel->handle(Request::create('/admin/people/permissions'));
    $this->assertEquals(200, $response->getStatusCode());

    $this->setRawContent($response->getContent());
    $this->assertTitle('Permissions | ');

    $drupal_version_parts = explode('.',\Drupal::VERSION );
    $drupal_minor = $drupal_version_parts[1];
    if ($drupal_minor !== "0") {
      // This test will require Drupal 8.1.x.
      $this->assertNoPattern('/<td[^>]*id="module-content_entity_base"/', 'The permissions page does not contain an entry for content entity base.');
      $this->assertPattern('/<td[^>]*id="module-ceb_test"/', 'The permissions page contains an entry for the CEB Test Entity module.');
    } else {
      $this->assertPattern('/<td[^>]*id="module-content_entity_base"/', 'The permissions page contains an entry for content entity base.');
      $this->assertNoPattern('/<td[^>]*id="module-ceb_test"/', 'The permissions page does not contain an entry for the CEB Test Entity module.');
    }

    $this->assertText('Access the CEB test content overview page');
    $this->assertText('Administer CEB test content');
    $this->assertText('Administer CEB test content types');
    $this->assertText('Bypass CEB test content access control');
    $this->assertText('View published CEB test content');
    $this->assertText('View own unpublished CEB test content');
    $this->assertText('Test Bundle: Delete any CEB test content');
    $this->assertText('Test Bundle: Delete CEB test content revisions');
    $this->assertText('Test Bundle: Create new CEB test content');
    $this->assertText('Test Bundle: Delete own CEB test content');
    $this->assertText('Test Bundle: Edit any CEB test content');
    $this->assertText('Test Bundle: Edit own CEB test content');
    $this->assertText('Test Bundle: Revert CEB test content revisions');
    $this->assertText('Test Bundle: View CEB test content revisions');
    $this->assertText('Delete all CEB test content revisions');
    $this->assertText('Revert all CEB test content revisions');
    $this->assertText('View all CEB test content revisions');
  }
}
