<?php
/**
 * Created by PhpStorm.
 * User: richardsweeney
 * Date: 2017-10-16
 * Time: 11:49
 */

namespace AngryCreative;

/**
 * Class ThemeTest
 *
 * @package AngryCreative
 */
class ThemeTest extends \PHPUnit_Framework_TestCase {

	public function testTheme() {
		try {
			$dir    = dirname( dirname( dirname( dirname( dirname( __DIR__ ) ) ) ) ) . '/public/wp-content/languages/themes';
			$plugin = new Theme( 'twentytwelve', '2.2.0.0', [ 'sv_SE' ] );

			$this->assertInternalType( 'array', $plugin->get_languages() );
			$this->assertNotEmpty( $plugin->get_languages() );

			$this->assertInternalType( 'array', $plugin->get_t10ns() );
			$this->assertNotEmpty( $plugin->get_t10ns() );

			$this->assertEquals( $dir, $plugin->get_dest_path( 'theme' ) );

			$result = $plugin->fetch_t10ns();
			$this->assertInternalType( 'array', $result );
			$this->assertNotEmpty( $result );

			$this->assertFileExists( $dir );
			$this->assertFileExists( $dir . '/twentytwelve-sv_SE.mo' );
			$this->assertFileExists( $dir . '/twentytwelve-sv_SE.po' );

		} catch ( \Exception $e ) {
			var_dump( $e->getMessage() );
		}
	}

}
