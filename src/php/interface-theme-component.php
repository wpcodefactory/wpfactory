<?php
/**
 * WPFactory theme - Theme Component Interface.
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  WPFactory
 */

namespace WPFactory\WPFactory_Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! interface_exists( 'WPFactory\WPFactory_Theme\Theme_Component' ) ) {

	interface Theme_Component {

		/**
		 * Initializes the component
		 *
		 * @return mixed
		 */
		public function init();
	}
}