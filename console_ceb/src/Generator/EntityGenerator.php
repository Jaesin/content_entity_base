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

    // Get the module, entity and template paths.
    $module_path = $this->getSite()->getModulePath($module);
    $entity_path = $this->getSite()->getEntityPath($module);
    $template_path = $this->getSite()->getTemplatePath($module);
    $module_filename = "{$module_path}/{$module}.module";


    // Use these parameters for content entity creation.
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
      "{$module_path}/{$module}.permissions.yml",
      $parameters,
      FILE_APPEND
    );

    $this->renderFile(
      'module/links.menu-entity-ceb.yml.twig',
      "{$module_path}/{$module}.links.menu.yml",
      $parameters,
      FILE_APPEND
    );

    $this->renderFile(
      'module/links.task-entity-ceb.yml.twig',
      "{$module_path}/{$module}.links.task.yml",
      $parameters,
      FILE_APPEND
    );

    $this->renderFile(
      'module/links.action-entity-ceb.yml.twig',
      "{$module_path}/{$module}.links.action.yml",
      $parameters,
      FILE_APPEND
    );

    /**
     * Create the permissions class.
     */
    $this->renderFile(
      'module/src/Entity/Access/permissions.php.twig',
      "{$entity_path}/Access/{$entity_class}Permissions.php",
      $parameters + [
        'class_name' => $entity_class.'Permissions',
      ]
    );

    /**
     * Create the content entity plugin.
     */
    $this->renderFile(
      'module/src/Entity/entity-ceb-content.php.twig',
      "{$entity_path}/{$entity_class}.php",
      $parameters
    );

    /**
     * Create the content entity's template files.
     */
    $this->renderFile(
      'module/entity-content-page.php.twig',
      "{$module_path}/{$entity_name}.page.inc",
      $parameters
    );

    $this->renderFile(
      'module/templates/entity-html.twig',
      "{$template_path}/{$entity_name}.html.twig",
      $parameters
    );

    if ($bundle_entity_type) {
      $entity_hyphenated = str_replace('_', '-', $entity_name);
      $this->renderFile(
        'module/templates/entity-with-bundle-content-add-list-html.twig',
        "{$template_path}/{$entity_hyphenated}-content-add-list.html.twig",
        $parameters
      );

      // Check for hook_theme() in module file and warn ...
      if (file_exists($module_filename) && preg_match("/function\\s+{$module}_theme/", file_get_contents($module_filename)) !== 0) {
        echo "================
Warning:
================
It looks like you have a hook_theme already declared!
Please manually merge the two hook_theme() implementations in {$module_filename}!
        ";
      } else {

        $this->renderFile(
          'module/src/Entity/entity-content-with-bundle.theme.php.twig',
          $module_filename,
          $parameters,
          FILE_APPEND
        );
      }

      /**
       * Compose the bundle parameters.
       */
      $bundle_entity_class = "{$entity_class}Type";
      $bundle_label = "{$label} type";
      $bundle_parameters = [
        'module' => $module,
        'entity_name' => $bundle_entity_type,
        'entity_class' => $bundle_entity_class,
        'label' => $bundle_label,
        'bundle_of' => $entity_name,
      ];

      /**
       * Render the bundle entity files.
       */
      $this->renderFile(
        'module/config/schema/entity.schema.yml.twig',
        "{$module_path}" . "/config/schema/{$bundle_entity_type}.schema.yml",
        $bundle_parameters
      );

      $this->renderFile(
        'module/links.menu-entity-config.yml.twig',
        "{$module_path}" . "/{$module}.links.menu.yml",
        $bundle_parameters,
        FILE_APPEND
      );

      $this->renderFile(
        'module/links.action-entity.yml.twig',
        "{$module_path}/{$module}.links.action.yml",
        $bundle_parameters,
        FILE_APPEND
      );

      $this->renderFile(
        'module/src/Entity/entity-ceb-bundle.php.twig',
        "{$entity_path}/{$bundle_entity_class}.php",
        $bundle_parameters
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
