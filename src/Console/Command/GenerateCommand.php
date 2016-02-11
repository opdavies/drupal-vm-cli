<?php

namespace DrupalVmConfigGenerator\Console\Command;

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

class GenerateCommand extends BaseCommand
{
    const FILENAME = 'config.yml';

    private $fileContents;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('config:generate')
            ->setDescription('Generate a new Drupal VM configuration file.')
            ->setAliases(['generate'])
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this
            ->generate()
            ->writeFile()
        ;
    }

    /**
     * Generates the contents of the file.
     *
     * @return GenerateCommand
     */
    private function generate()
    {
        $helper = $this->getHelper('question');
        $args = [];

        // Vagrant hostname.
        $args['vagrant_hostname'] = $helper->ask(
            $this->input,
            $this->output,
            new Question('Enter a hostname for Vagrant (defaults to drupalvm.dev)', 'drupalvm.dev')
        );

        // Vagrant machine name.
        $args['vagrant_machine_name'] = $helper->ask(
            $this->input,
            $this->output,
            new Question('Enter a Vagrant machine name (defaults to drupalvm)', 'drupalvm')
        );

        // Vagrant IP address.
        $args['vagrant_ip_address'] = $helper->ask(
            $this->input,
            $this->output,
            new Question('Enter an IP address for the Vagrant VM (defaults to 192.168.88.88)', '192.168.88.88')
        );

        // CPUs.
        $args['vagrant_cpus'] = $helper->ask(
            $this->input,
            $this->output,
            new Question('How many CPUs (defaults to 2)', 2)
        );

        // Memory.
        $args['vagrant_memory'] = $helper->ask(
            $this->input,
            $this->output,
            new Question('How much memory (defaults to 1024)', 1024)
        );

        // Which web server to use?
        $args['drupalvm_webserver'] = $helper->ask(
            $this->input,
            $this->output,
            new ChoiceQuestion('Which web server?', ['apache', 'nginx'])
        );

        // Domain name.
        $args['drupal_domain'] = $helper->ask(
            $this->input,
            $this->output,
            new Question('Enter a domain for your site (defaults to drupalvm.dev)', 'drupalvm.dev')
        );

        // Local site path.
        $args['local_path'] = $helper->ask(
            $this->input,
            $this->output,
            new Question(
                'Enter the local path for your Drupal site (defaults to ~/Sites/drupalvm)',
                '~/Sites/drupalvm'
            )
        );

        // Destination site path.
        $args['destination'] = $helper->ask(
            $this->input,
            $this->output,
            new Question(
                'Enter the destination path for your Drupal site (defaults to /var/www/drupalvm)',
                '/var/www/drupalvm'
            )
        );

        // Which version of Drupal?
        $args['drupal_major_version'] = $helper->ask(
            $this->input,
            $this->output,
            new ChoiceQuestion('Which version of Drupal (defaults to 8)?', ['8', '7'], '8')
        );

        $args['install_site'] = $helper->ask(
            $this->input,
            $this->output,
            new ChoiceQuestion(
                'Install the site (defaults to "yes"',
                [0 => 'No', 1 => 'Yes'],
                0
            )
        );

        // Installed extras.
        $question = new ChoiceQuestion(
            'Which installed extras? Enter a comma-separated list, or 0 for none',
            [
                'none',
                'adminer',
                'drupalconsole',
                'mailhog',
                'memcached',
                'pimpmylog',
                'varnish',
                'xdebug',
                'xhprof'
            ],
            'none'
        );
        $question->setMultiselect(true);

        $args['installed_extras'] = $helper->ask(
            $this->input,
            $this->output,
            $question
        );

        $this->fileContents = $this->twig->render('config.yml.twig', ['app' => $args]);

        return $this;
    }

    /**
     * Writes the file to disk.
     *
     * @return GenerateCommand
     */
    private function writeFile()
    {
        $this->fs->dumpFile($this->projectDir . DIRECTORY_SEPARATOR . self::FILENAME, $this->fileContents);

        return $this;
    }
}
