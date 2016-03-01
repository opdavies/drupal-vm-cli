<?php

// Look for and require the autoloader.
$appDir = __DIR__ . '/../';
if (file_exists($appDir . 'vendor/autoload.php')) {
    require_once $appDir . 'vendor/autoload.php';
} elseif (file_exists($appDir . '../autoload.php')) {
    require_once $appDir . '../autoload.php';
}

use DrupalVmConfigGenerator\Application;

$app = new Application('Drupal VM Config Generator', '@package_version@');

$app->run();
