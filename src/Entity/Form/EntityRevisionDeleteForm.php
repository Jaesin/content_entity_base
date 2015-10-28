<?php

/**
 * @file
 * Contains \Drupal\content_entity_base\Entity\Form\EntityRevisionDeleteForm.
 */

namespace Drupal\content_entity_base\Entity\Form;
use Drupal\content_entity_base\Entity\TimestampedRevisionInterface;
use Drupal\Core\Datetime\DateFormatter;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a generic entity revision delete form.
 */
class EntityRevisionDeleteForm extends ConfirmFormBase {

  /**
   * The entity revision to delete.
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
   * @var \Drupal\Core\Entity\EntityManagerInterface
   */
  protected $entityManager;

  /**
   * Constructs a new NodeRevisionDeleteForm.
   *
   * @param \Drupal\Core\Datetime\DateFormatter $date_formatter
   *   The date formatter service.
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   *   The entity manager.
   */
  public function __construct(DateFormatter $date_formatter, EntityManagerInterface $entity_manager) {
    $this->dateFormatter = $date_formatter;
    $this->entityManager = $entity_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('date.formatter'),
      $container->get('entity.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'entity_revision_delete_confirm';
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    if ($this->entityRevision instanceof TimestampedRevisionInterface) {
      return t('Are you sure you want to delete the revision from %revision-date?', ['%revision-date' => $this->dateFormatter->format($this->entityRevision->getRevisionCreationTime())]);
    }
    else {
      return t('Are you sure you want to delete to the revision');
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
    return t('Delete');
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
    $this->entityManager->getStorage($this->entityRevision->getEntityTypeId())->deleteRevision($this->entityRevision->getRevisionId());

    $this->logger('content')->notice('@type: deleted %title revision %revision.', ['@type' => $this->entityRevision->bundle(), '%title' => $this->entityRevision->label(), '%revision' => $this->entityRevision->getRevisionId()]);
    drupal_set_message(t('@type %title has been deleted', [
      '@type' => $this->entityRevision->{$this->entityRevision->getEntityType()->getKey('bundle')}->entity->label(),
      '%title' => $this->entityRevision->label(),
    ]));
    $form_state->setRedirectUrl($this->entityRevision->urlInfo('version-history'));
  }
}
