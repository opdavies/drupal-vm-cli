<?php

namespace DrupalVmGenerator\Command;

use GuzzleHttp\ClientInterface;
use DrupalVmGenerator\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use ZipArchive;

class NewCommand extends Command
{
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var string
     */
    private $zipFile;

    /**
     * @var string
     */
    private $version = 'master';

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('new')
            ->setDescription('Downloads a new copy of Drupal VM')
            ->setAliases(['download'])
            ->addArgument(
                'directory',
                InputArgument::OPTIONAL,
                '',
                'drupal-vm'
            )
            ->addOption(
                'latest',
                null,
                InputOption::VALUE_NONE
            );
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->assertDirectoryDoesNotExist();

        $io = $this->io;

        $io->comment('Downloading Drupal VM...');

        $this->zipFile = $this->makeFileName();

        $this->download()
            ->extract()
            ->cleanUp();

        $io->success(sprintf('Drupal VM downloaded to %s.', $input->getArgument('directory')));
    }

    /**
     * Check that the output directory doesn’t already exist.
     */
    private function assertDirectoryDoesNotExist()
    {
        $directory = $this->input->getArgument('directory');

        if (is_dir($directory)) {
            $this->io->error(sprintf('%s already exists.', $this->input->getArgument('directory')));

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
        return getcwd() . '/drupalvm_' . md5(time() . uniqid()) . '.zip';
    }

    /**
     * Download Drupal VM from GitHub.
     *
     * @return $this
     */
    private function download()
    {
        if (!$this->input->getOption('latest')) {
            $this->version = '2.4.0';
        }

        $url = sprintf('https://github.com/geerlingguy/drupal-vm/archive/%s.zip', $this->version);

        $response = $this->client->get($url)->getBody();

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
}
