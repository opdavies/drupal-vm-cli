<?php

namespace DrupalVmGenerator\Command;

use DrupalVmGenerator\Exception\FileEmptyException;
use Exception;
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

    /**
     * @var string
     */
    protected $fileContents;

    public function __construct(Twig_Environment $twig, Filesystem $filesystem)
    {
        $this->twig = $twig;

        $this->filesystem = $filesystem;

        parent::__construct();
    }

    /**
     * Check if the file already exists.
     */
    protected function assertFileAlreadyExists($filename)
    {
        if (file_exists($this->projectDir . '/' . $filename) && !$this->input->getOption('overwrite')) {
            $this->io->error(sprintf('%s already exists.', $filename));

            exit(1);
        }
    }

    /**
     * @param string $filename
     *
     * @return Command
     *
     * @throws Exception
     */
    protected function writeFile($filename)
    {
        if (empty($this->fileContents)) {
            throw new FileEmptyException('The generated file was empty.');
        }

        $this->filesystem->dumpFile($filename, $this->fileContents);

        $this->io->success(sprintf('%s created', $filename));

        return $this;
    }

}
