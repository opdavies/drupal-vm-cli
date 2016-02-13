<?php
/**
 * Created by PhpStorm.
 * User: odavies
 * Date: 13/02/2016
 * Time: 21:22
 */

namespace DrupalVmConfigGenerator\Console\Command;

use DrupalVmConfigGenerator\Console\Style\DrupalVmStyle;
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

        $filename = $this->projectDir . DIRECTORY_SEPARATOR . self::FILENAME;

        if ($filesystem->exists($filename)) {
            if (!$input->getOption('force')) {
                $io->error(sprintf('%s already exists.', self::FILENAME));
            } else {
                $filesystem->dumpFile(
                    $filename,
                    $this->fileContents
                );

                $io->success(sprintf('%s created', self::FILENAME));
            }
        }

        return $this;
    }
}
