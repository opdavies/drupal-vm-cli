<?php

use DrupalVm\Console\Application;

if (file_exists(__DIR__.'/../../../autoload.php')) {
    include __DIR__.'/../../../autoload.php';
} else {
    include __DIR__.'/../vendor/autoload.php';
}

$app = new Application();

$app->run();
