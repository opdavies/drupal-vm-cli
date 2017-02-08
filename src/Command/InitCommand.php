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
    protected function configure()
    {
        $this->setName('init')
            ->setDescription('Initialises the Drupal VM CLI')
            ->addOption('overwrite', null, InputOption::VALUE_NONE);
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

        if ($this->filesystem->exists($path) && !$this->input->getOption('overwrite')) {
            return $this->io->error(sprintf('%s already exists', $filename));
        }

        $this->filesystem->copy(
            __DIR__.'/../../config/dist/defaults.yml',
            $path
        );

        $this->io->success(sprintf('%s copied.', $filename));

        return $this;
    }
}
