<?php

namespace DrupalVmGenerator\Command;

use Symfony\Component\Filesystem\Filesystem;
use Twig_Environment;

abstract class GeneratorCommand extends Command
{
    /**
     * @var Twig_Environment $twig
     */
    protected $twig;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    public function __construct(Twig_Environment $twig, Filesystem $filesystem)
    {
        $this->twig = $twig;

        $this->filesystem = $filesystem;

        parent::__construct();
    }
}
