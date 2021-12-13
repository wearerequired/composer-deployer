# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
* Add `wp:post_rollout` task and `post_rollout_commands` option to run custom commands before finishing the deployment.

## [0.5.0] - 2021-11-02

### Added
* Add `wp:install` task to install WordPress if it is not installed.

## [0.4.0] - 2021-05-28

### Changed
* Require PHP 7.4.
* Require Composer 2.
* Remove the deprecated `--no-suggest` parameter for `composer install`.

### Added
- Add support for custom path to WP-CLI binary.

## [0.3.2] - 2020-10-09

### Changed
- Allow the `wp:upgrade_db` task to fail gracefully.

## [0.3.1] - 2020-10-03

### Fixed
- Move `wearerequired/coding-standards` dependency to dev dependencies.

## [0.3.0] - 2020-10-03

### Added
- Add `wordpress` option to allow skipping WordPress related task on deploy.

## [0.2.0] - 2020-09-25

### Added
- Add task to run the WordPress database update procedure on deployment.

### Changed
- Set `keep_releases` option `3` by default.

## [0.1.2] - 2020-09-23

### Changed
- Set `allow_anonymous_stats` option `false` by default.
- Update `composer_options` to use `--no-dev` flag for production deployments.

## [0.1.1] - 2020-09-23

### Changed
- The deploy.tpl.php is copied now (before it was read and written to a new file).

### Fixed
- Use correct path to deploy.php in uninstall event (path to the folder where the composer.json is located).

## [0.1.0] - 2020-09-22

### Added
- The initial version of this composer plugin.

[Unreleased]: https://github.com/wearerequired/composer-deployer/compare/0.5.0...HEAD
[0.5.0]: https://github.com/wearerequired/composer-deployer/compare/0.4.0...0.5.0
[0.4.0]: https://github.com/wearerequired/composer-deployer/compare/0.3.2...0.4.0
[0.3.2]: https://github.com/wearerequired/composer-deployer/compare/0.3.1...0.3.2
[0.3.1]: https://github.com/wearerequired/composer-deployer/compare/0.3.0...0.3.1
[0.3.0]: https://github.com/wearerequired/composer-deployer/compare/0.2.0...0.3.0
[0.2.0]: https://github.com/wearerequired/composer-deployer/compare/0.1.2...0.2.0
[0.1.2]: https://github.com/wearerequired/composer-deployer/compare/0.1.1...0.1.2
[0.1.1]: https://github.com/wearerequired/composer-deployer/compare/0.1.0...0.1.1
[0.1.0]: https://github.com/wearerequired/composer-deployer/compare/067a144f7bc33b3add8bb06ac05d08fb5c5abc32...0.1.0
