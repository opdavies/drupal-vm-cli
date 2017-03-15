<?php

namespace DrupalVm\Command\Config;

use DrupalVm\Command\Command;
use DrupalVm\Command\ExtrasTrait;
use DrupalVm\Command\GeneratorCommand;
use DrupalVm\Command\PackagesTrait;
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
    protected $command = 'config:generate';

    /**
     * {@inheritdoc}
     */
    protected $aliases = ['config', 'generate'];

    /**
     * {@inheritdoc}
     */
    protected $description = 'Generates a new config.yml file';

    private static $defaults = [
        'build-composer' => false,
        'build-composer-project' => false,
        'build-makefile' => false,
        'cpus' => 1,
        'database-name' => 'drupal',
        'database-password' => 'drupal',
        'database-user' => 'drupal',
        'destination' => '/var/www/drupavm',
        'docroot' => '/var/www/drupalvm/drupal',
        'drupal-version' => '8.x',
        'hostname' => 'drupalvm.dev',
        'install-site' => false,
        'ip-address' => '192.168.88.88',
        'machine-name' => 'drupalvm',
        'memory' => 2048,
        'no-comments' => false,
        'no-dashboard' => false,
        'path' => '.',
        'webserver' => 'apache',
    ];

    /**
     * {@inheritdoc}
     */
    protected function options()
    {
        return [
            ['machine-name', null, InputOption::VALUE_REQUIRED, 'The Vagrant machine name'],
            ['hostname', null, InputOption::VALUE_OPTIONAL, 'The Vagrant hostname'],
            ['ip-address', null, InputOption::VALUE_REQUIRED, 'The IP address for the VM'],
            ['cpus', null, InputOption::VALUE_REQUIRED, 'The number of CPUs'],
            ['memory', null, InputOption::VALUE_REQUIRED, 'The amount of memory'],
            ['webserver', null, InputOption::VALUE_OPTIONAL, 'Which webserver to use'],
            ['path', null, InputOption::VALUE_OPTIONAL, 'The local path for the synchronised folder'],
            ['destination', null, InputOption::VALUE_OPTIONAL, 'The destination path'],
            ['docroot', null, InputOption::VALUE_OPTIONAL, 'The path to the Drupal installation'],
            ['install-site', null, InputOption::VALUE_NONE, 'Install the site when the VM is provisioned'],
            ['drupal-version', null, InputOption::VALUE_OPTIONAL, 'Which version of Drupal to install is being used?'],
            ['database-name', null, InputOption::VALUE_OPTIONAL, 'The name of the database to use', null],
            ['database-user', null, InputOption::VALUE_OPTIONAL, 'The database user to use', null],
            ['database-password', null, InputOption::VALUE_OPTIONAL, 'The database password to use', null],
            ['build-composer', null, InputOption::VALUE_NONE, 'Whether to install using "composer create-project".'],
            ['build-composer-project', null, InputOption::VALUE_NONE, 'Whether to install from Drupal VM’s drupal.composer.json file.'],
            ['build-makefile', null, InputOption::VALUE_NONE, 'Whether to install from a Drush Make file'],
            ['installed-extras', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'Install from a predefined list of extra packages'],
            ['extra-packages', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'Add any additional apt or yum packages'],
            ['no-dashboard', null, InputOption::VALUE_NONE, 'Install without the Drupal VM Dashboard'],
            ['no-comments', null, InputOption::VALUE_NONE, 'Remove comments from config.yml'],
            ['overwrite', null, InputOption::VALUE_NONE, 'Overwrite an existing file if one exists'],
        ];
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
            $this->error('No defaults file found. Please run "drupalvm init", then re-run this command.');

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

        $defaults = $this->getDefaultOptions('config');

        // --machine-name option
        if (!$this->option('machine-name')) {
            $input->setOption(
                'machine-name',
                $this->ask(
                    'Enter a Vagrant machine name',
                    $defaults['vagrant_machine_name']
                )
            );
        }

        // --hostname option
        if (!$this->option('hostname')) {
            $input->setOption(
                'hostname',
                $this->ask(
                    'Enter a hostname for Vagrant',
                    $this->option('machine-name').'.'.$defaults['vagrant_hostname_suffix']
                )
            );
        }

        // --ip-address option
        if (!$this->option('ip-address')) {
            $input->setOption(
                'ip-address',
                $this->ask(
                    'Enter an IP address for the Vagrant VM',
                    $defaults['vagrant_ip']
                )
            );
        }

        // --cpus option
        if (!$this->option('cpus')) {
            $input->setOption(
                'cpus',
                $this->ask(
                    'How many CPUs?',
                    $defaults['vagrant_cpus']
                )
            );
        }

        // --memory option
        if (!$this->option('memory')) {
            $input->setOption(
                'memory',
                $this->ask(
                    'How much memory?',
                    $defaults['vagrant_memory']
                )
            );
        }

        $buildOptions = ['build-composer-project', 'build-composer', 'build-makefile'];
        $selectedBuildOption = null;

        foreach ($buildOptions as $option) {
            if ($this->option($option) === true) {
                $selectedBuildOption = $option;
            }
        }

        if (!$selectedBuildOption) {
            // --build-composer-project option.
            $input->setOption(
                'build-composer-project',
                $io->confirm('Build with Drupal Composer project')
            );

            if ($this->option('build-composer-project')) {
                $selectedBuildOption = 'build-composer-project';
            }
        }

        if (!$selectedBuildOption) {
            // --build-composer option.
            $input->setOption(
                'build-composer',
                $io->confirm('Build with Drupal VM\'s composer.json')
            );

            if ($this->option('build-composer')) {
                $selectedBuildOption = 'build-composer';
            }
        }

        if (!$selectedBuildOption) {
            // --build-makefile option
            $input->setOption(
                'build-makefile',
                $io->confirm('Build from make file')
            );

            if ($this->option('build-makefile')) {
                $selectedBuildOption = 'build-makefile';
            }
        }

        // --webserver option
        if (!$this->option('webserver')) {
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
        if (!$this->option('path')) {
            $input->setOption(
                'path',
                $this->ask(
                    'Enter the local path for your Drupal site',
                    getcwd()
                )
            );
        }

        // --destination option
        if (!$this->option('destination')) {
            $input->setOption(
                'destination',
                $this->ask(
                    'Enter the destination path for your Drupal site',
                    $defaults['destination']
                )
            );
        }

        // --docroot option
        if (!$this->option('docroot')) {
            $suffix = DIRECTORY_SEPARATOR.'drupal';

            if ($selectedBuildOption == 'build-composer-project' || $selectedBuildOption == 'build-composer') {
                $suffix .= DIRECTORY_SEPARATOR.'web';
            }

            $input->setOption(
                'docroot',
                $this->ask(
                    'Enter the path to the docroot of the Drupal site',
                    $this->option('destination').$suffix
                )
            );
        }

        // --install-site option
        if (!$this->option('install-site')) {
            $input->setOption(
                'install-site',
                $this->io->confirm(
                    'Install the site',
                    $defaults['install_site']
                )
            );
        }

        // --drupal-version option
        if ($this->option('install-site') && !$this->option('drupal-version')) {
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
        if (!$this->option('database-name')) {
            $input->setOption(
                'database-name',
                $this->ask(
                    'Enter the name of the database to use',
                    $defaults['database_name']
                )
            );
        }

        // --database-user option
        if (!$this->option('database-user')) {
            $input->setOption(
                'database-user',
                $this->ask(
                    'Enter the database username to use',
                    $defaults['database_user']
                )
            );
        }

        // --database-password option
        if (!$this->option('database-password')) {
            $input->setOption(
                'database-password',
                $this->ask(
                    'Enter the database password to use',
                    $defaults['database_password']
                )
            );
        }

        // --installed-extras option
        if (!$this->option('installed-extras')) {
            $input->setOption('installed-extras', $this->extrasQuestion($io));
        }

        // --extra-packages option
        if (!$this->option('extra-packages')) {
            $input->setOption('extra-packages', $this->packagesQuestion($io));
        }

        // --no-dashboard option
        if (!$this->option('no-dashboard')) {
            $useDashboard = $this->io->confirm(
                'Use the dashboard?',
                $defaults['use_dashboard']
            );
            $input->setOption('no-dashboard', !$useDashboard);
        }

        // --no-comments option
        if (!$this->option('no-comments')) {
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
        $args = [
            'build_makefile' => $this->get('build-makefile'),
            'build_composer' => $this->get('build-composer'),
            'build_composer_project' => $this->get('build-composer-project'),
            'comments' => !$this->get('no-comments'),
            'destination' => $this->get('destination'),
            'drupal_core_path' => $this->getDocroot(),
            'drupal_major_version' => $this->get('drupal-version'),
            'drupal_mysql_database' => $this->get('database-name'),
            'drupal_mysql_password' => $this->get('database-password'),
            'drupal_mysql_user' => $this->get('database-user'),
            'drupalvm_webserver' => $this->get('webserver'),
            'install_site' => $this->get('install-site'),
            'local_path' => $this->get('path'),
            'use_dashboard' => !$this->get('no-dashboard'),
            'vagrant_cpus' => $this->get('cpus'),
            'vagrant_hostname' => $this->get('hostname'),
            'vagrant_ip_address' => $this->get('ip-address'),
            'vagrant_machine_name' => $this->get('machine-name'),
            'vagrant_memory' => $this->get('memory'),
        ];

        $args['installed_extras'] = [];
        foreach ($this->option('installed-extras') as $item) {
            $args['installed_extras'] = array_merge(
                $args['installed_extras'],
                explode(',', $item)
            );
        }

        $args['extra_packages'] = [];
        foreach ($this->option('extra-packages') as $package) {
            $args['extra_packages'] = array_merge(
                $args['extra_packages'],
                explode(',', $package)
            );
        }

        return $this->render('config.yml.twig', $args);
    }

    private function get($name, $getDefault = true)
    {
        if ($result = $this->option($name)) {
            return $result;
        }

        return $getDefault ? self::$defaults[$name] : null;
    }

    private function getDocroot()
    {
        if ($result = $this->get('docroot', false)) {
            return $result;
        }

        $default = self::$defaults['docroot'];

        if ($this->get('build-makefile')) {
            return $default;
        }

        $build_composer = $this->get('build-composer');
        $build_composer_project = $this->get('build-composer-project');

        if ($build_composer || $build_composer_project) {
            return $default.DIRECTORY_SEPARATOR.'web';
        }

        return $default;
    }
}
