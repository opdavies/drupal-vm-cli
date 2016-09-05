<?php

namespace DrupalVmGenerator\Command\Config;

use DrupalVmGenerator\Command\Command;
use DrupalVmGenerator\Command\ExtrasTrait;
use DrupalVmGenerator\Command\GeneratorCommand;
use DrupalVmGenerator\Command\PackagesTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateCommand extends GeneratorCommand
{
    use ExtrasTrait;
    use PackagesTrait;

    const FILENAME = 'config.yml';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('config:generate')
            ->setDescription('Generate a new config.yml file')
            ->setAliases(['generate'])
            ->addOption(
                'machine-name',
                null,
                InputOption::VALUE_REQUIRED,
                'The Vagrant machine name'
            )
            ->addOption(
                'hostname',
                null,
                InputOption::VALUE_OPTIONAL,
                'The Vagrant hostname'
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
                'docroot',
                null,
                InputOption::VALUE_OPTIONAL,
                'The path to the Drupal installation'
            )
            ->addOption(
                'drupal-version',
                null,
                InputOption::VALUE_OPTIONAL,
                'Which version of Drupal to install is being used?'
            )
            ->addOption(
                'database-name',
                null,
                InputOption::VALUE_OPTIONAL,
                'The name of the database to use',
                null
            )
            ->addOption(
                'database-user',
                null,
                InputOption::VALUE_OPTIONAL,
                'The database user to use',
                null
            )
            ->addOption(
                'database-password',
                null,
                InputOption::VALUE_OPTIONAL,
                'The database password to use',
                null
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
                'extra-packages',
                null,
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'Add any additional apt or yum packages'
            )
            ->addOption(
                'no-dashboard',
                null,
                InputOption::VALUE_NONE,
                'Install without the Drupal VM Dashboard'
            )
            ->addOption(
                'no-comments',
                null,
                InputOption::VALUE_NONE,
                'Remove comments from config.yml'
            )
            ->addOption(
                'overwrite',
                null,
                InputOption::VALUE_NONE,
                'Overwrite an existing file if one exists'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function initialize(
        InputInterface $input,
        OutputInterface $output
    ) {
        parent::initialize($input, $output);

        // Check for a defaults file.
        if (!file_exists($path = sprintf('%s/.drupal-vm-generator/%s', $this->getUserHomeDirectory(), 'defaults.yml'))) {
            $this->io->error('No defaults file found. Please run "drupalvm init", then re-run this command.');

            exit(1);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $io = $this->io;

        $this->assertFileAlreadyExists(self::FILENAME);

        $defaults = $this->getDefaultOptions('config');

        // --machine-name option
        if (!$input->getOption('machine-name')) {
            $input->setOption(
                'machine-name',
                $this->io->ask(
                    'Enter a Vagrant machine name',
                    $defaults['vagrant_machine_name']
                )
            );
        }

        // --hostname option
        if (!$input->getOption('hostname')) {
            $input->setOption(
                'hostname',
                $this->io->ask(
                    'Enter a hostname for Vagrant',
                    $input->getOption('machine-name').'.'.$defaults['vagrant_hostname_suffix']
                )
            );
        }

        // --ip-address option
        if (!$input->getOption('ip-address')) {
            $input->setOption(
                'ip-address',
                $this->io->ask(
                    'Enter an IP address for the Vagrant VM',
                    $defaults['vagrant_ip']
                )
            );
        }

        // --cpus option
        if (!$input->getOption('cpus')) {
            $input->setOption(
                'cpus',
                $this->io->ask(
                    'How many CPUs?',
                    $defaults['vagrant_cpus']
                )
            );
        }

        // --memory option
        if (!$input->getOption('memory')) {
            $input->setOption(
                'memory',
                $this->io->ask(
                    'How much memory?',
                    $defaults['vagrant_memory']
                )
            );
        }

        // --webserver option
        if (!$input->getOption('webserver')) {
            $input->setOption(
                'webserver',
                $this->io->choiceNoList(
                    'Apache or Nginx?',
                    ['apache', 'nginx'],
                    $defaults['drupalvm_webserver']
                )
            );
        }


        // --path option
        if (!$input->getOption('path')) {
            $input->setOption(
                'path',
                $this->io->ask(
                    'Enter the local path for your Drupal site',
                    getcwd()
                )
            );
        }

        // --destination option
        if (!$input->getOption('destination')) {
            $input->setOption(
                'destination',
                $this->io->ask(
                    'Enter the destination path for your Drupal site',
                    $defaults['destination']
                )
            );
        }

        // --docroot option
        if (!$input->getOption('docroot')) {
            $input->setOption(
                'docroot',
                $this->io->ask(
                    'Enter the path to the docroot of the Drupal site',
                    $input->getOption(
                        'destination'
                    ).DIRECTORY_SEPARATOR.'drupal'
                )
            );
        }

        // --drupal-version option
        if (!$input->getOption('drupal-version')) {
            $input->setOption(
                'drupal-version',
                $this->io->choiceNoList(
                    'Which version of Drupal',
                    ['8', '7'],
                    $defaults['drupal_version']
                )
            );
        }

        // --database-name option
        if (!$input->getOption('database-name')) {
            $input->setOption(
                'database-name',
                $this->io->ask(
                    'Enter the name of the database to use',
                    $defaults['database_name']
                )
            );
        }

        // --database-user option
        if (!$input->getOption('database-user')) {
            $input->setOption(
                'database-user',
                $this->io->ask(
                    'Enter the database username to use',
                    $defaults['database_user']
                )
            );
        }

        // --database-password option
        if (!$input->getOption('database-password')) {
            $input->setOption(
                'database-password',
                $this->io->ask(
                    'Enter the database password to use',
                    $defaults['database_password']
                )
            );
        }

        // --build-makefile option
        if (!$input->getOption('build-makefile')) {
            $input->setOption(
                'build-makefile',
                $this->io->confirm(
                    'Build from make file',
                    $defaults['build_makefile']
                )
            );
        }

        // --install-site option
        if (!$input->getOption('install-site')) {
            $input->setOption(
                'install-site',
                $this->io->confirm(
                    'Install the site',
                    $defaults['install_site']
                )
            );
        }

        // --installed-extras option
        if (!$input->getOption('installed-extras')) {
            $input->setOption('installed-extras', $this->extrasQuestion($io));
        }

        // --extra-packages option
        if (!$input->getOption('extra-packages')) {
            $input->setOption('extra-packages', $this->packagesQuestion($io));
        }

        // --no-dashboard option
        if (!$input->getOption('no-dashboard')) {
            $useDashboard = $this->io->confirm(
                'Use the dashboard?',
                $defaults['use_dashboard']
            );
            $input->setOption('no-dashboard', !$useDashboard);
        }

        // --no-comments option
        if (!$input->getOption('no-comments')) {
            $input->setOption(
                'no-comments',
                $io->confirm(
                    'Remove comments?',
                    !$defaults['keep_comments']
                )
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->assertFileAlreadyExists(self::FILENAME);

        $this->writeFile(self::FILENAME, $this->generate());
    }

    /**
     * @return Command
     */
    private function generate()
    {
        $input = $this->input;

        $args = [
            'build_makefile' => $input->getOption('build-makefile'),
            'comments' => !$input->getOption('no-comments'),
            'destination' => $input->getOption('destination'),
            'drupal_core_path' => $input->getOption('docroot'),
            'drupal_major_version' => $input->getOption('drupal-version'),
            'drupal_mysql_database' => $input->getOption('database-name'),
            'drupal_mysql_password' => $input->getOption('database-password'),
            'drupal_mysql_user' => $input->getOption('database-user'),
            'drupalvm_webserver' => $input->getOption('webserver'),
            'install_site' => $input->getOption('install-site'),
            'keep_comments' => !$input->getOption('no-comments'),
            'local_path' => $input->getOption('path'),
            'use_dashboard' => !$input->getOption('no-dashboard'),
            'vagrant_cpus' => $input->getOption('cpus'),
            'vagrant_hostname' => $input->getOption('hostname'),
            'vagrant_ip_address' => $input->getOption('ip-address'),
            'vagrant_machine_name' => $input->getOption('machine-name'),
            'vagrant_memory' => $input->getOption('memory'),
        ];

        $args['installed_extras'] = [];
        foreach ($input->getOption('installed-extras') as $item) {
            $args['installed_extras'] = array_merge(
                $args['installed_extras'],
                explode(',', $item)
            );
        }

        $args['extra_packages'] = [];
        foreach ($input->getOption('extra-packages') as $package) {
            $args['extra_packages'] = array_merge(
                $args['extra_packages'],
                explode(',', $package)
            );
        }

        return $this->render('config.yml.twig', $args);
    }
}
