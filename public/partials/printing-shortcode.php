<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link      https://github.com/nahim-salami/
 * @since      1.0.0
 *
 * @package    Ahime_Image_Printer
 * @subpackage Ahime_Image_Printer/public
 */

$product_id = filter_input( INPUT_GET, 'id' );
?>

<div class="printing-container">

	<div class="printing-header">

		<div class="printing-nav" data-zoom="3">

			<li class="printing-nav-item printing-upload">

				<span class="printing-nav-title"><?php echo esc_html__( 'Upload more', 'ahime-image-printer' ); ?></span>

				<i class="bx bx-upload printing-icon"></i>

			</li>

			<li class="printing-nav-item printing-edit-all">

				<span class="printing-nav-title"><?php echo esc_html__( 'Edit all', 'ahime-image-printer' ); ?></span>

				<i class="bx bxs-wrench printing-icon"></i>

			</li>

			<li class="printing-nav-item printing-less">

				<span class="printing-nav-title"><?php echo esc_html__( 'View less', 'ahime-image-printer' ); ?></span>

				<i class="bx bxs-minus-circle printing-icon"></i>

			</li>

			<li class="printing-nav-item printing-more">

				<span class="printing-nav-title"><?php echo esc_html__( 'View more', 'ahime-image-printer' ); ?></span>

				<i class="bx bxs-plus-circle printing-icon"></i>

			</li>

			<li class="printing-nav-item printing-continue">

				<span class="printing-nav-title"><?php echo esc_html__( 'Continue', 'ahime-image-printer' ); ?></span>

				<i class="bx bxs-chevron-right printing-icon"></i>

			</li>

		</div>

		<div class="printing-info">

			<div class="printing-info-item printing-info-image">

				<span class="printing-info-title"><?php echo esc_html__( 'Number of images: ', 'ahime-image-printer' ); ?></span>

				<span class="printing-badge printing-badge-image">1</span>

			</div>

			<div class="printing-info-item printing-info-print">

				<span class="printing-info-title"><?php echo esc_html__( 'Number of prints: ', 'ahime-image-printer' ); ?></span>

				<span class="printing-badge printing-badge-print">1</span>

			</div>
			
		</div>

	</div>

	<div class="printing-inner">

		<div class="printing-cards-groups">

			<div class="printing-card printing-zoom-3" id="clone-printing-card" style="display:none;">

				<div class="printing-card-img printing-edit-single" data-visible="false">
					
					<div class="printing-loader"><div class="printing-loader-ring"></div></div>

					<img src="#" class="printing-card-img-self" style="display:none">

				</div>

				<div class="printing-card-body">

					<h4 class="printing-card-title"><?php echo esc_html__( 'Picture1.png', 'ahime-image-printer' ); ?></h4>

					<div class="printing-card-groups">

						<div class="printing-field">

							<label for="printing-qty-1a" class="printing-label printing-label-qty"><?php echo esc_html__( 'Quantity', 'ahime-image-printer' ); ?></label>

							<input type="number" value="0" min="0" name="printing-qty-1a" id="printing-qty-1a" class="printing-form-field printing-form-field-qty printing-form-field-number">

						</div>

						<div class="printing-field">

							<label for="printing-qty-1b" class="printing-label printing-label-size"><?php echo esc_html__( 'Size', 'ahime-image-printer' ); ?></label>

							<select name="printing-qty-1b" id="printing-qty-1b" class="printing-form-field printing-form-field-size printing-form-field-select">

							<option value="6x4"><?php echo esc_html__( '6" x 4" Print', 'ahime-image-printer' ); ?></option>
							<option value="8x6"><?php echo esc_html__( '8" x 6" Print', 'ahime-image-printer' ); ?></option>
							<option value="10x8"><?php echo esc_html__( '10" x 8" Print', 'ahime-image-printer' ); ?></option>
							<option value="12x8"><?php echo esc_html__( '12" x 8" Print', 'ahime-image-printer' ); ?></option>
							<option value="7x5"><?php echo esc_html__( '7" x 5" Print', 'ahime-image-printer' ); ?></option>

							</select>

						</div>

					</div>

					<div class="printing-btns-actions">

						<button type="button" class="printing-btn printing-btn-primary printing-duplicate-card printing-mr-10"><?php echo esc_html__( 'Duplicate', 'ahime-image-printer' ); ?></button>
						<button type="button" class="printing-btn printing-btn-secondary printing-delete-card"><?php echo esc_html__( 'Remove', 'ahime-image-printer' ); ?></button>

					</div>

				</div>

			</div>
		</div>

	</div>

