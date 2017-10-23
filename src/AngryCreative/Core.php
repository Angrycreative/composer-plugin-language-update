<?php
/**
 * Created by PhpStorm.
 * User: richardsweeney
 * Date: 2017-10-21
 * Time: 13:52
 */

namespace AngryCreative;

use GuzzleHttp\Client;

class Core extends T10ns {

	/**
	 * @var string Plugin t10ns API url.
	 */
	protected $api_url = 'https://api.wordpress.org/translations/core/1.0/';

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
	 * Core constructor.
	 *
	 * @param string $version
	 * @param array $languages
	 *
	 * @throws \Exception
	 */
	public function __construct( $version = '', array $languages ) {
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
	 * @return array
	 *
	 * @throws \Exception
	 */
	protected function get_available_t10ns() : array {
		$query = [];
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
	 * @return array
	 */
	public function fetch_t10ns() : array {
		$results = [];

		foreach ( $this->languages as $language ) {
			try {
				$this->fetch_core_t10ns( $language );
				$results[] = $language;

			} catch ( \Exception $e ) {
				// Maybe we should do something here?!
			}
		}

		return $results;
	}

	/**
	 * Fetch and move core t10ns to the correct directory.
	 *
	 * @param string $language Eg. 'sv_SE'.
	 *
	 * @throws \Exception
	 */
	protected function fetch_core_t10ns( $language ) {
		foreach ( $this->t10ns as $t10n ) {
			if ( $t10n->language !== $language ) {
				continue;
			}

			try {
				$this->download_and_move_t10ns( 'core', $t10n->package );

			} catch ( \Exception $e ) {
				throw new \Exception( $e->getMessage() );

			}
		}
	}

}
