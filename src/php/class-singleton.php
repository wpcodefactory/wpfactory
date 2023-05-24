<?php
/**
 * WPFactory theme - Singleton.
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  WPFactory
 */

namespace WPFactory\WPFactory_Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'WPFactory\WPFactory_Theme\Singleton' ) ) {
	abstract class Singleton {

		/**
		 * __construct.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		protected function __construct() {
		}

		/**
		 * get_instance.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return mixed
		 */
		public static function get_instance() {
			static $instances = array();

			$calledClass = get_called_class();

			if ( ! isset( $instances[ $calledClass ] ) ) {
				$instances[ $calledClass ] = new $calledClass();
			}

			return $instances[ $calledClass ];
		}

		/**
		 * __clone.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return void
		 */
		private function __clone() {
		}
	}
}