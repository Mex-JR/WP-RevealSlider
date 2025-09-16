<?php
/**
 * Plugin Name: Reveal Slider
 * Plugin URI: https://github.com/Mex-JR/WP-Webpage-Slider
 * Description: A robust WordPress plugin for creating customizable before and after image comparison sliders.
 * Version: 1.0.0
 * Author: Flowfunnel
 * Author URI: https://flowfunnel.io
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: reveal-slider
 * Domain Path: /languages
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define plugin constants
define( 'REVEAL_SLIDER_VERSION', '1.0.0' );
define( 'REVEAL_SLIDER_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'REVEAL_SLIDER_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

// Include the main class
require_once REVEAL_SLIDER_PLUGIN_PATH . 'includes/class-reveal-slider.php';

// Initialize the plugin

new RevealSlider();
