<?php
/**
 * WPFactory theme - Page builder tab.
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

if ( ! class_exists( 'WPFactory\WPFactory_Theme\Admin_Settings\Page_Builder_Tab' ) ) {

	class Page_Builder_Tab extends Tab {

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

			/*$this->get_container()->add_tab( __( 'Page builder', 'wpfactory' ), array(
				Field::make( 'complex', 'wpft_page_builder_settings', __( 'Settings' ) )
				     ->set_collapsed( true )
				     ->add_fields( array(
					     Field::make( 'select', 'cpt', 'Custom post type' )
					          ->set_options( function () {
						          return $this->get_formatted_post_types();
					          } ),
					     Field::make( 'complex', 'display_areas', __( 'Display areas' ) )
					          ->set_collapsed( true )
					          ->add_fields( array(
						          Field::make( 'text', 'area_name', 'Area name' ),
						          Field::make( 'textarea', 'hooks', 'Display hooks' ),
					          ) )
						     ->set_header_template( '
									    <% if (area_name) { %>
									        <%- area_name %>
									    <% } %>
							          ' ),


				     ) )
				     ->set_header_template( function () {
					     return 'Post type - <%- cpt %>';
				     } ),
					Field::make( 'separator', 'wpft_separator_1', __( 'Default modules', 'wpfactory' ) )

			) );*/


			/*Field::make( 'separator', 'wpft_separator_1', __( 'Modules', 'wpfactory' ) ),
				Field::make( 'set', 'wpft_modules_admin_cpt', 'The custom post type(s) on admin the modules should be loaded on' )
				     ->set_options( function () {
					     $post_types           = get_post_types( array(
						     'public' => true,
					     ), 'objects' );
					     $formatted_post_types = array();
					     foreach ( $post_types as $post_type_id => $post_type ) {
						     $formatted_post_types[ $post_type_id ] = $post_type->label;
					     }

					     return $formatted_post_types;
				     } ),
				Field::make( 'complex', 'wpft_modules_position_hooks', __( 'Position hooks' ) )
				     ->set_collapsed( true )
				     ->add_fields( array(
					     Field::make( 'text', 'action_hook', 'Action hook' ),
				     ) )
				     ->set_header_template( '
							    <% if (action_hook) { %>
							        <%- action_hook %>
							    <% } %>
					          ' ),*/
		}

		function create_settings() {
			$this->get_container()->add_tab( __( 'Page builder', 'wpfactory' ), array(
				Field::make( 'complex', 'wpft_pb_cpt_settings', __( 'Post type settings' ) )
				     ->set_collapsed( true )
				     ->add_fields( array(
					     Field::make( 'select', 'cpt_relation', 'Custom post type' )
					          ->set_options( function () {
						          return $this->get_formatted_post_types();
					          } ),
					     Field::make( 'complex', 'display_areas', __( 'Display areas' ) )
					          ->set_collapsed( true )
					          ->add_fields( array(
						          Field::make( 'text', 'area_name', 'Area name' )->set_width('30%'),
						          Field::make( 'text', 'id', 'ID' )->set_width( '30%' ),
						          Field::make( 'checkbox', 'editable', 'Editable' )->set_width('30%')->set_default_value(true),
						          Field::make( 'text', 'hook', 'Display hook' )->set_width( '50%' ),
						          Field::make( 'text', 'hook_priority', 'Hook priority' )->set_width( '50%' ),
						          Field::make( 'complex', 'default_modules', 'Default Modules' )
							          ->set_collapsed( true )
							          ->add_fields( array(
								          Field::make( 'select', 'module_id', 'Module' )
								               ->set_options( function () {
									               return wpft_get_theme()->get_component('Page_Builder')->get_modules()->get_modules_posts_formatted();
								               } )
							          ))
							          ->set_header_template( function () {
								          $modules_formatted_json = json_encode( wpft_get_theme()->get_component('Page_Builder')->get_modules()->get_modules_posts_formatted() );

								          return '<%-' . $modules_formatted_json . '[module_id] %>';
							          } )
						          /*Field::make( 'association', 'default_modules', 'Default modules' )->set_types( array(
							          array(
								          'type'      => 'post',
								          'post_type' => 'wpft_module',
							          )
						          ) )*/

					          ) )
					          ->set_header_template( '
									    <% if (area_name) { %>
									        <%- area_name %>
									    <% } %>
							          ' ),


				     ) )
				     ->set_header_template( function () {
					     return 'Post type - <%- cpt_relation %>';
				     } ),

			) );


		}

		function get_formatted_post_types() {
			$post_types           = get_post_types( array(
				'public' => true,
			), 'objects' );
			$formatted_post_types = array();
			foreach ( $post_types as $post_type_id => $post_type ) {
				$formatted_post_types[ $post_type_id ] = $post_type->label;
			}

			return $formatted_post_types;
		}
	}
}