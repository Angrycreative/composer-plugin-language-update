<?php
/**
 * Created by PhpStorm.
 * User: richardsweeney
 * Date: 2017-10-15
 * Time: 15:44
 */

namespace AngryCreative;

use GuzzleHttp\Client;
use ZipArchive;

class Plugin extends T10ns {

	/**
	 * @var string Plugin t10ns API url.
	 */
	protected $api_url = 'https://api.wordpress.org/translations/plugins/1.0/';

	/**
	 * @var string
	 */
	protected $slug;

	/**
	 * @var float|string
	 */
	protected $version;

	/**
	 * @var array
	 */
	protected $languages = [];

	/**
	 * @var array
	 */
	protected $t10ns = [];

	/**
	 * PluginT10ns constructor.
	 *
	 * @param string       $slug
	 * @param float|string $version
	 *
	 * @throws \Exception
	 */
	public function __construct( $slug, $version = '' ) {
		$this->slug    = $slug;
		$this->version = $version;

		try {
			$this->languages = $this->get_site_languages();
		} catch ( \Exception $e ) {
			throw new \Exception( $e->getMessage() );
		}

		try {
			$this->t10ns = $this->get_available_t10ns();
		} catch ( \Exception $e ) {
			throw new \Exception( $e->getMessage() );
		}
	}

	/**
	 * @return array
	 */
	public function get_languages() : array {
		return $this->languages;
	}

	/**
	 * @return array
	 */
	public function get_t10ns() : array {
		return $this->t10ns;
	}

	/**
	 * Get a list of plugin t10ns via the API.
	 *
	 * @throws \Exception
	 * @return array An array of plugin t10ns.
	 */
	protected function get_available_t10ns() {
		$query = [
			'slug' => $this->slug,
		];
		if ( ! empty( $this->version ) ) {
			$query['version'] = $this->version;
		}

		$client   = new Client();
		$response = $client->request( 'GET', $this->api_url, [
			'query' => $query,
		] );

		if ( 200 !== $response->getStatusCode() ) {
			throw new \Exception( 'Got status code ' . $response->getStatusCode() );
		}

		$body = json_decode( $response->getBody() );

		if ( empty( $body->translations ) ) {
			throw new \Exception( 'No t10ns found' );
		}

		return $body->translations;
	}

	/**
	 * Fetch all available t10ns for a plugin.
	 *
	 * @return array
	 */
	public function fetch_t10ns() {
		$results = [];

		foreach ( $this->languages as $language ) {
			try {
				$this->fetch_plugin_t10ns( $language );
				$results[] = $language;

			} catch ( \Exception $e ) {
				// Maybe we should do something here?!
			}
		}

		return $results;
	}

	/**
	 * Fetch and move a plugins' t10ns to the correct
	 * directory.
	 *
	 * @param string $language Eg. 'sv_SE'.
	 *
	 * @throws \Exception
	 */
	protected function fetch_plugin_t10ns( $language ) {
		foreach ( $this->t10ns as $t10n ) {
			if ( $t10n->language !== $language ) {
				continue;
			}

			try {
				$this->download_plugin_t10ns( $t10n->package );

			} catch ( \Exception $e ) {
				throw new \Exception( $e->getMessage() );

			}
		}
	}

	/**
	 * @param string $package_url The URL to the package t10ns.
	 *
	 * @throws \Exception
	 */
	protected function download_plugin_t10ns( $package_url ) {
		try {
			$dest_path = $this->get_dest_path( 'plugin' );

			try {
				$t10n_files = $this->download_t10ns( $package_url );
				$zip        = new ZipArchive();

				if ( true === $zip->open( $t10n_files ) ) {
					for ( $i = 0; $i < $zip->numFiles; $i++ ) {
						$zip->extractTo( $dest_path, [ $zip->getNameIndex( $i ) ] );
					}
					$zip->close();

				} else {
					throw new \Exception( 'The was an error unzipping or moving the t10n files' );

				}
			} catch ( \Exception $e ) {
				throw new \Exception( $e->getMessage() );

			}
		} catch ( \Exception $e ) {
			throw new \Exception( $e->getMessage() );

		}
	}

}
