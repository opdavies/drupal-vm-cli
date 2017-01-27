<?php

use DrupalVm\Tests\Command\CommandTest;

class NewCommandTest extends CommandTest
{
    public function testNoOptions()
    {
        $output = $this->runCommand('php drupalvm new');

        $this->assertContains('Drupal VM downloaded to drupal-vm.', $output);
        $this->assertTrue($this->fs->exists('drupal-vm'));

        $this->fs->remove('drupal-vm');
    }

    public function testWithDirectoryOption()
    {
        $output = $this->runCommand('php drupalvm new foo');

        $this->assertContains('Drupal VM downloaded to foo.', $output);
        $this->assertTrue($this->fs->exists('foo'));

        $this->fs->remove('foo');
    }
}
