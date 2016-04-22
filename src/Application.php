<?php

namespace DrupalVmGenerator;

use DrupalVmGenerator\Command\Config\GenerateCommand as ConfigGenerateCommand;
use DrupalVmGenerator\Command\Make\GenerateCommand as MakeGenerateCommand;
use DrupalVmGenerator\Command\NewCommand;
use GuzzleHttp\Client;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Filesystem\Filesystem;
use Twig_Environment;
use Twig_Loader_Filesystem;

class Application extends ConsoleApplication
{

    /**
     * @var string
     */
    const NAME = 'Drupal VM Generator';

    /**
     * @var string
     */
    const VERSION = '2.3.0';

    /**
     * @var string
     */
    const SUPPORTED_DRUPAL_VM_VERSION = '2.4.0';

    public function __construct()
    {
        parent::__construct(self::NAME, self::VERSION);

        $twig = new Twig_Environment(
            new Twig_Loader_Filesystem(__DIR__ . '/../templates')
        );

        $filesystem = new Filesystem();

        $client = new Client();

        $this->addCommands([
            new NewCommand($client),
            new ConfigGenerateCommand($twig, $filesystem),
            new MakeGenerateCommand($twig, $filesystem),
        ]);

        // TODO: Make this configurable when user settings are added.
        $this->setDefaultCommand('list');
    }

}
