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
		 * Init.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return void
		 */
		function init() {
			// Modules.
			$modules = new Modules();
			$modules->init();
			// Codemirror field.
			$codemirror_field = new Codemirror_Field();
			$codemirror_field->init();
			// Adds css classes to page builder modules.
			add_filter( 'wpft_module_css_classes', array( $this, 'add_css_classes_to_page_builder_module' ) );
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
	}
}