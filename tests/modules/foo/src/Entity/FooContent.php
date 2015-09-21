<?php

/**
 * @file
 *   Contains Drupal\foo_entity\Entity\DCContent.
 */

namespace Drupal\foo_entity\Entity;

use Drupal\content_entity_base\Entity\EntityBase;

/**
 * Defines a custom entity class.
 *
 * @ContentEntityType(
 *   id                      = "foo_content",
 *   label                   = @Translation("Destination Central content"),
 *   bundle_label            = @Translation("Destination Central content type"),
 *   base_table              = "foo_content",
 *   revision_table          = "foo_content_revision",
 *   data_table              = "foo_content_field_data",
 *   translatable            = TRUE,
 *   admin_permission        = "administer foo_content",
 *   bundle_entity_type      = "foo_content_type",
 *   field_ui_base_route     = "entity.foo_content_type.edit_form",
 *   common_reference_target = TRUE,
 *   permission_granularity  = "bundle",
 *   render_cache            = TRUE,
 *   handlers = {
 *     "storage"      = "\Drupal\Core\Entity\Sql\SqlContentEntityStorage",
 *     "access"       = "\Drupal\content_entity_base\Entity\Access\EntityBaseAccessControlHandler",
 *     "translation"  = "\Drupal\content_translation\ContentTranslationHandler",
 *     "list_builder" = "\Drupal\content_entity_base\Entity\Listing\EntityBaseListBuilder",
 *     "view_builder" = "\Drupal\Core\Entity\EntityViewBuilder",
 *     "views_data"   = "\Drupal\foo_entity\Entity\Views\DCContentViewsData",
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
 *     "collection"   = "/admin/foo_content/",
 *     "canonical"    = "/admin/foo_content/{foo_content}",
 *     "delete-form"  = "/admin/foo_content/{foo_content}/delete",
 *     "edit-form"    = "/admin/foo_content/{foo_content}/edit",
 *   },
 * )
 */
class DCContent extends EntityBase {
}
