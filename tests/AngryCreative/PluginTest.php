<?php
/**
 * Created by PhpStorm.
 * User: richardsweeney
 * Date: 2017-10-16
 * Time: 11:49
 */

namespace AngryCreative;

/**
 * Class TestPlugin
 *
 * @package AngryCreative
 */
class PluginTest extends \PHPUnit_Framework_TestCase {

	public function testPlugin() {
		try {
			$dir    = dirname( dirname( dirname( dirname( dirname( __DIR__ ) ) ) ) ) . '/public/wp-content/languages/plugins';
			$plugin = new Plugin( 'redirection' );

			$this->assertInternalType( 'array', $plugin->get_languages() );
			$this->assertNotEmpty( $plugin->get_languages() );

			$this->assertInternalType( 'array', $plugin->get_t10ns() );
			$this->assertNotEmpty( $plugin->get_t10ns() );

			$this->assertEquals( $dir, $plugin->get_dest_path( 'plugin' ) );

			$result = $plugin->fetch_t10ns();
			$this->assertInternalType( 'array', $result );
			$this->assertNotEmpty( $result );

			$this->assertFileExists( $dir );
			$this->assertFileExists( $dir . '/redirection-sv_SE.mo' );
			$this->assertFileExists( $dir . '/redirection-sv_SE.po' );

		} catch ( \Exception $e ) {
			var_dump( $e->getMessage() );
		}
	}

}
