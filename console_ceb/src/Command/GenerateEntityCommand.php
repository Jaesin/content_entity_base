<?php

/**
 * @file
 * Contains \Drupal\content_entity_base\Command\GenerateEntityCommand.
 */

namespace Drupal\console_ceb\Command;

use Drupal\Console\Command\Generate\EntityCommand;
use Drupal\console_ceb\Generator\EntityContentBaseGenerator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class GenerateEntityCommand.
 *
 * @package Drupal\content_entity_base
 */
class GenerateEntityCommand extends EntityCommand  {

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    $this->setEntityType('EntityContent');
    $this->setCommandName('generate:entity:ceb');
    parent::configure();
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $module = $input->getOption('module');
    $entity_class = $input->getOption('entity-class');
    $entity_name = $input->getOption('entity-name');
    $label = $input->getOption('label');

    $bundle_entity_name = $entity_name . '_type';

    $this
      ->getGenerator()
      ->generate($module, $entity_name, $entity_class, $label, $bundle_entity_name);

    $this->getChain()->addCommand(
      'generate:entity:config', [
        '--module' => $module,
        '--entity-class' => $entity_class . 'Type',
        '--entity-name' => $bundle_entity_name,
        '--label' => $label . ' type',
        '--bundle-of' => $entity_name
      ]
    );
  }

  protected function createGenerator() {
    return new EntityContentBaseGenerator();
  }
}
