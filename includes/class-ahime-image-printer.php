<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://github.com/nahim-salami/
 * @since      1.0.0
 *
 * @package    Ahime_Image_Printer
 * @subpackage Ahime_Image_Printer/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Ahime_Image_Printer
 * @subpackage Ahime_Image_Printer/includes
 * @author     Ahime <nahim.salami@outlook.fr>
 */
class Ahime_Image_Printer {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Ahime_Image_Printer_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'AHIME_IMAGE_PRINTER_VERSION' ) ) {
			$this->version = AHIME_IMAGE_PRINTER_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'ahime-image-printer';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Ahime_Image_Printer_Loader. Orchestrates the hooks of the plugin.
	 * - Ahime_Image_Printer_i18n. Defines internationalization functionality.
	 * - Ahime_Image_Printer_Admin. Defines all hooks for the admin area.
	 * - Ahime_Image_Printer_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ahime-image-printer-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ahime-image-printer-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-ahime-image-printer-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-ahime-image-printer-public.php';

		$this->loader = new Ahime_Image_Printer_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Ahime_Image_Printer_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Ahime_Image_Printer_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin  = new Ahime_Image_Printer_Admin( $this->get_plugin_name(), $this->get_version() );
		$plugin_config = new Ahime_Image_Config( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_notices', $plugin_admin, 'notify_prerequisites' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_submenu' );

		$this->loader->add_action( 'woocommerce_product_options_general_product_data', $plugin_admin, 'get_product_config_selector' );
		$this->loader->add_action( 'woocommerce_product_after_variable_attributes', $plugin_admin, 'get_variation_product_config_selector', 10, 3 );
		$this->loader->add_action( 'woocommerce_save_product_variation', $plugin_admin, 'save_config', 10, 1 );

		$this->loader->add_action( 'wp_ajax_ahime_save_setting', $plugin_admin, 'save_setting' );
		$this->loader->add_action( 'wp_ajax_nopriv_ahime_save_setting', $plugin_admin, 'save_setting' );

		$this->loader->add_action( 'save_post_product', $plugin_admin, 'save_config' );
		$this->loader->add_action( 'wp_ajax_ahime_image_download_zip', $plugin_config, 'ahime_image_download_zip' );
		$this->loader->add_action( 'wp_ajax_nopriv_ahime_image_download_zip', $plugin_config, 'ahime_image_download_zip' );
		$this->loader->add_action( 'wp_ajax_image_delete_tmp_file', $plugin_config, 'image_delete_tmp_file' );
		$this->loader->add_action( 'wp_ajax_nopriv_image_delete_tmp_file', $plugin_config, 'image_delete_tmp_file' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Ahime_Image_Printer_Public( $this->get_plugin_name(), $this->get_version() );
		$plugin_config = new Ahime_Image_Config( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		$this->loader->add_action( 'init', $plugin_public, 'init_shortcode_handler' );
		$this->loader->add_action( 'woocommerce_after_add_to_cart_button', $plugin_public, 'get_customize_btn' );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_config, 'enqueue_scripts' );
		$this->loader->add_action( 'wp_ajax_ahime_image_add_to_cart', $plugin_config, 'add_image_to_cart_by_ajax' );
		$this->loader->add_action( 'wp_ajax_nopriv_ahime_image_add_to_cart', $plugin_config, 'add_image_to_cart_by_ajax' );
		$this->loader->add_action( 'wp_ajax_ahime_image_save_user_choice', $plugin_public, 'save_user_choice' );
		$this->loader->add_action( 'wp_ajax_nopriv_ahime_image_save_user_choice', $plugin_public, 'save_user_choice' );

		$this->loader->add_action( 'woocommerce_after_order_itemmeta', $plugin_config, 'get_order_custom_admin_data', 10, 3 );
		$this->loader->add_filter( 'woocommerce_get_item_data', $plugin_config, 'display_cart_item_custom_meta_data', 10, 2 );
		$this->loader->add_action( 'woocommerce_checkout_create_order_line_item', $plugin_config, 'save_cart_item_custom_meta_as_order_item_meta', 10, 4 );
		// $this->loader->add_action( 'woocommerce_cart_item_removed', $plugin_config, 'printing_delete_cart_file', 10, 2 );
		// Send custom image as attachement.
		$this->loader->add_filter( 'woocommerce_email_attachments', $plugin_config, 'add_order_custom_image_to_mail', 10, 3 );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Ahime_Image_Printer_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
