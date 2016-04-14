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
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var Twig_Environment
     */
    protected $twig;

    /**
     * @var string
     */
    protected $projectDir;

    /**
     * @var DrupalVmStyle
     */
    protected $io;

    public function __construct(Twig_Environment $twig, Filesystem $filesystem)
    {
        $this->twig = $twig;

        $this->filesystem = $filesystem;

        parent::__construct();
    }

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
}
