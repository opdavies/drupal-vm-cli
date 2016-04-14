<?php

namespace DrupalVmGenerator\Command;

use DrupalVmGenerator\Style\DrupalVmStyle;
use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Twig_Environment;

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
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->projectDir = getcwd();

        $this->input = $input;
        $this->output = $output;

        $this->io = new DrupalVmStyle($input, $output);
    }

    /**
     * A shortcut for rendering a Twig template.
     *
     * @param $template
     * @param array $parameters
     *
     * @return string
     */
    protected function render($template, array $parameters)
    {
        return $this->twig->render($template, $parameters);
    }
}
