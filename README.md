# composer-deployer

A plugin for Composer to create the Deployer configuration file (deploy.php).

## Installation

Via Composer

```
composer require wearerequired/composer-deployer
```

## Features

* Supports for Deployer v7.
* Creates `deploy.php` in project root directory.
* Defines a `deploy` task to deploy a project.
* Installs and updates WordPress translations via WP-CLI if `wp_languages` option is set.
* Clears OPcache via WP-CLI (requires [WP-CLI Clear OPcache](https://github.com/wearerequired/wp-cli-clear-opcache)). Can be disabled via `wp_clear_opcache` option.
* Runs WordPress database routine if `wordpress` option is set.
* Runs custom commands via `post_rollout_commands` option before the deployment is finished.
* Provides a [reusable workflow for GitHub](./github/workflows/deploy.yml) for deployment.

## Configuration

Next to `deploy.php` you should create a `deploy.yml` file in the project root directory. For the supported syntax see [Deployer's documentation](https://deployer.org/docs/7.x/yaml) or the following example:

```yml
.base: &base
  hostname: ssh.example.ch
  remote_user: jane
  application: example.ch
  repository: git@github.com:wearerequired/example.git
  deploy_path: ~/public_html/{{application}}/{{stage}}
  branch: main
  shared_files:
    - wordpress/.htaccess
  shared_dirs:
    - wordpress/content/uploads
  wp_languages:
    - de_DE
    - de_DE_formal
    - de_CH
  wp_clear_opcache: true
  post_rollout_commands:
    - "{{bin/wp}} litespeed-purge all || true"

hosts:
  staging:
    <<: *base
    labels:
      stage: staging

  production:
    <<: *base
    branch: production
    shared_files:
      - wordpress/.htaccess
      - wordpress/google123456789abc.html
    labels:
      stage: production
```
