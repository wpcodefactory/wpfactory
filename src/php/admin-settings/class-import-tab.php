<?php
/**
 * WPFactory theme - Import tab.
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

if ( ! class_exists( 'WPFactory\WPFactory_Theme\Admin_Settings\Import_Tab' ) ) {

	class Import_Tab extends Tab {
		/**
		 * init.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return void
		 */
		function init() {
			$this->create_settings();
		}

		function create_settings() {
			$this->get_container()->add_tab( __( 'Import', 'wpfactory' ), array(
				Field::make( 'separator', 'wpft_separator_import', __( 'Import', 'wpfactory' ) ),
				Field::make( 'file', 'wpft_pb_import_file', __( 'Import file' ) ),
				Field::make( 'checkbox', 'wpft_pb_import', __( 'Import' ) ),
			) );
		}
	}
}