<?php
/**
 * WPFactory theme - Product Features Area.
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  WPFactory
 */

namespace WPFactory\WPFactory_Theme\Component;

use Carbon_Fields\Container;
use Carbon_Fields\Field;
use WPFactory\WPFactory_Theme\Carbon_Fields\Carbon_Fields_Post_Meta_Datastore;
use WPFactory\WPFactory_Theme\Theme_Component;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'WPFactory\WPFactory_Theme\Component\Product_Features_Area' ) ) {

	class Product_Features_Area implements Theme_Component {
		public function init() {
			add_filter( 'wpft_before_area_prod_features_modules', array( $this, 'add_id_on_features_area' ) );
			//wpft_before_area_{$current_area_info['id']}_modules
		}

		function add_id_on_features_area() {
			//error_log('asdasd');
			return '<div id="features"></div>';
			//return '<h1>TEST</h1>';
		}
	}
}