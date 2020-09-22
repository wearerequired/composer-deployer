# composer-deployer

A plugin for Composer to create the Deployer configuration file (deploy.php).

## Installation

Via Composer

```
composer require wearerequired/composer-deployer
```

## Features

* Creates `deploy.php` in root level
  * This config deploys a project
  * Executes WordPress translation installs/updates (via wp-cli)
  * Executes a opcache clear command (via wp-cli) when activated
