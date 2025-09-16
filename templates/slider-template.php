<?php
/**
 * Template file for frontend slider display
 *
 * This file will contain the HTML templates
 * for rendering the reveal slider on the frontend.
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Slider HTML template
?>
<div class="reveal-slider-wrapper <?php echo esc_attr( $orientation_class ); ?>"
	id="<?php echo esc_attr( $slider_id ); ?>"
	data-initial-position="<?php echo esc_attr( $slider->initial_position ); ?>"
	data-orientation="<?php echo esc_attr( $slider->orientation ); ?>"
	data-control-type="<?php echo esc_attr( isset( $slider->control_type ) ? $slider->control_type : 'arrows' ); ?>">

	<div class="reveal-slider-container">
		<div class="reveal-slider-before">
			<img src="<?php echo esc_url( $slider->before_image ); ?>" alt="<?php echo esc_attr( $slider->before_label ); ?>">
			<?php if ( ! empty( $slider->before_label ) ) : ?>
				<div class="reveal-slider-label reveal-slider-label-before">
					<?php echo esc_html( $slider->before_label ); ?>
				</div>
			<?php endif; ?>
		</div>

		<div class="reveal-slider-after">
			<img src="<?php echo esc_url( $slider->after_image ); ?>" alt="<?php echo esc_attr( $slider->after_label ); ?>">
			<?php if ( ! empty( $slider->after_label ) ) : ?>
				<div class="reveal-slider-label reveal-slider-label-after">
					<?php echo esc_html( $slider->after_label ); ?>
				</div>
			<?php endif; ?>
		</div>

		<div class="reveal-slider-handle">
			<div class="reveal-slider-handle-line"></div>
			<div class="reveal-slider-handle-button">
				<?php if ( empty( $slider->control_type ) || $slider->control_type === 'arrows' ) : ?>
					<span class="reveal-slider-handle-arrow-left"></span>
					<span class="reveal-slider-handle-arrow-right"></span>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>
