<?php

namespace DrupalVm\Console;

use Github\Client as GithubClient;
use Github\HttpClient\CachedHttpClient;
use GuzzleHttp\Client;
use Opdavies\Twig\Extensions\TwigBooleanStringExtension;
use Pimple\Container;
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
    const VERSION = '2.10.0';

    /**
     * @var string
     */
    const SUPPORTED_DRUPAL_VM_VERSION = '3.5.2';

    public function __construct(Container $container)
    {
        parent::__construct(self::NAME, self::VERSION);

        $this
            ->registerServices($container)
            ->registerCommands($container);
    }

    private function registerServices(Container $container)
    {
        $container['twig.template_dir'] = __DIR__.'/../../templates';

        $container['twig'] = function ($container) {
            return new \Twig_Environment(
                new \Twig_Loader_Filesystem($container['twig.template_dir'])
            );
        };

        $container['twig']->addExtension(new TwigBooleanStringExtension());

        $container['filesystem'] = function () {
            return new Filesystem();
        };

        $container['guzzle'] = function () {
            return new Client();
        };

        $container['github.cache_dir'] = '/tmp/github_api_cache';

        $container['github'] = function ($container) {
            return new GithubClient(
                new CachedHttpClient([
                    'cache_dir' => $container['github.cache_dir'],
                ])
            );
        };

        return $this;
    }

    private function registerCommands(Container $container)
    {
        $commands = [
            \DrupalVm\Command\AboutCommand::class,
            \DrupalVm\Command\InitCommand::class,
            \DrupalVm\Command\NewCommand::class,
            \DrupalVm\Command\Config\GenerateCommand::class,
        ];

        foreach ($commands as $command) {
            $this->add(new $command($container));
        }

        // TODO: Make this configurable when user settings are added.
        $this->setDefaultCommand('about');

        return $this;
    }
}
