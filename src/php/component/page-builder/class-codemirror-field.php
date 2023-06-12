<?php
/**
 * WPFactory theme - Codemirror field.
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  WPFactory
 */

namespace WPFactory\WPFactory_Theme\Component\Page_Builder;

use Carbon_Fields\Container;
use Carbon_Fields\Field;
use Carbon_Fields\Helper\Helper;
use \Timber\Timber;
use WPFactory\WPFactory_Theme\Carbon_Fields\Carbon_Fields_Post_Meta_Datastore;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'WPFactory\WPFactory_Theme\Component\Page_Builder\Codemirror_Field' ) ) {

	class Codemirror_Field {
		function init() {
			add_action( 'admin_enqueue_scripts', array( $this, 'load_admin_scripts' ) );
			add_filter( 'wpft_admin_js_modules_required', array( $this, 'load_codemirror_field_js_module' ) );
			add_filter( 'wpft_admin_js_info', array( $this, 'append_info_to_admin_js' ) );
		}

		function load_codemirror_field_js_module( $required_modules ) {
			$required_modules[] = 'codemirror-field';

			return $required_modules;
		}

		function append_info_to_admin_js( $js_info ) {
			$js_info['codemirror_textarea_selector'] = '.cf-container-carbon_fields_container_template .cf-textarea__input';

			return $js_info;
		}

		function is_module_cpt_admin_page() {
			global $pagenow;

			return 'post.php' === $pagenow && isset( $_GET['post'] ) && 'wpft_module' === get_post_type( $_GET['post'] );
		}

		/**
		 * load_admin_style.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return void
		 */
		function load_admin_scripts() {
			// Global object containing current admin page
			global $pagenow;

			// If current page is post.php and post isset than query for its post type
			// if the post type is 'event' do something
			if ( ! $this->is_module_cpt_admin_page() ) {
				return;
			}

			// Codemirror.
			wp_enqueue_style( 'wpfactory-codemirror', get_theme_file_uri( '/assets/css/vendor/codemirror.css' ), '' );
			wp_enqueue_script( 'wpfactory-codemirror-js',
				get_theme_file_uri( '/assets/js/vendor/codemirror.js' ),
				array( 'jquery', 'carbon-fields-vendor' ),
				'',
				false
			);
			wp_enqueue_script( 'wpfactory-codemirror-mode',
				get_theme_file_uri( '/assets/js/vendor/codemirror-twig-mode.js' ),
				array( 'jquery', 'carbon-fields-vendor' ),
				false,
				false
			);
			wp_enqueue_script( 'wpfactory-codemirror-xml-mode',
				get_theme_file_uri( '/assets/js/vendor/codemirror-xml-mode.js' ),
				array( 'jquery', 'carbon-fields-vendor' ),
				false,
				false
			);
			wp_enqueue_script( 'wpfactory-codemirror-multiplex-mode',
				get_theme_file_uri( '/assets/js/vendor/codemirror-multiplex-mode.js' ),
				array( 'jquery', 'carbon-fields-vendor' ),
				false,
				false
			);
		}
	}
}