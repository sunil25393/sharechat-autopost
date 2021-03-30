<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://sunilprajapati.com/
 * @since      1.0.0
 *
 * @package    Sharechat_Autopost
 * @subpackage Sharechat_Autopost/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Sharechat_Autopost
 * @subpackage Sharechat_Autopost/includes
 * @author     Sunil Prajapati <sdprajapati999@gmail.com>
 */
class Sharechat_Autopost_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'sharechat-autopost',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
