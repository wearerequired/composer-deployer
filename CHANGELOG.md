# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

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

[Unreleased]: https://github.com/wearerequired/composer-deployer/compare/0.2.0...HEAD
[0.2.0]: https://github.com/wearerequired/composer-deployer/compare/0.1.2...0.2.0
[0.1.2]: https://github.com/wearerequired/composer-deployer/compare/0.1.1...0.1.2
[0.1.1]: https://github.com/wearerequired/composer-deployer/compare/0.1.0...0.1.1
[0.1.0]: https://github.com/wearerequired/composer-deployer/compare/067a144f7bc33b3add8bb06ac05d08fb5c5abc32...0.1.0
