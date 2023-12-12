<?php
/**
 * @package AngryCreative
 */

namespace AngryCreative;

/**
 * Class Plugin
 */
class Plugin extends T10ns {

	/**
	 * Plugin t10ns API url.
	 *
	 * @var string
	 */
	protected $api_url = 'https://api.wordpress.org/translations/plugins/1.0/';

	/**
	 * The package type.
	 *
	 * @var string
	 */
	protected $package_type = 'plugin';

	/**
	 * The plugin slug, eg 'query-monitor'.
	 *
	 * @var string
	 */
	protected $slug;

	/**
	 * The plugin version.
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
	 * Plugin constructor.
	 *
	 * @param string       $slug            Plugin slug.
	 * @param float|string $version         Plugin version.
	 * @param array        $languages       Array of languages.
	 * @param string       $wp_content_path Path to wp-content.
	 *
	 * @throws \Exception
	 */
	public function __construct( $slug, $version, array $languages, $wp_content_path ) {
		$this->slug            = $slug;
		$this->version         = $version;
		$this->languages       = $languages;
		$this->wp_content_path = $wp_content_path;

		if ( empty( $this->languages ) || empty( $this->wp_content_path ) ) {
			throw new \Exception( 'Languages or wp_content_path empty' );
		}

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
	 * Fetch all available t10ns for a plugin.
	 *
	 * @throws \Exception
	 * @return array
	 */
	public function fetch_t10ns() : array {
		$results = [];

		foreach ( $this->languages as $language ) {
			try {
				$result = $this->fetch_plugin_t10ns( $language );
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
	 * Fetch and move a plugins' t10ns to the correct
	 * directory.
	 *
	 * @param string $language Eg. 'sv_SE'.
	 *
	 * @throws \Exception
	 * @return bool True if the t10ns could be downloaded, or false.
	 */
	protected function fetch_plugin_t10ns( $language ) : bool {
		$has_updated = false;
		foreach ( $this->t10ns as $t10n ) {
			if ( $t10n->language !== $language ) {
				continue;
			}

			try {
				$this->download_and_move_t10ns( 'plugin', $t10n->package, $this->wp_content_path );
				$has_updated = true;

			} catch ( \Exception $e ) {
				throw new \Exception( $e->getMessage() );

			}
		}

		return $has_updated;
	}

}
