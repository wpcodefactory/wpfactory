<?php
/**
 * WPFactory theme - Product.
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

if ( ! class_exists( 'WPFactory\WPFactory_Theme\Component\Product' ) ) {

	//class Menus {
	class Product implements Theme_Component {
		public function init() {
			add_action( 'after_setup_theme', array( $this, 'setup_woocommerce_product' ) );
		}

		function setup_woocommerce_product() {
			remove_all_actions('woocommerce_before_single_product');
			remove_all_actions('woocommerce_before_single_product_summary');
			remove_all_actions('woocommerce_single_product_summary');
			remove_all_actions('woocommerce_after_single_product_summary');
			remove_all_actions('woocommerce_after_single_product');

			add_action( 'woocommerce_single_product_summary', array( $this, 'setup_page_builder' ) );
		}

		function setup_page_builder(){
			/*global $product;
			$test = get_the_ID();
			error_log(print_r($test,true));*/
			do_action( 'wpft_product_modules_wrapper' );
		}
	}
}