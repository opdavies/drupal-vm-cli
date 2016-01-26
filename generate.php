<?php

require_once __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Console\Application;
use DrupalVmConfigGenerator\Console\Command\GenerateCommand;

define('PROJECT_ROOT', __DIR__);
$app = new Application();

$app->add(new GenerateCommand());

$app->setDefaultCommand('generate');

$app->run();
