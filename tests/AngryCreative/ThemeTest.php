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
		$dir    = dirname( dirname( dirname( dirname( dirname( __DIR__ ) ) ) ) ) . '/public/wp-content';
		$theme = new Theme( 'twentytwelve', '2.2.0.0', [ 'sv_SE' ], $dir );

		$this->assertInternalType( 'array', $theme->get_languages() );
		$this->assertNotEmpty( $theme->get_languages() );
		$this->assertEquals( $theme->get_languages(), [ 'sv_SE' ] );

		$this->assertInternalType( 'array', $theme->get_t10ns() );
		$this->assertNotEmpty( $theme->get_t10ns() );

		$this->assertEquals( $dir . '/languages/themes', $theme->get_dest_path( 'theme', $dir ) );

		$result = $theme->fetch_t10ns();
		$this->assertInternalType( 'array', $result );
		$this->assertNotEmpty( $result );

		$this->assertFileExists( $theme->get_dest_path( 'theme', $dir ) );
		$this->assertFileExists( $theme->get_dest_path( 'theme', $dir ) . '/twentytwelve-sv_SE.mo' );
		$this->assertFileExists( $theme->get_dest_path( 'theme', $dir ) . '/twentytwelve-sv_SE.po' );
	}

}
