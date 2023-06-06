<?php
/**
 * WPFactory theme - Page builder.
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  WPFactory
 */

namespace WPFactory\WPFactory_Theme\Page_Builder;


if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'WPFactory\WPFactory_Theme\Page_Builder\Page_Builder' ) ) {

	class Page_Builder {
		/**
		 * Init.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return void
		 */
		function init() {
			// Modules
			$modules = new Modules();
			$modules->init();
			// Codemirror field
			$codemirror_field = new Codemirror_Field();
			$codemirror_field->init();
		}
	}
}