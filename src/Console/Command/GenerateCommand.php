<?php

namespace DrupalVmConfigGenerator\Console\Command;

use DrupalVmConfigGenerator\Console\Command\ExtrasTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateCommand extends BaseCommand
{
    use ExtrasTrait;

    const FILENAME = 'config.yml';

    private $fileContents;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('config:generate')
            ->setDescription('Generate a new Drupal VM configuration file.')
            ->setAliases(['generate'])
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
        $args = [];

        // Vagrant hostname.
        $args['vagrant_hostname'] = $this->io->ask(
            'Enter a hostname for Vagrant',
            'drupalvm.dev'
        );

        // Vagrant machine name.
        $args['vagrant_machine_name'] = $this->io->ask(
            'Enter a Vagrant machine name',
            'drupalvm'
        );

        // Vagrant IP address.
        $args['vagrant_ip_address'] = $this->io->ask(
            'Enter an IP address for the Vagrant VM',
            '192.168.88.88'
        );

        // CPUs.
        $args['vagrant_cpus'] = $this->io->ask(
            'How many CPUs?',
            2
        );

        // Memory.
        $args['vagrant_memory'] = $this->io->ask(
            'How much memory?',
            1024
        );

        // Which web server to use?
        $args['drupalvm_webserver'] = $this->io->choiceNoList(
            'Apache or Nginx?',
            ['apache', 'nginx'],
            null
        );

        // Domain name.
        $args['drupal_domain'] = $this->io->ask(
            'Enter a domain for your site',
            $args['vagrant_hostname']
        );

        // Local site path.
        $args['local_path'] = $this->io->ask(
            'Enter the local path for your Drupal site',
            '~/Sites/drupalvm'
        );

        // Destination site path.
        $args['destination'] = $this->io->ask(
            'Enter the destination path for your Drupal site',
            '/var/www/drupalvm'
        );

        // Which version of Drupal?
        $args['drupal_major_version'] = $this->io->choiceNoList(
            'Which version of Drupal',
            ['8', '7']
        );

        $args['build_makefile'] = $this->io->confirm(
            'Build from make file',
            false
        ) ? 'yes' : 'no';

        $args['install_site'] = $this->io->confirm(
            'Install the site',
            false
        ) ? 'yes' : 'no';

        $args['installed_extras'] = $this->extrasQuestion($this->io);

        // Add some default arguments.
        $args += [
            'drupal_mysql_database' => 'drupal',
            'drupal_mysql_user' => 'drupal',
            'drupal_mysql_password' => 'drupal',
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
