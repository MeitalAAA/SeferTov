<?php

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://320up.com
 * @since      3.1.0
 *
 * @package    Woo_Align
 * @subpackage Woo_Align/includes
 * @author     320up <support@320up.com>
 */
class Woo_Align_i18n {

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    3.1.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'woo-align',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}

}
