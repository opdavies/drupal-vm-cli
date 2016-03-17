<?php

namespace DrupalVmGenerator\Command\Config;

use DrupalVmGenerator\Command\Command;
use DrupalVmGenerator\Command\ExtrasTrait;
use DrupalVmGenerator\Command\FileTrait;
use DrupalVmGenerator\Style\DrupalVmStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class GenerateCommand extends Command
{
    use ExtrasTrait;
    use FileTrait;

    const FILENAME = 'config.yml';

    /**
     * @var string
     */
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
                'Which version of Drupal to install',
                8
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

        // --docroot option
        if (!$input->getOption('docroot')) {
            $input->setOption('docroot', $this->io->ask(
                'Enter the path to the docroot of the Drupal site',
                $input->getOption('destination') . DIRECTORY_SEPARATOR . 'drupal'
            ));
        }

        // --drupal-version option
        if (!$input->getOption('drupal-version')) {
            $input->setOption('drupal-version', $this->io->choiceNoList(
                'Which version of Drupal',
                ['8', '7']
            ));
        }

        // --database-name option
        if (!$input->getOption('database-name')) {
            $input->setOption('database-name', $this->io->ask(
                'Enter the name of the database to use',
                'drupal'
            ));
        }

        // --database-user option
        if (!$input->getOption('database-user')) {
            $input->setOption('database-user', $this->io->ask(
                'Enter the database username to use',
                'drupal'
            ));
        }

        // --database-password option
        if (!$input->getOption('database-password')) {
            $input->setOption('database-password', $this->io->ask(
                'Enter the database password to use',
                'drupal'
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

        // --no-dashboard option
        if (!$input->getOption('no-dashboard')) {
            $useDashboard = $this->io->confirm(
                'Use the dashboard?',
                true
            );
            $input->setOption('no-dashboard', !$useDashboard);
        }

        // --remove-comments option
        if (!$input->getOption('no-comments')) {
            $input->setOption('no-comments', $io->confirm(
                'Remove comments?',
                false
            ));
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
            'vagrant_hostname' => $input->getOption('hostname'),
            'vagrant_machine_name' => $input->getOption('machine-name'),
            'vagrant_ip_address' => $input->getOption('ip-address'),
            'vagrant_cpus' => $input->getOption('cpus'),
            'vagrant_memory' => $input->getOption('memory'),
            'drupalvm_webserver' => $input->getOption('webserver'),
            'drupal_core_path' => $input->getOption('docroot'),
            'local_path' => $input->getOption('path'),
            'destination' => $input->getOption('destination'),
            'drupal_major_version' => $input->getOption('drupal-version'),
            'drupal_mysql_database' => $input->getOption('database-name'),
            'drupal_mysql_user' => $input->getOption('database-user'),
            'drupal_mysql_password' => $input->getOption('database-password'),
            'build_makefile' => $input->getOption('build-makefile'),
            'install_site' => $input->getOption('install-site'),
            'use_dashboard' => !$input->getOption('no-dashboard'),
            'keep_comments' => !$input->getOption('no-comments'),
        ];

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
