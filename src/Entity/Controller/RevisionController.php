<?php

/**
 * @file
 * Contains \Drupal\foo\Entity\Controller\RevisionController.
 */

namespace Drupal\content_entity_base\Entity\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Render\RendererInterface;

class RevisionController extends ControllerBase {

  use RevisionControllerTrait;

  public function entityManager() {
    // TODO: Implement entityManager() method.
  }

  /**
   * {@inheritdoc}
   */
  public function renderer() {
    return \Drupal::service('renderer');
  }

  public function languageManager() {
    return \Drupal::service('language_manager');
  }

  public function showRevision($revision_id) {
  }

  public function revisionPageTitle($revision_id) {
  }

  protected function hasRevertRevisionPermission(EntityInterface $entity) {
    return $this->currentUser()->hasPermission("revert all {$entity->id()} revisions");
  }

  protected function hasDeleteRevisionPermission(EntityInterface $entity) {
    return $this->currentUser()->hasPermission("delete all {$entity->id()} revisions");
  }

  protected function buildRevertRevisionLink(EntityInterface $entity, $revision_id) {
  }

  protected function buildDeleteRevisionLink(EntityInterface $entity, $revision_id) {
  }

  protected function getRevisionDescription(EntityInterface $revision, $is_current = FALSE) {
  }

  protected function getRevisionTitle(EntityInterface $revision) {
  }

  protected function getRevisionEntityTypeId() {
  }

  protected function getEntityViewBuilder(EntityManagerInterface $entity_manager, RendererInterface $renderer) {
  }

  public function revisionOverview(ContentEntityInterface $entity) {
    $a = 123;
  }

  protected function getOperationLinks(EntityInterface $entity, $revision_id) {
  }

}
