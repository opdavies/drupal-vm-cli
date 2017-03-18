<?php

use DrupalVm\tests\Command\CommandTest;

class NewCommandTest extends CommandTest
{
    /** @test */
    public function downloads_drupal_vm()
    {
        $output = $this->runCommand('php drupalvm new');

        $this->assertContains('Drupal VM downloaded to drupal-vm.', $output);
        $this->assertTrue($this->fs->exists('drupal-vm'));

        $this->fs->remove('drupal-vm');
    }

    /** @test */
    public function downloads_drupal_vm_into_a_named_directory()
    {
        $output = $this->runCommand('php drupalvm new foo');

        $this->assertContains('Drupal VM downloaded to foo.', $output);
        $this->assertTrue($this->fs->exists('foo'));

        $this->fs->remove('foo');
    }
}
