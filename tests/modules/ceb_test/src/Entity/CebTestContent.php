<?php

/**
 * @file
 *   Contains Drupal\ceb_test_entity\Entity\DCContent.
 */

namespace Drupal\ceb_test\Entity;

use Drupal\content_entity_base\Entity\EntityBase;

/**
 * Defines a custom entity class.
 *
 * @ContentEntityType(
 *   id                      = "ceb_test_content",
 *   label                   = @Translation("Destination Central content"),
 *   bundle_label            = @Translation("Destination Central content type"),
 *   base_table              = "ceb_test_content",
 *   revision_table          = "ceb_test_content_revision",
 *   data_table              = "ceb_test_content_field_data",
 *   translatable            = TRUE,
 *   admin_permission        = "administer ceb_test_content",
 *   bundle_entity_type      = "ceb_test_content_type",
 *   field_ui_base_route     = "entity.ceb_test_content_type.edit_form",
 *   common_reference_target = TRUE,
 *   permission_granularity  = "bundle",
 *   render_cache            = TRUE,
 *   handlers = {
 *     "storage"      = "\Drupal\Core\Entity\Sql\SqlContentEntityStorage",
 *     "access"       = "\Drupal\content_entity_base\Entity\Access\EntityBaseAccessControlHandler",
 *     "translation"  = "\Drupal\content_translation\ContentTranslationHandler",
 *     "list_builder" = "\Drupal\content_entity_base\Entity\Listing\EntityBaseListBuilder",
 *     "view_builder" = "\Drupal\Core\Entity\EntityViewBuilder",
 *     "views_data"   = "\Drupal\ceb_test\Entity\Views\DCContentViewsData",
 *     "form" = {
 *       "add"        = "\Drupal\content_entity_base\Entity\Form\EntityBaseForm",
 *       "edit"       = "\Drupal\content_entity_base\Entity\Form\EntityBaseForm",
 *       "default"    = "\Drupal\content_entity_base\Entity\Form\EntityBaseForm",
 *       "delete"     = "\Drupal\content_entity_base\Entity\Form\EntityBaseDeleteForm",
 *     },
 *   },
 *   entity_keys = {
 *     "id"           = "id",
 *     "bundle"       = "type",
 *     "label"        = "name",
 *     "langcode"     = "langcode",
 *     "uuid"         = "uuid",
 *     "revision"     = "revision_id",
 *   },
 *   links = {
 *     "collection"   = "/admin/ceb_test_content/",
 *     "canonical"    = "/admin/ceb_test_content/{ceb_test_content}",
 *     "delete-form"  = "/admin/ceb_test_content/{ceb_test_content}/delete",
 *     "edit-form"    = "/admin/ceb_test_content/{ceb_test_content}/edit",
 *   },
 * )
 */
class DCContent extends EntityBase {
}
