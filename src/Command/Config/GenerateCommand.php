<?php

namespace DrupalVm\Command\Config;

use DrupalVm\Command\Command;
use DrupalVm\Command\ExtrasTrait;
use DrupalVm\Command\GeneratorCommand;
use DrupalVm\Command\PackagesTrait;
use Pimple\Container;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateCommand extends GeneratorCommand
{
    use ExtrasTrait, PackagesTrait;

    const FILENAME = 'config.yml';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('config:generate')
            ->setDescription('Generate a new config.yml file')
            ->setAliases(['config', 'generate'])
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
                'install-site',
                null,
                InputOption::VALUE_NONE,
                'Install the site when the VM is provisioned'
            )
            ->addOption(
                'drupal-version',
                null,
                InputOption::VALUE_OPTIONAL,
                'Which version of Drupal to install.'
            )
            ->addOption(
                'drush-version',
                null,
                InputOption::VALUE_OPTIONAL,
                'Which version of Drush to install.'
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
                'build-composer',
                null,
                InputOption::VALUE_NONE,
                'Whether to install using "composer create-project".'
            )
            ->addOption(
                'build-composer-project',
                null,
                InputOption::VALUE_NONE,
                'Whether to install from Drupal VM’s drupal.composer.json file.'
            )
            ->addOption(
                'build-makefile',
                null,
                InputOption::VALUE_NONE,
                'Whether to install from a Drush Make file'
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
     * @var \DrupalVm\Command\Config\ConfigFile
     */
    private $configFile;

    public function __construct(Container $container)
    {
        parent::__construct($container);

        $this->configFile = new ConfigFile();
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
        $this->assertFileAlreadyExists(self::FILENAME);

        $io = $this->io;

        $io->title('Welcome to the Drupal VM config file generator');

        // Get the default values from the template.
        $defaults = $this->configFile->toArray();

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
                    $defaults['vagrant_ip_address']
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

        $this->configFile->setBuildComposerProject($input->getOption('build-composer-project'));
        $this->configFile->setBuildComposer($input->getOption('build-composer'));
        $this->configFile->setBuildMakeFile($input->getOption('build-makefile'));

        if (!$this->configFile->isSelectedBuildOption()) {
            // --build-composer-project option.
            $answer = $io->confirm('Build with Drupal Composer project');
            $input->setOption('build-composer-project', $answer);
            $this->configFile->setBuildComposerProject($answer);
        }

        if (!$this->configFile->isSelectedBuildOption()) {
            // --build-composer option.
            $answer = $io->confirm('Build with Drupal VM\'s composer.json');
            $input->setOption('build-composer', $answer);
            $this->configFile->setBuildComposer($answer);
        }

        if (!$this->configFile->isSelectedBuildOption()) {
            // --build-makefile option
            $answer = $io->confirm('Build from make file');
            $input->setOption('build-makefile', $answer);
            $this->configFile->setBuildMakeFile($answer);
        }

        // --webserver option
        if (!$input->getOption('webserver')) {
            $input->setOption(
                'webserver',
                $this->io->choiceNoList(
                    'Which webserver?',
                    $this->configFile->getWebserverOptions(),
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
                    $this->configFile->getDocroot()
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

        // --drupal-version option
        if ($input->getOption('install-site') && !$input->getOption('drupal-version')) {
            $input->setOption(
                'drupal-version',
                $this->io->choiceNoList(
                    'Which version of Drupal',
                    $this->configFile->getDrupalVersions(),
                    $defaults['drupal_major_version']
                )
            );
        }

        // --database-name option
        if (!$input->getOption('database-name')) {
            $input->setOption(
                'database-name',
                $this->io->ask(
                    'Enter the name of the database to use',
                    $defaults['drupal_mysql_database']
                )
            );
        }

        // --database-user option
        if (!$input->getOption('database-user')) {
            $input->setOption(
                'database-user',
                $this->io->ask(
                    'Enter the database username to use',
                    $defaults['drupal_mysql_user']
                )
            );
        }

        // --database-password option
        if (!$input->getOption('database-password')) {
            $input->setOption(
                'database-password',
                $this->io->ask(
                    'Enter the database password to use',
                    $defaults['drupal_mysql_password']
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
                    !$defaults['comments']
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

        $this->configFile->setOptions($input->getOptions());

        $this->writeFile(self::FILENAME, $this->generate());
    }

    /**
     * @return string
     */
    private function generate()
    {
        return $this->render('config.yml.twig', $this->configFile->toArray());
    }
}
