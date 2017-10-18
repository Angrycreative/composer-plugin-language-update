<?php
/**
 * Created by PhpStorm.
 * User: richardsweeney
 * Date: 2017-10-16
 * Time: 11:49
 */

namespace AngryCreative;

use AngryCreative\Plugin;

/**
 * Class TestPlugin
 *
 * @package AngryCreative
 */
class PluginTest extends \PHPUnit_Framework_TestCase {

	public function testMe() {
		$this->assertTrue( true );
	}

	public function testPlugin() {
		try {
			$plugin = new Plugin( 'redirection' );
		} catch ( \Exception $e ) {
			var_dump( $e->getMessage() );
		}
	}

	//public function plugin_test() {
	//	try {
	//		$plugin = new Plugin( 'redirection' );
	//		$this->assertTrue( $plugin instanceof Plugin );
	//
			//$languages = $plugin->get_languages();
			//$this->assertInternalType( 'array', $languages );
			//$this->assertTrue( ! empty( $languages ) );

		//} catch ( \Exception $e ) {}
	//}

}
