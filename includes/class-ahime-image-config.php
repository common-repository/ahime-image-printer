<?php

/**
 * The product configuration.
 */
class Ahime_Image_Config {
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
	 * Register the JavaScript for the users area.
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		$page_id = get_option( 'ahime-page', false );
		$custom  = filter_input( INPUT_GET, 'custom' );
		if ( isset( $custom ) && $page_id === $custom ) {
			wp_enqueue_script(
				'fabric-js',
				plugin_dir_url( __FILE__ ) . 'js/fabric.min.js',
				array( 'jquery' ),
				$this->version,
				false
			);

			wp_enqueue_script(
				'editor-js',
				plugin_dir_url( __FILE__ ) . 'js/editor.js',
				array( 'jquery' ),
				$this->version,
				false
			);

			wp_enqueue_script(
				'printing-perfect-scrollbar-js',
				plugin_dir_url( __FILE__ ) . 'js/printing.perfect.scrollbar.min.js',
				array(),
				'0.8.1',
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
	}


	/**
	 * Add designs to cart.
	 *
	 * @param array $final_canvas_parts The canvas data.
	 * @param array $related_variations The related variations.
	 * @return string|boolean
	 */
	private function add_designs_to_cart( $final_canvas_parts, $related_variations = array() ) {
		global $woocommerce;
		$newly_added_cart_item_key = false;

		$product  = wc_get_product( $related_variations['product_id'] );
		$quantity = $related_variations['qty'];
		if ( ! $product ) {
			return false;
		}

		if ( 'variable' === $product->get_type() || 'variation' === $product->get_type() ) {
			session_start();

			$variation_id = (int) filter_var( $related_variations['product_id'] );
			$session_key  = 'ahime-user-choice-' . $variation_id;

			if ( isset( $_SESSION[ $session_key ] ) && ! empty( $_SESSION[ $session_key ] ) ) {
				$variation = (int) $_SESSION[ sanitize_key( $session_key ) ];
			} else {
				$variation = array();
			}

			$product_id                = $product->get_parent_id();
			$newly_added_cart_item_key = $woocommerce->cart->add_to_cart(
				$product_id,
				$quantity,
				$variation_id,
				$variation,
				array(
					'ahime_image_generated_data' => $final_canvas_parts,
				)
			);
		} else {
			$newly_added_cart_item_key = $woocommerce->cart->add_to_cart(
				$related_variations['product_id'],
				$quantity,
				'',
				'',
				array(
					'ahime_image_generated_data' => $final_canvas_parts,
				)
			);
		}

		if ( method_exists( $woocommerce->cart, 'maybe_set_cart_cookies' ) ) {
			$woocommerce->cart->maybe_set_cart_cookies();
		}
		return $newly_added_cart_item_key;
	}

	/**
	 * Get order custom admin data.
	 *
	 * @param int   $item_id The item id.
	 * @param mixed $item The item data.
	 * @param mixed $product The product data.
	 * @return void
	 */
	public function get_order_custom_admin_data( $item_id, $item, $product ) {
		$order_data = wc_get_order_item_meta( $item_id, 'ahime_image_generated_data' );

		if ( isset( $order_data['size'] ) ) {
			?>
				<div class="printing-order-file">
					<span>
						<?php
						echo esc_html__( 'size', 'ahime-image-printer' );
						?>
					:
					<?php
						echo esc_html( $order_data['size'] );
					?>
					</span>
					<button class="button" data-id="<?php echo esc_attr( $order_data['final']['folder'] ); ?>">
						<?php
							echo esc_html__( 'Download file', 'ahime-image-printer' );
						?>
					</button>
				</div>				
			<?php
		}
	}

	/**
	 * Copy dir with content.
	 *
	 * @param string $source The dir source path.
	 * @param string $destination The destination source path.
	 * @return mixed
	 */
	public function copy_rep( $source, $destination ) {
		if ( ! WP_Filesystem() ) {
			exit;
		}
		global $wp_filesystem;
		$wp_filesystem->mkdir( $destination );
		return copy_dir( $source, $destination );
	}

	/**
	 * Get design page url.
	 *
	 * @return boolean|int
	 */
	public function get_design_page_url() {
		$page_id = get_option( 'ahime-page', false );
		if ( ! is_nan( $page_id ) && get_permalink( $page_id ) ) {
			return get_permalink( $page_id ) . '?custom=' . $page_id;
		} else {
			return false;
		}
	}

	/**
	 * Add image to cart by ajax.
	 *
	 * @return void
	 */
	public function add_image_to_cart_by_ajax() {
		$cart_url = wc_get_cart_url();
		if ( isset( $_POST['image_data'] ) ) { // phpcs:ignore
			$found = array(
				'6x4'  => array(),
				'8x6'  => array(),
				'10x8' => array(),
				'12x8' => array(),
				'7x5'  => array(),
			);

			$result     = array();
			$data       = json_decode( filter_input( INPUT_POST, 'image_data' ), true );
			$product_id = filter_input( INPUT_POST, 'product_id' );
			$qty        = filter_input( INPUT_POST, 'quantity' );

			$old_folder_name = uniqid();
			$compresed_link  = AHIME_IMAGE_ORDER_UPLOAD_PATH . DIRECTORY_SEPARATOR . $old_folder_name;

			foreach ( $found as $size => $none ) {
				$old_name = uniqid();
				foreach ( $data as $key => $value ) {
					if ( $value['bound'] === $size ) {
						if ( isset( $found[ $size ]['qty'] ) ) {
							$qtys = $found[ $size ]['qty'] + $value['qty'];
						} else {
							$qtys = $value['qty'];
						}
						$original_name = explode( '.', $value['name'] );
						$file_name     = uniqid();
						$folder_name   = $old_name . DIRECTORY_SEPARATOR . $size;
						$this->save_canvas_part_image( $value['url'], $folder_name, $file_name );
						if ( 'svg' !== $original_name[1] ) {
							$this->save_canvas_part_image( $value['original'], $folder_name, $original_name );
						} else {
							$this->save_svg_image( $value['original'], $folder_name, $original_name[0] );
						}

						$found[ $size ] = array(
							'product_id' => $product_id,
							'qty'        => $qtys,
							'folder'     => $old_name,
						);
					}
				}
			}

			foreach ( $found as $key => $value ) {
				if ( count( $value ) >= 1 ) {
					$final = array(
						'size'  => $key,
						'final' => array(
							'url'    => AHIME_IMAGE_ORDER_UPLOAD_URL . DIRECTORY_SEPARATOR . $value['folder'],
							'path'   => AHIME_IMAGE_ORDER_UPLOAD_PATH . DIRECTORY_SEPARATOR . $value['folder'],
							'folder' => $value['folder'],
						),
					);

					$key = $this->add_designs_to_cart( $final, $value );
					update_option( $key, AHIME_IMAGE_ORDER_UPLOAD_PATH . DIRECTORY_SEPARATOR . $value['folder'] );
					array_push( $result, $key );
				}
			}

			if ( ! in_array( false, $result, true ) && count( $result ) >= 1 ) {
				echo esc_html( $cart_url );
			} else {
				echo 'echec';
			}

			die;
		}

		echo 'echec';
		die;
	}

	/**
	 * Display information on cart.
	 *
	 * @param array $item_data The item data.
	 * @param array $cart_item The cart item data.
	 * @return array
	 */
	public function display_cart_item_custom_meta_data( $item_data, $cart_item ) {
		$meta_key = 'ahime_image_generated_data';
		if ( isset( $cart_item[ $meta_key ] ) ) {
			$item_data[] = array(
				'key'   => 'size',
				'value' => $cart_item[ $meta_key ]['size'],
			);
		}
		return $item_data;
	}

	/**
	 * Save order information.
	 *
	 * @param object $item The item data.
	 * @param string $cart_item_key The cart item key.
	 * @param array  $values The cart values.
	 * @param array  $order The order data.
	 * @return void
	 */
	public function save_cart_item_custom_meta_as_order_item_meta( $item, $cart_item_key, $values, $order ) {
		$meta_key = 'ahime_image_generated_data';
		if ( isset( $values[ $meta_key ] ) ) {
			$item->update_meta_data( $meta_key, $values[ $meta_key ] );
		}
	}

	/**
	 * Creates a compressed zip file.
	 *
	 * @param string $source The data source.
	 * @param string $destination The sip destination.
	 * @return boolean
	 */
	private function zip_data( $source, $destination ) {
		$dir    = opendir( $source );
		$result = ( $dir === false ? false : true ); // phpcs:ignore

		if ( false !== $result ) {

			$root_path = realpath( $source );

			// Initialize archive object.
			$zip         = new ZipArchive();
			$zipfilename = $destination . '.zip';
			$zip->open( $zipfilename, ZipArchive::CREATE | ZipArchive::OVERWRITE );

			// Create recursive directory iterator.
			$files = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $root_path ), RecursiveIteratorIterator::LEAVES_ONLY );

			foreach ( $files as $name => $file ) {
				// Skip directories (they would be added automatically).
				if ( ! $file->isDir() ) {
					// Get real and relative path for current file.
					$file_path     = $file->getRealPath();
					$relative_path = substr( $file_path, strlen( $root_path ) + 1 );

					// Add current file to archive.
					$zip->addFile( $file_path, $relative_path );
				}
			}

			// Zip archive will be created only after closing object.
			$zip->close();

			return true;
		} else {
			return false;
		}

	}

	/**
	 * Save canvas image.
	 *
	 * @param string $image The image data.
	 * @param string $folder_name The folder name.
	 * @param string $file_name The file name.
	 * @return mixed
	 */
	private function save_canvas_part_image( $image, $folder_name, $file_name ) {
		$upload_dirs = AHIME_IMAGE_ORDER_UPLOAD_PATH . DIRECTORY_SEPARATOR . $folder_name;
		$upload_dir  = $upload_dirs . DIRECTORY_SEPARATOR;
		$img         = $image;
		if ( is_array( $file_name ) ) {
			$img = str_replace( 'data:image/' . $file_name[1] . ';base64,', '', $img );
		} else {
			$img = str_replace( 'data:image/png;base64,', '', $img );
		}
		$img  = str_replace( ' ', '+', $img );
		$data = base64_decode( $img ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
		if ( is_array( $file_name ) ) {
			$file = $upload_dir . $file_name[0] . '.' . $file_name[1];
		} else {
			$file = $upload_dir . $file_name . '.png';
		}
		wp_mkdir_p( $upload_dirs );
		$success = file_put_contents( $file, $data ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_file_put_contents
		return $success;
	}

	/**
	 * Save svg image.
	 *
	 * @param string $data The image data.
	 * @param string $folder_name The folder name.
	 * @param string $file_name The file name.
	 * @return boolean
	 */
	public function save_svg_image( $data, $folder_name, $file_name ) {

		$folder = AHIME_IMAGE_ORDER_UPLOAD_PATH . DIRECTORY_SEPARATOR . $folder_name;

		$data   = str_replace( 'data:image/svg+xml;base64,', '', $data );
		$data   = str_replace( ' ', '+', $data );
		$data   = base64_decode( $data ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
		$result = false;

		if ( ! file_exists( $folder ) ) {
			wp_mkdir_p( $folder );
		}

		if ( $file = fopen( $folder . DIRECTORY_SEPARATOR . $file_name . '.svg', 'x+' ) ) { // phpcs:ignore WordPress.CodeAnalysis.AssignmentInCondition.Found, Squiz.PHP.DisallowMultipleAssignments.FoundInControlStructure, WordPress.WP.AlternativeFunctions.file_system_read_fopen
			if ( fwrite( $file, $data ) ) { // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fwrite
				$result = true;
			} else {
				$result = false;
			}
			fclose( $file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fclose
		} else {
			$result = false;
		}

		return $result;
	}

	/**
	 * Copy directory with content to destination
	 *
	 * @param string $source The source dir path.
	 * @param string $destination The dir destination.
	 * @return void
	 */
	public function copy_to_dir( $source, $destination ) {
		if ( is_dir( $source ) ) {
			@mkdir( $destination ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			$directory = dir( $source );
			while ( false !== ( $readdirectory = $directory->read() ) ) { // phpcs:ignore WordPress.CodeAnalysis.AssignmentInCondition.FoundInWhileCondition
				if ( '.' === $readdirectory || '..' === $readdirectory ) {
					continue;
				}
				$path_dir = $source . '/' . $readdirectory;
				if ( is_dir( $path_dir ) ) {
					copy_directory( $path_dir, $destination . '/' . $readdirectory );
					continue;
				}
				copy( $path_dir, $destination . '/' . $readdirectory );
			}

			$directory->close();
		} else {
			copy( $source, $destination );
		}
	}

	/**
	 * Delete nonempty folder.
	 *
	 * @param string $dir_path The path.
	 * @return void
	 */
	private static function del_folder( $dir_path ) {
		if ( ! is_dir( $dir_path ) ) {
			throw new InvalidArgumentException( "$dir_path must be a directory" );
		}
		if ( '/' !== substr( $dir_path, strlen( $dir_path ) - 1, 1 ) ) {
			$dir_path .= '/';
		}
		$files = glob( $dir_path . '*', GLOB_MARK );
		foreach ( $files as $file ) {
			if ( is_dir( $file ) ) {
				self::del_folder( $file );
			} else {
				unlink( $file );
			}
		}
		rmdir( $dir_path );
	}


	/**
	 * Set if directory is empty.
	 *
	 * @param string $dir The dir path.
	 * @return boolean
	 */
	public function dir_is_empty( $dir ) {
		if ( $handle = opendir( $dir ) ) { // phpcs:ignore WordPress.CodeAnalysis.AssignmentInCondition.Found, Squiz.PHP.DisallowMultipleAssignments.FoundInControlStructure
			while ( false !== ( $entry = readdir( $handle ) ) ) { // phpcs:ignore WordPress.CodeAnalysis.AssignmentInCondition.FoundInWhileCondition
				if ( '.' !== $entry && '..' !== $entry ) {
					closedir( $handle );
					return false;
				}
			}
			closedir( $handle );
		}
		return true;
	}

	/**
	 * Generate and download zip file.
	 *
	 * @return void
	 */
	public function ahime_image_download_zip() {
		$folder_name    = filter_input( INPUT_POST, 'folder' );
		$compresed_path = AHIME_IMAGE_ORDER_UPLOAD_PATH . DIRECTORY_SEPARATOR . $folder_name;
		$destination    = AHIME_IMAGE_TMP_UPLOAD_PATH . DIRECTORY_SEPARATOR . $folder_name;
		$zip_url        = AHIME_IMAGE_TMP_UPLOAD_URL . DIRECTORY_SEPARATOR . $folder_name . '.zip';

		if ( ! file_exists( $destination . '.zip' ) ) {
			if ( $this->zip_data( $compresed_path, $destination ) ) {
				echo esc_url( $zip_url );
			} else {
				echo 'error';
			}
		} else {
			echo esc_url( $zip_url );
		}

		die;
	}

	/**
	 * Delete temp file.
	 *
	 * @return void
	 */
	public function image_delete_tmp_file() {
		try {
			if ( ! $this->dir_is_empty( AHIME_IMAGE_TMP_UPLOAD_PATH ) ) {
				$this->del_folder( AHIME_IMAGE_TMP_UPLOAD_PATH );
			}
			echo 'success';
			die;
		} catch ( Exception $e ) {
			echo 'error';
			die;
		}
	}

	/**
	 * Delete cart file.
	 *
	 * @param string $cart_item_key The cart item key.
	 * @param mixed  $cart The cart data.
	 * @return void
	 */
	public function printing_delete_cart_file( $cart_item_key, $cart ) {
		$dirpath = get_option( $cart_item_key, false );
		if ( ! $this->dir_is_empty( $dirpath ) ) {
			$this->del_folder( $dirpath );
		}
	}

	/**
	 * Add order custom image to mail.
	 *
	 * @param array $attachments The attachments.
	 * @param array $status The status.
	 * @param mixed $order The order.
	 * @return array The attachments.
	 */
	public function add_order_custom_image_to_mail( $attachments, $status, $order ) {
		$allowed_statuses = array( 'new_order', 'customer_invoice', 'customer_processing_order', 'customer_completed_order' );
		if ( isset( $status ) && in_array( $status, $allowed_statuses, true ) ) {
			$items       = $order->get_items();
			$folder_name = 'order-' . $order->get_id();
			$destination = AHIME_IMAGE_TMP_UPLOAD_PATH .
			DIRECTORY_SEPARATOR .
			$folder_name;
			wp_mkdir_p( $destination );
			foreach ( $items as $order_item_id => $item ) {
				$upload_dir = wp_upload_dir();
				if ( isset( $item['ahime_image_generated_data']['final']['path'] ) ) {
					$size   = $item['ahime_image_generated_data']['size'];
					$source = $item['ahime_image_generated_data']['final']['path'] .
					DIRECTORY_SEPARATOR . $size;
					$this->copy_to_dir( $source, $destination . DIRECTORY_SEPARATOR . $size );
				}
			}
			$this->zip_data( $destination, $destination );
			array_push( $attachments, $destination . '.zip' );
			$this->del_folder( $destination );
		}
		return str_replace( '"', '', $attachments );
	}

}

?>
