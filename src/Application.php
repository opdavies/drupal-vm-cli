<?php

namespace DrupalVmGenerator;

use DrupalVmGenerator\Command\Config\GenerateCommand as ConfigGenerateCommand;
use DrupalVmGenerator\Command\Make\GenerateCommand as MakeGenerateCommand;
use DrupalVmGenerator\Command\NewCommand;
use Github\Client as GithubClient;
use Github\HttpClient\CachedHttpClient;
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

        $github = new GithubClient(
           new CachedHttpClient(['cache_dir' => '/tmp/github_api_cache'])
        );

        $this->addCommands([
            new NewCommand($client, $github),
            new ConfigGenerateCommand($twig, $filesystem),
            new MakeGenerateCommand($twig, $filesystem),
        ]);

        // TODO: Make this configurable when user settings are added.
        $this->setDefaultCommand('list');
    }

}
