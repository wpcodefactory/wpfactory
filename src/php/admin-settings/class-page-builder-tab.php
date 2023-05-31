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
			$container = $this->get_container()->add_tab( __( 'Page builder', 'wpfactory' ), array(
				Field::make( 'separator', 'wpft_separator_1', __( 'Modules', 'wpfactory' ) ),
				Field::make( 'multiselect', 'wpft_modules_admin_cpt', 'The custom post type(s) on admin the modules should be loaded on' )
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
					          ' ),
			) );
		}
	}
}