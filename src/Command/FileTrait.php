<?php

namespace DrupalVmGenerator\Command;

use DrupalVmGenerator\Style\DrupalVmStyle;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Filesystem\Filesystem;

trait FileTrait
{
    /**
     * @param Filesystem $filesystem
     * @param InputInterface $input
     * @param DrupalVmStyle $io
     *
     * @return Command
     * @throws \Exception
     */
    private function writeFile(Filesystem $filesystem, InputInterface $input, DrupalVmStyle $io)
    {
        if (empty($this->fileContents)) {
            throw new \Exception('The generated file is empty.');
        }

        if ($filesystem->exists(self::FILENAME) && !$input->getOption('force')) {
            $io->error(sprintf('%s already exists.', self::FILENAME));
        } else {
            $filesystem->dumpFile(
                self::FILENAME,
                $this->fileContents
            );

            $io->success(sprintf('%s created', self::FILENAME));
        }

        return $this;
    }
}
