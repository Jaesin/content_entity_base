<?php

/**
 * @file
 *   Contains Drupal\ceb_test\Entity\CebTestContentType.
 */

namespace Drupal\ceb_test\Entity;

use Drupal\content_entity_base\Entity\EntityTypeBase;

/**
 * Defines the ceb_test_content type configuration entity.
 *
 * @ConfigEntityType(
 *   id               = "ceb_test_content_type",
 *   label            = @Translation("Ceb_test content type"),
 *   admin_permission = "administer ceb_test_content",
 *   config_prefix    = "content_type",
 *   bundle_of        = "ceb_test_content",
 *   handlers = {
 *     "form" = {
 *       "default" = "Drupal\content_entity_base\Entity\Form\EntityTypeBaseForm",
 *       "add"     = "Drupal\content_entity_base\Entity\Form\EntityTypeBaseForm",
 *       "edit"    = "Drupal\content_entity_base\Entity\Form\EntityTypeBaseForm",
 *       "delete"  = "Drupal\content_entity_base\Entity\Form\EntityTypeBaseDeleteForm",
 *     },
 *     "list_builder" = "Drupal\content_entity_base\Entity\Listing\EntityTypeBaseListBuilder",
 *     "route_provider" = {
 *       "html" = "\Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider",
 *       "crud" = "\Drupal\content_entity_base\Entity\Routing\CrudUiRouteProvider",
 *     },
 *   },
 *   entity_keys = {
 *     "id"           = "id",
 *     "label"        = "label",
 *   },
 *   links = {
 *     "collection"   = "/admin/structure/ceb_test_content",
 *     "add-form"     = "/admin/structure/ceb_test_content/manage/add",
 *     "edit-form"    = "/admin/structure/ceb_test_content/manage/{ceb_test_content_type}",
 *     "delete-form"  = "/admin/structure/ceb_test_content/manage/{ceb_test_content_type}/delete",
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "revision",
 *     "description",
 *   }
 * )
 */
class CebTestContentType extends EntityTypeBase {
}
