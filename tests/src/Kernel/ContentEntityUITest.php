<?php

namespace Drupal\Tests\content_entity_base\Kernel;

use Drupal\ceb_test\Entity\CebTestContent;
use Drupal\ceb_test\Entity\CebTestContentType;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\user\Entity\User;
use Symfony\Component\HttpFoundation\Request;

/**
 * Tests the entity UI support of content_entity_base.
 *
 * @group content_entity_base
 */
class ContentEntityUITest extends CEBKernelTestBase {

  /**
   * @var \Symfony\Component\HttpKernel\HttpKernelInterface
   */
  protected $httpKernel;

  /**
   * @var \Drupal\user\Entity\User[]
   */
  protected $users;

  /**
   * @var \Drupal\content_entity_base\Entity\EntityBaseInterface
   */
  protected $entity_definition;

  /**
   * @var \Drupal\Core\Session\AccountSwitcherInterface
   */
  protected $account_switcher;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->installConfig('system');

    \Drupal::service('router.builder')->rebuild();

    $this->httpKernel = \Drupal::service('http_kernel');

    /** @var EntityTypeManagerInterface $entity_manager */
    $entity_manager = \Drupal::service('entity_type.manager');
    $this->entity_definition = $entity_manager->getDefinition('ceb_test_content');
    $root_user = User::create(['uid' => 1, 'name' => $this->randomString()]);
    $root_user->save();
    $this->users = [
      'anon' => User::load(0),
      'root' => $root_user,
      'ceb_admin' => $this->drupalCreateUser(['administer '. $this->entity_definition->id()]),
    ];

    $this->account_switcher = \Drupal::service('account_switcher');
  }

  public function testEntityUIWithNoBundles() {
    // #### ANON ####
    // Test anon access to the entity listing page.
    $response = $this->httpKernel->handle(Request::create('/admin/content/ceb_test_content'));
    $this->assertEquals(403, $response->getStatusCode());

    // Test anon access to the entity add page.
    $response = $this->httpKernel->handle(Request::create('/admin/ceb_test_content/add'));
    $this->assertEquals(403, $response->getStatusCode());

    // #### ROOT ####
    $this->account_switcher->switchTo($this->users['root']);
    $response = $this->httpKernel->handle(Request::create('/admin/content/ceb_test_content'));
    $this->assertEquals(200, $response->getStatusCode());
    $this->setRawContent($response->getContent());
    $this->assertText('There are no CEB Test entities yet.');
    // Test the add page.
    $response = $this->httpKernel->handle(Request::create('/admin/ceb_test_content/add'));
    $this->assertEquals(200, $response->getStatusCode());
    $this->setRawContent($response->getContent());
    $this->assertText('There is no ceb test content type yet.');

    // #### CEB ADMIN ####
    $this->account_switcher->switchTo($this->users['ceb_admin']);
    $response = $this->httpKernel->handle(Request::create('/admin/content/ceb_test_content'));
    $this->assertEquals(200, $response->getStatusCode());
    $response = $this->httpKernel->handle(Request::create('/admin/ceb_test_content/add'));
    $this->assertEquals(200, $response->getStatusCode());

    // Switch back to the anon account.
    $this->account_switcher->switchBack();
  }


  public function testEntityAddPageWithOneBundle() {

    $this->createFirstBundle();

    // #### ANON ####
    // Test anon access to the entity listing page.
    $response = $this->httpKernel->handle(Request::create('/admin/content/ceb_test_content'));
    $this->assertEquals(403, $response->getStatusCode());

    // Test anon access to the entity add page.
    $response = $this->httpKernel->handle(Request::create('/admin/ceb_test_content/add'));
    $this->assertEquals(403, $response->getStatusCode());

    // #### ROOT ####
    $this->account_switcher->switchTo($this->users['root']);
    $response = $this->httpKernel->handle(Request::create('/admin/content/ceb_test_content'));
    $this->assertEquals(200, $response->getStatusCode());
    $this->setRawContent($response->getContent());
    $this->assertText('There are no CEB Test entities yet.');
    // Test the add page.
    $response = $this->httpKernel->handle(Request::create('/admin/ceb_test_content/add'));
    // The content add page should redirect to  ceb_test_content/add/{{bundle_0_id}} when there is only one bundle.
    $this->assertEquals(302, $response->getStatusCode());
    $this->assertEquals('http://localhost/admin/ceb_test_content/add/'. $this->bundles[0]->id(), $response->getTargetUrl());
    // Test the add form.
    $response = $this->httpKernel->handle(Request::create('/admin/ceb_test_content/add/'. $this->bundles[0]->id()));
    $this->assertEquals(200, $response->getStatusCode());
    $this->setRawContent($response->getContent());
    $this->assertTitle('Add ceb test | ');

    // #### CEB ADMIN ####
    $this->account_switcher->switchTo($this->users['ceb_admin']);
    $response = $this->httpKernel->handle(Request::create('/admin/content/ceb_test_content'));
    $this->assertEquals(200, $response->getStatusCode());
    $response = $this->httpKernel->handle(Request::create('/admin/ceb_test_content/add'));
    $this->assertEquals(200, $response->getStatusCode());

    // Switch back to the anon account.
    $this->account_switcher->switchBack();
  }

  protected function doTestAnonDenied() {}

}
