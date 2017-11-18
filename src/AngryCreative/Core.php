<?php
/**
 * Created by PhpStorm.
 * User: richardsweeney
 * Date: 2017-10-21
 * Time: 13:52
 *
 * @package AngryCreative
 */

namespace AngryCreative;

/**
 * Class Core
 *
 * @package AngryCreative
 */
class Core extends T10ns {

	/**
	 * Core t10ns API url.
	 *
	 * @var string.
	 */
	protected $api_url = 'https://api.wordpress.org/translations/core/1.0/';

	/**
	 * Core version.
	 *
	 * @var float|string
	 */
	protected $version;

	/**
	 * Array of languages availale on the current site.
	 *
	 * @var array
	 */
	protected $languages = [];

	/**
	 * Path to the wp-content directory.
	 *
	 * @var string
	 */
	protected $wp_content_path;

	/**
	 * A list of available t10s.
	 *
	 * @var array
	 */
	protected $t10ns = [];

	/**
	 * Core constructor.
	 *
	 * @param float|string $version         Core version.
	 * @param array        $languages       Array of languages.
	 * @param string       $wp_content_path Path to wp-content.
	 *
	 * @throws \Exception
	 */
	public function __construct( $version = '', array $languages, $wp_content_path ) {
		$this->version         = $version;
		$this->languages       = $languages;
		$this->wp_content_path = $wp_content_path;

		try {
			$this->t10ns = $this->get_available_t10ns( $this->api_url, $this->version );
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
	 * Fetch all available t10ns for Core.
	 *
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
	 * @return bool
	 */
	protected function fetch_core_t10ns( $language ) : bool {
		$has_updated = false;

		foreach ( $this->t10ns as $t10n ) {
			if ( $t10n->language !== $language ) {
				continue;
			}

			try {
				$this->download_and_move_t10ns( 'core', $t10n->package, $this->wp_content_path );
				$has_updated = true;

			} catch ( \Exception $e ) {
				throw new \Exception( $e->getMessage() );

			}
		}

		return $has_updated;
	}

}
