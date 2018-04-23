<?php
/**
 *
 * @package AngryCreative
 */

namespace AngryCreative;

use Composer\Installer\PackageEvent;
use Composer\Package\PackageInterface;

/**
 * Class PostUpdateLanguageUpdate
 *
 * @todo Handle removal of Plugins and/or Themes
 *
 * @package AngryCreative
 */
class PostUpdateLanguageUpdate {

	/**
	 * @var array
	 */
	protected static $languages = [];

	/**
	 * @var string
	 */
	protected static $wp_content_path = '';

	/**
	 * @var PackageEvent
	 */
	protected static $event;

	/**
	 * Require composer autoloader
	 *
	 * @param PackageEvent $event
	 */
	public static function require_autoloader( PackageEvent $event ) {
		$vendorDir = $event->getComposer()->getConfig()->get('vendor-dir');
		require_once $vendorDir . '/autoload.php';
	}


	/**
	 * Update t10ns when a package is installed
	 *
	 * @param PackageEvent $event
	 */
	public static function install_t10ns( PackageEvent $event ) {
		self::$event = $event;

		try {
			self::require_autoloader( $event );
			self::set_config();
			self::get_t10ns_for_package( self::$event->getOperation()->getPackage() );

		} catch ( \Exception $e ) {
			self::$event->getIO()->writeError( $e->getMessage() );
		}
	}

	/**
	 * Update t10ns when a package is updated
	 *
	 * @param PackageEvent $event
	 */
	public static function update_t10ns( PackageEvent $event ) {
		self::$event = $event;

		try {
			self::require_autoloader( $event );
			self::set_config();
			self::get_t10ns_for_package( self::$event->getOperation()->getTargetPackage() );

		} catch ( \Exception $e ) {
			self::$event->getIO()->writeError( $e->getMessage() );
		}
	}

	/**
	 * Set the config
	 *
	 * @throws \Exception
	 */
	protected static function set_config() {
		$extra = self::$event->getComposer()->getPackage()->getExtra();

		if ( ! empty( $extra['wordpress-languages'] ) ) {
			self::$languages = $extra['wordpress-languages'];
		}

		if ( ! empty( $extra['wordpress-path-to-content-dir'] ) ) {
			self::$wp_content_path = dirname( dirname( dirname( dirname( dirname( __DIR__ ) ) ) ) ) . '/' . $extra['wordpress-path-to-content-dir'];
		}

		if ( empty( self::$languages ) || empty( self::$wp_content_path ) ) {
			throw new \Exception( 'Oops :( Did you forget to add the wordpress-langagues or path to content dir to the extra section of your composer.json?' );
		}
	}

	/**
	 * Get t10ns for a package, where applicable.
	 *
	 * @param PackageInterface $package
	 */
	protected static function get_t10ns_for_package( PackageInterface $package ) {
		switch ( $package->getType() ) {
			case 'wordpress-plugin':
				$slug = str_replace( 'wpackagist-plugin/', '', $package->getName() );

				self::update_plugin_t10ns( $slug, $package->getVersion() );
				break;

			case 'wordpress-theme':
				$slug = str_replace( 'wpackagist-theme/', '', $package->getName() );

				self::update_theme_t10ns( $slug, $package->getVersion() );
				break;

			case 'package':
				if ( 'johnpbloch/wordpress' === $package->getName() ) {
					self::update_core_t10ns( $package->getVersion() );
				}
				break;

		}
	}

	/**
	 * @param string $slug    Plugin slug.
	 * @param string $version Plugin version.
	 */
	protected static function update_plugin_t10ns( $slug, $version ) {
		try {
			$plugin_t10ns = new Plugin( $slug, $version, self::$languages, self::$wp_content_path );
			$results      = $plugin_t10ns->fetch_t10ns();

			if ( empty( $results ) ) {
				self::$event->getIO()->write( "No translations updated for plugin: {$slug}" );

			} else {
				foreach ( $results as $result ) {
					self::$event->getIO()->write( "Updated translation {$result} for plugin: {$slug}" );
				}
			}
		} catch ( \Exception $e ) {
			self::$event->getIO()->writeError( $e->getMessage() );

		}
	}

	/**
	 * @param string $slug    Theme slug.
	 * @param string $version Theme version.
	 */
	protected static function update_theme_t10ns( $slug, $version ) {
		try {
			$theme_t10ns = new Theme( $slug, $version, self::$languages, self::$wp_content_path );
			$results     = $theme_t10ns->fetch_t10ns();

			if ( empty( $results ) ) {
				self::$event->getIO()->write( "No translations updated for theme: {$slug}" );

			} else {
				foreach ( $results as $result ) {
					self::$event->getIO()->write( "Updated translation {$result} for theme: {$slug}" );
				}
			}
		} catch ( \Exception $e ) {
			self::$event->getIO()->writeError( $e->getMessage() );

		}
	}

	/**
	 * Update|Install core t10ns.
	 *
	 * @param string $version Core version.
	 */
	protected static function update_core_t10ns( $version ) {
		try {
			$core    = new Core( $version, self::$languages, self::$wp_content_path );
			$results = $core->fetch_t10ns();

			if ( empty( $results ) ) {
				self::$event->getIO()->write( "No translations updated for core v.{$version}" );

			} else {
				foreach ( $results as $result ) {
					self::$event->getIO()->write( "Updated translation {$result} for core v.{$version}" );
				}
			}
		} catch ( \Exception $e ) {
			self::$event->getIO()->writeError( $e->getMessage() );

		}
	}

	/**
	 * Remove t10ns on uninstall.
	 *
	 * @param PackageEvent $event
	 *
	 * @todo maybe implement this?
	 */
	public static function remove_t10ns( PackageEvent $event ) {
		self::$event = $event;
		exit;
	}

}
