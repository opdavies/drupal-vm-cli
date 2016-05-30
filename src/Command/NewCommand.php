<?php

namespace DrupalVmGenerator\Command;

use Github\Client as GithubClient;
use GuzzleHttp\ClientInterface;
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
     * @var GithubClient
     */
    private $github;

    /**
     * @var string
     */
    private $zipFile;

    /**
     * @var string
     */
    private $version = 'master';

    public function __construct(ClientInterface $client, GithubClient $github)
    {
        $this->client = $client;
        $this->github = $github;

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
                InputOption::VALUE_NONE,
                'Download the latest development version'
            )
            ->addOption(
                'dev',
                null,
                InputOption::VALUE_NONE,
                'Download the latest development version'
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

        $io->success(
            sprintf(
                'Drupal VM downloaded to %s.',
                $input->getArgument('directory')
            )
        );
    }

    /**
     * Check that the output directory doesn’t already exist.
     */
    private function assertDirectoryDoesNotExist()
    {
        $directory = $this->input->getArgument('directory');

        if (is_dir($directory)) {
            $this->io->error(
                sprintf(
                    '%s already exists.',
                    $this->input->getArgument('directory')
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
        $input = $this->input;

        if (!$input->getOption('latest') && !$input->getOption('dev')) {
            $this->version = $this->getLatestVersion();
        }

        $url = sprintf(
            'https://github.com/geerlingguy/drupal-vm/archive/%s.zip',
            $this->version
        );

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

    /**
     * Get the latest release from GitHub.
     *
     * @return string
     */
    private function getLatestVersion()
    {
        $result = $this->github->api('repo')->releases()->latest(
            'geerlingguy',
            'drupal-vm'
        );

        return $result['tag_name'];
    }
}