</div>

<!-- Preview Box Edit All Design Begin -->

<div class="printing-modal printing-preview-box-edit-all">

	<div class="printing-preview-title"><?php echo esc_html__( 'Change all prints', 'ahime-image-printer' ); ?></div>

	<div class="printing-icon-close printing-icon-edit-all-cross"><i class='bx bx-x'></i></div>

	<div class="printing-field">

		<label for="printing-edit-all-1" class="printing-label"><?php echo esc_html__( 'Change photos which are currently', 'ahime-image-printer' ); ?></label>

		<select name="printing-edit-all-1" id="printing-edit-all-1" class="printing-form-field printing-edit-all-current printing-form-field-select">

			<option value="6x4"><?php echo esc_html__( '6" x 4" Print', 'ahime-image-printer' ); ?></option>
			<option value="8x6"><?php echo esc_html__( '8" x 6" Print', 'ahime-image-printer' ); ?></option>
			<option value="10x8"><?php echo esc_html__( '10" x 8" Print', 'ahime-image-printer' ); ?></option>
			<option value="12x8"><?php echo esc_html__( '12" x 8" Print', 'ahime-image-printer' ); ?></option>
			<option value="7x5"><?php echo esc_html__( '7" x 5" Print', 'ahime-image-printer' ); ?></option>

		</select>

	</div>

	<div class="printing-field">

		<label for="printing-edit-all-2" class="printing-label"><?php echo esc_html__( 'And make them', 'ahime-image-printer' ); ?></label>

		<select name="printing-edit-all-2" id="printing-edit-all-2" class="printing-form-field printing-edit-all-changed printing-form-field-select">

			<option value="6x4"><?php echo esc_html__( '6" x 4" Print', 'ahime-image-printer' ); ?></option>
			<option value="8x6"><?php echo esc_html__( '8" x 6" Print', 'ahime-image-printer' ); ?></option>
			<option value="10x8"><?php echo esc_html__( '10" x 8" Print', 'ahime-image-printer' ); ?></option>
			<option value="12x8"><?php echo esc_html__( '12" x 8" Print', 'ahime-image-printer' ); ?></option>
			<option value="7x5"><?php echo esc_html__( '7" x 5" Print', 'ahime-image-printer' ); ?></option>

		</select>

	</div>

	<div class="printing-field">

		<label for="printing-edit-all-3" class="printing-label"><?php echo esc_html__( 'Update all quantities to', 'ahime-image-printer' ); ?></label>

		<input type="number" value="0" min="0" name="printing-edit-all-3" id="printing-edit-all-3" class="printing-form-field printing-edit-all-qty printing-form-field-number">

	</div>

	<div class="printing-btns-actions">

		<button type="button" class="printing-btn printing-btn-secondary printing-mr-40 printing-cancel-edit-all"><?php echo esc_html__( 'Cancel', 'ahime-image-printer' ); ?></button>
		<button type="button" class="printing-btn printing-btn-primary printing-update-edit-all"><?php echo esc_html__( 'Update', 'ahime-image-printer' ); ?></button>

	</div>

			
</div>

<div class="printing-shadow printing-shadow-edit-all"></div>


<!-- Preview Box Edit All Design End -->

<!-- Preview Box Select File Or Drag And Drop Design Begin -->

<div class="printing-modal printing-preview-box-file printing-show">

	<div class="printing-icon-close printing-icon-file-cross"><i class='bx bx-x'></i></div>

	<div class="printing-drag-area">

		<div class="printing-icon-upload"><i class="bx bx-upload"></i></div>

		<div class="printing-head"><?php echo esc_html__( 'Drag & Drop to upload File', 'ahime-image-printer' ); ?></div>

		<span class="printing-or"><?php echo esc_html__( 'OR', 'ahime-image-printer' ); ?></span>

		<span class="printing-btn printing-btn-primary printing-file">Select File</span>

		<input type="file" id="printing-file" hidden accept="image/*" multiple="multiple">

	</div>

</div>

<div class="printing-shadow printing-shadow-file printing-show"></div>

<!-- Preview Box Select File Or Drag And Drop Design End -->


<!-- Preview Box Continue Design Begin -->

