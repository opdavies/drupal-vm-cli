<?php

namespace DrupalVmGenerator;

use DrupalVmGenerator\Command\Config\GenerateCommand;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;

class Application extends ConsoleApplication
{

    /**
     * @var string
     */
    const NAME = 'Drupal VM Generator';

    /**
     * @var string
     */
    const VERSION = '2.2.1';

    /**
     * @var string
     */
    const SUPPORTED_DRUPAL_VM_VERSION = '2.4.0';

    public function __construct()
    {
        parent::__construct(self::NAME, self::VERSION);

        $this->addCommands($this->getCommands());

        // TODO: Make this configurable when user settings are added.
        $this->setDefaultCommand('list');
    }

    /**
     * Returns all available commands.
     *
     * @return Command[]
     */
    private function getCommands()
    {
        return [
            new GenerateCommand()
        ];
    }

}
