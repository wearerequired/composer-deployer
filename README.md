# composer-deployer

A plugin for Composer to create the Deployer configuration file (deploy.php).

## Installation

Via Composer

```
composer require wearerequired/composer-deployer
```

## Features

* Creates `deploy.php` in project root directory.
* Defines a `deploy` task to deploy a project.
* Installs and updates WordPress translations via WP-CLI if `wp_languages` option is set.
* Clears OPcache via WP-CLI (requires [WP-CLI Clear OPcache](https://github.com/wearerequired/wp-cli-clear-opcache)). Can be disabled via `wp_clear_opcache` option.
* Runs WordPress database routine if `wordpress` option is set.
