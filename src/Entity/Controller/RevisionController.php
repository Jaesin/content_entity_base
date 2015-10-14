<?php

/**
 * @file
 * Contains \Drupal\foo\Entity\Controller\RevisionController.
 */

namespace Drupal\content_entity_base\Entity\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\content_entity_base\Entity\EntityRevisionLogInterface;
use Drupal\content_entity_base\Entity\ExpandedEntityRevisionInterface;
use Drupal\content_entity_base\Entity\Routing\RevisionObjectExtractionTrait;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\user\EntityOwnerInterface;

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

  protected function dateFormatter() {
    return \Drupal::service('date.formatter');
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

  protected function getRevisionDescription(ContentEntityInterface $revision, $is_current = FALSE) {
    /** @var \Drupal\Core\Entity\ContentEntityInterface|\Drupal\user\EntityOwnerInterface $revision */

    if ($revision instanceof EntityOwnerInterface) {
      $username = [
        '#theme' => 'username',
        '#account' => $revision->getOwner(),
      ];
    }
    else {
      $username = '';
    }

    if ($revision instanceof ExpandedEntityRevisionInterface) {
      // Use revision link to link to revisions that are not active.
      $date = $this->dateFormatter()->format($revision->getRevisionCreationTime(), 'short');
      if (!$is_current) {
        $link = $this->l($date, $revision->urlInfo('revision'));
      }
      else {
        $link = $revision->link($date);
      }
    }
    else {
      $link = $revision->link($revision->label(), 'revision');
    }

    $markup = '';
    if ($revision instanceof EntityRevisionLogInterface) {
      $markup = $revision->getRevisionLog();
    }

    if ($username) {
      $template = '{% trans %}{{ date }} by {{ username }}{% endtrans %}{% if message %}<p class="revision-log">{{ message }}</p>{% endif %}';
    }
    else {
      $template = '{% trans %} {{ date }} {% endtrans %}{% if message %}<p class="revision-log">{{ message }}</p>{% endif %}';
    }

    $column = [
      'data' => [
        '#type' => 'inline_template',
        '#template' => $template,
        '#context' => [
          'date' => $link,
          'username' => $this->renderer()->renderPlain($username),
          'message' => ['#markup' => $markup, '#allowed_tags' => Xss::getHtmlTagList()],
        ],
      ],
    ];
    return $column;
  }

  protected function getRevisionTitle(EntityInterface $revision) {
    return $revision->label();
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
