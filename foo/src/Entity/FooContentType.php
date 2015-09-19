<?php

/**
 * @file
 *   Contains Drupal\foo\Entity\FooContentType.
 */

namespace Drupal\foo\Entity;

use Drupal\content_entity_base\Entity\EntityTypeBase;

/**
 * Defines the foo_content type configuration entity.
 *
 * @ConfigEntityType(
 *   id               = "foo_content_type",
 *   label            = @Translation("Foo content type"),
 *   admin_permission = "administer foo_content",
 *   config_prefix    = "content_type",
 *   bundle_of        = "foo_content",
 *   handlers = {
 *     "form" = {
 *       "default" = "Drupal\content_entity_base\Entity\Form\EntityTypeBaseForm",
 *       "add"     = "Drupal\content_entity_base\Entity\Form\EntityTypeBaseForm",
 *       "edit"    = "Drupal\content_entity_base\Entity\Form\EntityTypeBaseForm",
 *       "delete"  = "Drupal\content_entity_base\Entity\Form\EntityTypeBaseDeleteForm",
 *     },
 *     "list_builder" = "Drupal\content_entity_base\Entity\Listing\EntityTypeBaseListBuilder",
 *   },
 *   entity_keys = {
 *     "id"           = "id",
 *     "label"        = "label",
 *   },
 *   links = {
 *     "edit-form"    = "/admin/structure/foo_content/manage/{foo_content_type}",
 *     "delete-form"  = "/admin/structure/foo_content/manage/{foo_content_type}/delete",
 *     "collection"   = "/admin/structure/foo_content",
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "revision",
 *     "description",
 *   }
 * )
 */
class FooContentType extends EntityTypeBase {
}
