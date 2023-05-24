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