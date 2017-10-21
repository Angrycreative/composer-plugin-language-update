<?php
/**
 * Created by PhpStorm.
 * User: richardsweeney
 * Date: 2017-10-13
 * Time: 08:04
 */

namespace AngryCreative;

require dirname( dirname( dirname( dirname( __DIR__ ) ) ) ) . '/autoload.php';

use Composer\Installer\PackageEvent;

/**
 * Class PostUpdateLanguageUpdate
 *
 * @todo Handle removal of Plugins and/or Themes
 *
 * @package AngryCreative
 */
class PostUpdateLanguageUpdate {

	/**
	 * @param PackageEvent $event
	 */
	public static function update_t10ns( PackageEvent $event ) {
		$package = $event->getOperation()->getPackage();

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
			$plugin_t10ns = new Plugin( $slug, $version );
			$results      = $plugin_t10ns->fetch_t10ns();

			if ( empty( $results ) ) {
				echo "No translations updated for plugin: {$slug}" . PHP_EOL;

			} else {
				foreach ( $results as $result ) {
					echo "Updated translation {$result} for plugin: {$slug}" . PHP_EOL;
				}
			}
		} catch ( \Exception $e ) {
			echo 'Error :( ' . $e->getMessage() . PHP_EOL;

		}
	}

	/**
	 * Update|Install core t10ns.
	 *
	 * @param string $version Core version.
	 */
	protected static function update_core_t10ns( $version ) {
		try {
			$core    = new Core( $version );
			$results = $core->fetch_t10ns();

			if ( empty( $results ) ) {
				echo "No translations updated for core v.{$version}" . PHP_EOL;

			} else {
				foreach ( $results as $result ) {
					echo "Updated translation {$result} for core v.{$version}" . PHP_EOL;
				}
			}
		} catch ( \Exception $e ) {
			echo 'Error :( ' . $e->getMessage() . PHP_EOL;

		}
	}

	/**
	 * @param string $slug    Theme slug.
	 * @param string $version Theme version.
	 */
	protected static function update_theme_t10ns( $slug, $version ) {
		try {
			$theme_t10ns = new Theme( $slug, $version );
			$results     = $theme_t10ns->fetch_t10ns();

			if ( empty( $results ) ) {
				echo "No translations updated for theme: {$slug}" . PHP_EOL;

			} else {
				foreach ( $results as $result ) {
					echo "Updated translation {$result} for theme: {$slug}" . PHP_EOL;
				}
			}
		} catch ( \Exception $e ) {
			echo 'Error :( ' . $e->getMessage() . PHP_EOL;

		}
	}

	/**
	 * @param PackageEvent $event
	 *
	 * @todo maybe implement this?
	 */
	public static function remove_t10ns( PackageEvent $event ) {
		//$package = $event->getOperation()->getPackage();

		//$type    = $package->getType();
		//$version = $package->getVersion();
		//$name    = $package->getName();

		//var_dump( $type, $version, $name );
	}

}
