<?php
/**
 * Reveal Slider main class
 *
 * This file contains the RevealSlider class extracted from the main plugin file.
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Flowfunnel_Reveal_Slider {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'wp_ajax_flowfunnel_save_reveal_slider', array( $this, 'save_slider_ajax' ) );
		add_action( 'wp_ajax_flowfunnel_delete_reveal_slider', array( $this, 'delete_slider_ajax' ) );

		// Include required files
		$this->include_files();
	}

	/**
	 * Include required files
	 */
	private function include_files() {
		require_once FLOWFUNNEL_REVEAL_SLIDER_PLUGIN_PATH . 'includes/functions.php';
	}

	/**
	 * Initialize the plugin
	 */
	public function init() {
		// Register shortcode
		add_shortcode( 'flowfunnel_reveal_slider', array( $this, 'render_shortcode' ) );

		// Textdomain is loaded automatically for plugins hosted on WordPress.org
		// If you need to load a custom textdomain from a non-standard location,
		// use load_textdomain() here. We avoid load_plugin_textdomain() as it
		// has been discouraged since WP 4.6 when using the plugins directory.
	}

	/**
	 * Enqueue scripts and styles
	 */
	public function enqueue_scripts() {
		// Enqueue CSS
		wp_enqueue_style(
			'flowfunnel-reveal-slider-style',
			FLOWFUNNEL_REVEAL_SLIDER_PLUGIN_URL . 'assets/css/style.css',
			array(),
			FLOWFUNNEL_REVEAL_SLIDER_VERSION
		);

		// Enqueue JavaScript
		wp_enqueue_script(
			'flowfunnel-reveal-slider-script',
			FLOWFUNNEL_REVEAL_SLIDER_PLUGIN_URL . 'assets/js/script.js',
			array( 'jquery' ),
			FLOWFUNNEL_REVEAL_SLIDER_VERSION,
			true
		);
	}

	/**
	 * Enqueue admin scripts and styles
	 */
	public function admin_enqueue_scripts( $hook_suffix ) {
		if ( $hook_suffix === 'toplevel_page_flowfunnel-reveal-sliders' ) {
			// Ensure media scripts are available
			wp_enqueue_media();
			wp_enqueue_script(
				'flowfunnel-reveal-slider-admin',
				FLOWFUNNEL_REVEAL_SLIDER_PLUGIN_URL . 'assets/js/admin.js',
				array( 'jquery', 'media-editor' ),
				FLOWFUNNEL_REVEAL_SLIDER_VERSION,
				true
			);

			// Add small inline debug helper to confirm wp.media availability in browser console
			$inline = "(function(){ try{ console.log('flowfunnel-reveal-slider inline debug: wp.media=', typeof wp !== 'undefined' && typeof wp.media !== 'undefined' ? 'available' : 'unavailable'); }catch(e){} })();";
			wp_add_inline_script( 'flowfunnel-reveal-slider-admin', $inline );
			wp_localize_script(
				'flowfunnel-reveal-slider-admin',
				'flowfunnelRevealSliderAjax',
				array(
					'ajaxurl' => admin_url( 'admin-ajax.php' ),
					'nonce'   => wp_create_nonce( 'flowfunnel_reveal_slider_nonce' ),
				)
			);
			wp_enqueue_style(
				'flowfunnel-reveal-slider-admin',
				FLOWFUNNEL_REVEAL_SLIDER_PLUGIN_URL . 'assets/css/admin.css',
				array(),
				FLOWFUNNEL_REVEAL_SLIDER_VERSION
			);
		}
	}

	/**
	 * Add admin menu
	 */
	public function add_admin_menu() {
		add_menu_page(
			__( 'Flowfunnel Reveal Sliders', 'flowfunnel-reveal-slider' ),           // Page title
			__( 'Reveal Sliders', 'flowfunnel-reveal-slider' ),           // Menu title
			'manage_options',                                 // Capability
			'flowfunnel-reveal-sliders',                                // Menu slug
			array( $this, 'admin_page' ),                      // Callback function
			'dashicons-images-alt2',                         // Icon
			30                                               // Position
		);
	}

	/**
	 * Admin page callback
	 */
	public function admin_page() {
			// Unsplash/unslash incoming GET data before sanitizing per WP coding standards
			$action_raw    = isset( $_GET['action'] ) ? wp_unslash( $_GET['action'] ) : 'list'; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.Recommended
			$action        = sanitize_text_field( $action_raw );
			$slider_id_raw = isset( $_GET['slider_id'] ) ? wp_unslash( $_GET['slider_id'] ) : 0; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.Recommended
			$slider_id     = intval( $slider_id_raw );

			// Nonce verification for add/edit actions
		if ( in_array( $action, array( 'add', 'edit' ), true ) ) {
			$nonce = isset( $_GET['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ) : '';
			if ( ! wp_verify_nonce( $nonce, 'flowfunnel_reveal_slider_nonce' ) ) {
				wp_die( esc_html__( 'Security check failed. Please try again.', 'flowfunnel-reveal-slider' ) );
			}
		}

		switch ( $action ) {
			case 'add':
				$this->render_add_edit_page();
				break;
			case 'edit':
				$this->render_add_edit_page( $slider_id );
				break;
			default:
				$this->render_list_page();
				break;
		}
	}

	/**
	 * Render sliders list page
	 */
	private function render_list_page() {
		$sliders = $this->get_all_sliders();
		?>
		<div class="wrap">
			<h1>
				<?php echo esc_html__( 'Flowfunnel Reveal Sliders', 'flowfunnel-reveal-slider' ); ?>
					<a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=flowfunnel-reveal-sliders&action=add' ), 'flowfunnel_reveal_slider_nonce' ) ); ?>" class="page-title-action">
					<?php echo esc_html__( 'Add New', 'flowfunnel-reveal-slider' ); ?>
				</a>
			</h1>

			<?php if ( empty( $sliders ) ) : ?>
				<div class="notice notice-info">
					<p><?php echo esc_html__( 'No sliders found. Create your first slider!', 'flowfunnel-reveal-slider' ); ?></p>
				</div>
			<?php else : ?>
				<table class="wp-list-table widefat fixed striped">
					<thead>
						<tr>
							<th><?php echo esc_html__( 'ID', 'flowfunnel-reveal-slider' ); ?></th>
							<th><?php echo esc_html__( 'Name', 'flowfunnel-reveal-slider' ); ?></th>
							<th><?php echo esc_html__( 'Shortcode', 'flowfunnel-reveal-slider' ); ?></th>
							<th><?php echo esc_html__( 'Created', 'flowfunnel-reveal-slider' ); ?></th>
							<th><?php echo esc_html__( 'Actions', 'flowfunnel-reveal-slider' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $sliders as $slider ) : ?>
							<tr>
								<td><?php echo esc_html( $slider->id ); ?></td>
								<td><?php echo esc_html( $slider->name ); ?></td>
								<td>
									<code>[flowfunnel_reveal_slider id="<?php echo esc_attr( $slider->id ); ?>"]</code>
									<button type="button" class="button-link copy-shortcode" data-shortcode='[flowfunnel_reveal_slider id="<?php echo esc_attr( $slider->id ); ?>"]'>
										<?php echo esc_html__( 'Copy', 'flowfunnel-reveal-slider' ); ?>
									</button>
								</td>
								<td><?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $slider->created_at ) ) ); ?></td>
								<td>
									<a href="<?php echo esc_url( admin_url( 'admin.php?page=flowfunnel-reveal-sliders&action=edit&slider_id=' . $slider->id ) ); ?>" class="button button-small">
										<?php echo esc_html__( 'Edit', 'flowfunnel-reveal-slider' ); ?>
									</a>
									<button type="button" class="button button-small delete-slider" data-id="<?php echo esc_attr( $slider->id ); ?>">
										<?php echo esc_html__( 'Delete', 'flowfunnel-reveal-slider' ); ?>
									</button>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Render add/edit page
	 */
	private function render_add_edit_page( $slider_id = 0 ) {
		$slider = null;
		if ( $slider_id ) {
			$slider = $this->get_slider( $slider_id );
		}

		$name             = $slider ? $slider->name : '';
		$before_image     = $slider ? $slider->before_image : '';
		$after_image      = $slider ? $slider->after_image : '';
		$before_label     = $slider ? $slider->before_label : 'Before';
		$after_label      = $slider ? $slider->after_label : 'After';
		$initial_position = $slider ? $slider->initial_position : 50;
		$orientation      = $slider ? $slider->orientation : 'horizontal';
		$control_type     = $slider ? ( isset( $slider->control_type ) ? $slider->control_type : 'arrows' ) : 'arrows';
		?>
		<div class="wrap reveal-slider-admin-page">
			<h1>
				<?php echo $slider_id ? esc_html__( 'Edit Slider', 'flowfunnel-reveal-slider' ) : esc_html__( 'Add New Slider', 'flowfunnel-reveal-slider' ); ?>
			</h1>

			<form id="reveal-slider-form" method="post">
				<?php wp_nonce_field( 'flowfunnel_reveal_slider_nonce', 'flowfunnel_reveal_slider_nonce' ); ?>
				<input type="hidden" name="slider_id" value="<?php echo esc_attr( $slider_id ); ?>">

				<div id="poststuff">
					<div id="post-body" class="metabox-holder columns-2">
						<div id="post-body-content">
							<div class="meta-box-sortables ui-sortable">
								<div class="postbox">
									<h2 class="hndle"><span><?php echo esc_html__( 'General Settings', 'flowfunnel-reveal-slider' ); ?></span></h2>
									<div class="inside">
										<table class="form-table">
											<tr>
												<th scope="row">
													<label for="slider_name"><?php echo esc_html__( 'Slider Name', 'flowfunnel-reveal-slider' ); ?></label>
												</th>
												<td>
													<input type="text" id="slider_name" name="slider_name" value="<?php echo esc_attr( $name ); ?>" class="regular-text" required>
												</td>
											</tr>
										</table>
									</div>
								</div>
								<div class="postbox">
									<h2 class="hndle"><span><?php echo esc_html__( 'Images', 'flowfunnel-reveal-slider' ); ?></span></h2>
									<div class="inside">
										<div class="image-upload-wrapper">
											<div class="image-upload-column">
												<label for="before_image"><?php echo esc_html__( 'Before Image', 'flowfunnel-reveal-slider' ); ?></label>
												<div class="image-uploader" data-target="before_image">
													<input type="hidden" id="before_image" name="before_image" value="<?php echo esc_url( $before_image ); ?>">
													<div class="image-preview" id="before_image_preview">
														<?php if ( $before_image ) : ?>
															<img src="<?php echo esc_url( $before_image ); ?>" alt="Before Image">
														<?php else : ?>
															<span><?php echo esc_html__( 'Click to upload', 'flowfunnel-reveal-slider' ); ?></span>
														<?php endif; ?>
													</div>
												</div>
											</div>
											<div class="image-upload-column">
												<label for="after_image"><?php echo esc_html__( 'After Image', 'flowfunnel-reveal-slider' ); ?></label>
												<div class="image-uploader" data-target="after_image">
													<input type="hidden" id="after_image" name="after_image" value="<?php echo esc_url( $after_image ); ?>">
													<div class="image-preview" id="after_image_preview">
														<?php if ( $after_image ) : ?>
															<img src="<?php echo esc_url( $after_image ); ?>" alt="After Image">
														<?php else : ?>
															<span><?php echo esc_html__( 'Click to upload', 'flowfunnel-reveal-slider' ); ?></span>
														<?php endif; ?>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div id="postbox-container-1" class="postbox-container">
							<div class="meta-box-sortables">
								<div class="postbox">
									<h2 class="hndle"><span><?php echo esc_html__( 'Slider Options', 'flowfunnel-reveal-slider' ); ?></span></h2>
									<div class="inside">
										<p>
											<label for="before_label"><?php echo esc_html__( 'Before Label', 'flowfunnel-reveal-slider' ); ?></label>
											<input type="text" id="before_label" name="before_label" value="<?php echo esc_attr( $before_label ); ?>" class="widefat">
										</p>
										<p>
											<label for="after_label"><?php echo esc_html__( 'After Label', 'flowfunnel-reveal-slider' ); ?></label>
											<input type="text" id="after_label" name="after_label" value="<?php echo esc_attr( $after_label ); ?>" class="widefat">
										</p>
										<p>
											<label for="initial_position"><?php echo esc_html__( 'Initial Position (%)', 'flowfunnel-reveal-slider' ); ?></label>
											<input type="number" id="initial_position" name="initial_position" value="<?php echo esc_attr( $initial_position ); ?>" min="0" max="100" class="small-text">
										</p>
										<p>
											<label for="orientation"><?php echo esc_html__( 'Orientation', 'flowfunnel-reveal-slider' ); ?></label>
											<select id="orientation" name="orientation" class="widefat">
												<option value="horizontal" <?php selected( $orientation, 'horizontal' ); ?>><?php echo esc_html__( 'Horizontal', 'flowfunnel-reveal-slider' ); ?></option>
												<option value="vertical" <?php selected( $orientation, 'vertical' ); ?>><?php echo esc_html__( 'Vertical', 'flowfunnel-reveal-slider' ); ?></option>
											</select>
										</p>
										<p>
											<label for="control_type"><?php echo esc_html__( 'Control Type', 'flowfunnel-reveal-slider' ); ?></label>
											<select id="control_type" name="control_type" class="widefat">
												<option value="arrows" <?php selected( $control_type, 'arrows' ); ?>><?php echo esc_html__( 'Arrows (default)', 'flowfunnel-reveal-slider' ); ?></option>
												<option value="line" <?php selected( $control_type, 'line' ); ?>><?php echo esc_html__( 'Line (no arrows, draggable)', 'flowfunnel-reveal-slider' ); ?></option>
												<option value="hover" <?php selected( $control_type, 'hover' ); ?>><?php echo esc_html__( 'Hover (line only, moves on hover)', 'flowfunnel-reveal-slider' ); ?></option>
											</select>
										</p>
									</div>
								</div>
								<div class="postbox">
									<div class="inside">
										<p class="submit">
											<button type="submit" class="button button-primary button-large"><?php echo esc_html__( 'Save Slider', 'flowfunnel-reveal-slider' ); ?></button>
											<a href="<?php echo esc_url( admin_url( 'admin.php?page=flowfunnel-reveal-sliders' ) ); ?>" class="button button-large"><?php echo esc_html__( 'Cancel', 'flowfunnel-reveal-slider' ); ?></a>
										</p>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
		<?php
	}

	/**
	 * Shortcode callback
	 */
	public function render_shortcode( $atts ) {
		// Parse shortcode attributes
		$atts = shortcode_atts(
			array(
				'id' => '',
			),
			$atts,
			'flowfunnel_reveal_slider'
		);

		if ( empty( $atts['id'] ) ) {
			return '<div class="reveal-slider-error">' . esc_html__( 'Slider ID is required.', 'flowfunnel-reveal-slider' ) . '</div>';
		}

		$slider = $this->get_slider( intval( $atts['id'] ) );
		if ( ! $slider ) {
			return '<div class="reveal-slider-error">' . esc_html__( 'Slider not found.', 'flowfunnel-reveal-slider' ) . '</div>';
		}

		$slider_id         = 'reveal-slider-' . $slider->id;
		$orientation_class = $slider->orientation === 'vertical' ? 'reveal-slider-vertical' : 'reveal-slider-horizontal';

		ob_start();
		// Load the template from templates/slider-template.php
		$slider_template = FLOWFUNNEL_REVEAL_SLIDER_PLUGIN_PATH . 'templates/slider-template.php';
		if ( file_exists( $slider_template ) ) {
			// Make variables available to the template
			$slider_id         = $slider_id;
			$orientation_class = $orientation_class;
			$slider            = $slider;
			include $slider_template;
		} else {
			// Fallback if template is missing
			return '<div class="reveal-slider-error">' . esc_html__( 'Slider template not found.', 'flowfunnel-reveal-slider' ) . '</div>';
		}
		return ob_get_clean();
	}

	/**
	 * Get all sliders
	 */
	private function get_all_sliders() {
		$sliders = get_option( 'flowfunnel_reveal_slider_sliders', array() );
		// Sort by created_at DESC
		usort(
			$sliders,
			function ( $a, $b ) {
				return strtotime( $b['created_at'] ) - strtotime( $a['created_at'] );
			}
		);
		// Convert to objects for compatibility
		return array_map(
			function ( $slider ) {
				return (object) $slider;
			},
			$sliders
		);
	}

	/**
	 * Get single slider by ID
	 */
	private function get_slider( $id ) {
		$sliders = get_option( 'flowfunnel_reveal_slider_sliders', array() );
		foreach ( $sliders as $slider ) {
			if ( intval( $slider['id'] ) === intval( $id ) ) {
				return (object) $slider;
			}
		}
		return null;
	}

	/**
	 * Save slider via AJAX
	 */
	public function save_slider_ajax() {
		check_ajax_referer( 'flowfunnel_reveal_slider_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to perform this action.', 'flowfunnel-reveal-slider' ) );
		}

		// Unslesh POST data before sanitization to follow WP security guidelines
		$slider_id        = isset( $_POST['slider_id'] ) ? intval( wp_unslash( $_POST['slider_id'] ) ) : 0;
		$name             = isset( $_POST['slider_name'] ) ? sanitize_text_field( wp_unslash( $_POST['slider_name'] ) ) : '';
		$before_image     = isset( $_POST['before_image'] ) ? esc_url_raw( wp_unslash( $_POST['before_image'] ) ) : '';
		$after_image      = isset( $_POST['after_image'] ) ? esc_url_raw( wp_unslash( $_POST['after_image'] ) ) : '';
		$before_label     = isset( $_POST['before_label'] ) ? sanitize_text_field( wp_unslash( $_POST['before_label'] ) ) : '';
		$after_label      = isset( $_POST['after_label'] ) ? sanitize_text_field( wp_unslash( $_POST['after_label'] ) ) : '';
		$initial_position = isset( $_POST['initial_position'] ) ? intval( wp_unslash( $_POST['initial_position'] ) ) : 50;
		$orientation      = isset( $_POST['orientation'] ) ? sanitize_text_field( wp_unslash( $_POST['orientation'] ) ) : 'horizontal';
		$control_type     = isset( $_POST['control_type'] ) ? sanitize_text_field( wp_unslash( $_POST['control_type'] ) ) : 'arrows';

		$sliders = get_option( 'flowfunnel_reveal_slider_sliders', array() );

		if ( $slider_id ) {
			$found = false;
			foreach ( $sliders as &$slider ) {
				if ( intval( $slider['id'] ) === $slider_id ) {
					$slider['name']             = $name;
					$slider['before_image']     = $before_image;
					$slider['after_image']      = $after_image;
					$slider['before_label']     = $before_label;
					$slider['after_label']      = $after_label;
					$slider['initial_position'] = $initial_position;
					$slider['orientation']      = $orientation;
					$slider['control_type']     = in_array( $control_type, array( 'arrows', 'line', 'hover' ), true ) ? $control_type : 'arrows';
					$slider['updated_at']       = current_time( 'mysql' );
					$found                      = true;
					break;
				}
			}
			if ( ! $found ) {
				wp_send_json_error( array( 'message' => esc_html__( 'Slider not found.', 'flowfunnel-reveal-slider' ) ) );
				return;
			}
		} else {
			// Generate new ID
			$max_id = 0;
			foreach ( $sliders as $slider ) {
				if ( $slider['id'] > $max_id ) {
					$max_id = $slider['id'];
				}
			}
			$new_id    = $max_id + 1;
			$sliders[] = array(
				'id'               => $new_id,
				'name'             => $name,
				'before_image'     => $before_image,
				'after_image'      => $after_image,
				'before_label'     => $before_label,
				'after_label'      => $after_label,
				'initial_position' => $initial_position,
				'orientation'      => $orientation,
				'control_type'     => in_array( $control_type, array( 'arrows', 'line', 'hover' ), true ) ? $control_type : 'arrows',
				'created_at'       => current_time( 'mysql' ),
				'updated_at'       => current_time( 'mysql' ),
			);
		}

		$result = update_option( 'flowfunnel_reveal_slider_sliders', $sliders );

		if ( $result ) {
			wp_send_json_success(
				array(
					'message'  => esc_html__( 'Slider saved successfully!', 'flowfunnel-reveal-slider' ),
					'redirect' => admin_url( 'admin.php?page=flowfunnel-reveal-sliders' ),
				)
			);
		} else {
			wp_send_json_error(
				array(
					'message' => esc_html__( 'Failed to save slider.', 'flowfunnel-reveal-slider' ),
				)
			);
		}
	}

	/**
	 * Delete slider via AJAX
	 */
	public function delete_slider_ajax() {
		check_ajax_referer( 'flowfunnel_reveal_slider_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to perform this action.', 'flowfunnel-reveal-slider' ) );
		}

		$slider_id = isset( $_POST['slider_id'] ) ? intval( $_POST['slider_id'] ) : 0;

		$sliders     = get_option( 'flowfunnel_reveal_slider_sliders', array() );
		$new_sliders = array();
		$found       = false;
		foreach ( $sliders as $slider ) {
			if ( intval( $slider['id'] ) !== $slider_id ) {
				$new_sliders[] = $slider;
			} else {
				$found = true;
			}
		}
		$result = update_option( 'flowfunnel_reveal_slider_sliders', $new_sliders );

		if ( $found && $result ) {
			wp_send_json_success(
				array(
					'message' => esc_html__( 'Slider deleted successfully!', 'flowfunnel-reveal-slider' ),
				)
			);
		} else {
			wp_send_json_error(
				array(
					'message' => esc_html__( 'Failed to delete slider.', 'flowfunnel-reveal-slider' ),
				)
			);
		}
	}

	/**
	 * Plugin activation
	 */
		// No activation/deactivation or table creation needed for Options API
}
