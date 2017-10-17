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
				$slug = str_replace( 'wpackagist-plugin/', '', $package->getName() );
				self::update_plugin_t10ns( $event, $slug );
				break;


			case 'wordpress-theme':
				$slug = str_replace( 'wpackagist-theme/', '', $package->getName() );
				self::update_theme_t10ns( $event, $slug );
				break;

		}
	}

	/**
	 * @param PackageEvent $event
	 * @param string       $slug
	 */
	protected static function update_plugin_t10ns( PackageEvent $event, $slug ) {
		try {
			$plugin_t10ns = new Plugin( $slug );
			$results      = $plugin_t10ns->fetch_t10ns();

			if ( empty( $results ) ) {
				$event->getID()->write( "No translations updated for package: {$slug}" );

			} else {
				foreach ( $results as $result ) {
					$event->getID()->write( "Updated translation {$result} for package: {$slug}" );
				}
			}
		} catch ( \Exception $e ) {
			$event->getID()->write( 'Error :( ' . $e->getMessage() );

		}
	}

	/**
	 * @param PackageEvent $event
	 * @param string       $slug
	 *
	 * @todo Implement this!
	 */
	protected static function update_theme_t10ns( PackageEvent $event, $slug ) {
		// Do something!
	}

	/**
	 * @param PackageEvent $event
	 */
	public static function remove_t10ns( PackageEvent $event ) {
		$package = $event->getOperation()->getPackage();

		$type    = $package->getType();
		$version = $package->getVersion();
		$name    = $package->getName();

		//var_dump( $type, $version, $name );
	}

}
