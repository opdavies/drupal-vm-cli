<?php

namespace DrupalVm\tests\Command\Config;

use DrupalVm\tests\Command\FileGeneratorCommandTest;

class GenerateCommandTest extends FileGeneratorCommandTest {
    /**
     * {@inheritdoc}
     */
    public function setUp() {
        parent::setUp();

        $this->filename = 'config.yml';
    }

    /** @test */
    public function generates_a_configuration_file() {
        $output = $this->runCommand('php drupalvm config:generate');

        $this->assertContains("{$this->filename} created", $output);

        $this->assertTrue($this->fs->exists($this->filename));
        $this->assertFileContains($this->filename,
            '# Created by the Drupal VM CLI (https://github.com/opdavies/drupal-vm-cli).');
    }

    /** @test */
    public function can_set_the_machine_name() {
        $this->runCommand('php drupalvm config:generate --machine-name=foo');

        $this->assertFileContains($this->filename, 'vagrant_machine_name: foo');
    }

    /** @test */
    public function can_set_the_hostname() {
        $this->runCommand('php drupalvm config:generate --hostname=foo');

        $this->assertFileContains($this->filename, 'vagrant_hostname: foo');
    }

    /** @test */
    public function can_set_the_ip_address() {
        $this->runCommand('php drupalvm config:generate --ip-address=1.2.3.4');

        $this->assertFileContains($this->filename, 'vagrant_ip: "1.2.3.4"');
    }

    /** @test */
    public function can_set_the_number_of_cpus() {
        $this->runCommand('php drupalvm config:generate --cpus=2');

        $this->assertFileContains($this->filename, 'vagrant_cpus: 2');
    }

    /** @test */
    public function can_set_the_amount_of_memory() {
        $this->runCommand('php drupalvm config:generate --memory=1024');

        $this->assertFileContains($this->filename, 'vagrant_memory: 1024');
    }

    /** @test */
    public function can_use_apache() {
        $this->runCommand('php drupalvm config:generate --webserver=apache');

        $this->assertFileContains($this->filename,
            'drupalvm_webserver: apache');
        $this->assertFileContains($this->filename, 'apache_vhosts:');
        $this->assertFileNotContains($this->filename,
            'drupalvm_webserver: nginx');
    }

    /** @test */
    public function can_use_nginx() {
        $this->runCommand('php drupalvm config:generate --webserver=nginx');

        $this->assertFileContains($this->filename, 'drupalvm_webserver: nginx');
        $this->assertFileContains($this->filename, 'nginx_hosts:');
        $this->assertFileNotContains($this->filename,
            'drupalvm_webserver: apache');
    }

    /** @test */
    public function can_set_the_local_path() {
        $this->runCommand('php drupalvm config:generate --path="./site"');

        $this->assertFileContains($this->filename, 'local_path: ./site');
    }

    /** @test */
    public function can_set_the_database_details() {
        $this->runCommand('php drupalvm config:generate --database-name=foo --database-user=bar --database-password=baz');

        $output = <<<'EOF'
drupal_mysql_user: bar
drupal_mysql_password: baz
drupal_mysql_database: foo
EOF;

        $this->assertFileContains($this->filename, $output);
    }

    /** @test */
    public function can_add_installed_extras() {
        $this->runCommand('php drupalvm config:generate --installed-extras=adminer,xdebug');

        $output = <<<'EOF'
installed_extras:
  - adminer
  - xdebug
EOF;

        $this->assertFileContains($this->filename, $output);
        $this->assertFileNotContains($this->filename, 'installed_extras: []');
    }

    /** @test */
    public function can_remove_the_dashboard_with_apache() {
        $this->runCommand('php drupalvm config:generate --webserver=apache --no-dashboard');

        $this->assertFileNotContains($this->filename,
            'serveralias: "dashboard.{{ vagrant_hostname }}"');
        $this->assertFileNotContains($this->filename,
            'dashboard_install_dir: /var/www/dashboard');
    }

    /** @test */
    public function can_remove_the_dashboard_with_nginx() {
        $this->runCommand('php drupalvm config:generate --webserver=nginx --no-dashboard');

        $this->assertFileNotContains($this->filename,
            'server_name: "{{ vagrant_ip }} dashboard.{{ vagrant_hostname }}"');
        $this->assertFileNotContains($this->filename,
            'dashboard_install_dir: /var/www/dashboard');
    }

    /** @test */
    public function can_remove_comments() {
        $comment = <<<'EOF'
# `vagrant_box` can also be set to geerlingguy/centos6, geerlingguy/centos7,
# geerlingguy/ubuntu1204, parallels/ubuntu-14.04, etc.
EOF;

        $this->runCommand('php drupalvm config:generate --no-comments');

        $this->assertFileNotContains($this->filename, $comment);
    }

    /** @test */
    public function can_build_with_drush_make() {
        $this->runCommand('php drupalvm config:generate --build-makefile');

        $this->assertFileContains($this->filename, 'build_makefile: true');
        $this->assertFileContains($this->filename, 'drush_makefile_path:');
        $this->assertFileContains($this->filename,
            'drupal_core_path: "/var/www/drupalvm/drupal"');

        $this->assertFileContains($this->filename,
            'build_composer_project: false');
        $this->assertFileNotContains($this->filename,
            'drupal_composer_project_package:');

        $this->assertFileContains($this->filename, 'build_composer: false');
        $this->assertFileNotContains($this->filename, 'drupal_composer_path:');
    }

    /** @test */
    public function can_build_using_composer() {
        $this->runCommand('php drupalvm config:generate --build-composer');

        $this->assertFileContains($this->filename, 'build_composer: true');
        $this->assertFileContains($this->filename, 'drupal_composer_path:');
        $this->assertFileContains($this->filename,
            'drupal_core_path: "/var/www/drupalvm/drupal/web"');

        $this->assertFileContains($this->filename,
            'build_composer_project: false');
        $this->assertFileNotContains($this->filename,
            'drupal_composer_project_package:');

        $this->assertFileContains($this->filename, 'build_makefile: false');
        $this->assertFileNotContains($this->filename, 'drush_makefile_path:');
    }

    /** @test */
    public function can_build_using_drupal_composer_project() {
        $this->runCommand('php drupalvm config:generate --build-composer-project');

        $this->assertFileContains($this->filename,
            'build_composer_project: true');
        $this->assertFileContains($this->filename,
            'drupal_composer_project_package:');
        $this->assertFileContains($this->filename,
            'drupal_core_path: "/var/www/drupalvm/drupal/web"');

        $this->assertFileContains($this->filename, 'build_composer: false');
        $this->assertFileNotContains($this->filename, 'drupal_composer_path:');

        $this->assertFileContains($this->filename, 'build_makefile: false');
        $this->assertFileNotContains($this->filename, 'drush_makefile_path:');
    }

    /** @test */
    public function can_set_the_drupal_version() {
        $this->runCommand('php drupalvm config:generate');

        $this->assertFileNotContains($this->filename, 'drupal_major_version:');
    }

    /** @test */
    public function can_set_the_drush_version() {
        $this->runCommand('php drupalvm config:generate --drush-version=8.x');

        $this->assertFileContains($this->filename, 'drush_version: "8.x"');
    }

    /** @test */
    public function drush_has_a_default_version() {
        $this->runCommand('php drupalvm config:generate');

        $this->assertFileContains($this->filename, 'drush_version: "master"');
    }

    /** @test */
    public function can_set_whether_to_install_the_site() {
        $this->runCommand('php drupalvm config:generate --install-site');

        $this->assertFileContains($this->filename, 'drupal_major_version:');
    }
}
