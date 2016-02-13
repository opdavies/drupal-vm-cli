<?php

namespace DrupalVmConfigGenerator\Console\Command;

use DrupalVmConfigGenerator\Console\Command\ExtrasTrait;
use DrupalVmConfigGenerator\Console\Command\FileTrait;
use DrupalVmConfigGenerator\Console\Style\DrupalVmStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class GenerateCommand extends BaseCommand
{
    use ExtrasTrait;
    use FileTrait;

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
            ->addOption(
                'hostname',
                null,
                InputOption::VALUE_OPTIONAL,
                'The Vagrant hostname'
            )
            ->addOption(
                'machine-name',
                null,
                InputOption::VALUE_REQUIRED,
                'The Vagrant machine name'
            )
            ->addOption(
                'ip-address',
                null,
                InputOption::VALUE_REQUIRED,
                'The IP address for the VM'
            )
            ->addOption(
                'cpus',
                null,
                InputOption::VALUE_REQUIRED,
                'The number of CPUs'
            )
            ->addOption(
                'memory',
                null,
                InputOption::VALUE_REQUIRED,
                'The amount of memory'
            )
            ->addOption(
                'webserver',
                null,
                InputOption::VALUE_OPTIONAL,
                'Which webserver to use'
            )
            ->addOption(
                'domain',
                null,
                InputOption::VALUE_OPTIONAL,
                'The domain name for the site'
            )
            ->addOption(
                'path',
                null,
                InputOption::VALUE_OPTIONAL,
                'The local path for the synchronised folder'
            )
            ->addOption(
                'destination',
                null,
                InputOption::VALUE_OPTIONAL,
                'The destination path'
            )
            ->addOption(
                'drupal-version',
                null,
                InputOption::VALUE_OPTIONAL,
                'Which version of Drupal to install',
                8
            )
            ->addOption(
                'build-makefile',
                null,
                InputOption::VALUE_OPTIONAL,
                'Whether to install from a Drush Make file'
            )
            ->addOption(
                'install-site',
                null,
                InputOption::VALUE_OPTIONAL,
                'Install the site when the VM is provisioned'
            )
            ->addOption(
                'installed-extras',
                null,
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'Install from a predefined list of extra packages'
            )
            ->addOption(
                'force',
                'f',
                InputOption::VALUE_NONE
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $io = new DrupalVmStyle($input, $output);

        // --hostname option
        if (!$input->getOption('hostname')) {
            $input->setOption('hostname', $this->io->ask(
                'Enter a hostname for Vagrant',
                'drupalvm.dev'
            ));
        }

        // --machine-name option
        if (!$input->getOption('machine-name')) {
            $input->setOption('machine-name', $this->io->ask(
                'Enter a Vagrant machine name',
                'drupalvm'
            ));
        }

        // ip-address option
        if (!$input->getOption('ip-address')) {
            $input->setOption('ip-address', $this->io->ask(
                'Enter an IP address for the Vagrant VM',
                '192.168.88.88'
            ));
        }

        // --cpus option
        if (!$input->getOption('cpus')) {
            $input->setOption('cpus', $this->io->ask(
                'How many CPUs?',
                2
            ));
        }

        // --memory option
        if (!$input->getOption('memory')) {
            $input->setOption('memory', $this->io->ask(
                'How much memory?',
                1024
            ));
        }

        // --webserver option
        if (!$input->getOption('webserver')) {
            $input->setOption('webserver', $this->io->choiceNoList(
                'Apache or Nginx?',
                ['apache', 'nginx']
            ));
        }


        // --domain option
        if (!$input->getOption('domain')) {
            $input->setOption('domain', $this->io->ask(
                'Enter a domain for your site',
                'drupalvm.dev'
            ));
        }

        // --path option
        if (!$input->getOption('path')) {
            $input->setOption('path', $this->io->ask(
                'Enter the local path for your Drupal site',
                '~/Sites/drupalvm'
            ));
        }

        // --destination option
        if (!$input->getOption('destination')) {
            $input->setOption('destination', $this->io->ask(
                'Enter the destination path for your Drupal site',
                '/var/www/drupalvm'
            ));
        }

        // --drupal-version option
        if (!$input->getOption('drupal-version')) {
            $input->setOption('drupal-version', $this->io->choiceNoList(
                'Which version of Drupal',
                ['8', '7']
            ));
        }

        // --build-makefile option
        if (!$input->getOption('build-makefile')) {
            $input->setOption('build-makefile', $this->io->confirm(
                'Build from make file',
                false
            ) ? 'yes' : 'no');
        }

        // --install-site option
        if (!$input->getOption('install-site')) {
            $input->setOption('install-site', $this->io->confirm(
                'Install the site',
                false
            ) ? 'yes' : 'no');
        }

        // --installed-extras option
        if (!$input->getOption('installed-extras')) {
            $input->setOption('installed-extras', $this->extrasQuestion($io));
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this
            ->generate($input)
            ->writeFile(new Filesystem(), $input, $this->io)
        ;
    }

    /**
     * @param InputInterface $input
     *
     * @return GenerateCommand
     */
    private function generate(InputInterface $input)
    {
        $args = [
            // Add some default arguments.
            'drupal_mysql_database' => 'drupal',
            'drupal_mysql_user' => 'drupal',
            'drupal_mysql_password' => 'drupal',
        ];

        $args['vagrant_hostname'] = $input->getOption('hostname');
        $args['vagrant_machine_name'] = $input->getOption('machine-name');
        $args['vagrant_ip_address'] = $input->getOption('ip-address');
        $args['vagrant_cpus'] = $input->getOption('cpus');
        $args['vagrant_memory'] = $input->getOption('memory');
        $args['drupalvm_webserver'] = $input->getOption('webserver');
        $args['drupal_domain'] = $input->getOption('domain');
        $args['local_path'] = $input->getOption('path');
        $args['destination'] = $input->getOption('destination');
        $args['drupal_major_version'] = $input->getOption('drupal-version');
        $args['build_makefile'] = $input->getOption('build-makefile');
        $args['install_site'] = $input->getOption('install-site');

        $args['installed_extras'] = [];
        foreach ($input->getOption('installed-extras') as $item) {
            $args['installed_extras'] = array_merge(
                $args['installed_extras'],
                explode(',', $item)
            );
        }

        $this->fileContents = $this->twig->render('config.yml.twig', ['app' => $args]);

        return $this;
    }
}
