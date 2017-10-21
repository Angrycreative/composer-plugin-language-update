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
 * @todo Handle Core t10ns
 * @todo Handle Theme t10ns
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
				$slug    = str_replace( 'wpackagist-plugin/', '', $package->getName() );
				$version = self::get_wp_standard_version_number( $package->getVersion() );

				self::update_plugin_t10ns( $slug, $version );
				break;


			case 'wordpress-theme':
				$slug    = str_replace( 'wpackagist-theme/', '', $package->getName() );
				$version = self::get_wp_standard_version_number( $package->getVersion() );
				self::update_theme_t10ns( $slug, $version );
				break;

			case 'package':
				if ( 'johnpbloch/wordpress' === $package->getName() ) {
					$version = self::get_wp_standard_version_number( $package->getVersion() );

					self::update_core_t10ns( $version );
				}
				break;

		}
	}

	/**
	 * Ensure a version number has no more than 3 parts.
	 *
	 * @param string $version.
	 *
	 * @return string
	 */
	protected static function get_wp_standard_version_number( $version ) {
		$version_parts = explode( '.', $version );

		if ( count( $version_parts ) > 3 ) {
			array_splice( $version_parts, 3 );
		}

		return implode( '.', $version_parts );
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
				echo "No translations updated for package: {$slug}" . PHP_EOL;

			} else {
				foreach ( $results as $result ) {
					echo "Updated translation {$result} for package: {$slug}" . PHP_EOL;
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
	 *
	 * @todo Implement this!
	 */
	protected static function update_theme_t10ns( $slug, $version ) {
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
