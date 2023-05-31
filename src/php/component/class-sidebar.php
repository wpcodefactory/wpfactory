<?php
/**
 * WPFactory theme - Sidebar.
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  WPFactory
 */

namespace WPFactory\WPFactory_Theme\Component;

use WPFactory\WPFactory_Theme\Theme_Component;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'WPFactory\WPFactory_Theme\Component\Sidebar' ) ) {

	class Sidebar implements Theme_Component {

		/**
		 * Init.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return void
		 */
		function init() {
			// Leabe sidebar only on shop.
			add_action( 'get_header', array( $this, 'leave_sidebar_on_shop_only' ) );
			add_filter( 'body_class', array( $this, 'set_full_width_css_on_all_pages_but_shop' ) );
		}

		/**
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param $classes
		 *
		 * @return mixed
		 */
		function set_full_width_css_on_all_pages_but_shop( $classes ) {
			if (
				wpft_is_current_page_full_width_content()
			) {
				$classes[] = 'storefront-full-width-content';
			}

			return $classes;
		}

		/**
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return void
		 */
		function leave_sidebar_on_shop_only() {
			if (
				wpft_is_current_page_full_width_content()
			) {
				remove_action( 'storefront_sidebar', 'storefront_get_sidebar', 10 );
			}
		}
	}
}