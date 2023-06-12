<?php
/**
 * WPFactory theme - Products.
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

if ( ! class_exists( 'WPFactory\WPFactory_Theme\Component\Products' ) ) {

	//class Menus {
	class Products implements Theme_Component {
		public function init() {
			add_action( 'after_setup_theme', array( $this, 'setup_woocommerce_product' ) );
		}

		function setup_woocommerce_product() {
			remove_all_actions( 'woocommerce_before_single_product' );
			remove_all_actions( 'woocommerce_before_single_product_summary' );
			remove_all_actions( 'woocommerce_single_product_summary' );
			remove_all_actions( 'woocommerce_after_single_product_summary' );
			remove_all_actions( 'woocommerce_after_single_product' );

			// Remove add to cart button from loop
			remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart' );

			//add_action( 'woocommerce_single_product_summary', array( $this, 'setup_page_builder' ) );
		}

		/*function setup_page_builder() {
			do_action( 'wpft_product_modules_wrapper' );
		}*/
	}
}