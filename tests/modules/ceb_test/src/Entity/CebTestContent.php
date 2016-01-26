<?php

/**
 * @file
 *   Contains Drupal\ceb_test_entity\Entity\CebTestContent.
 */

namespace Drupal\ceb_test\Entity;

use Drupal\content_entity_base\Entity\EntityBase;

/**
 * Defines a custom entity class.
 *
 * @ContentEntityType(
 *   id                      = "ceb_test_content",
 *   label                   = @Translation("CEB test content"),
 *   bundle_label            = @Translation("CEB test content type"),
 *   base_table              = "ceb_test_content",
 *   revision_table          = "ceb_test_content_revision",
 *   data_table              = "ceb_test_content_field_data",
 *   revision_data_table     = "ceb_test_content_field_revision",
 *   translatable            = TRUE,
 *   admin_permission        = "administer ceb_test_content",
 *   bundle_entity_type      = "ceb_test_content_type",
 *   field_ui_base_route     = "entity.ceb_test_content_type.edit_form",
 *   common_reference_target = TRUE,
 *   permission_granularity  = "bundle",
 *   render_cache            = TRUE,
 *   handlers = {
 *     "storage"      = "\Drupal\content_entity_base\Entity\Storage\ContentEntityBaseStorage",
 *     "access"       = "\Drupal\content_entity_base\Entity\Access\EntityBaseAccessControlHandler",
 *     "translation"  = "\Drupal\content_translation\ContentTranslationHandler",
 *     "list_builder" = "\Drupal\content_entity_base\Entity\Listing\EntityBaseListBuilder",
 *     "view_builder" = "\Drupal\Core\Entity\EntityViewBuilder",
 *     "views_data"   = "\Drupal\content_entity_base\Entity\Views\EntityBaseViewsData",
 *     "route_provider" = {
 *       "html" = "\Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider",
 *       "crud" = "\Drupal\content_entity_base\Entity\Routing\CrudUiRouteProvider",
 *       "revision" = "\Drupal\content_entity_base\Entity\Routing\RevisionHtmlRouteProvider"
 *     },
 *     "form" = {
 *       "add"        = "\Drupal\content_entity_base\Entity\Form\EntityBaseForm",
 *       "edit"       = "\Drupal\content_entity_base\Entity\Form\EntityBaseForm",
 *       "default"    = "\Drupal\content_entity_base\Entity\Form\EntityBaseForm",
 *       "delete"     = "\Drupal\Core\Entity\ContentEntityDeleteForm",
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
 *     "collection"   = "/admin/content/ceb_test_content",
 *     "canonical"    = "/admin/ceb_test_content/{ceb_test_content}",
 *     "add-page"    = "/admin/ceb_test_content/add",
 *     "add-form"    = "/admin/ceb_test_content/add/{type}",
 *     "delete-form"  = "/admin/ceb_test_content/{ceb_test_content}/delete",
 *     "edit-form"    = "/admin/ceb_test_content/{ceb_test_content}/edit",
 *     "version-history" = "/admin/ceb_test_content/{ceb_test_content}/revisions",
 *     "revision" = "/admin/ceb_test_content/{ceb_test_content}/revisions/{ceb_test_content_revision}/view",
 *     "revision-revert" = "/admin/ceb_test_content/{ceb_test_content}/revisions/{ceb_test_content_revision}/revert",
 *     "revision-delete" = "/admin/ceb_test_content/{ceb_test_content}/revisions/{ceb_test_content_revision}/delete",
 *   },
 * )
 */
class CebTestContent extends EntityBase {
}
