# Drupal VM Config Generator

[![Latest Stable Version](https://poser.pugx.org/opdavies/drupal-vm-config-generator/v/stable)](https://packagist.org/packages/opdavies/drupal-vm-config-generator) [![Total Downloads](https://poser.pugx.org/opdavies/drupal-vm-config-generator/downloads)](https://packagist.org/packages/opdavies/drupal-vm-config-generator) [![Latest Unstable Version](https://poser.pugx.org/opdavies/drupal-vm-config-generator/v/unstable)](https://packagist.org/packages/opdavies/drupal-vm-config-generator) [![License](https://poser.pugx.org/opdavies/drupal-vm-config-generator/license)](https://packagist.org/packages/opdavies/drupal-vm-config-generator)

A [Symfony Console](http://symfony.com/doc/current/components/console/introduction.html) application that manages and customises configuration files for [Drupal VM](http://www.drupalvm.com) projects.

## Installation

### Download the Phar

Downloading `drupalvm-generate.phar` is the quickest way to get up and running. You can download it like this.

    curl -LO https://github.com/opdavies/drupal-vm-config-generator/releases/download/1.1.0/drupalvm-generate.phar

Make it executable.

    chmod +x drupalvm-generate.phar

Rename it and move it into a location within your `$PATH`.

    mv drupalvm-generate.phar /usr/local/bin/drupalvm-generate

### Install via Composer

You can add the Drupal VM Config Generator into any existing Composer managed project by adding the following to your `composer.json` file.

```json
{
    "require": {
        "opdavies/drupal-vm-config-generator": "@stable"
    }
}
```

By default this will install to `vendor/bin/drupal-vm-config-generator`.

### Download via Git

For development purposes, you can clone the repository from GitHub to get the latest version.

```
git clone git@github.com:opdavies/drupal-vm-config-generator
cd drupal-vm-config-generator
composer install
```

At this point, `bin/drupalvm-generate` should be usable.

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
  --domain=example.com \
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

All bug reports and feature and support requests should be logged in the [issue tracker](https://github.com/opdavies/drupal-vm-config-generator/issues).

Please run `drupalvm-generate --version` and include the version number with any bug report or support request.

All pull requests should be from a topic branch in your forked repository, merging back into `master`.