<div class="printing-modal printing-preview-box-continue">

	<div class="printing-preview-title"><?php echo esc_html__( 'Warning', 'ahime-image-printer' ); ?></div>

	<div class="printing-icon-close printing-icon-continue-cross"><i class='bx bx-x'></i></div>

	<p class="printing-p">
		
		<?php echo esc_html__( 'The aspect ratio of some of your images does not match the aspect ratio of the selected photo size. This means that your images will be cropped along the dotted red line.', 'ahime-image-printer' ); ?>
		
	</p>

	<p class="printing-p">

		<span class="printing-note"><?php echo esc_html__( 'PLEASE REVIEW YOUR IMAGES NOW.', 'ahime-image-printer' ); ?></span>
		<span><?php echo esc_html__( 'It is not possible to preview your photos or make further edits from the basket.', 'ahime-image-printer' ); ?></span>

	</p>

	<div class="printing-btns-actions">

		<button type="button" class="printing-btn printing-btn-secondary printing-mr-10 printing-continue-anyway"><?php echo esc_html__( "I don't mind, proceed anyway", 'ahime-image-printer' ); ?></button>
		<button type="button" class="printing-btn printing-btn-primary printing-go-back"><?php echo esc_html__( 'Go back and edit', 'ahime-image-printer' ); ?></button>

	</div>

			
</div>

<div class="printing-shadow printing-shadow-continue"></div>

<!-- Preview Box Continue Design End -->

<!-- Preview Box Edit Design Begin -->

<div class="printing-modal printing-preview-box-edit">

	<div class="printing-preview-title"><?php echo esc_html__( 'Edit this photo', 'ahime-image-printer' ); ?></div>

	<div class="printing-icon-close printing-icon-edit-cross"><i class='bx bx-x'></i></div>

	<div class="printing-editing-inner">

		<div class="printing-column printing-column-left">

			<div class="printing-canvas-inner">
				<div class="printing-card-img-self" id="myCanvas" data-id="<?php echo esc_attr( $product_id ); ?>">
					<canvas id="canvas" width="400" height="400">
							Canvas ne fonctionne pas sous vôtre navigateur
					</canvas>
				</div>
			</div>

			<div class="printing-controls">

				<span class="printing-control printing-control-minus"><i class='bx bx-minus'></i></span>
				<span class="printing-control printing-control-plus"><i class='bx bx-plus'></i></span>
				<span class="printing-control printing-control-rotate"><i class='bx bx-rotate-left' ></i></span>

			</div>

		</div>

		<div class="printing-column printing-column-right">

			<p class="printing-p"><?php echo esc_html__( 'Use the red squares on your photo to manually crop your photo. Alternatively, use the presets below.', 'ahime-image-printer' ); ?></p>

			<div class="printing-field">

				<label for="printing-edit-crop-orientation" class="printing-label"><?php echo esc_html__( 'Crop orientation', 'ahime-image-printer' ); ?></label>

				<select name="printing-edit-crop-orientation" id="printing-edit-crop-orientation" class="printing-form-field printing-edit-all-changed printing-form-field-select">

					<option value="landscape"><?php echo esc_html__( 'Landscape', 'ahime-image-printer' ); ?></option>
					<option value="portrait"><?php echo esc_html__( 'Portrait', 'ahime-image-printer' ); ?></option>

				</select>

			</div>

			<div class="printing-field">

				<label for="printing-edit-crop-preset" class="printing-label"><?php echo esc_html__( 'Crop orientation', 'ahime-image-printer' ); ?></label>

				<select name="printing-edit-crop-preset" id="printing-edit-crop-preset" class="printing-form-field printing-edit-all-changed printing-form-field-select">
					<option value="fill"><?php echo esc_html__( 'Fill', 'ahime-image-printer' ); ?></option>
					<option value="fit"><?php echo esc_html__( 'Fit', 'ahime-image-printer' ); ?></option>

				</select>

			</div>

			<div class="printing-field">

				<label for="printing-edit-qty" class="printing-label"><?php echo esc_html__( 'Quantity of this print', 'ahime-image-printer' ); ?></label>

				<input type="number" value="0" min="0"  name="printing-edit-qty" id="printing-edit-qty" class="printing-form-field printing-edit-qty printing-form-field-number">

			</div>

			<div class="printing-btns-actions">

				<button type="button" class="printing-btn printing-btn-secondary printing-mr-10 printing-cancel-edit"><?php echo esc_html__( 'Cancel', 'ahime-image-printer' ); ?></button>
				<button type="button" class="printing-btn printing-btn-primary printing-editing-edit"><?php echo esc_html__( 'Done editing', 'ahime-image-printer' ); ?></button>

			</div>

		</div>

	</div>

			
</div>

<div class="printing-shadow printing-shadow-edit"></div>
<div class="printing-clone-canvas" style="display:none">
	<canvas></canvas>
</div>

<!-- Preview Box Edit Design End -->
