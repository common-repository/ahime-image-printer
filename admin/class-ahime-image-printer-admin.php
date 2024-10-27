<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/nahim-salami/
 * @since      1.0.0
 *
 * @package    Ahime_Image_Printer
 * @subpackage Ahime_Image_Printer/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Ahime_Image_Printer
 * @subpackage Ahime_Image_Printer/admin
 * @author     Ahime <nahim.salami@outlook.fr>
 */
class Ahime_Image_Printer_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Ahime_Image_Printer_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Ahime_Image_Printer_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style(
			$this->plugin_name,
			plugin_dir_url( __FILE__ ) . 'css/ahime-image-printer-admin.css',
			array(),
			$this->version,
			'all'
		);

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Ahime_Image_Printer_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Ahime_Image_Printer_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script(
			$this->plugin_name,
			plugin_dir_url( __FILE__ ) . 'js/ahime-image-printer-admin.js',
			array( 'jquery' ),
			$this->version,
			false
		);

		wp_localize_script(
			$this->plugin_name,
			'ahime',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
			)
		);
	}


	/**
	 * Add admin menu.
	 *
	 * @return void
	 */
	public function add_submenu() {
		if ( class_exists( 'WooCommerce' ) ) {
			$icon = AHIME_URL . 'admin/assets/images/resize_ahime.png';
			add_menu_page(
				__( 'Ahime', 'ahime-image-printer' ),
				__( 'Ahime', 'ahime-image-printer' ),
				'manage_options',
				'image-printing-setting',
				array( $this, 'set_admin_setting_content' ),
				$icon,
				10
			);
		}
	}

	/**
	 * Set admin settings content.
	 *
	 * @return void
	 */
	public function set_admin_setting_content() {
		$page_id         = get_all_page_ids();
		$defined_page_id = get_option( 'ahime-page', false );
		?>
			<div>
				<h2 class="ahime-image-msg">
					<?php
						echo esc_html__( 'Setting', 'ahime-image-printer' );
					?>
				</h2>
				<h3 class="ahime-image-msg">
					<?php
						echo esc_html__( 'Define configurator page', 'ahime-image-printer' );
					?>
				</h3>
				<div class="printing-admin-row">
					<select id="setting-page" name="ahime-page" class="printing-admin-select">
						<?php
						if ( ! empty( $defined_page_id ) ) {
							?>
									<option value="<?php echo esc_attr( $defined_page_id ); ?>">
										<?php echo esc_html( get_the_title( $defined_page_id ) ); ?>
									</option>
								<?php
						} else {
							?>
									<option value="">Select page </option>
							<?php
						}
						foreach ( $page_id as $key => $value ) {
							$page_name = get_the_title( $value );
							?>
								<option value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $page_name ); ?></option>
								<?php
						}
						?>
					</select>
					<button type="submit" id="image_printing_save_setting" class="printing-admin-btn">
						<?php
							echo esc_html__( 'Submit', 'ahime-image-printer' );
						?>
					</button>
				</div>
			</div>

			<div>
				<h3 class="ahime-image-msg">
					<?php
						echo esc_html__( 'Clear temp cache', 'ahime-image-printer' );
					?>
				</h3>
				<button id="printing-clear-tmp" class="printing-admin-btn">
					<?php
						echo esc_html__( 'Clear', 'ahime-image-printer' );
					?>
				</button>
			</div>
		<?php
	}

	/**
	 * Get product config selector.
	 *
	 * @return void
	 */
	public function get_product_config_selector() {
		$product = wc_get_product();
		if ( ! $product || 'simple' !== $product->get_type() ) {
			return;
		}
		echo wp_kses_post( $this->get_variation_product_config_selector( null, null, $product ) );
	}


	/**
	 * Get product selector.
	 *
	 * @param mixed $loop Loop item.
	 * @param array $variation_data Variation data.
	 * @param mixed $variation Variation.
	 * @return void
	 */
	public function get_variation_product_config_selector( $loop, $variation_data, $variation ) {
		if ( isset( $variation->ID ) ) {
			$variation_id = $variation->ID;
		} else {
			$variation_id = $variation->get_id();
		}

		if ( isset( $variation->post_type ) ) {
			$product_type = $variation->post_type;
		} else {
			$product_type = $variation->get_type();
		}

		$post_meta = get_post_meta( $variation_id, 'ahime-image', false );
		if ( ! empty( $post_meta ) ) {
			$post_meta = $post_meta[ array_key_last( $post_meta ) ];
			$config    = true;
		} else {
			$config = false;
		}
		?>
			<p class="form-field _regular_price_field ">
				<label>
					<?php
						echo esc_html__( 'Printing Image config', 'ahime-image-printer' );
					?>
				</label>
				<select name="ahime-image[<?php echo esc_attr( $variation_id ); ?>][config]" class="printing-image" data-type="<?php echo esc_attr( $product_type ); ?>">
					<?php
					if ( $config ) {
						if ( 'yes' === $post_meta[ $variation_id ]['config'] ) {
							?>
									<option value="yes">
										<?php
											echo esc_html__( 'yes', 'ahime-image-printer' );
										?>
									</option>
									<option value="no">
										<?php
											echo esc_html__( 'no', 'ahime-image-printer' );
										?>
									</option>
								<?php
						} else {
							?>
									<option value="no">
									<?php
										echo esc_html__( 'no', 'ahime-image-printer' );
									?>
									</option>
									<option value="yes">
										<?php
											echo esc_html__( 'yes', 'ahime-image-printer' );
										?>
									</option>
								<?php
						}

						if ( 'yes' === $post_meta[ $variation_id ]['config'] ) {
							$config = true;
						}
					} else {
						?>
								<option value="no">
								<?php
									echo esc_html__( 'no', 'ahime-image-printer' );
								?>
								</option>
								<option value="yes">
								<?php
									echo esc_html__( 'yes', 'ahime-image-printer' );
								?>
								</option>
							<?php
					}
					?>
				</select>
				<input type="hidden" name="ahime-nonce-security" value="<?php echo esc_html( wp_create_nonce( 'ahime-nonce-security' ) ); ?>">
			</p>
		<?php
	}


	/**
	 * Save user config.
	 *
	 * @param int $variation_id The variation id.
	 * @return void
	 */
	public function save_config( $variation_id ) {
		$meta_key = 'ahime-image';
		$meta     = filter_input( INPUT_POST, 'ahime-image' );
		$nonce    = filter_input(
			INPUT_POST,
			'ahime-nonce-security',
			FILTER_DEFAULT,
			FILTER_REQUIRE_ARRAY
		);

		if ( wp_verify_nonce( $nonce, 'ahime-nonce-security' ) ) {
			if ( ! empty( $meta ) ) {
				$variation = wc_get_product( $variation_id );
				$old_metas = get_post_meta( $variation_id, $meta_key, true );
				if ( empty( $old_metas ) ) {
					$old_metas = array();
				}

				if ( empty( $old_metas ) ) {
					add_post_meta( $variation_id, $meta_key, $meta );
				} else {
					update_post_meta( $variation_id, $meta_key, $meta );
				}
			}
		}

	}

	/**
	 * Save settings.
	 *
	 * @return void
	 */
	public function save_setting() {
		$meta = filter_input( INPUT_POST, 'setting' );
		if ( ! empty( $meta ) ) {
			$option = get_option( 'ahime-page', false );
			update_option( 'ahime-page', $meta );
			$msg = array(
				'msg'     => __( 'The configurator page has been added', 'ahime-image-printer' ),
				'success' => true,
			);
		} else {
			$msg = array(
				'msg'     => __( 'An error has occurred. The page could not be added', 'ahime-image-printer' ), 
				'success' => false,
			);
		}

		echo wp_json_encode( $msg );

		die;
	}

	/**
	 * Alerts the administrator if the minimum requirements are not met.
	 */
	public function notify_prerequisites() {
		global $atd_settings;
		$minimum_required_parameters = array(
			'memory_limit'        => array( 128, 'M' ),
			'post_max_size'       => array( 8, 'M' ),
			'upload_max_filesize' => array( 32, 'M' ),
		);
		$messages                    = array();
		$image_page_id               = get_option( 'ahime-page', false );
		$settings_url                = get_bloginfo( 'url' ) . '/wp-admin/admin.php?page=image-printing-setting';

		$permalinks_structure = get_option( 'permalink_structure' );

		if ( ! class_exists( 'WooCommerce' ) ) {
			$messages[] = 'WooCommerce is not installed on your website. You will not be able to use the features of the plugin';
		} elseif ( empty( $image_page_id ) ) {
			$messages[] = "The design page is not defined. Please set one here <a href='" . esc_html( $settings_url ) . "'>plugin settings page</a>: .</p>";
		} elseif ( ! extension_loaded( 'zip' ) ) {
			$messages[] = "ZIP extension not loaded on this server. You won't be able to generate zip outputs.</p>";
		}

		foreach ( $minimum_required_parameters as $key => $min_arr ) {
			$defined_value = ini_get( $key );
			if ( ! stristr( $defined_value, 'G', true ) ) {
				$defined_value_int = str_replace( $min_arr[1], '', $defined_value );
				if ( $defined_value_int < $min_arr[0] && -1 !== $defined_value_int ) {
					$messages[] = "Your PHP setting <b>$key</b> is currently set to <b>$defined_value</b>. We recommand to set this value at least to <b>" . implode( '', $min_arr ) . '</b> to avoid any issue with our plugin.<br><br><b>' . esc_html__( 'How to fix this: You can edit your php.ini file to increase the specified variables to the recommanded values or you can ask your hosting company to make the changes for you.', 'atd' ) . '</b>';
				}
			}
		}

		if ( strpos( $permalinks_structure, 'index.php' ) !== false ) {
			$message .= 'Your <a href="' . esc_url( admin_url() . 'options-permalink.php' ) . '">permalinks</a> structure is currently set to <b>custom</b> with index.php present in the structure. We recommand to set this value to <b>Post name</b> to avoid any issue with our plugin.<br>';
		}

		if ( isset( $messages ) && ! empty( $messages ) ) {
			foreach ( $messages as $key => $message ) {
				?>
				<div class="error">
					<p>
						<b>
							<?php
								echo esc_html__( 'Printing Image', 'ahime-image-printer' );
							?>
							:
						</b>
						<br>
						<?php echo esc_html( $message ); ?>
					</p>
				</div>
				<?php
			}
		}
	}

}
