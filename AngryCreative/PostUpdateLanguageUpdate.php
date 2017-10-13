<?php
/**
 * Created by PhpStorm.
 * User: richardsweeney
 * Date: 2017-10-13
 * Time: 08:04
 */

namespace AngryCreative;

use Composer\Installer\PackageEvent;

class PostUpdateLanguageUpdate {

	/**
	 * @param PackageEvent $event
	 */
	public static function updateT10ns(PackageEvent $event) {
		//$installedPackage = $event->getOperation()->getPackage();
		$installedPackage = $event->getOperation();

		var_dump( $installedPackage );
	}

	/**
	 * @param PackageEvent $event
	 */
	public static function removeT10ns(PackageEvent $event) {
		//$installedPackage = $event->getOperation()->getPackage();
		$installedPackage = $event->getOperation();

		var_dump( $installedPackage );
	}

}
