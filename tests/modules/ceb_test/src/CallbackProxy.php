<?php
/**
 * @file
 *   Contains \Drupal\ceb_test\CallbackProxy
 */

namespace Drupal\ceb_test;


use Drupal\content_entity_base\EntityBaseCallbackProxy;

/**
 * Provides a callback proxy based off the content_entity_base callback proxy.
 * The only thing that should be required in this file is the $entity_type and
 * $bundle_type variables. Functions in the base class will handle instantiating
 * objects required by the callback based on these two settings.
 *
 * @package Drupal\ceb_test
 */
class CallbackProxy extends EntityBaseCallbackProxy{

  /**
   * {@inheritdoc}
   */
  public static $entity_type = 'ceb_test_content';

  /**
   * {@inheritdoc}
   */
  public static $bundle_type = 'ceb_test_content_type';

}
