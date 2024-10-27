<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/nahim-salami/
 * @since             1.0.0
 * @package           Ahime_Image_Printer
 *
 * @wordpress-plugin
 * Plugin Name:       Ahime Image Printer
 * Plugin URI:        https://github.com/ahiime/
 * Description:       A simple tool to resize and print images
 * Version:           1.0.0
 * Author:            Ahime
 * Author URI:        https://github.com/nahim-salami/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ahime-image-printer
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'AHIME_IMAGE_PRINTER_VERSION', '1.0.0' );
define( 'AHIME_IMAGE_PRINTER_SLUG', 'ahime-image-printer' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-ahime-image-printer-activator.php
 */
function activate_ahime_image_printer() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ahime-image-printer-activator.php';
	Ahime_Image_Printer_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-ahime-image-printer-deactivator.php
 */
function deactivate_ahime_image_printer() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ahime-image-printer-deactivator.php';
	Ahime_Image_Printer_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_ahime_image_printer' );
register_deactivation_hook( __FILE__, 'deactivate_ahime_image_printer' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-ahime-image-printer.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-ahime-image-config.php';
require plugin_dir_path( __FILE__ ) . 'includes/functions.php';

/**
 * Define constant variables.
 */
define( 'AHIME_IMAGE_PRINTER_URL', plugins_url( '/', __FILE__ ) );
define( 'AHIME_IMAGE_PRINTER_DIR', dirname( __FILE__ ) );

$date            = explode( '-', gmdate( 'y-m-j' ) );
$years           = intval( '20' . $date[0] ) + 1;
$upload_dir      = wp_upload_dir( $years . '/12', 'ahime' );
$generation_path = $upload_dir['path'] . '/ahime/';
$generation_url  = $upload_dir['url'] . '/ahime/';

define( 'AHIME_URL', plugins_url( '/', __FILE__ ) );

define( 'AHIME_IMAGE_TMP_UPLOAD_PATH', $generation_path . 'TMP' );
define( 'AHIME_IMAGE_TMP_UPLOAD_URL', $generation_url . 'TMP' );

define( 'AHIME_IMAGE_ORDER_UPLOAD_PATH', $generation_path . 'ORDER' );
define( 'AHIME_IMAGE_ORDER_UPLOAD_URL', $generation_url . 'ORDER' );

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_ahime_image_printer() {

	$plugin = new Ahime_Image_Printer();
	$plugin->run();

}
run_ahime_image_printer();
