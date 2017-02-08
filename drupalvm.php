<?php

use DrupalVm\Console\Application;
use Pimple\Container;

include __DIR__.'/vendor/autoload.php';

$app = new Application(new Container());

$app->run();
