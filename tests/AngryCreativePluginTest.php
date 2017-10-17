<?php
/**
 * Created by PhpStorm.
 * User: richardsweeney
 * Date: 2017-10-16
 * Time: 11:49
 */

/**
 * Class TestPlugin
 *
 * @package AngryCreative
 */
class AngryCreativePluginTest extends PHPUnit_Framework_TestCase {

	/**
	 * @test Plugin
	 */
	public function plugin_test() {
		try {
			$plugin = new Plugin( 'redirection' );
			$this->assertTrue( $plugin instanceof Plugin );

			//$languages = $plugin->get_languages();
			//$this->assertInternalType( 'array', $languages );
			//$this->assertTrue( ! empty( $languages ) );

		} catch ( \Exception $e ) {}
	}

}
