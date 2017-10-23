<?php
/**
 * Created by PhpStorm.
 * User: richardsweeney
 * Date: 2017-10-15
 * Time: 15:44
 */

namespace AngryCreative;

use GuzzleHttp\Client;

class Theme extends T10ns {

	/**
	 * @var string Plugin t10ns API url.
	 */
	protected $api_url = 'https://api.wordpress.org/translations/themes/1.0/';

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
	 * @param array        $languages
	 *
	 * @throws \Exception
	 */
	public function __construct( $slug, $version = '', array $languages ) {
		$this->slug      = $slug;
		$this->version   = $version;
		$this->languages = $languages;

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
	 * Get a list of theme t10ns via the API.
	 *
	 * @throws \Exception
	 * @return array An array of theme t10ns.
	 */
	protected function get_available_t10ns() : array {
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
	 * Fetch all available t10ns for a theme.
	 *
	 * @return array
	 */
	public function fetch_t10ns() : array {
		$results = [];

		foreach ( $this->languages as $language ) {
			try {
				$result = $this->fetch_theme_t10ns( $language );
				if ( $result ) {
					$results[] = $language;
				}
			} catch ( \Exception $e ) {
				// Maybe we should do something here?!
			}
		}

		return $results;
	}

	/**
	 * Fetch and move a themes' t10ns to the correct
	 * directory.
	 *
	 * @param string $language Eg. 'sv_SE'.
	 *
	 * @return bool True if the t10ns could be downloaded, or false.
	 *
	 * @throws \Exception
	 */
	protected function fetch_theme_t10ns( $language ) {
		$has_updated = false;
		foreach ( $this->t10ns as $t10n ) {
			if ( $t10n->language !== $language ) {
				continue;
			}

			try {
				$this->download_and_move_t10ns( 'theme', $t10n->package );
				$has_updated = true;

			} catch ( \Exception $e ) {
				throw new \Exception( $e->getMessage() );

			}
		}

		return $has_updated;
	}

}
