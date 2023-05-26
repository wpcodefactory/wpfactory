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
			add_action( 'admin_enqueue_scripts', array( $this, 'load_admin_style' ) );
			// Sanitize options.
			add_action( 'carbon_fields_container_activated', array( $this, 'sanitize_fields' ) );
		}

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
		function sanitize_fields( $container ) {
			foreach ( $container->get_fields() as $field ) {
				add_filter( "sanitize_option__{$field->get_base_name()}", function ( $value, $option ) use ( $field ) {
					if ( strpos( substr( $option, 0, 6 ), 'wpft_' ) !== false ) {
						$value = sanitize_text_field($value);
					}
					return $value;
				}, 10, 2 );
			}
		}

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
		}

		/**
		 * load_admin_style.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return void
		 */
		function load_admin_style() {
			$suffix  = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			$version = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? current_time( 'timestamp' ) : wpf_get_theme()->get_version();

			// Main admin style.
			wp_enqueue_style( 'wpfactory-admin-style', get_theme_file_uri( '/assets/css/admin' . $suffix . '.css' ), '', $version );
		}

		/**
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return void
		 */
		function create_settings_page() {
			$container = Container::make( 'theme_options', __( 'WPFactory settings', 'wpfactory' ) )
			                      ->set_page_menu_title( __( 'WPFactory', 'wpfactory' ) )
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