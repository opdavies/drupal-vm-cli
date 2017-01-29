<?php

namespace DrupalVm\Console;

use Github\Client as GithubClient;
use Github\HttpClient\CachedHttpClient;
use GuzzleHttp\Client;
use Opdavies\Twig\Extensions\TwigBooleanStringExtension;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Filesystem\Filesystem;

class Application extends ConsoleApplication
{
    /**
     * @var string
     */
    const NAME = 'Drupal VM CLI';

    /**
     * @var string
     */
    const VERSION = '2.9.1';

    /**
     * @var string
     */
    const SUPPORTED_DRUPAL_VM_VERSION = '3.0.0';

    public function __construct()
    {
        parent::__construct(self::NAME, self::VERSION);

        $twig = new \Twig_Environment(
            new \Twig_Loader_Filesystem(__DIR__.'/../../templates')
        );

        $twig->addExtension(new TwigBooleanStringExtension());

        $filesystem = new Filesystem();

        $client = new Client();

        $github = new GithubClient(
            new CachedHttpClient(['cache_dir' => '/tmp/github_api_cache'])
        );

        $commands = [
            new \DrupalVm\Command\AboutCommand(),
            new \DrupalVm\Command\InitCommand($filesystem),
            new \DrupalVm\Command\NewCommand($client, $github),
            new \DrupalVm\Command\Config\GenerateCommand($twig, $filesystem)
        ];

        $this->addCommands($commands);

        // TODO: Make this configurable when user settings are added.
        $this->setDefaultCommand('about');
    }
}
