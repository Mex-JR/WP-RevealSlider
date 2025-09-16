<?php
/**
 * Utility functions for the Reveal Slider plugin
 *
 * This file contains helper functions and utilities
 * for the Reveal Slider plugin functionality.
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get slider data by ID
 *
 * @param int $slider_id The slider ID
 * @return object|null Slider data or null if not found
 */
function reveal_slider_get_slider( $slider_id ) {
	global $wpdb;
	$table_name = $wpdb->prefix . 'reveal_sliders';

	$cache_key = 'reveal_slider_' . $slider_id;
	$slider    = wp_cache_get( $cache_key, 'reveal_slider' );
	if ( false === $slider ) {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$slider = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM `{$wpdb->prefix}reveal_sliders` WHERE id = %d",
				$slider_id
			)
		);
		if ( $slider ) {
			wp_cache_set( $cache_key, $slider, 'reveal_slider' );
		}
	}
	return $slider;
}

/**
 * Validate image URL
 *
 * @param string $url The image URL to validate
 * @return bool True if valid image URL, false otherwise
 */
function reveal_slider_validate_image_url( $url ) {
	if ( empty( $url ) ) {
		return false;
	}

	// Check if it's a valid URL
	if ( ! filter_var( $url, FILTER_VALIDATE_URL ) ) {
		return false;
	}

	// Check if it's an image by extension
	$image_extensions = array( 'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg' );
	$extension        = strtolower( pathinfo( $url, PATHINFO_EXTENSION ) );

	return in_array( $extension, $image_extensions );
}

/**
 * Sanitize slider settings
 *
 * @param array $settings Raw settings array
 * @return array Sanitized settings
 */
function reveal_slider_sanitize_settings( $settings ) {
	$sanitized = array();

	$sanitized['name']             = isset( $settings['name'] ) ? sanitize_text_field( $settings['name'] ) : '';
	$sanitized['before_image']     = isset( $settings['before_image'] ) ? esc_url_raw( $settings['before_image'] ) : '';
	$sanitized['after_image']      = isset( $settings['after_image'] ) ? esc_url_raw( $settings['after_image'] ) : '';
	$sanitized['before_label']     = isset( $settings['before_label'] ) ? sanitize_text_field( $settings['before_label'] ) : 'Before';
	$sanitized['after_label']      = isset( $settings['after_label'] ) ? sanitize_text_field( $settings['after_label'] ) : 'After';
	$sanitized['initial_position'] = isset( $settings['initial_position'] ) ? intval( $settings['initial_position'] ) : 50;
	$sanitized['orientation']      = isset( $settings['orientation'] ) && in_array( $settings['orientation'], array( 'horizontal', 'vertical' ) ) ? $settings['orientation'] : 'horizontal';

	// Validate position range
	$sanitized['initial_position'] = max( 0, min( 100, $sanitized['initial_position'] ) );
	$sanitized['control_type']     = isset( $settings['control_type'] ) && in_array( $settings['control_type'], array( 'arrows', 'line', 'hover' ), true ) ? $settings['control_type'] : 'arrows';

	return $sanitized;
}

/**
 * Get default slider settings
 *
 * @return array Default settings
 */
function reveal_slider_get_default_settings() {
	return array(
		'name'             => '',
		'before_image'     => '',
		'after_image'      => '',
		'before_label'     => 'Before',
		'after_label'      => 'After',
		'initial_position' => 50,
		'orientation'      => 'horizontal',
		'control_type'     => 'arrows',
	);
}

/**
 * Check if user can manage sliders
 *
 * @return bool True if user has permission, false otherwise
 */
function reveal_slider_user_can_manage() {
	return current_user_can( 'manage_options' );
}

/**
 * Get slider count
 *
 * @return int Number of sliders
 */
function reveal_slider_get_count() {
	global $wpdb;
	$table_name = $wpdb->prefix . 'reveal_sliders';

	$cache_key = 'reveal_slider_count';
	$count     = wp_cache_get( $cache_key, 'reveal_slider' );
	if ( false === $count ) {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$count = (int) $wpdb->get_var(
			"SELECT COUNT(*) FROM `{$wpdb->prefix}reveal_sliders`"
		);
		wp_cache_set( $cache_key, $count, 'reveal_slider' );
	}
	return $count;
}
