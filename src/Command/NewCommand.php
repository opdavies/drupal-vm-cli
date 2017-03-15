<?php

namespace DrupalVm\Command;

use Symfony\Component\Console\Input\Input;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use ZipArchive;

class NewCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected $command = 'new';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Downloads a new copy of Drupal VM';

    /**
     * {@inheritdoc}
     */
    protected $aliases = ['download'];

    /**
     * @var string
     */
    private $zipFile;

    /**
     * @var string
     */
    private $version = 'master';

    /**
     * {@inheritdoc}
     */
    protected function arguments()
    {
        return [
            ['directory', InputArgument::OPTIONAL, '', 'drupal-vm'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function options()
    {
        return [
            ['latest', null, InputOption::VALUE_NONE, 'Download the latest development version.'],
            ['dev', null, InputOption::VALUE_NONE, 'Download the latest development version.'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->assertDirectoryDoesNotExist();

        $io = $this->io;

        $output->writeln('<comment>Downloading Drupal VM...</comment>');

        $this->zipFile = $this->makeFileName();

        $this->download()
            ->extract()
            ->cleanUp();

        $io->writeln(
            sprintf(
                '<info>Drupal VM downloaded to %s.</info>',
                $this->argument('directory')
            )
        );
    }

    /**
     * Check that the output directory doesn’t already exist.
     */
    private function assertDirectoryDoesNotExist()
    {
        $directory = $this->argument('directory');

        if (is_dir($directory)) {
            $this->output->writeln(
                sprintf(
                    '<error>%s already exists.</error>',
                    $this->argument('directory')
                )
            );

            exit(1);
        }
    }

    /**
     * Creates a unique file name.
     *
     * @return string
     */
    private function makeFileName()
    {
        return getcwd().'/drupalvm_'.md5(time().uniqid()).'.zip';
    }

    /**
     * Download Drupal VM from GitHub.
     *
     * @return $this
     */
    private function download()
    {
        if (!$this->option('latest') && !$this->option('dev')) {
            $this->version = $this->getLatestVersion();
        }

        $url = sprintf(
            'https://github.com/geerlingguy/drupal-vm/archive/%s.zip',
            $this->version
        );

        $response = $this->container['guzzle']->get($url)->getBody();

        file_put_contents($this->zipFile, $response);

        return $this;
    }

    /**
     * Extract the contents of the zip file.
     *
     * @return $this
     */
    private function extract()
    {
        $archive = new ZipArchive();

        $archive->open($this->zipFile);
        $archive->extractTo('.');
        $archive->close();

        rename(
            sprintf('drupal-vm-%s', $this->version),
            $this->input->getArgument('directory')
        );

        return $this;
    }

    /**
     * Removes any temporary files.
     *
     * @return $this
     */
    private function cleanUp()
    {
        @chmod($this->zipFile, 0777);
        @unlink($this->zipFile);

        return $this;
    }

    /**
     * Get the latest release from GitHub.
     *
     * @return string
     */
    private function getLatestVersion()
    {
        $result = $this->container['github']->api('repo')->releases()->latest(
            'geerlingguy',
            'drupal-vm'
        );

        return $result['tag_name'];
    }
}
