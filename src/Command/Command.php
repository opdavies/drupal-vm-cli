<?php

namespace DrupalVm\Command;

use DrupalVm\Style\DrupalVmStyle;
use Pimple\Container;
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
     * @var Container
     */
    protected $container;

    /**
     * @var string The new command.
     */
    protected $command;

    /**
     * @var string A description for the new command.
     */
    protected $description;

    /**
     * @var array Aliases for the new command.
     */
    protected $aliases = [];

    public function __construct(Container $container)
    {
        $this->container = $container;

        parent::__construct();
    }

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
     * {@inheritdoc}
     */
    protected function configure() {
        $this
            ->setName($this->command)
            ->setDescription($this->description)
            ->setAliases($this->aliases);

        $this->getArguments();
        $this->getOptions();
    }

    protected function arguments()
    {
        return [];
    }

    protected function options()
    {
        return [];
    }

    private function getArguments()
    {
        foreach ($this->arguments() as $argument) {
            $this->addArgument($argument[0], $argument[1], $argument[2], $argument[3]);
        }
    }

    private function getOptions()
    {
        foreach ($this->options() as $option) {
            $this->addOption($option[0], $option[1], $option[2]);
        }
    }

    protected function argument($name)
    {
        return $this->input->getArgument($name);
    }

    protected function option($name)
    {
        return $this->input->getOption($name);
    }

    protected function error($message)
    {
        return $this->io->error($message);
    }

    protected function ask($question, $default = null)
    {
        return $this->io->ask($question, $default);
    }

    protected function success($message)
    {
        return $this->io->success($message);
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
