<?php
/**
 * Deployer configuration.
 *
 * THIS FILE IS AUTO-GENERATED. DO NOT EDIT THIS FILE.
 * Source: https://github.com/wearerequired/composer-deployer
 */

namespace Deployer;

require 'recipe/common.php';

// Default options.
set( 'allow_anonymous_stats', false );
set( 'default_stage', 'staging' );
set(
	'composer_options',
	function () {
		$production = 'production' === get( 'stage' );
		return sprintf(
			'{{composer_action}} --verbose --prefer-dist --no-progress --no-interaction %s --optimize-autoloader',
			$production ? '--no-dev' : ''
		);
	}
);
set( 'keep_releases', 3 );
set( 'wordpress', true );
set(
	'bin/wp',
	fn(): string => locateBinaryPath( 'wp' )
);

// Load options and hosts from inventory.
inventory( 'deploy.yml' );

// Tasks.
desc( 'Check WordPress is installed' );
task(
	'wp:install',
	function (): void {
		if ( ! get( 'wordpress' ) ) {
			return;
		}

		within(
			'{{release_path}}',
			function (): void {
				$is_installed = test( '{{bin/wp}} core is-installed' );
				if ( $is_installed ) {
					return;
				}
				writeln( '<comment>Installing WordPress</comment>' );
				$env                    = run( 'set -o allexport; source wordpress/.env; set +o allexport; echo "https://$_HTTP_HOST,$MULTISITE"' );
				[ $url, $is_multisite ] = explode( ',', $env );
				if ( $is_multisite ) {
					run( "{{bin/wp}} core multisite-install --url={$url} --title=WordPress --admin_user=required --admin_email=info@required.ch --skip-email --skip-config" );
				} else {
					run( "{{bin/wp}} core install --url={$url} --title=WordPress --admin_user=required --admin_email=info@required.ch --skip-email" );
				}
			}
		);
	}
);

desc( 'Install WordPress translations' );
task(
	'wp:install_translations',
	function (): void {
		if ( ! get( 'wordpress' ) || ! has( 'wp_languages' ) ) {
			return;
		}

		within(
			'{{release_path}}',
			function (): void {
				$wp_languages = implode( ' ', get( 'wp_languages' ) );
				run( "{{bin/wp}} language core install {$wp_languages} --skip-plugins=wordpress-seo" );
				run( "{{bin/wp}} language plugin install --all {$wp_languages} --format=csv --skip-plugins=wordpress-seo" );
				run( "{{bin/wp}} language theme install --all {$wp_languages} --format=csv --skip-plugins=wordpress-seo" );
			}
		);
	}
);

desc( 'Update WordPress translations' );
task(
	'wp:update_translations',
	function (): void {
		if ( ! get( 'wordpress' ) || ! has( 'wp_languages' ) ) {
			return;
		}

		within(
			'{{release_path}}',
			function (): void {
				run( '{{bin/wp}} language core update --quiet' );
				run( '{{bin/wp}} language plugin update --all --quiet' );
				run( '{{bin/wp}} language theme update --all --quiet' );
			}
		);
	}
);

desc( 'Install and update WordPress translations' );
task( 'wp:translations', [ 'wp:install_translations', 'wp:update_translations' ] );

desc( 'Clear OPcache' );
task(
	'wp:opcache_clear',
	function (): void {
		if ( ! get( 'wordpress' ) || ! get( 'wp_clear_opcache', false ) ) {
			return;
		}

		within(
			'{{release_path}}',
			function (): void {
				$is_installed = test( '{{bin/wp}} plugin is-installed wp-cli-clear-opcache' );
				if ( ! $is_installed ) {
					writeln( '<comment>Skipped because wp-cli-clear-opcache is not installed.</comment>' );
				} else {
					run( '{{bin/wp}} plugin activate wp-cli-clear-opcache --quiet' );
					run( '{{bin/wp}} opcache clear' );
				}
			}
		);
	}
);

desc( 'Runs the WordPress database update procedure' );
task(
	'wp:upgrade_db',
	function (): void {
		if ( ! get( 'wordpress' ) ) {
			return;
		}

		try {
			within(
				'{{release_path}}',
				function (): void {
					$is_multisite = test( '{{bin/wp}} core is-installed --network' );
					run( '{{bin/wp}} core update-db' . ( $is_multisite ? ' --network' : '' ) );
				}
			);
		} catch ( \Throwable $t ) {
			writeln( '<error>WordPress database could not be updated. Run manually via wp-admin/upgrade.php if necessary.</error>' );
		}
	}
);

desc( 'Run commands before finishing the deployment' );
task(
	'wp:post_rollout',
	function (): void {
		if ( ! has( 'post_rollout_commands' ) ) {
			return;
		}

		$commands = get( 'post_rollout_commands' );
		if ( ! \is_array( $commands ) ) {
			$commands = [ $commands ];
		}

		within(
			'{{release_path}}',
			function () use ( $commands ): void {
				foreach ( $commands as $command ) {
					run( $command );
				}
			}
		);
	}
);

desc( 'Deploy your project' );
task(
	'deploy',
	[
		'deploy:info',
		'deploy:prepare',
		'deploy:lock',
		'deploy:release',
		'deploy:update_code',
		'deploy:shared',
		'deploy:writable',
		'deploy:vendors',
		'wp:install',
		'wp:translations',
		'deploy:clear_paths',
		'deploy:symlink',
		'wp:opcache_clear',
		'wp:upgrade_db',
		'wp:post_rollout',
		'deploy:unlock',
		'cleanup',
		'success',
	]
);

// If deploy fails automatically unlock.
after( 'deploy:failed', 'deploy:unlock' );
