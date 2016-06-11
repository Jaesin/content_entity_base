<?php

namespace Drupal\Tests\content_entity_base\Kernel;

use Drupal\ceb_test\Entity\CebTestContent;
use Drupal\ceb_test\Entity\CebTestContentType;
use Drupal\user\Entity\User;
use Symfony\Component\HttpFoundation\Request;

/**
 * Tests the revision UI support of content_entity_base.
 *
 * @group content_entity_base
 */
class RevisionUiTest extends CEBKernelTestBase {

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

    $this->createFirstBundle();

    $root_user = User::create([
      'name' => 'admin',
    ]);
    $root_user->save();
  }

  public function testPages() {
    /** @var \Drupal\Core\Session\AccountSwitcherInterface $account_switcher */
    $account_switcher = \Drupal::service('account_switcher');

    $entity = CebTestContent::create([
      'type' => $this->getFirstBundleID(),
    ]);
    $entity->save();

    $user = $this->drupalCreateUser(['administer ceb_test_content']);
    $account_switcher->switchTo($user);

    $response = $this->httpKernel->handle(Request::create($entity->url('canonical')));
    $this->assertEquals(200, $response->getStatusCode());

    $response = $this->httpKernel->handle(Request::create($entity->url('add-page')));
    // Redirects automatically to the right form.
    $this->assertEquals(302, $response->getStatusCode());

    $response = $this->httpKernel->handle(Request::create($entity->url('edit-form')));
    $this->assertEquals(200, $response->getStatusCode());
  }

  public function testRevisionViewPage() {
    /** @var \Drupal\Core\Session\AccountSwitcherInterface $account_switcher */
    $account_switcher = \Drupal::service('account_switcher');

    $entity = $this->createTestEntity()->set('name', 'original name');
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
    $date = \Drupal::service('date.formatter')->format($entity->getRevisionCreationTime());
    $title = "Revision of original name from $date | ";
    $this->assertTitle($title);
    $this->assertRaw('<h1>Revision of <em class="placeholder">original name</em>');
  }

  public function testRevisionHistoryPagesWithMoreThanOneRevision() {
    /** @var \Drupal\Core\Session\AccountSwitcherInterface $account_switcher */
    $account_switcher = \Drupal::service('account_switcher');

    $entity = $this->createTestEntity()->setRevisionCreationTime(NULL);
    $entity->save();

    $first_revision_id = $entity->getRevisionId();

    $entity->setNewRevision(TRUE);
    $entity->set('name', $this->randomString())
      ->setRevisionCreationTime(1420070400)
      ->save();

    $user = $this->drupalCreateUser(['access ceb_test_content']);
    $account_switcher->switchTo($user);

    $response = $this->httpKernel->handle(Request::create($entity->url('version-history')));
    $this->assertEquals(403, $response->getStatusCode());

    $user = $this->drupalCreateUser(['access ceb_test_content', 'view all ceb_test_content revisions']);
    $account_switcher->switchTo($user);

    $response = $this->httpKernel->handle(Request::create($entity->url('version-history')));
    $this->assertEquals(200, $response->getStatusCode());
    $this->setRawContent($response->getContent());

    $this->assertText('Current revision');
    // Ensure that we have a link to the current and prevision revision.
    $this->assertLinkByHref($entity->url('canonical'));
    $this->assertLinkByHref($entity->url('revision'));

    $old_revision = \Drupal::entityTypeManager()->getStorage('ceb_test_content')->loadRevision($first_revision_id);
    $this->assertLinkByHref($old_revision->url('revision'));

    // Make sure null timestamps don't cause an error.
    $this->assertText('Unknown revision date');
  }

}
