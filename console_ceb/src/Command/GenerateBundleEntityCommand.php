<?php
/**
 * @file
 *   Contains \Drupal\console_ceb\Command\GenerateBundleEntityCommand.
 */

namespace Drupal\console_ceb\Command;

use Drupal\Console\Command\Generate\EntityCommand;
use Drupal\Console\Style\DrupalStyle;
use Drupal\console_ceb\Generator\BundleEntityGenerator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class GenerateEntityCommand.
 *
 * @package Drupal\content_entity_base
 */
class GenerateBundleEntityCommand extends EntityCommand {

  protected function configure()
  {
    $this->setEntityType('EntityConfig');
    $this->setCommandName('generate:entity:ceb_type');
    parent::configure();

    $this->addOption(
      'bundle-of',
      null,
      InputOption::VALUE_NONE,
      $this->trans('commands.generate.entity.config.options.bundle-of')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function interact(InputInterface $input, OutputInterface $output)
  {
    parent::interact($input, $output);

    $io = new DrupalStyle($input, $output);

    // --bundle-of option
    $bundle_of = $input->getOption('bundle-of');
    if (!$bundle_of) {
      $bundle_of = $io->confirm(
        $this->trans('commands.generate.entity.config.questions.bundle-of'),
        false
      );
      $input->setOption('bundle-of', $bundle_of);
    }
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
    $bundle_of = $input->getOption('bundle-of');

    $this
      ->getGenerator()
      ->generate($module, $entity_name, $entity_class, $label, $bundle_of);
  }

  protected function createGenerator() {
    return new BundleEntityGenerator();
  }
}
