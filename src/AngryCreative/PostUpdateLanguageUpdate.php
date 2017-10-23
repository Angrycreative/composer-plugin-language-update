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
	 * @var array
	 */
	static $languages = [];

	/**
	 * @param PackageEvent $event
	 */
	public static function update_t10ns( PackageEvent $event ) {
		$extra = $event->getComposer()->getPackage()->getExtra();
		if ( ! empty( $extra['wordpress-languages'] ) ) {
			self::$languages = $extra['wordpress-languages'];
		}

		if ( empty( self::$languages ) ) {
			$event->getIO()->writeError( 'Did you forget to add the wordpress-langagues to the extra section of your composer.json?' );

			exit;
		}

		$package = $event->getOperation()->getPackage();

		switch ( $package->getType() ) {
			case 'wordpress-plugin':
				$slug = str_replace( 'wpackagist-plugin/', '', $package->getName() );

				self::update_plugin_t10ns( $slug, $package->getVersion(), $event );
				break;


			case 'wordpress-theme':
				$slug = str_replace( 'wpackagist-theme/', '', $package->getName() );

				self::update_theme_t10ns( $slug, $package->getVersion(), $event );
				break;

			case 'package':
				if ( 'johnpbloch/wordpress' === $package->getName() ) {
					self::update_core_t10ns( $package->getVersion(), $event );
				}
				break;

		}
	}

	/**
	 * @param string       $slug    Plugin slug.
	 * @param string       $version Plugin version.
	 * @param PackageEvent $event
	 */
	protected static function update_plugin_t10ns( $slug, $version, PackageEvent $event ) {
		try {
			$plugin_t10ns = new Plugin( $slug, $version, self::$languages );
			$results      = $plugin_t10ns->fetch_t10ns();

			if ( empty( $results ) ) {
				$event->getIO()->write( "No translations updated for plugin: {$slug}" );

			} else {
				foreach ( $results as $result ) {
					$event->getIO()->write( "Updated translation {$result} for plugin: {$slug}" );
				}
			}
		} catch ( \Exception $e ) {
			$event->getIO()->writeError( $e->getMessage() );

		}
	}

	/**
	 * @param string       $slug    Theme slug.
	 * @param string       $version Theme version.
	 * @param PackageEvent $event
	 */
	protected static function update_theme_t10ns( $slug, $version, PackageEvent $event ) {
		try {
			$theme_t10ns = new Theme( $slug, $version, self::$languages );
			$results     = $theme_t10ns->fetch_t10ns();

			if ( empty( $results ) ) {
				$event->getIO()->write( "No translations updated for theme: {$slug}" );

			} else {
				foreach ( $results as $result ) {
					$event->getIO()->write( "Updated translation {$result} for theme: {$slug}" );
				}
			}
		} catch ( \Exception $e ) {
			$event->getIO()->writeError( $e->getMessage() );

		}
	}

	/**
	 * Update|Install core t10ns.
	 *
	 * @param string       $version Core version.
	 * @param PackageEvent $event
	 */
	protected static function update_core_t10ns( $version, PackageEvent $event ) {
		try {
			$core    = new Core( $version, self::$languages );
			$results = $core->fetch_t10ns();

			if ( empty( $results ) ) {
				$event->getIO()->write( "No translations updated for core v.{$version}" );

			} else {
				foreach ( $results as $result ) {
					$event->getIO()->write( "Updated translation {$result} for core v.{$version}" );
				}
			}
		} catch ( \Exception $e ) {
			$event->getIO()->writeError( $e->getMessage() );

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
