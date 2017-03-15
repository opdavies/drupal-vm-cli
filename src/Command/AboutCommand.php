<?php

namespace DrupalVm\Command;

use DrupalVm\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AboutCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected $command = 'about';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Displays information about the Drupal VM CLI';

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $io = $this->io;

        /** @var Application $application */
        $application = $this->getApplication();

        $io->title(sprintf(
            '%s (%s) | Supports Drupal VM %s',
            $application->getName(),
            $application->getVersion(),
            $application::SUPPORTED_DRUPAL_VM_VERSION
        ));

        $content = <<<'EOF'
This is the unofficial CLI app for <info>Drupal VM</info> to start new projects
and generate configuration files.

To download the latest version of Drupal VM, run the following command:

  <comment>drupal-vm new</comment>

This will automatically download Drupal VM and place it in a directory named
<info>drupal-vm</info>.

If you want to change the directory name, e.g. <info>myproject</info>, run the
following the command changing the directory name as needed:

  <comment>drupalvm new myproject</comment>

To initialise Drupal VM CLI:

  <comment>drupalvm init</comment>

To generate a new <info>config.yml</info> file, run the following command:

  <comment>drupalvm config:generate</comment>

This command will fail if there is an existing config.yml file. To <info>overwrite
an existing config.yml file</info>, run the following command:

  <comment>drupalvm config:generate --overwrite</comment>

Updating the Drupal VM CLI
--------------------------

The Drupal VM CLI is installed and managed by <info>Composer</info>. To update
your Drupal VM CLI version, execute the following command:

  <comment>composer global update opdavies/drupal-vm-cli</comment>

EOF;
        $output->writeln($content);
    }
}
