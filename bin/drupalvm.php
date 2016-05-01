<?php

use DrupalVmGenerator\Console\Application;

if (file_exists(__DIR__ . '/../../../autoload.php')) {
    require __DIR__ . '/../../../autoload.php';
} else {
    require __DIR__ . '/../vendor/autoload.php';
}

$app = new Application();

$app->run();
