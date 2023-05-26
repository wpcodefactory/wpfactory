<?php
/**
 * WPFactory theme - Tab.
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  WPFactory
 */

namespace WPFactory\WPFactory_Theme\Admin_Settings;

use \Carbon_Fields\Container;
use \Carbon_Fields\Field;

use \Carbon_Fields\Container\Theme_Options_Container;


if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'WPFactory\WPFactory_Theme\Admin_Settings\Tab' ) ) {

	class Tab {
		protected $container;

		public function __construct( Theme_Options_Container $container ) {
			$this->container = $container;
		}

		/**
		 * @return Theme_Options_Container
		 */
		public function get_container(): Theme_Options_Container {
			return $this->container;
		}



	}
}