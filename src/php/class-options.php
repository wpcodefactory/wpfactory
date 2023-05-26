<?php
/**
 * WPFactory theme - Options.
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  WPFactory
 */

namespace WPFactory\WPFactory_Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'WPFactory\WPFactory_Theme\Options' ) ) {

	class Options {

		/**
		 * Options.
		 *
		 * @since 1.0.0
		 *
		 * @var array
		 */
		protected $options = array();

		/**
		 * Init.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		function init() {

		}

		/**
		 * get_option.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param           $option_name
		 * @param   string  $default_value
		 * @param   bool    $get_cached_version
		 *
		 * @return mixed
		 */
		function get_option( $option_name, $default_value = '', $get_cached_version = true ) {
			if (
				( $get_cached_version && ! isset( $this->options[ $option_name ] ) ) ||
				! $get_cached_version
			) {
				$this->options[ $option_name ] = get_option( $option_name, $default_value );
			}

			return $this->options[ $option_name ];
		}
	}
}