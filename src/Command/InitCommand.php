<?php

namespace DrupalVm\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class InitCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected $command = 'init';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Initialises the Drupal VM CLI';

    /**
     * {@inheritdoc}
     */
    protected function options()
    {
        return [
            ['overwrite', null, InputOption::VALUE_NONE],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->copyFiles();
    }

    /**
     * @return $this
     */
    private function copyFiles()
    {
        $filename = 'defaults.yml';
        $path = sprintf('%s/.drupal-vm-generator/%s', $this->getUserHomeDirectory(), $filename);

        if ($this->container['filesystem']->exists($path) && !$this->option('overwrite')) {
            return $this->error(sprintf('%s already exists', $filename));
        }

        $this->container['filesystem']->copy(
            __DIR__.'/../../config/dist/defaults.yml',
            $path
        );

        $this->success(sprintf('%s copied.', $filename));

        return $this;
    }
}
