<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Console\Application;
use DrupalVmConfigGenerator\Console\Command\GenerateCommand;

$app = new Application('Drupal VM Config Generator', '@package_version@');

$app->add(new GenerateCommand());

$app->setDefaultCommand('config:generate');

$app->run();
