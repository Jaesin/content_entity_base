<?php

/**
 * @file
 * Contains
 *   \Drupal\content_entity_base\Entity\Controller\RevisionControllerTrait.
 */

namespace Drupal\content_entity_base\Entity\Controller;

use Drupal\content_entity_base\Entity\TimestampedRevisionInterface;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Defines a trait for common revision UI functionality.
 */
trait RevisionControllerTrait {

  /**
   * @return \Drupal\Core\Entity\EntityManagerInterface
   */
  public abstract function entityManager();

  /**
   * @return \Drupal\Core\Render\RendererInterface
   */
  public abstract function renderer();

  /**
   * @return \Drupal\Core\Language\LanguageManagerInterface
   */
  public abstract function languageManager();

  /**
   * Displays an entity revision.
   *
   * @param ContentEntityInterface $entity_revision
   *   The entity revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function showRevision(ContentEntityInterface $entity_revision) {
    return $this->doShowRevision($entity_revision);
  }

  protected function doShowRevision(ContentEntityInterface $entity_revision) {
    $view_controller = $this->entityManager()->getViewBuilder($entity_revision->getEntityTypeId());
    $page = $view_controller->view($entity_revision);
    unset($page[$entity_revision->getEntityTypeId() . 's'][$entity_revision->id()]['#cache']);
    return $page;
  }

  /**
   * Determines if the user has permission to revert revisions.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity to check revert access for.
   *
   * @return bool
   *   TRUE if the user has revert access.
   */
  abstract protected function hasRevertRevisionPermission(EntityInterface $entity);

  /**
   * Determines if the user has permission to delete revisions.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity to check delete revision access for.
   *
   * @return bool
   *   TRUE if the user has delete revision access.
   */
  abstract protected function hasDeleteRevisionPermission(EntityInterface $entity);

  /**
   * Builds a link to revert an entity revision.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity_revision
   *   The entity to build a revert revision link for.
   *
   * @return array A link render array.
   * A link render array.
   * @internal param int $revision_id The revision ID of the revert link.*   The revision ID of the revert link.
   *
   */
  abstract protected function buildRevertRevisionLink(EntityInterface $entity_revision);

  /**
   * Builds a link to delete an entity revision.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity_revision
   *   The entity to build a delete revision link for.
   *
   * @return array A link render array.
   * A link render array.
   * @internal param int $revision_id The revision ID of the delete link.*   The revision ID of the delete link.
   *
   */
  abstract protected function buildDeleteRevisionLink(EntityInterface $entity_revision);

  /**
   * Returns a string providing details of the revision.
   *
   * E.g. Node describes its revisions using {date} by {username}. For the
   *   non-current revision, it also provides a link to view that revision.
   *
   * @param \Drupal\Core\Entity\ContentEntityInterface $revision
   * @param bool $is_current
   *   TRUE if the revision is the current revision.
   *
   * @return string
   *   Returns a string to provide the details of the revision.
   */
  abstract protected function getRevisionDescription(ContentEntityInterface $revision, $is_current = FALSE);

  /**
   * Generates an overview table of older revisions of an entity.
   *
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   *   An entity object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(ContentEntityInterface $entity) {
    $langcode = $this->languageManager()
      ->getCurrentLanguage(LanguageInterface::TYPE_CONTENT)
      ->getId();
    /** @var \Drupal\content_entity_base\Entity\Storage\RevisionableStorageInterface $entity_storage */
    $entity_storage = $this->entityManager()
      ->getStorage($entity->getEntityTypeId());

    $header = array($this->t('Revision'), $this->t('Operations'));

    $rows = [];

    $vids = $entity_storage->revisionIds($entity);
    $entity_revisions = array_combine($vids, array_map(function($vid) use ($entity_storage) {
      return $entity_storage->loadRevision($vid);
      }, $vids));

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      $row = [];
      /** @var \Drupal\Core\Entity\ContentEntityInterface $revision */
      $revision = $entity_revisions[$vid];
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)
          ->isRevisionTranslationAffected()
      ) {
        if ($latest_revision) {
          $row[] = $this->getRevisionDescription($revision, TRUE);
          $row[] = [
            'data' => [
              '#prefix' => '<em>',
              '#markup' => $this->t('Current revision'),
              '#suffix' => '</em>',
            ],
          ];
          foreach ($row as &$current) {
            $current['class'] = ['revision-current'];
          }
          $latest_revision = FALSE;
        }
        else {
          $row[] = $this->getRevisionDescription($revision, FALSE);
          $links = $this->getOperationLinks($revision);

          $row[] = [
            'data' => [
              '#type' => 'operations',
              '#links' => $links,
            ],
          ];
        }
      }

      $rows[] = $row;
    }

    $build[$entity->getEntityTypeId() . '_revisions_table'] = array(
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    );

    // We have no clue about caching yet.
    $build['#cache']['max-age'] = 0;

    return $build;
  }

  /**
   * Get the links of the operations for an entity revision.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity_revision
   *   The entity to build the revision links for.
   *
   * @return array
   *   The operation links.
   */
  protected function getOperationLinks(EntityInterface $entity_revision) {
    $links = [];
    $revert_permission = $this->hasRevertRevisionPermission($entity_revision);
    $delete_permission = $this->hasDeleteRevisionPermission($entity_revision);
    if ($revert_permission) {
      $links['revert'] = $this->buildRevertRevisionLink($entity_revision);
    }

    if ($delete_permission) {
      $links['delete'] = $this->buildDeleteRevisionLink($entity_revision);
    }
    return $links;
  }

}
