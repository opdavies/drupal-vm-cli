<?php

require_once __DIR__ . '/../vendor/autoload.php';

use DrupalVmConfigGenerator\Console\Application;

$app = new Application('Drupal VM Config Generator', '@package_version@');

$app->run();
