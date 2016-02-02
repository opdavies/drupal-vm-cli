<?php

namespace DrupalVmConfigGenerator\Console\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateCommand extends BaseCommand
{
    const FILENAME = 'config.yml';

    private $fileContents;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('generate')
            ->setDescription('Generate a new Drupal VM configuration file.')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this
            ->generate()
            ->writeFile()
        ;
    }

    /**
     * Generates the contents of the file.
     *
     * @return GenerateCommand
     */
    private function generate()
    {
        $this->questionHelper = $this->getHelper('question');

        $args = [
            'vagrant_hostname' => 'drupalvm.dev',
            'vagrant_machine_name' => 'drupalvm',
            'vagrant_ip_address' => '192.168.88.88',
            'local_path' => '~/Sites/drupalvm',
            'destination' => '/var/www/drupalvm',
            'vagrant_cpus' => 2,
            'vagrant_memory' => 1024,
            'drupalvm_webserver' => 'apache',
            'install_site' => 'true',
            'drupal_major_version' => 8,
            'drupal_domain' => 'drupalvm.dev',
        ];

        $this->fileContents = $this->twig->render('config.yml.twig', ['app' => $args]);

        return $this;
    }

    /**
     * Writes the file to disk.
     *
     * @return GenerateCommand
     */
    private function writeFile()
    {
        $this->fs->dumpFile($this->projectDir . DIRECTORY_SEPARATOR . self::FILENAME, $this->fileContents);

        return $this;
    }
}
