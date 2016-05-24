<?php

namespace Drupal\Tests\content_entity_base\Kernel;

use Drupal\ceb_test\Entity\CebTestContentType;
use Drupal\KernelTests\KernelTestBase;
use Drupal\user\Entity\Role;
use Drupal\user\Entity\User;

/**
 * @coversDefaultClass \Drupal\content_entity_base\Entity\Storage\ContentEntityBaseStorage
 * @group content_entity_base
 */
class CEBKernelTestBase extends KernelTestBase {

  /**
   * @var \Drupal\ceb_test\Entity\CebTestContentType[]
   */
  protected $bundles;

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'content_entity_base',
    'ceb_test',
    'system',
    'user',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->installEntitySchema('ceb_test_content');
    $this->installEntitySchema('user');
    $this->installSchema('system', ['router', 'sequences']);
  }

  /**
   * @return \Drupal\content_entity_base\Entity\Storage\RevisionableStorageInterface
   */
  protected function getStorage() {
    return \Drupal::entityTypeManager()->getStorage('ceb_test_content');
  }

  /**
   * Creates a test user.
   *
   * @param array $permissions
   * @return \Drupal\user\UserInterface|static
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

  /**
   * Creates a test entity bundle.
   *
   * @return \Drupal\ceb_test\Entity\CebTestContentType|static
   */
  protected function createTestBundle() {
    $test_bundle = CebTestContentType::create([
      'id' => $this->randomMachineName(),
      'label' => $this->randomString(),
      'revision' => TRUE,
    ]);
    $test_bundle->save();
    return $test_bundle;
  }

  /**
   * Helper to create the first test bundle.
   */
  protected function createFirstBundle() {
    $this->bundles = [
      $this->createTestBundle(),
    ];
  }

  /**
   * Creates additional bundles.
   */
  protected function createAdditionalBundle() {
    $this->bundles[] = $this->createTestBundle();
  }

}
