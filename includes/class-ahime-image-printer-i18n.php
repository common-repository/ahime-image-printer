<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://github.com/nahim-salami/
 * @since      1.0.0
 *
 * @package    Ahime_Image_Printer
 * @subpackage Ahime_Image_Printer/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Ahime_Image_Printer
 * @subpackage Ahime_Image_Printer/includes
 * @author     Ahime <nahim.salami@outlook.fr>
 */
class Ahime_Image_Printer_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'ahime-image-printer',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
