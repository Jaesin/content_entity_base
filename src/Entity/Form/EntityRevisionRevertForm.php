<?php

/**
 * @file
 * Contains \Drupal\content_entity_base\Entity\Form\EntityRevisionRevertForm.
 */

namespace Drupal\content_entity_base\Entity\Form;

use Drupal\content_entity_base\Entity\EntityRevisionLogInterface;
use Drupal\content_entity_base\Entity\TimestampedRevisionInterface;
use Drupal\Core\Datetime\DateFormatter;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a generic entity revision revert form.
 */
class EntityRevisionRevertForm extends ConfirmFormBase {

  /**
   * The entity revision to revert.
   *
   * @var \Drupal\Core\Entity\ContentEntityInterface
   */
  protected $entityRevision;

  /**
   * The date formatter service.
   *
   * @var \Drupal\Core\Datetime\DateFormatter
   */
  protected $dateFormatter;

  /**
   * Constructs a new NodeRevisionRevertForm.
   *
   * @param \Drupal\Core\Datetime\DateFormatter $date_formatter
   *   The date formatter service.
   */
  public function __construct(DateFormatter $date_formatter) {
    $this->dateFormatter = $date_formatter;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('date.formatter')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'entity_revision_revert_confirm';
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    if ($this->entityRevision instanceof TimestampedRevisionInterface) {
      return t('Are you sure you want to revert to the revision from %revision-date?', ['%revision-date' => $this->dateFormatter->format($this->entityRevision->getRevisionCreationTime())]);
    }
    else {
      return t('Are you sure you want to revert to the revision');
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return $this->entityRevision->urlInfo('version-history');
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return t('Revert');
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return '';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $_entity_revision = NULL) {
    $this->entityRevision = $_entity_revision;
    $form = parent::buildForm($form, $form_state);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // The revision timestamp will be updated when the revision is saved. Keep
    // the original one for the confirmation message.

    $this->entityRevision->setNewRevision();
    $this->entityRevision->isDefaultRevision(TRUE);

    if ($this->entityRevision instanceof EntityRevisionLogInterface) {
      if ($this->entityRevision instanceof TimestampedRevisionInterface) {
        $original_revision_timestamp = $this->entityRevision->getRevisionCreationTime();
        $this->entityRevision->revision_log = t('Copy of the revision from %date.', ['%date' => $this->dateFormatter->format($original_revision_timestamp)]);
      }
      else {
        $this->entityRevision->revision_log = t('Copy of the revision');
      }
    }
    $this->entityRevision->save();

    $this->logger('content')->notice('@type: reverted %title revision %revision.', ['@type' => $this->entityRevision->bundle(), '%title' => $this->entityRevision->label(), '%revision' => $this->entityRevision->getRevisionId()]);
    drupal_set_message(t('@type %title has been reverted to the revision', [
      '@type' => $this->entityRevision->{$this->entityRevision->getEntityType()->getKey('bundle')}->entity->label(),
      '%title' => $this->entityRevision->label(),
    ]));
    $form_state->setRedirectUrl($this->entityRevision->urlInfo('version-history'));
  }

}
