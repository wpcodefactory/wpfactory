<?php
/**
 * WPFactory theme - Home.
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

if ( ! class_exists( 'WPFactory\WPFactory_Theme\Component\Home' ) ) {
	class Home implements Theme_Component {

		/**
		 * Init.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return void
		 */
		function init() {
			add_action( 'after_setup_theme', array( $this, 'setup_storefront' ) );
		}

		/**
		 * Setup_storefront.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return void
		 */
		function setup_storefront() {
			remove_all_actions( 'homepage' );
			add_action( 'homepage', array( $this, 'add_pagebuilder_container' ) );
		}

		function add_pagebuilder_container() {
			$post_id = get_the_ID();
			do_action( 'wpft_page_modules_wrapper', $post_id );
		}
	}
}