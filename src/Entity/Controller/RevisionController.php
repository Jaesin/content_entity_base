<?php

/**
 * @file
 * Contains \Drupal\foo\Entity\Controller\RevisionController.
 */

namespace Drupal\content_entity_base\Entity\Controller;

use Drupal\content_entity_base\Entity\Routing\RevisionObjectExtractionTrait;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\Routing\RouteMatchInterface;

class RevisionController extends ControllerBase {

  use RevisionControllerTrait;
  use RevisionObjectExtractionTrait;

  /**
   * {@inheritdoc}
   */
  public function entityManager() {
    return \Drupal::service('entity.manager');
  }

  /**
   * {@inheritdoc}
   */
  public function renderer() {
    return \Drupal::service('renderer');
  }

  /**
   * {@inheritdoc}
   */
  public function languageManager() {
    return \Drupal::service('language_manager');
  }

  /**
   * {@inheritdoc}
   */
  public function showRevision($revision_id) {
  }

  /**
   * {@inheritdoc}
   */
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

  public function revisionOverviewController(RouteMatchInterface $route_match) {
    $entity_revision = $this->extractEntityFromRouteMatch($route_match);
    return $this->revisionOverview($entity_revision);
  }

  protected function getOperationLinks(EntityInterface $entity, $revision_id) {
  }

}
