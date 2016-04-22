<?php

namespace DrupalVmGenerator\Command;

use GuzzleHttp\ClientInterface;
use DrupalVmGenerator\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
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
            );
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->zipFile = $this->makeFileName();

        $this->download()
            ->extract()
            ->cleanUp();
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
        $url = 'https://github.com/geerlingguy/drupal-vm/archive/master.zip';

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

        rename('drupal-vm-master', $this->input->getArgument('directory'));

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
