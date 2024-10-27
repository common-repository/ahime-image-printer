<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://github.com/nahim-salami/
 * @since      1.0.0
 *
 * @package    Ahime_Image_Printer
 * @subpackage Ahime_Image_Printer/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Ahime_Image_Printer
 * @subpackage Ahime_Image_Printer/public
 * @author     Ahime <nahim.salami@outlook.fr>
 */
class Ahime_Image_Printer_Public {

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
	 * @param      string $plugin_name       The name of the plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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
			plugin_dir_url( __FILE__ ) . 'css/ahime-image-printer-public.css',
			array(),
			$this->version,
			'all'
		);

		wp_enqueue_style(
			'boxicons',
			plugin_dir_url( __FILE__ ) . 'css/boxicons/css/boxicons.min.css',
			array(),
			$this->version,
			'all'
		);

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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
			plugin_dir_url( __FILE__ ) . 'js/ahime-image-printer-public.js',
			array( 'jquery' ),
			$this->version,
			false
		);

	}

	/**
	 * Display of configurator.
	 *
	 * @param mixed $content The html content.
	 * @return mixed
	 */
	public function printing_shortcode_designer_display( $content ) {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/printing-shortcode.php';
		$content .= ob_get_clean();
		return $content;
	}

	/**
	 * Display customizing btn.
	 *
	 * @return void
	 */
	public function get_customize_btn() {
		$config     = new Ahime_Image_Config( $this->plugin_name, $this->version );
		$product_id = get_the_ID();
		$product    = wc_get_product( $product_id );
		$meta_key   = 'ahime-image';
		$hide_btn   = array();

		if ( ! $product || ! $product->is_in_stock() ) {
			return;
		}

		if ( 'variable' === $product->get_type() ) {
			$variations    = $product->get_available_variations();
			$variations_id = wp_list_pluck( $variations, 'variation_id' );
			foreach ( $variations_id as $key => $value ) {
				$meta        = get_post_meta( $value, $meta_key, true );
				$var_product = wc_get_product( $value );
				$price       = $var_product->get_price();
				if ( ! empty( $meta ) && 'yes' === $meta[ array_key_last( $meta ) ]['config'] && ! empty( $price ) ) {
					array_push( $hide_btn, $value );
				}
			}
			$btn_class = 'ahime-image-container_variable';

			$url = $config->get_design_page_url();
		} else {
			$meta  = get_post_meta( $product_id, $meta_key, true );
			$price = $product->get_price();
			if ( ! empty( $meta ) && 'yes' === $meta[ array_key_last( $meta ) ]['config'] && ! empty( $price ) ) {
				$hide_btn = false;
			} else {
				$hide_btn = true;
			}
			$btn_class = 'ahime-image-container';
			$url       = $config->get_design_page_url() . '&id=' . $product_id;
		}

		$data = array(
			'url' => $url,
		);
		?>
		<div class="<?php echo esc_attr( $btn_class ); ?>" data-type="<?php echo esc_attr( $product->get_type() ); ?>" data-id="<?php echo esc_attr( $product_id ); ?>">
			<a href="<?php echo esc_attr( $url ); ?>" class="button alt">
				<?php
					echo esc_html__( 'Choose your photo', 'ahime-image-printer' );
				?>
			</a>
			<script>
				var design_page_url = "<?php echo wp_json_encode( $data ); ?>";
				var hide_btn = <?php echo wp_json_encode( $hide_btn ); ?> ;
			</script>
		</div>
		<?php
	}

	/**
	 * Init shortcode handler.
	 *
	 * @return void
	 */
	public function init_shortcode_handler() {
		$page_id = get_option( 'ahime-page', false );
		$custom  = filter_input( INPUT_GET, 'custom' );
		if ( ! empty( $custom ) && $page_id === $custom ) {
			add_filter( 'the_content', array( $this, 'printing_shortcode_designer_display' ) );
		}
	}

	/**
	 * Save user choice for variable product on product page.
	 *
	 * @return void
	 */
	public function save_user_choice() {
		if ( isset( $_POST['ahime_image_data_attr'] ) ) { // phpcs:ignore
			$image_attr = json_decode( filter_input( INPUT_POST, 'ahime_image_data_attr' ), true );
			$can_save   = true;

			foreach ( $image_attr as $key => $value ) {
				if ( empty( $value ) ) {
					$can_save = false;
				}
			}

			if ( $can_save ) {
				session_start();
				$variation_id                                     = filter_input( INPUT_POST, 'variation_id' );
				$_SESSION[ 'ahime-user-choice-' . $variation_id ] = $image_attr;
				echo 'success';
				die;
			}
		}

		echo 'fail';
		die;
	}

}
