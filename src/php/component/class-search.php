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
			add_action('after_setup_theme',array($this,'setup_storefront'));

		}

		function setup_storefront(){
			remove_action( 'storefront_header', 'storefront_product_search', 40 );
		}
	}
}