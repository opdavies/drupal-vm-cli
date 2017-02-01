<?php

namespace DrupalVm\tests\Command\Config;

use DrupalVm\tests\Command\FileGeneratorCommandTest;

class GenerateCommandTest extends FileGeneratorCommandTest
{
    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->filename = 'config.yml';
    }

    public function testNoOptions()
    {
        $output = $this->runCommand('php drupalvm config:generate');

        $this->assertContains("{$this->filename} created", $output);

        $this->assertTrue($this->fs->exists($this->filename));
        $this->assertFileContains($this->filename, '# Created by the Drupal VM CLI (https://github.com/opdavies/drupal-vm-cli).');
    }

    public function testMachineNameOption()
    {
        $this->runCommand('php drupalvm config:generate --machine-name=foo');

        $this->assertFileContains($this->filename, 'vagrant_machine_name: foo');
    }

    public function testHostnameOption()
    {
        $this->runCommand('php drupalvm config:generate --hostname=foo');

        $this->assertFileContains($this->filename, 'vagrant_hostname: foo');
    }

    public function testIpAddressOption()
    {
        $this->runCommand('php drupalvm config:generate --ip-address=1.2.3.4');

        $this->assertFileContains($this->filename, 'vagrant_ip: "1.2.3.4"');
    }

    public function testCpusOption()
    {
        $this->runCommand('php drupalvm config:generate --cpus=2');

        $this->assertFileContains($this->filename, 'vagrant_cpus: 2');
    }

    public function testMemoryOption()
    {
        $this->runCommand('php drupalvm config:generate --memory=1024');

        $this->assertFileContains($this->filename, 'vagrant_memory: 1024');
    }

    public function testWebServerOption()
    {
        // Apache.
        $this->runCommand('php drupalvm config:generate --webserver=apache');

        $this->assertFileContains($this->filename, 'drupalvm_webserver: apache');
        $this->assertFileContains($this->filename, 'apache_vhosts:');
        $this->assertFileNotContains($this->filename, 'drupalvm_webserver: nginx');

        // Nginx.
        $this->runCommand('php drupalvm config:generate --overwrite --webserver=nginx');

        $this->assertFileContains($this->filename, 'drupalvm_webserver: nginx');
        $this->assertFileContains($this->filename, 'nginx_hosts:');
        $this->assertFileNotContains($this->filename, 'drupalvm_webserver: apache');
    }

    public function testPathOption()
    {
        $this->runCommand('php drupalvm config:generate --path="./site"');

        $this->assertFileContains($this->filename, 'local_path: ./site');
    }

    public function testDatabaseOptions()
    {
        $this->runCommand('php drupalvm config:generate --database-name=foo --database-user=bar --database-password=baz');

        $output = <<<EOF
drupal_mysql_user: bar
drupal_mysql_password: baz
drupal_mysql_database: foo
EOF;

        $this->assertFileContains($this->filename, $output);
    }

    public function testInstalledExtrasOption()
    {
        $this->runCommand('php drupalvm config:generate --installed-extras=adminer,xdebug');

        $output = <<<EOF
installed_extras:
  - adminer
  - xdebug
EOF;

        $this->assertFileContains($this->filename, $output);
        $this->assertFileNotContains($this->filename, 'installed_extras: []');
    }

    public function testNoDashboardOption()
    {
        // Apache.
        $this->runCommand('php drupalvm config:generate --webserver=apache');

        $this->assertFileContains($this->filename, 'serveralias: "dashboard.{{ vagrant_hostname }}"');
        $this->assertFileContains($this->filename, 'dashboard_install_dir: /var/www/dashboard');

        $this->runCommand('php drupalvm config:generate --overwrite --webserver=apache --no-dashboard');

        $this->assertFileNotContains($this->filename, 'serveralias: "dashboard.{{ vagrant_hostname }}"');
        $this->assertFileNotContains($this->filename, 'dashboard_install_dir: /var/www/dashboard');

        // Nginx.
        $this->runCommand('php drupalvm config:generate --overwrite --webserver=nginx');

        $this->assertFileContains($this->filename, 'server_name: "{{ vagrant_ip }} dashboard.{{ vagrant_hostname }}"');
        $this->assertFileContains($this->filename, 'dashboard_install_dir: /var/www/dashboard');

        $this->runCommand('php drupalvm config:generate --overwrite --webserver=nginx --no-dashboard');

        $this->assertFileNotContains($this->filename, 'server_name: "{{ vagrant_ip }} dashboard.{{ vagrant_hostname }}"');
        $this->assertFileNotContains($this->filename, 'dashboard_install_dir: /var/www/dashboard');
    }

    public function testNoCommentsOption()
    {
        $comment = <<<EOF
# `vagrant_box` can also be set to geerlingguy/centos6, geerlingguy/centos7,
# geerlingguy/ubuntu1204, parallels/ubuntu-14.04, etc.
EOF;

        $this->runCommand('php drupalvm config:generate');
        $this->assertFileContains($this->filename, $comment);

        $this->runCommand('php drupalvm config:generate --overwrite --no-comments');
        $this->assertFileNotContains($this->filename, $comment);
    }

    public function testBuildComposerProjectOption()
    {
        $this->runCommand('php drupalvm config:generate --build-composer-project');

        $this->assertFileContains($this->filename, 'build_composer_project: true');
        $this->assertFileContains($this->filename, 'drupal_composer_project_package:');
        $this->assertFileContains($this->filename, 'drupal_core_path: "/var/www/drupalvm/drupal/web"');

        $this->assertFileContains($this->filename, 'build_composer: false');
        $this->assertFileNotContains($this->filename, 'drupal_composer_path:');

        $this->assertFileContains($this->filename, 'build_makefile: false');
        $this->assertFileNotContains($this->filename, 'drush_makefile_path:');
    }

    public function testBuildComposerOption()
    {
        $this->runCommand('php drupalvm config:generate --build-composer');

        $this->assertFileContains($this->filename, 'build_composer: true');
        $this->assertFileContains($this->filename, 'drupal_composer_path:');
        $this->assertFileContains($this->filename, 'drupal_core_path: "/var/www/drupalvm/drupal/web"');

        $this->assertFileContains($this->filename, 'build_composer_project: false');
        $this->assertFileNotContains($this->filename, 'drupal_composer_project_package:');

        $this->assertFileContains($this->filename, 'build_makefile: false');
        $this->assertFileNotContains($this->filename, 'drush_makefile_path:');
    }

    public function testBuildMakefileOptions()
    {
        $this->runCommand('php drupalvm config:generate --build-makefile');

        $this->assertFileContains($this->filename, 'build_makefile: true');
        $this->assertFileContains($this->filename, 'drush_makefile_path:');
        $this->assertFileContains($this->filename, 'drupal_core_path: "/var/www/drupalvm/drupal"');

        $this->assertFileContains($this->filename, 'build_composer_project: false');
        $this->assertFileNotContains($this->filename, 'drupal_composer_project_package:');

        $this->assertFileContains($this->filename, 'build_composer: false');
        $this->assertFileNotContains($this->filename, 'drupal_composer_path:');
    }
}
