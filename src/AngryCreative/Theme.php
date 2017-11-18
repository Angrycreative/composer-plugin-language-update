<?php
/**
 * @package AngryCreative
 */

namespace AngryCreative;

/**
 * Class Theme
 *
 * @package AngryCreative
 */
class Theme extends T10ns {

	/**
	 * Theme t10ns API url.
	 *
	 * @var string Plugin t10ns API url.
	 */
	protected $api_url = 'https://api.wordpress.org/translations/themes/1.0/';

	/**
	 * Theme slug, eg 'twenty-seventeen'.
	 *
	 * @var string
	 */
	protected $slug;

	/**
	 * Theme version.
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
	 * Theme constructor.
	 *
	 * @param string       $slug            Theme slug.
	 * @param float|string $version         Theme version.
	 * @param array        $languages       Array of languages.
	 * @param string       $wp_content_path Path to wp-content.
	 *
	 * @throws \Exception
	 */
	public function __construct( $slug, $version = '', array $languages, $wp_content_path ) {
		$this->slug            = $slug;
		$this->version         = $version;
		$this->languages       = $languages;
		$this->wp_content_path = $wp_content_path;

		try {
			$this->t10ns = $this->get_available_t10ns( $this->api_url, $this->version, $this->slug );
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
	 * @param string $language (locale) Eg. 'sv_SE'.
	 *
	 * @return bool True if the t10ns could be downloaded, or false.
	 *
	 * @throws \Exception
	 */
	protected function fetch_theme_t10ns( $language ) : bool {
		$has_updated = false;
		foreach ( $this->t10ns as $t10n ) {
			if ( $t10n->language !== $language ) {
				continue;
			}

			try {
				$this->download_and_move_t10ns( 'theme', $t10n->package, $this->wp_content_path );
				$has_updated = true;

			} catch ( \Exception $e ) {
				throw new \Exception( $e->getMessage() );

			}
		}

		return $has_updated;
	}

}
