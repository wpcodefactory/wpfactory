<?php
/**
 * WPFactory functions.
 *
 * @package wpfactory
 */

if ( ! function_exists( 'wpf_get_theme' ) ) {
	/**
	 * Gets the main theme
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 *
	 * @return \WPFactory\WPFactory_Theme\WPFactory_Theme
	 */
	function wpf_get_theme() {
		return \WPFactory\WPFactory_Theme\WPFactory_Theme::get_instance();
	}
}

if ( ! function_exists( 'wpf_get_option' ) ) {
	/**
	 * Gets theme option
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 *
	 * @return mixed
	 */
	function wpf_get_option( $option_name, $default_value = '', $get_cached_version = true ) {
		return wpf_get_theme()->get_options()->get_option( $option_name, $default_value, $get_cached_version );
	}
}