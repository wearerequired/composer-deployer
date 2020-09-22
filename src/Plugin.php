<?php
/**
 * Plugin class
 */

namespace Required\Deployer;

use Composer\Composer;
use Composer\DependencyResolver\Operation\InstallOperation;
use Composer\DependencyResolver\Operation\UpdateOperation;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Installer\PackageEvent;
use Composer\Installer\PackageEvents;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;

/**
 * Class used to hook into Composer.
 */
class Plugin implements PluginInterface, EventSubscriberInterface {

	/**
	 * Composer.
	 *
	 * @var \Composer\Composer
	 */
	protected $composer;

	/**
	 * Input/Output helper interface.
	 *
	 * @var \Composer\IO\IOInterface
	 */
	protected $io;

	/**
	 * The root directory.
	 *
	 * @var string
	 */
	protected $rootDir = '';

	/**
	 * Package name of this plugin.
	 */
	protected const PLUGIN_PACKAGE_NAME = 'wearerequired/composer-deployer';

	/**
	 * Applies plugin modifications to Composer.
	 *
	 * @param \Composer\Composer       $composer Composer.
	 * @param \Composer\IO\IOInterface $io       Input/Output helper interface.
	 */
	public function activate( Composer $composer, IOInterface $io ) {
		$this->composer = $composer;
		$this->io       = $io;

		$config = $this->composer->getConfig();

		$this->rootDir = dirname( $config->getConfigSource()->getName() );
	}

	/**
	 * Removes any hooks from Composer.
	 *
	 * This will be called when a plugin is deactivated before being
	 * uninstalled, but also before it gets upgraded to a new version
	 * so the old one can be deactivated and the new one activated.
	 *
	 * @param \Composer\Composer       $composer Composer.
	 * @param \Composer\IO\IOInterface $io       Input/Output helper interface.
	 */
	public function deactivate( Composer $composer, IOInterface $io ) { // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
	}

	/**
	 * Prepares the plugin to be uninstalled.
	 *
	 * This will be called after deactivate.
	 *
	 * @param \Composer\Composer       $composer Composer.
	 * @param \Composer\IO\IOInterface $io       Input/Output helper interface.
	 */
	public function uninstall( Composer $composer, IOInterface $io ) { // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
	}

	/**
	 * Subscribes to package update/install/uninstall events.
	 *
	 * @return array Subscribed events.
	 */
	public static function getSubscribedEvents() {
		return [
			PackageEvents::POST_PACKAGE_INSTALL   => [
				[ 'copyDeployerConfig' ],
			],
			PackageEvents::POST_PACKAGE_UPDATE    => [
				[ 'copyDeployerConfig' ],
			],
			PackageEvents::POST_PACKAGE_UNINSTALL => [
				[ 'deleteDeployerConfig' ],
			],
		];
	}

	/**
	 * Copies deployer.php
	 *
	 * @param \Composer\Installer\PackageEvent $event The current event.
	 */
	public function copyDeployerConfig( PackageEvent $event ) {
		$operation = $event->getOperation();

		if ( $operation instanceof InstallOperation ) {
			$package = $operation->getPackage();
		} elseif ( $operation instanceof UpdateOperation ) {
			$package = $operation->getTargetPackage();
		} else {
			throw new \Exception( 'Unknown operation: ' . \get_class( $operation ) );
		}

		if ( self::PLUGIN_PACKAGE_NAME !== $package->getName() ) {
			return;
		}

		$source = dirname( __DIR__ ) . '/res/deploy.tpl.php';
		$dest   = $this->rootDir . '/deploy.php';

		$copied = file_put_contents( $dest, file_get_contents( $source ) );

		if ( false !== $copied ) {
			$this->io->writeError( '    deploy.php has been copied to ' . $dest . '.' );
		} else {
			$this->io->writeError( '<error>deploy.php could not be copied to ' . $dest . '.</error>' );
		}
	}

	/**
	 * Deletes deploy.php after package is being uninstalled.
	 *
	 * @param \Composer\Installer\PackageEvent $event The current event.
	 */
	public function deleteDeployerConfig( PackageEvent $event ) {
		/** @var \Composer\DependencyResolver\Operation\UninstallOperation $operation */
		$operation = $event->getOperation();
		$package   = $operation->getPackage();

		if ( self::PLUGIN_PACKAGE_NAME !== $package->getName() ) {
			return;
		}

		$deployConfigFile = dirname( __DIR__ ) . '/deploy.php';

		if ( is_file( $deployConfigFile ) ) {
			unlink( $deployConfigFile );
			$this->io->writeError( '    deploy.php has been removed.' );
		}
	}
}
