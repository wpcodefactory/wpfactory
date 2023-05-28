<?php
/**
 * WPFactory theme - Footer.
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

if ( ! class_exists( 'WPFactory\WPFactory_Theme\Component\Credit' ) ) {

	class Credit implements Theme_Component {

		/**
		 * Init.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return void
		 */
		function init() {
            // Setup storefront
			add_action( 'after_setup_theme', array( $this, 'setup_storefront' ) );

            // Remove texts from original.
			add_filter( 'storefront_privacy_policy_link', '__return_false' );
			add_filter( 'storefront_credit_link', '__return_false' );

            // Change text from original.
			add_filter( 'storefront_copyright_text', array( $this, 'copyright_text' ) );
		}

		/**
		 * copyright_text.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param $text
		 *
		 * @return string
		 */
		function copyright_text( $text ) {
			$text = esc_html( apply_filters( 'wpfactory_copyright_text', $content = '&copy; ' . gmdate( 'Y' ) . ' ' . get_bloginfo( 'name' ) . '.' ) );
			$text .= ' ' . esc_html__( 'All rights reserved.', 'wpfactory' );

			return $text;
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
			remove_action( 'storefront_footer', 'storefront_credit', 20 );
			add_action( 'storefront_footer', 'storefront_credit', 40 );
			add_action( 'storefront_footer', array( $this, 'add_credit_wrapper' ), 39 );
			add_action( 'storefront_footer', array( $this, 'close_credit_wrapper' ), 41 );
		}

		/**
		 * add_credit_wrapper.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return void
		 */
		function add_credit_wrapper() {
			?>
            <div class="site-info-wrapper">
            <div class="col-full">
			<?php
		}

		/**
		 * close_credit_wrapper.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return void
		 */
		function close_credit_wrapper() {
			?>
            </div>
            </div>
			<?php
		}
	}
}