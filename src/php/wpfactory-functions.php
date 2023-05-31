<?php
/**
 * WPFactory functions.
 *
 * @package wpfactory
 */

if ( ! function_exists( 'wpft_get_theme' ) ) {
	/**
	 * Gets the main theme.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 *
	 * @return \WPFactory\WPFactory_Theme\WPFactory_Theme
	 */
	function wpft_get_theme() {
		return \WPFactory\WPFactory_Theme\WPFactory_Theme::get_instance();
	}
}

if ( ! function_exists( 'wpft_get_option' ) ) {
	/**
	 * Gets theme option.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 *
	 * @return mixed
	 */
	function wpft_get_option( $option_name, $default_value = '', $get_cached_version = true ) {
		return wpft_get_theme()->get_options()->get_option( $option_name, $default_value, $get_cached_version );
	}
}

if ( ! function_exists( 'wpft_is_current_page_full_width_content' ) ) {
	/**
	 * wpf_is_current_content_full_width.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 *
	 * @return mixed
	 */
	function wpft_is_current_page_full_width_content() {
		return ! (
			is_shop() ||
			is_product_category()
		);
	}
}