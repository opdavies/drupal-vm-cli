<?php

namespace DrupalVmGenerator\Command\Make;

use DrupalVmGenerator\Command\GeneratorCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateCommand extends GeneratorCommand
{
    const FILENAME = 'drupal.make.yml';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('make:generate')
            ->setDescription('Generate a new drupal.make.yml file')
            ->addOption(
                'drupal-version',
                null,
                InputOption::VALUE_OPTIONAL,
                'Which version of Drupal'
            )
            ->addOption(
                'branch',
                null,
                InputOption::VALUE_OPTIONAL,
                'Which branch to use'
            )
            ->addOption(
                'other-projects',
                null,
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL,
                ''
            )
            ->addOption(
                'no-comments',
                null,
                InputOption::VALUE_NONE,
                'Removes all comments from the generated file'
            )
            ->addOption(
                'overwrite',
                null,
                InputOption::VALUE_NONE,
                'Overwrites an existing file'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $this->assertFileAlreadyExists(self::FILENAME);

        // --drupal-version option.
        if (!$input->getOption('drupal-version')) {
            $input->setOption(
                'drupal-version',
                $this->io->ask(
                    'Enter a Drupal version',
                    '8.x'
                )
            );
        }

        // --branch option.
        if (!$input->getOption('branch')) {
            $input->setOption(
                'branch',
                $this->io->ask(
                    'Enter a branch name',
                    '8.1.x'
                )
            );
        }

        // --other-projects option.
        if (!$input->getOption('other-projects')) {
            $input->setOption('other-projects', $this->otherProjectQuestion());
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

    private function generate()
    {
        $input = $this->input;

        $parameters = array(
            'drupal_version' => $input->getOption('drupal-version'),
            'branch' => $input->getOption('branch'),
            'keep_comments' => !$input->getOption('no-comments'),
            'other_projects' => $input->getOption('other-projects'),
        );

        return $this->render('drupal.make.yml.twig', $parameters);
    }

    private function otherProjectQuestion()
    {
        $io = $this->io;

        $projects = [];

        if ($this->io->confirm('Add more projects?', false)) {
            $io->text(
                "\nEnter a project name, such as <info>devel</info>, followed by a version such as <info>8.x-1.x</info>.\nThis is optional, press <info>enter</info> to <info>continue</info>."
            );

            while (true) {
                $name = $io->ask('Project name', false);

                if (empty($name)) {
                    break;
                }

                $version = $io->ask('Project version');

                $projects[] = [
                    'name' => $name,
                    'version' => $version,
                ];
            }
        }

        return $projects;
    }
}
