<?php

namespace DrupalVmGenerator\Tests\Command\Make;

use DrupalVmGenerator\Tests\Command\CommandTest;

class GenerateCommandTest extends CommandTest
{
    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->filename = 'drupal.make.yml';
    }

    /**
     * {@inheritdoc}
     */
    public function tearDown()
    {
        $this->deleteFile();
    }

    public function testNoOptions()
    {
        $output = $this->runCommand('bin/drupalvm make:generate');

        $this->assertContains('drupal.make.yml created', $output);
        $this->assertTrue($this->fs->exists($this->filename));

        $this->assertFileContains($this->filename, '# Created by the Drupal VM Generator (https://github.com/opdavies/drupal-vm-generator).');
    }

    public function testDrupalCoreVersionOption()
    {
        $this->runCommand('bin/drupalvm make:generate --drupal-version=8.0.x');

        $this->assertFileNotContains($this->filename, 'core: ""');
        $this->assertFileContains($this->filename, 'core: "8.0.x"');
    }

    public function testBranchOption()
    {
        $this->runCommand('bin/drupalvm make:generate --branch=8.0.x');

        $this->assertFileNotContains($this->filename, 'branch: ""');
        $this->assertFileContains($this->filename, 'branch: "8.0.x"');
    }
}
