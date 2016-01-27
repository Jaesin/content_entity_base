<?php
/**
 * @file
 *   Contains \Drupal\console_ceb\Generator\EntityGenerator.
 */

namespace Drupal\console_ceb\Generator;

use Drupal\Console\Generator\EntityContentGenerator;

/**
 * Handles generating the files required for a Content Entity Base entity.
 *
 * Generates:
 *
 *  1. module.permissions.yml
 *  2. module.links.menu.yml
 *  3. module.links.action.yml
 *  4. module.links.task.yml
 *  5. module.page.inc
 *  6. templates/entity.html.twig
 *  7. templates/entity-content-add-list.html.twig
 *  8. src/Entity/EntityClass.php
 *  9. src/Entity/Access/EntityClassPermissions.php
 *
 * @package Drupal\console_ceb\Generator
 */
class EntityGenerator extends EntityContentGenerator {
  /**
   * Generator Entity.
   *
   * @param string $module             Module name
   * @param string $entity_name        Entity machine name
   * @param string $entity_class       Entity class name
   * @param string $label              Entity label
   * @param string $bundle_entity_type (Config) entity type acting as bundle
   */
  public function generate($module, $entity_name, $entity_class, $label, $bundle_entity_type = null) {
    $parameters = [
      'module' => $module,
      'entity_name' => $entity_name,
      'entity_class' => $entity_class,
      'label' => $label,
      'bundle_entity_type' => $bundle_entity_type,
    ];

    /**
     * Create the yml files.
     */
    $this->renderFile(
      'module/permissions-entity-ceb.yml.twig',
      $this->getSite()->getModulePath($module).'/'.$module.'.permissions.yml',
      $parameters,
      FILE_APPEND
    );

    $this->renderFile(
      'module/links.menu-entity-ceb.yml.twig',
      $this->getSite()->getModulePath($module).'/'.$module.'.links.menu.yml',
      $parameters,
      FILE_APPEND
    );

    $this->renderFile(
      'module/links.task-entity-ceb.yml.twig',
      $this->getSite()->getModulePath($module).'/'.$module.'.links.task.yml',
      $parameters,
      FILE_APPEND
    );

    $this->renderFile(
      'module/links.action-entity-ceb.yml.twig',
      $this->getSite()->getModulePath($module).'/'.$module.'.links.action.yml',
      $parameters,
      FILE_APPEND
    );

    /**
     * Create the permissions class.
     */
    $this->renderFile(
      'module/src/Entity/Access/permissions.php.twig',
      $this->getSite()->getEntityPath($module).'/Access/'.$entity_class.'Permissions.php',
      $parameters + [
        'class_name' => $entity_class.'Permissions',
      ]
    );

    /**
     * Create the content entity plugin.
     */
    $this->renderFile(
      'module/src/Entity/entity-ceb-content.php.twig',
      $this->getSite()->getEntityPath($module).'/'.$entity_class.'.php',
      $parameters
    );

    /**
     * The rest of this file is from drupal console's default content entity.
     */
    $this->renderFile(
      'module/entity-content-page.php.twig',
      $this->getSite()->getModulePath($module).'/'.$entity_name.'.page.inc',
      $parameters
    );

    $this->renderFile(
      'module/templates/entity-html.twig',
      $this->getSite()->getTemplatePath($module).'/'.$entity_name.'.html.twig',
      $parameters
    );

    if ($bundle_entity_type) {
      $this->renderFile(
        'module/templates/entity-with-bundle-content-add-list-html.twig',
        $this->getSite()->getTemplatePath($module).'/'.str_replace('_', '-', $entity_name).'-content-add-list.html.twig',
        $parameters
      );

      // Check for hook_theme() in module file and warn ...
      $module_filename = $this->getSite()->getModulePath($module).'/'.$module.'.module';
      $module_file_contents = file_get_contents($module_filename);
      if (strpos($module_file_contents, 'function ' . $module . '_theme') !== false) {
        echo "================\nWarning:\n================\n" .
          "It looks like you have a hook_theme already declared!\n".
          "Please manually merge the two hook_theme() implementations in $module_filename!\n";
      }

      $this->renderFile(
        'module/src/Entity/entity-content-with-bundle.theme.php.twig',
        $this->getSite()->getModulePath($module).'/'.$module.'.module',
        $parameters,
        FILE_APPEND
      );
    }

    $content = $this->getRenderHelper()->render(
      'module/src/Entity/entity-content.theme.php.twig',
      $parameters
    );

    if ($this->isLearning()) {
      echo 'Add this to your hook_theme:'.PHP_EOL;
      echo $content;
    }
  }
}
