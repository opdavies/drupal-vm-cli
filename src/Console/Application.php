<?php

namespace DrupalVmGenerator\Console;

use DrupalVmGenerator\Command\AboutCommand;
use DrupalVmGenerator\Command\Config\GenerateCommand as ConfigGenerateCommand;
use DrupalVmGenerator\Command\InitCommand;
use DrupalVmGenerator\Command\Make\GenerateCommand as MakeGenerateCommand;
use DrupalVmGenerator\Command\NewCommand;
use DrupalVmGenerator\Command\Self\UpdateCommand as SelfUpdateCommand;
use Github\Client as GithubClient;
use Github\HttpClient\CachedHttpClient;
use GuzzleHttp\Client;
use Opdavies\Twig\Extensions\TwigBooleanStringExtension;
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
    const VERSION = '2.8.1';

    /**
     * @var string
     */
    const SUPPORTED_DRUPAL_VM_VERSION = '3.0.0';

    public function __construct()
    {
        parent::__construct(self::NAME, self::VERSION);

        $twig = new Twig_Environment(
            new Twig_Loader_Filesystem(__DIR__.'/../../templates')
        );

        $twig->addExtension(new TwigBooleanStringExtension());

        $filesystem = new Filesystem();

        $client = new Client();

        $github = new GithubClient(
            new CachedHttpClient(['cache_dir' => '/tmp/github_api_cache'])
        );

        $commands = [
            new AboutCommand(),
            new InitCommand($filesystem),
            new NewCommand($client, $github),
            new ConfigGenerateCommand($twig, $filesystem),
            new MakeGenerateCommand($twig, $filesystem),
        ];

        $this->addCommands($commands);

        // TODO: Make this configurable when user settings are added.
        $this->setDefaultCommand('about');
    }
}
