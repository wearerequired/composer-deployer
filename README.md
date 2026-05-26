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
* Provides a [reusable workflow for GitHub](./.github/workflows/deploy.yml) for deployment.

### Reusable deploy workflow

The reusable workflow at [`.github/workflows/deploy.yml`](./.github/workflows/deploy.yml)
maps the pushed branch to an environment tier (`staging`/`testing`/`production`),
selects matching Deployer hosts, and reports the deployment back to GitHub and Slack.

How the Deployer host selector is chosen:

- For branches listed in `production_branches` / `testing_branches`, the branch
  name itself is used as the selector: `dep deploy <branch>`. Deployer 7 matches
  this against host aliases and label values, so the inventory has to define a
  matching host (alias `production-sg`, label `branch=production-sg`, etc.).
- Everything else falls back to the staging tier (`dep deploy stage=staging`),
  which can match multiple hosts via the `stage` label.

The deployed branch is taken from each host's `branch:` configuration in
`deploy.yml` — `--branch` is not forced from the workflow side.

#### Minimal caller

```yml
jobs:
  deploy:
    uses: wearerequired/composer-deployer/.github/workflows/deploy.yml@v1
    secrets: inherit
```

#### Two production branches targeting separate hosts

`deploy.yml` (host config — branch and stage label per host):

```yml
hosts:
  production:
    branch: production
    labels:
      stage: production
  production-sg:
    branch: production-sg
    labels:
      stage: production
```

Caller workflow:

```yml
jobs:
  deploy:
    uses: wearerequired/composer-deployer/.github/workflows/deploy.yml@v1
    with:
      production_branches: |
        production
        production-sg
      environment_urls: |
        production=${{ secrets.ENVIRONMENT_URL_PRODUCTION }}
        production-sg=${{ secrets.ENVIRONMENT_URL_PRODUCTION_SG }}
    secrets: inherit
```

Local deploys via `dep deploy stage=production` still target both hosts at
once, because the `stage` label is shared.

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

<br>

[![a required open source product - let's get in touch](https://media.required.com/images/open-source-banner.png)](https://required.com/en/lets-get-in-touch/)
