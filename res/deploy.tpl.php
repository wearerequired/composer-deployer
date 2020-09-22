<?php
/**
 * Deployer configuration.
 */

namespace Deployer;

require 'recipe/common.php';

inventory( 'deploy.yml' );

set( 'default_stage', 'staging' );

// Tasks.
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

task( 'wp:translations', [ 'wp:install_translations', 'wp:update_translations' ] );

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
