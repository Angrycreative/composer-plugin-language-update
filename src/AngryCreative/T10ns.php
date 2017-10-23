<?php
/**
 * Created by PhpStorm.
 * User: richardsweeney
 * Date: 2017-10-15
 * Time: 15:49
 */

namespace AngryCreative;

use GuzzleHttp\Client;

abstract class T10ns {

	/**
	 * @return array
	 */
	abstract protected function get_available_t10ns() : array;

	/**
	 * @return array
	 */
	abstract public function fetch_t10ns() : array;

	/**
	 * Get the destination path for a type of object: either
	 * 'plugin', 'theme' or 'core'.
	 *
	 * This will also create the directory if if doesn't exist.
	 *
	 * @param string $type            The object type.
	 * @param string $wp_content_path The path to the wp_content directory.
	 *
	 * @return string path to the destination directory.
	 *
	 * @throws \Exception
	 */
	public function get_dest_path( $type = 'plugin', $wp_content_path ) : string {
		$dest_path = $wp_content_path . '/languages';

		if ( ! file_exists( $dest_path ) ) {
			$result = mkdir( $dest_path, 0775 );
			if ( ! $result ) {
				throw new \Exception( 'Failed to create directory at: ' . $dest_path );
			}
		}

		$path = '';
		switch ( $type ) {
			case 'plugin' :
				$path = '/plugins';
				break;

			case 'theme' :
				$path = '/themes';
				break;
		}

		$dest_path .= $path;

		if ( ! file_exists( $dest_path ) ) {
			$result = mkdir( $dest_path, 0775 );
			if ( ! $result ) {
				throw new \Exception( 'Failed to create directory at: ' . $dest_path );
			}
		}

		return $dest_path;
	}

	/**
	 * Download a zipped file of t10ns.
	 *
	 * @param $url
	 *
	 * @throws \Exception
	 * @return string Path to the downloaded files.
	 */
	public function download_t10ns( $url ) : string {
		$client   = new Client();
		$tmp_name = sys_get_temp_dir() . '/' . basename( $url );
		$request  = $client->request( 'GET', $url, [
			'sink' => $tmp_name,
		] );

		if ( 200 !== $request->getStatusCode() ) {
			throw new \Exception( 'T10ns not found' );
		}

		return $tmp_name;
	}

	/**
	 * @param string $t10n_files Path to the zipped t10n files.
	 * @param string $dest_path  Path to expand the zipped files to.
	 *
	 * @throws \Exception
	 */
	public function unpack_and_more_archived_t10ns( $t10n_files, $dest_path ) : void {
		$zip = new \ZipArchive();

		if ( true === $zip->open( $t10n_files ) ) {
			for ( $i = 0; $i < $zip->numFiles; $i++ ) {
				$ok = $zip->extractTo( $dest_path, [ $zip->getNameIndex( $i ) ] );
				if ( false === $ok ) {
					throw new \Exception( 'There was an error moving the translation to the destination directory' );
				}
			}
			$zip->close();

		} else {
			throw new \Exception( 'The was an error unzipping or moving the t10n files' );

		}
	}

	/**
	 * Download t10ns, unzip them and move to the relevant directory.
	 *
	 * @param string $package_type    The package type, currently only 'plugin', 'theme', or 'core'.
	 * @param string $package_url     The URL for the package t10ns.
	 * @param string $wp_content_path The Path to the wp_content directory.
	 *
	 * @throws \Exception
	 */
	public function download_and_move_t10ns( $package_type = 'plugin', $package_url, $wp_content_path ) : void {
		try {
			$dest_path = $this->get_dest_path( $package_type, $wp_content_path );

			try {
				$t10n_files = $this->download_t10ns( $package_url );

				try {
					$this->unpack_and_more_archived_t10ns( $t10n_files, $dest_path );

				} catch ( \Exception $e ) {
					throw new \Exception( $e->getMessage() );
				}
			} catch ( \Exception $e ) {
				throw new \Exception( $e->getMessage() );
			}
		} catch ( \Exception $e ) {
			throw new \Exception( $e->getMessage() );
		}
	}

}
