<?php

namespace Drupal\content_entity_base\Entity\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\content_entity_base\Entity\EntityBaseInterface;
use Drupal\content_entity_base\Entity\Revision\RevisionLogInterface;
use Drupal\content_entity_base\Entity\Routing\RevisionObjectExtractionTrait;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Utility base class for CEB entities.
 *
 * @see \Drupal\Core\Controller\ControllerBase
 *
 * @ingroup content_entity_base
 */
class RevisionController extends ControllerBase {

  use RevisionControllerTrait;
  use RevisionObjectExtractionTrait;

  /**
   * {@inheritdoc}
   */
  public function showRevision(ContentEntityInterface $_entity_revision) {
    return $this->doShowRevision($_entity_revision);
  }

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

  protected function dateFormatter() {
    return \Drupal::service('date.formatter');
  }

  protected function hasDeleteRevisionPermission(EntityInterface $entity) {
    return $this->currentUser()->hasPermission("delete all {$entity->id()} revisions");
  }

  protected function buildRevertRevisionLink(EntityInterface $entity_revision) {
    return [
      'title' => t('Revert'),
      'url' => $entity_revision->toUrl('revision-revert'),
    ];
  }

  protected function buildDeleteRevisionLink(EntityInterface $entity_revision) {
    return [
      'title' => t('Delete'),
      'url' => $entity_revision->toUrl('revision-delete'),
    ];
  }

  protected function getRevisionDescription(ContentEntityInterface $revision, $is_current = FALSE) {
    /** @var EntityBaseInterface $revision */

    if ($revision instanceof EntityOwnerInterface) {
      $username = [
        '#theme' => 'username',
        '#account' => $revision->getOwner(),
      ];
    }
    else {
      $username = '';
    }

    if ($revision instanceof RevisionLogInterface) {
      // Use revision link to link to revisions that are not active.
      $date = $this->dateFormatter()->format($revision->getRevisionCreationTime(), 'short');
      if (!$is_current) {
        $link = $this->l($date, $revision->toUrl('revision'));
      }
      else {
        $link = $revision->toLink($date);
      }
    }
    else {
      $link = $revision->toLink($revision->label(), 'revision');
    }

    $markup = '';
    if ($revision instanceof RevisionLogInterface) {
      $markup = $revision->getRevisionLogMessage();
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

  /**
   * Returns a string providing the title of the revision.
   *
   * @param \Drupal\Core\Entity\EntityInterface $_entity_revision
   *   Returns a string to provide the title of the revision.
   *
   * @return string
   *   Revision title.
   */
  public function revisionTitle(EntityInterface $_entity_revision) {
    /** @var EntityBaseInterface $_entity_revision */
    if ($_entity_revision instanceof RevisionLogInterface) {
      return $this->t('Revision of %title from %date', array('%title' => $_entity_revision->label(), '%date' => format_date($_entity_revision->getRevisionCreationTime())));
    }
    else {
      return $this->t('Revision of %title', array('%title' => $_entity_revision->label()));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function revisionOverviewController(RouteMatchInterface $route_match) {
    $entity_revision = $this->extractEntityFromRouteMatch($route_match);
    return $this->revisionOverview($entity_revision);
  }

  /**
   * {@inheritdoc}
   */
  protected function hasRevertRevisionPermission(EntityInterface $entity) {
    return AccessResult::allowedIfHasPermission($this->currentUser(), "revert all {$entity->getEntityTypeId()} revisions")->orIf(
      AccessResult::allowedIfHasPermission($this->currentUser(), "revert {$entity->bundle()} {$entity->getEntityTypeId()} revisions")
    );
  }

}
