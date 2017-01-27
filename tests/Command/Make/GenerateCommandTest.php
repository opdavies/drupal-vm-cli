<?php

namespace DrupalVmGenerator\tests\Command\Make;

use DrupalVmGenerator\Tests\Command\FileGeneratorCommandTest;

class GenerateCommandTest extends FileGeneratorCommandTest
{
    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->filename = 'drupal.make.yml';
    }

    public function testNoOptions()
    {
        $output = $this->runCommand('php drupalvm make:generate');

        $this->assertContains('drupal.make.yml created', $output);
        $this->assertTrue($this->fs->exists($this->filename));

        $this->assertFileContains($this->filename, '# Created by the Drupal VM CLI (https://github.com/opdavies/drupal-vm-generator).');
    }

    public function testDrupalCoreVersionOption()
    {
        $this->runCommand('php drupalvm make:generate --drupal-version=8.0.x');

        $this->assertFileNotContains($this->filename, 'core: ""');
        $this->assertFileContains($this->filename, 'core: "8.0.x"');
    }

    public function testBranchOption()
    {
        $this->runCommand('php drupalvm make:generate --branch=8.0.x');

        $this->assertFileNotContains($this->filename, 'branch: ""');
        $this->assertFileContains($this->filename, 'branch: "8.0.x"');
    }
}
