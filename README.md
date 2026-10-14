# composer-deployer

[![Latest Stable Version](https://poser.pugx.org/wearerequired/composer-deployer/v/stable)](https://packagist.org/packages/wearerequired/composer-deployer)
[![Latest Unstable Version](https://poser.pugx.org/wearerequired/composer-deployer/v/unstable)](https://packagist.org/packages/wearerequired/composer-deployer)


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
* Syncs cron jobs to the remote crontab if `crontab:jobs` option is set (requires Deployer 7.4+). See [Cron jobs](#cron-jobs).
* Provides a [reusable workflow for GitHub](./.github/workflows/deploy.yml) for deployment.

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

### Cron jobs

Cron jobs can be managed in `deploy.yml` instead of the hosting control panel. On every deployment the `wp:crontab` task writes the jobs of the deployed host into the remote user's crontab, using Deployer's [crontab recipe](https://deployer.org/docs/7.x/contrib/crontab). Placeholders like `{{bin/php}}`, `{{bin/wp}}` and `{{current_path}}` are resolved, so the crontab always uses the same PHP binary as the deployment.

```yml
hosts:
  production:
    <<: *base
    branch: production
    crontab:jobs:
      - '*/5 * * * * cd {{current_path}} && {{bin/wp}} cron event run --due-now --quiet'
    labels:
      stage: production
```

The jobs are written into a section delimited by `###< {{application}}-{{stage}}` and `###> {{application}}-{{stage}}`. Only this section is replaced, all other lines of the crontab are kept. Because the identifier contains the stage, multiple stages can share the same SSH user. Override `crontab:identifier` to change it.

> [!IMPORTANT]
> * Requires Deployer 7.4 or newer. Older versions fail the deployment if `crontab:jobs` is set.
> * Jobs are only written for hosts that define `crontab:jobs`. Defining them on `production` only leaves `staging` without cron jobs.
> * Jobs must not contain single quotes, use double quotes instead.
> * `%` must be escaped as `\%` because cron treats it as a newline.
> * Manual changes inside the managed section are overwritten on the next deployment.
> * Removing `crontab:jobs` from a host does not remove its section. Remove it manually via `crontab -e`.
> * `dep rollback` does not sync the crontab.

When migrating an existing project, remove the hand-made entries in the control panel after the first deployment with `crontab:jobs`. Only lines identical to a configured job (after placeholders are resolved) are moved into the section automatically.

<br>

[![a required open source product - let's get in touch](https://media.required.com/images/open-source-banner.png)](https://required.com/en/lets-get-in-touch/)
