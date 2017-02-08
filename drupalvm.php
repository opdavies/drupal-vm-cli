<?php

use DrupalVm\Console\Application;

include __DIR__.'/vendor/autoload.php';

$container = new Pimple\Container();

$app = new Application($container);

$app->run();
