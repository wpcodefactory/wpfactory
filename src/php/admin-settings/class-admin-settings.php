<?php
/**
 * WPFactory theme - Admin Settings.
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  WPFactory
 */

namespace WPFactory\WPFactory_Theme\Admin_Settings;

use \Carbon_Fields\Container;
use \Carbon_Fields\Field;
use WPFactory\WPFactory_Theme\Carbon_Fields\Carbon_Fields_Post_Meta_Datastore;
use WPFactory\WPFactory_Theme\Carbon_Fields\Carbon_Fields_Theme_Options_Datastore;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'WPFactory\WPFactory_Theme\Admin_Settings\Admin_Settings' ) ) {

	class Admin_Settings {

		/**
		 * init.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return void
		 */
		function init() {
			add_action( 'after_setup_theme', array( $this, 'initialize_carbon_fields_library' ) );
			add_action( 'carbon_fields_register_fields', array( $this, 'create_settings_page' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'load_admin_scripts' ) );
			// Sanitize options.
			//add_action( 'carbon_fields_container_activated', array( $this, 'sanitize_fields' ), 50 );
			// Menu page
			//add_action( 'admin_menu', array( $this, 'add_menu_page' ) );
		}

		/*function add_menu_page(){
			add_menu_page(
				__( 'WPFactory settings', 'wpfactory' ),
				__( 'WPFactory', 'wpfactory' ),
				'manage_options',
				'wpft',
				function(){
					//echo 'asdd';
				},
				//'',
				get_theme_file_uri( '/src/img/wpfactory-logo.png' ),
				6
			);
			//remove_menu_page('wpft');
			remove_submenu_page( 'wpft', 'wpft' );  // 'parent-slug', 'subpage-slug'
		}*/

		/**
		 * sanitize_fields.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param $container
		 *
		 * @return void
		 */
		/*function sanitize_fields( $container ) {
			foreach ( $container->get_fields() as $field ) {
				add_filter( "sanitize_option__{$field->get_base_name()}", function ( $value, $option ) use ( $field ) {
					if ( strpos( substr( $option, 0, 6 ), 'wpft_' ) !== false ) {
						$value = sanitize_text_field( $value );
					}

					return $value;
				}, 10, 2 );

				add_filter( "sanitize_post_meta__wpft_template", function ( $value, $option ) {
					//error_log( 'asdasd' );
					if ( strpos( substr( $option, 0, 6 ), 'wpft_' ) !== false ) {
						$value = sanitize_text_field( $value );
					}

					return $value;
				}, 10, 2 );
			}
		}*/

		/**
		 * handle_tabs.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param $admin_settings_page_container
		 *
		 * @return void
		 */
		function handle_tabs( $admin_settings_page_container ) {
			$tab = new General_Tab( $admin_settings_page_container );
			$tab->init();
			$tab = new Page_Builder_Tab( $admin_settings_page_container );
			$tab->init();
			/*$tab = new Import_Tab( $admin_settings_page_container );
			$tab->init();
			$tab = new Export_Tab( $admin_settings_page_container );
			$tab->init();*/
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
			$suffix  = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			$version = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? current_time( 'timestamp' ) : wpft_get_theme()->get_version();

			// Main admin style.
			wp_enqueue_style( 'wpfactory-admin-style', get_theme_file_uri( '/assets/css/admin' . $suffix . '.css' ), '', $version );

			// Main frontend script.
			wp_enqueue_script( 'wpfactory-admin-js',
				get_theme_file_uri( '/assets/js/admin' . $suffix . '.js' ),
				array( 'jquery', 'carbon-fields-vendor' ),
				$version,
				false
			);
			wp_add_inline_script( 'wpfactory-admin-js', 'const WPFTAJS = ' . json_encode( apply_filters( 'wpft_admin_js_info', array(
					'themeURI'        => get_theme_file_uri(),
					'modulesRequired' => apply_filters( 'wpft_admin_js_modules_required', array() )
				) ) ), 'before'
			);


		}

		/**
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return void
		 */
		function create_settings_page() {
			$container = Container::make( 'theme_options', __( 'WPFactory settings', 'wpfactory' ) )
			                      ->set_datastore( new Carbon_Fields_Theme_Options_Datastore() )
				//->set_page_parent( 'wpft' ) // reference to a top level container
				                  ->set_page_menu_title( __( 'Theme settings', 'wpfactory' ) )
			                      ->set_icon( get_theme_file_uri( '/src/img/wpfactory-logo.png' ) );
			$this->handle_tabs( $container );
		}

		/**
		 * initialize_carbon_fields_library.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return void
		 */
		function initialize_carbon_fields_library() {
			\Carbon_Fields\Carbon_Fields::boot();
		}
	}
}