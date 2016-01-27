<?php
/**
 * @file
 *   Contains \Drupal\console_ceb\Generator\BundleEntityGenerator.
 */

namespace Drupal\console_ceb\Generator;

use Drupal\Console\Generator\EntityConfigGenerator;

/**
 * Handles generating the files required for a Content Entity Base entity.
 *
 * Generates:
 *
 *  1. module.links.menu.yml
 *  2. module.links.action.yml
 *  3. config/schema/entity_type.schema.yml
 *  4. src/Entity/EntityTypeClass.php
 *
 * @package Drupal\console_ceb\Generator
 */
class BundleEntityGenerator extends EntityConfigGenerator {
  /**
   * Generator Entity.
   *
   * @param string $module       Module name
   * @param string $entity_name  Entity machine name
   * @param string $entity_class Entity class name
   * @param string $label        Entity label
   * @param string $bundle_of    Entity machine name of the content entity this config entity acts as a bundle for.
   */
  public function generate($module, $entity_name, $entity_class, $label, $bundle_of = null) {
    $parameters = [
      'module' => $module,
      'entity_name' => $entity_name,
      'entity_class' => $entity_class,
      'label' => $label,
      'bundle_of' => $bundle_of,
    ];

    $this->renderFile(
      'module/config/schema/entity.schema.yml.twig',
      $this->getSite()->getModulePath($module).'/config/schema/'.$entity_name.'.schema.yml',
      $parameters
    );

    $this->renderFile(
      'module/links.menu-entity-config.yml.twig',
      $this->getSite()->getModulePath($module).'/'.$module.'.links.menu.yml',
      $parameters,
      FILE_APPEND
    );

    $this->renderFile(
      'module/links.action-entity.yml.twig',
      $this->getSite()->getModulePath($module).'/'.$module.'.links.action.yml',
      $parameters,
      FILE_APPEND
    );

    $this->renderFile(
      'module/src/Entity/entity-ceb-bundle.php.twig',
      $this->getSite()->getEntityPath($module).'/'.$entity_class.'.php',
      $parameters
    );
  }
}
