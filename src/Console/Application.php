<?php

namespace DrupalVmConfigGenerator\Console;

use DrupalVmConfigGenerator\Console\Command\GenerateCommand;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Input\InputInterface;

class Application extends ConsoleApplication
{
    /**
     * {@inheritdoc}
     */
    protected function getCommandName(InputInterface $input)
    {
        return 'config:generate';
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultCommands()
    {
        $defaultCommands = parent::getDefaultCommands();

        $defaultCommands[] = new GenerateCommand();

        return $defaultCommands;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        $inputDefinition = parent::getDefinition();

        $inputDefinition->setArguments();

        return $inputDefinition;
    }
}
