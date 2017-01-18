<?php

namespace DrupalVm\Command;

use DrupalVm\Style\DrupalVmStyle;
use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

abstract class Command extends BaseCommand
{
    /**
     * @var InputInterface
     */
    protected $input;

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @var string
     */
    protected $projectDir;

    /**
     * @var DrupalVmStyle
     */
    protected $io;

    /**
     * {@inheritdoc}
     */
    protected function initialize(
        InputInterface $input,
        OutputInterface $output
    ) {
        $this->projectDir = getcwd();

        $this->input = $input;
        $this->output = $output;

        $this->io = new DrupalVmStyle($input, $output);
    }

    /**
     * Loads default arguments from a configuration file.
     *
     * @param string $type
     *                     The type of defaults to load (i.e. "config" or "make")
     *
     * @return array
     */
    protected function getDefaultOptions($type)
    {
        // Load and parse the defaults file.
        $path = sprintf('%s/.drupal-vm-generator/%s', $this->getUserHomeDirectory(), 'defaults.yml');
        $values = Yaml::parse(file_get_contents($path));

        return $values['defaults'][$type];
    }

    /**
     * @return string
     */
    protected function getUserHomeDirectory()
    {
        return rtrim(getenv('HOME') ?: getenv('USERPROFILE'), '/\\');
    }
}
