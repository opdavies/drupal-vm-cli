# Drupal VM Config Generator

A [Symfony Console](http://symfony.com/doc/current/components/console/introduction.html) application that manages and customises configuration files for [Drupal VM](http://www.drupalvm.com) projects.

## Installation

### Download the Phar

```
curl -LO https://github.com/opdavies/drupal-vm-config-generator/releases/download/1.0.0/drupalvm-generate.phar
chmod +x drupalvm-generate.phar
mv drupalvm-generate.phar /usr/local/bin/drupalvm-generate
```

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
