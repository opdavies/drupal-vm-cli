<?php

namespace DrupalVm\tests\Command;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

abstract class CommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $rootDir;

    /**
     * @var Filesystem
     */
    protected $fs;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->rootDir = realpath(__DIR__.'/../../');
        $this->fs = new Filesystem();
    }

    protected function runCommand($command, $workingDirectory = null)
    {
        $process = new Process($command);
        $process->setWorkingDirectory($workingDirectory ?: $this->rootDir);
        $process->mustRun();

        return $process->getOutput();
    }

    protected function assertFileContains($filename, $search)
    {
        $contents = file_get_contents($filename);

        return $this->assertContains($search, $contents);
    }

    protected function assertFileNotContains($filename, $search)
    {
        $contents = file_get_contents($filename);

        return $this->assertNotContains($search, $contents);
    }

    protected function deleteFile()
    {
        if ($this->filename) {
            $this->fs->remove($this->filename);
        }
    }
}
