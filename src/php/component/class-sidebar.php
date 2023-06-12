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
			// Leave sidebar only on shop.
			add_action( 'get_header', array( $this, 'leave_sidebar_on_shop_only' ) );
			add_filter( 'body_class', array( $this, 'handle_sidebar_body_css' ), 90 );
		}

		function handle_sidebar_body_css( $classes ) {
			$left_or_right = get_theme_mod( 'storefront_layout' );
			$sidebar_class = $left_or_right . '-sidebar';
			if (
				wpft_does_current_page_have_sidebar()
			) {
				$classes[] = $sidebar_class;
			} else {
				if ( in_array( $sidebar_class, $classes ) ) {
					unset( $classes[ array_search( $sidebar_class, $classes ) ] );
				}
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
				! wpft_does_current_page_have_sidebar()
			) {
				remove_action( 'storefront_sidebar', 'storefront_get_sidebar', 10 );
			}
		}
	}
}