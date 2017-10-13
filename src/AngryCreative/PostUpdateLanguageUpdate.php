<?php
/**
 * Created by PhpStorm.
 * User: richardsweeney
 * Date: 2017-10-13
 * Time: 08:04
 */

namespace AngryCreative;

use Composer\Installer\PackageEvent;
use GuzzleHttp\Client;

class PostUpdateLanguageUpdate {

	/**
	 * @param PackageEvent $event
	 */
	public static function updateT10ns(PackageEvent $event)
	{
		$installedPackage = $event->getOperation()->getPackage();

		if ('wordpress-plugin' === $installedPackage->getType()) {
			self::updatePluginT10ns($installedPackage);
		}
	}

	/**
	 * @param PackageEvent $event
	 */
	public static function removeT10ns(PackageEvent $event)
	{
		$installedPackage = $event->getOperation()->getPackage();

		$type = $installedPackage->getType();
		$version = $installedPackage->getVersion();
		$name = $installedPackage->getName();

		var_dump( $type, $version, $name );
	}

	/**
	 * Get translations for a plugin.
	 *
	 * @param $installedPackage
	 */
	public static function updatePluginT10ns($installedPackage)
	{
		$plugin_slug = str_replace('wpackagist-plugin/', '', $installedPackage->getName());

		$client = new Client([
			'base_uri' => 'https://api.wordpress.org/translations/plugins/1.0/?slug=' . $plugin_slug,
		]);

		$response = $client->request('GET');
		$response->getStatusCode();

		$t10ns = json_decode($response->getBody());
		var_dump($response->getStatusCode(), $t10ns);
	}

}
