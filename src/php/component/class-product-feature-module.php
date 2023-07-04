<?php
/**
 * WPFactory theme - Product Feature Module.
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

if ( ! class_exists( 'WPFactory\WPFactory_Theme\Component\Product_Feature_Module' ) ) {

	//class Menus {
	class Product_Feature_Module implements Theme_Component {
		protected static $module_count = 0;

		public function init() {
			add_filter( 'wpft_module_prod_feature_wrapper_css_classes', array( $this, 'add_even_css_class' ) );
		}

		function add_even_css_class( $css_classes ) {

			self::$module_count ++;
			if ( self::$module_count % 2 == 0 ) {
				$css_classes[] = 'even';
			}

			return $css_classes;
		}
	}
}