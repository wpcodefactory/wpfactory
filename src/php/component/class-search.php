<?php
/**
 * WPFactory theme - Search.
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

if ( ! class_exists( 'WPFactory\WPFactory_Theme\Component\Search' ) ) {

	class Search implements Theme_Component {

		/**
		 * Init.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return void
		 */
		function init() {
			add_action( 'after_setup_theme', array( $this, 'setup_storefront' ) );
			add_action( 'storefront_before_content', array( $this, 'add_search_bar' ) );

			// JS.
			add_filter( 'wpft_js_modules_required', array( $this, 'load_search_js' ) );
			add_filter( 'wpft_frontend_js_info', array( $this, 'append_info_to_frontend_js' ) );

            // Search icon at the Header.
			add_filter( 'wpft_header_search_li_html', array( $this, 'get_header_search_li_html' ) );
		}

		/**
		 * get_header_search_li_html.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param $cart_html
		 *
		 * @return false|string
		 */
		function get_header_search_li_html( $cart_html ) {
			ob_start();
			?>
            <li class="wpft-icon-search wpft-has-icon wpft-hide-label">
                <a><label class="hide"><?php _e( 'Search', 'wpfactory' ) ?></label></a>
            </li>
			<?php
			return ob_get_clean();
		}

		/**
         * Load search js.
         *
		 * @version 1.0.0
		 * @since   1.0.0
         *
		 * @param $required_modules
		 *
		 * @return mixed
		 */
		function load_search_js( $required_modules ) {
			$required_modules[] = 'search';

			return $required_modules;
		}

		/**
		 * append_info_to_frontend_js.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param $js_info
		 *
		 * @return mixed
		 */
		function append_info_to_frontend_js( $js_info ) {
			$js_info['search_toggler_selector'] = '.top-right-navigation .wpft-icon-search';
			$js_info['search_bar_selector']    = '.wpft-search-bar';

			return $js_info;
		}

		/**
		 * Add search bar.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return void
		 */
		function add_search_bar() {
			?>
            <div class="wpft-search-bar">
                <div class="col-full">
					<?php storefront_product_search(); ?>
                </div>
            </div>
			<?php
		}

		/**
		 * setup_storefront.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return void
		 */
		function setup_storefront() {
			remove_action( 'storefront_header', 'storefront_product_search', 40 );
		}
	}
}