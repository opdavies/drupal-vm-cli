<?php

require_once __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Console\Application;
use DrupalVmConfigGenerator\Console\Command\GenerateCommand;

$app = new Application();

$app->add(new GenerateCommand());

$app->setDefaultCommand('generate');

$app->run();
