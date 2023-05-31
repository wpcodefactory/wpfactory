<?php
/**
 * WPFactory theme - Carbon Fields Data store.
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  WPFactory
 */

namespace WPFactory\WPFactory_Theme\Carbon_Fields;

use \Carbon_Fields\Container;
use \Carbon_Fields\Field;
use \Carbon_Fields\Datastore\Post_Meta_Datastore;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'WPFactory\WPFactory_Theme\Carbon_Fields\Carbon_Fields_Sanitizing' ) ) {

	class Carbon_Fields_Sanitizing {
		static function sanitize_field( \Carbon_Fields\Field\Field $field, $value ) {
			switch ( $field->type ) {
				default:
					$value = wp_kses_post( $value );
			}
			return $value;
		}
	}
}