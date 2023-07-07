<?php
/**
 * WPFactory theme - Page builder.
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  WPFactory
 */

namespace WPFactory\WPFactory_Theme\Component\Page_Builder;


use WPFactory\WPFactory_Theme\Theme_Component;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'WPFactory\WPFactory_Theme\Component\Page_Builder\Page_Builder' ) ) {

	class Page_Builder implements Theme_Component {

		/**
		 * @var Modules
		 */
		protected $modules;

		/**
		 * Init.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return void
		 */
		function init() {
			// Modules.
			$this->modules = new Modules();
			$this->modules->init();
			// Codemirror field.
			$codemirror_field = new Codemirror_Field();
			$codemirror_field->init();
			// Import Export
			$import_export = new Import_Export();
			$import_export->init();
			// Adds css classes to page builder modules.
			add_filter( 'wpft_module_css_classes', array( $this, 'add_css_classes_to_page_builder_module' ) );
			// Remove association edit link.
			add_filter( 'carbon_fields_association_field_options', array( $this, 'remove_association_edit_link' ) );
			// Handle field saving.
			add_filter( 'carbon_fields_should_save_field_value', array(
				$this,
				'prevent_conditional_empty_field_from_saving'
			), 10, 3 );

			// Useful function to change option name.
			//add_action('admin_init',array($this,'update_options'));
		}

		function prevent_conditional_empty_field_from_saving( $save, $value, $field ) {
			if (
				! empty( $field->get_conditional_logic() ) &&
				'checkbox' !== $field->get_type() &&
				'' === $value
			) {
				$save = false;
			}

			return $save;
		}

		/*function update_options(){
			global $wpdb;

			// Define the old and new values
			$old_value = '_wpft_page_builder_settings';
			$new_value = '_wpft_pb_cpt_settings';

			// Prepare the SQL query
			$query = $wpdb->prepare(
				"UPDATE {$wpdb->prefix}options
    			SET option_name = REPLACE(option_name, %s, %s)
    			WHERE option_name LIKE %s",
				$old_value,
				$new_value,
				'%' . $old_value . '%'
			);

			// Run the query
			$wpdb->query( $query );
		}*/

		function remove_association_edit_link( $options ) {
			foreach ( $options as $k => $v ) {
				unset( $options[ $k ]['edit_link'] );
			}

			return $options;
		}

		/**
		 * add_css_classes_to_page_builder_module.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param $classes
		 *
		 * @return mixed
		 */
		function add_css_classes_to_page_builder_module( $classes ) {
			$classes[] = 'col-full';

			return $classes;
		}

		/**
		 * @return Modules
		 */
		public function get_modules(): Modules {
			return $this->modules;
		}


	}
}