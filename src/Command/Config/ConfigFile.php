<?php

namespace DrupalVm\Command\Config;

class ConfigFile
{
    /**
     * Is Composer being used?
     *
     * @var bool
     */
    private $buildComposer = false;

    /**
     * Is the Drupal Composer project being used?
     *
     * @var bool
     */
    private $buildComposerProject = true;

    /**
     * Is Drush Make being used?
     *
     * @var bool
     */
    private $buildMakeFile = false;

    /**
     * The database name.
     *
     * @var string
     */
    private $databaseName = 'drupal';

    /**
     * The database password.
     *
     * @var string
     */
    private $databasePassword = 'drupal';

    /**
     * The database username.
     *
     * @var string
     */
    private $databaseUser = 'drupal';

    /**
     * @var string
     */
    private $destination = '/var/www/drupalvm';

    /**
     * @var string
     */
    private $drupalVersion = 8;

    /**
     * The version of Drush to install.
     *
     * @var string
     */
    private $drushVersion = 'master';

    /**
     * Any extra packages to install.
     *
     * @var array
     */
    private $extraPackages = [];

    /**
     * Any installed extras.
     *
     * @var array
     */
    private $installedExtras = [];

    /**
     * Whether to install Drupal automatically.
     *
     * @var bool
     */
    private $installSite = true;

    /**
     * The local path to the site installation.
     *
     * @var string
     */
    private $localPath = '.';

    /**
     * Whether to show comments in the generated file.
     *
     * @var bool
     */
    private $showComments = true;

    /**
     * Whether to use the Drupal VM dashboard.
     *
     * @var bool
     */
    private $useDashboard = true;

    /**
     * The number of CPUs to allocate to the VM.
     *
     * @var string
     */
    private $vagrantCpus = '1';

    /**
     * The hostname for the VM.
     *
     * @var string
     */
    private $vagrantHostname = 'drupalvm.dev';

    /**
     * The IP address to assign to the VM.
     *
     * @var string
     */
    private $vagrantIpAddress = '192.168.88.88';

    /**
     * The Vagrant machine name.
     *
     * @var string
     */
    private $vagrantMachineName = 'drupalvm';

    /**
     * The amount of memory to allocate to the VM.
     *
     * @var string
     */
    private $vagrantMemory = '2048';

    /**
     * Which webserver to use.
     *
     * @var string
     */
    private $webserver = 'apache';

    /**
     * @param bool $buildComposer
     */
    public function setBuildComposer($buildComposer)
    {
        $this->buildComposer = $buildComposer;
    }

    /**
     * @param bool $buildComposerProject
     */
    public function setBuildComposerProject($buildComposerProject)
    {
        $this->buildComposerProject = $buildComposerProject;
    }

    /**
     * @param bool $buildMakeFile
     */
    public function setBuildMakeFile($buildMakeFile)
    {
        $this->buildMakeFile = $buildMakeFile;
    }

    /**
     * @param array $options
     */
    public function setOptions(array $options)
    {
        $this->buildComposer = $options['build-composer'];
        $this->buildComposerProject = $options['build-composer-project'];
        $this->buildMakeFile = $options['build-makefile'];
        $this->databaseName = $options['database-name'];
        $this->databasePassword = $options['database-password'];
        $this->databaseUser = $options['database-user'];
        $this->installSite = $options['install-site'];
        $this->localPath = $options['path'];
        $this->showComments = !$options['no-comments'];
        $this->useDashboard = !$options['no-dashboard'];
        $this->vagrantCpus = $options['cpus'];
        $this->vagrantHostname = $options['hostname'];
        $this->vagrantIpAddress = $options['ip-address'];
        $this->vagrantMachineName = $options['machine-name'];
        $this->vagrantMemory = $options['memory'];
        $this->webserver = $options['webserver'];

        if ($options['drush-version'] !== null) {
            $this->drushVersion = $options['drush-version'];
        }

        if ($options['extra-packages']) {
            foreach ($options['extra-packages'] as $package) {
                $this->extraPackages = array_merge(
                    preg_split('/s*,/s*', $package)
                );
            }
        }

        if ($options['installed-extras']) {
            foreach ($options['installed-extras'] as $item) {
                $this->installedExtras = array_merge(
                    $this->installedExtras,
                    preg_split('/\s*,\s*/', $item)
                );
            }
        }
    }

    /**
     * Calculate the docroot path including any sub-directories.
     *
     * @return string
     */
    public function getDocroot()
    {
        if ($this->buildMakeFile) {
            return "{$this->destination}/drupal";
        }

        if ($this->isUsingComposer()) {
            return "{$this->destination}/drupal/web";
        }

        return $this->destination;
    }

    /**
     * Return possible Drupal core versions.
     *
     * @return array
     */
    public function getDrupalVersions()
    {
        return [8, 7];
    }

    /**
     * Determine the version of Drush to install.
     *
     * @return string
     */
    public function getDrushVersion()
    {
        // TODO: Calculate default value based on Drupal version.

        return $this->drushVersion;
    }

    /**
     * Return possible webserver options.
     *
     * @return array
     */
    public function getWebserverOptions()
    {
        return ['apache', 'nginx'];
    }

    /**
     * Determine if a build option has been selected.
     *
     * @return bool
     */
    public function isSelectedBuildOption()
    {
        // Return true if a build type is selected.
        return in_array(true, [
            $this->buildComposerProject,
            $this->buildComposer,
            $this->buildMakeFile,
        ]);
    }

    /**
     * Return an array representation of the template.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'build_composer' => $this->buildComposer,
            'build_composer_project' => $this->buildComposerProject,
            'build_makefile' => $this->buildMakeFile,
            'comments' => $this->showComments,
            'destination' => $this->destination,
            'drupal_core_path' => $this->getDocroot(),
            'drupal_major_version' => $this->drupalVersion,
            'drupal_mysql_database' => $this->databaseName,
            'drupal_mysql_password' => $this->databasePassword,
            'drupal_mysql_user' => $this->databaseUser,
            'drupalvm_webserver' => $this->webserver,
            'drush_version' => $this->getDrushVersion(),
            'extra_packages' => $this->extraPackages,
            'install_site' => $this->installSite,
            'installed_extras' => $this->installedExtras,
            'local_path' => $this->localPath,
            'use_dashboard' => $this->useDashboard,
            'vagrant_cpus' => $this->vagrantCpus,
            'vagrant_hostname' => $this->vagrantHostname,
            'vagrant_hostname_suffix' => 'dev',
            'vagrant_ip_address' => $this->vagrantIpAddress,
            'vagrant_machine_name' => $this->vagrantMachineName,
            'vagrant_memory' => $this->vagrantMemory,
        ];
    }

    /**
     * Determine if Composer is being used as the build method.
     *
     * @return bool
     */
    private function isUsingComposer()
    {
        return !$this->buildMakeFile && ($this->buildComposer || $this->buildComposerProject);
    }
}
