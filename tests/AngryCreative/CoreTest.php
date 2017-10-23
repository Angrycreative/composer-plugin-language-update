<?php
/**
 * Created by PhpStorm.
 * User: richardsweeney
 * Date: 2017-10-16
 * Time: 11:49
 */

namespace AngryCreative;

/**
 * Class CoreTest
 *
 * @package AngryCreative
 */
class CoreTest extends \PHPUnit_Framework_TestCase {

	public function testCore() {
		try {
			$dir  = dirname( dirname( dirname( dirname( dirname( __DIR__ ) ) ) ) ) . '/public/wp-content/languages';
			$core = new Core( '4.8.2', [ 'sv_SE' ] );

			$this->assertEquals( $dir, $core->get_dest_path( 'core' ) );

			$result = $core->fetch_t10ns();
			$this->assertInternalType( 'array', $result );
			$this->assertNotEmpty( $result );

			$this->assertFileExists( $dir );
			$this->assertFileExists( $dir . '/sv_SE.mo' );
			$this->assertFileExists( $dir . '/sv_SE.po' );
			$this->assertFileExists( $dir . '/admin-sv_SE.mo' );
			$this->assertFileExists( $dir . '/admin-sv_SE.po' );

		} catch ( \Exception $e ) {
			var_dump( $e->getMessage() );
		}
	}

}
