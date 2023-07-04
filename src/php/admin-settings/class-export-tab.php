<?php
/**
 * WPFactory theme - Export tab.
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

if ( ! class_exists( 'WPFactory\WPFactory_Theme\Admin_Settings\Export_Tab' ) ) {

	class Export_Tab extends Tab {
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

		function get_taxonomies_formatted() {
			$taxonomies    = get_taxonomies();
			$taxonomy_data = array();

			if ( $taxonomies ) {
				//sort( $taxonomies );

				foreach ( $taxonomies as $taxonomy ) {
					$taxonomy_obj   = get_taxonomy( $taxonomy );
					$taxonomy_id    = $taxonomy_obj->name;
					$taxonomy_label = $taxonomy_obj->label;

					$taxonomy_data[ $taxonomy_id ] = $taxonomy_label . ' ' . '(' . $taxonomy_obj->name . ')';
				}
				// Sort taxonomy data based on labels
				uasort( $taxonomy_data, function ( $a, $b ) {
					return strcasecmp( $a, $b );
				} );
			}
			return $taxonomy_data;
		}

		function get_formatted_post_types() {
			$post_types           = get_post_types( array(//'public' => true,
			), 'objects' );
			$formatted_post_types = array();
			foreach ( $post_types as $post_type_id => $post_type ) {
				$formatted_post_types[ $post_type_id ] = $post_type->label . ' ' . '(' . $post_type_id . ')';
			}
			uasort( $formatted_post_types, function ( $a, $b ) {
				return strcasecmp( $a, $b );
			} );

			return $formatted_post_types;
		}

		function get_specific_pages_default_value() {
			$home_page = get_page_by_path( 'home' );
			if ( ! empty( $home_page ) ) {
				return array( 'post:page:' . $home_page->ID );
			} else {
				return array();
			}
		}

		function create_settings() {
			$this->get_container()->add_tab( __( 'Export', 'wpfactory' ), array(
				Field::make( 'separator', 'wpft_separator_export', __( 'Export', 'wpfactory' ) ),
				Field::make( 'set', 'wpft_pb_export_cpts', __( 'Post types' ) )
				     ->set_options( function () {
					     return $this->get_formatted_post_types();
				     } )
				     ->set_default_value( array( 'product', 'wpft_faq', 'wpft_website' ) ),
				Field::make( 'association', 'wpft_pb_export_pages', __( 'Specific pages' ) )
				     ->set_types( array(
					     array(
						     'type'      => 'post',
						     'post_type' => 'page',
					     )
				     ) )
				->set_default_value(  $this->get_specific_pages_default_value() ),
				Field::make( 'set', 'wpft_pb_export_taxonomies', __( 'Taxonomies' ) )
				     ->set_options( function () {
					     return $this->get_taxonomies_formatted();
				     } )
				     ->set_default_value( array( 'product_cat', 'product_tag', 'wpft_faq_category' ) ),
				Field::make( 'checkbox', 'wpft_pb_export', __( 'Export' ) ),
			) );
		}
	}
}