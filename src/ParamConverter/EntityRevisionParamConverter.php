<?php

/**
 * @file
 * Contains \Drupal\content_entity_base\ParamConverter\EntityRevisionParamConverter.
 */

namespace Drupal\content_entity_base\ParamConverter;

use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\ParamConverter\ParamConverterInterface;
use Symfony\Component\Routing\Route;

/**
 * Parameter converter for single revisions.
 */
class EntityRevisionParamConverter implements ParamConverterInterface {

  /** @var \Drupal\Core\Entity\EntityManagerInterface */
  protected $entityManager;

  /**
   * Creates a new EntityRevisionParamConverter instance.
   *
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   */
  public function __construct(EntityManagerInterface $entity_manager) {
    $this->entityManager = $entity_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function convert($value, $definition, $name, array $defaults) {
    list (, $entity_type_id) = explode(':', $definition['type']);
    $entity_storage = $this->entityManager->getStorage($entity_type_id);
    return $entity_storage->loadRevision($value);
  }

  /**
   * {@inheritdoc}
   */
  public function applies($definition, $name, Route $route) {
    return isset($definition['type']) && strpos($definition['type'], 'entity_revision:') !== FALSE;
  }

}
