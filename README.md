# Drupal VM Generator

[![Latest Stable Version](https://poser.pugx.org/opdavies/drupal-vm-config-generator/v/stable)](https://packagist.org/packages/opdavies/drupal-vm-config-generator) [![Total Downloads](https://poser.pugx.org/opdavies/drupal-vm-config-generator/downloads)](https://packagist.org/packages/opdavies/drupal-vm-config-generator) [![Latest Unstable Version](https://poser.pugx.org/opdavies/drupal-vm-config-generator/v/unstable)](https://packagist.org/packages/opdavies/drupal-vm-config-generator) [![License](https://poser.pugx.org/opdavies/drupal-vm-config-generator/license)](https://packagist.org/packages/opdavies/drupal-vm-config-generator)

A [Symfony Console](http://symfony.com/doc/current/components/console/introduction.html) application that manages and customises configuration files for [Drupal VM](http://www.drupalvm.com) projects.

## Installation

See https://github.com/opdavies/drupal-vm-generator/wiki/Installation.

## Usage

With the `drupalvm-generate` command installed, you can now run it to generate your configuration file.

Each variable configurable with the application has an option that you can set when running the command.

Here is an example with all of the options set beforehand:

```
drupalvm-generate \
  --hostname=example.com \
  --machine-name=example \
  --ip-address=192.168.88.88 \
  --cpus=1 \
  --memory=512 \
  --webserver=nginx \
  --path=../site \
  --destination=/var/www/site \
  --docroot=/var/www/site/drupal \
  --drupal-version=8 \
  --build-makefile=no \
  --install-site=true \
  --installed-extras=xdebug,xhprof \
  --force
```

If an option is not set, you will be asked a question instead to collect the value.

## Author

[Oliver Davies](https://www.oliverdavies.uk) - PHP Developer & Linux System Administrator

## Contributing

I’m happy to receive support and feature requests, bug reports, and [pull requests](https://help.github.com/articles/creating-a-pull-request) torwards this project.

All bug reports and feature and support requests should be logged in the [issue tracker](https://github.com/opdavies/drupal-vm-generator/issues).

Please run `drupalvm-generate --version` and include the version number with any bug report or support request.

All pull requests should be from a topic branch in your forked repository, merging back into `master`.
