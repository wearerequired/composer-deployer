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
			'{{composer_action}} --verbose --prefer-dist --no-progress --no-interaction %s --optimize-autoloader --no-suggest',
			$production ? '--no-dev' : ''
		);
	}
);
set( 'keep_releases', 3 );

// Load options and hosts from inventory.
inventory( 'deploy.yml' );

// Tasks.
desc( 'Install WordPress translations' );
task(
	'wp:install_translations',
	function () {
		within(
			'{{release_path}}',
			function () {
				$wp_languages = implode( ' ', get( 'wp_languages' ) );
				run( "wp language core install {$wp_languages} --skip-plugins=wordpress-seo" );
				run( "wp language plugin install --all {$wp_languages} --format=csv --skip-plugins=wordpress-seo" );
				run( "wp language theme install --all {$wp_languages} --format=csv --skip-plugins=wordpress-seo" );
			}
		);
	}
);

desc( 'Update WordPress translations' );
task(
	'wp:update_translations',
	function () {
		within(
			'{{release_path}}',
			function () {
				run( 'wp language core update --quiet' );
				run( 'wp language plugin update --all --quiet' );
				run( 'wp language theme update --all --quiet' );
			}
		);
	}
);

desc( 'Install and update WordPress translations' );
task( 'wp:translations', [ 'wp:install_translations', 'wp:update_translations' ] );

desc( 'Clear OPcache' );
task(
	'wp:opcache_clear',
	function () {
		within(
			'{{release_path}}',
			function () {
				if ( get( 'wp_clear_opcache' ) ) {
					run( 'wp plugin activate wp-cli-clear-opcache --quiet' );
					run( 'wp opcache clear' );
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
		'wp:translations',
		'deploy:clear_paths',
		'deploy:symlink',
		'wp:opcache_clear',
		'deploy:unlock',
		'cleanup',
		'success',
	]
);

// If deploy fails automatically unlock.
after( 'deploy:failed', 'deploy:unlock' );
