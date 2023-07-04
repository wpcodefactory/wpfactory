<?php
/**
 * WPFactory theme - Free Vs Pro.
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  WPFactory
 */

namespace WPFactory\WPFactory_Theme\Component;

use Carbon_Fields\Container;
use Carbon_Fields\Field;
use WPFactory\WPFactory_Theme\Carbon_Fields\Carbon_Fields_Post_Meta_Datastore;
use WPFactory\WPFactory_Theme\Theme_Component;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'WPFactory\WPFactory_Theme\Component\Free_Vs_Pro' ) ) {

	class Free_Vs_Pro implements Theme_Component {
		public function init() {
			// Free vs pro cmb
			add_action( 'carbon_fields_register_fields', array( $this, 'create_free_vs_pro_cmb' ), 11 );
		}

		function create_free_vs_pro_cmb() {
			if (
				true === filter_var( carbon_get_theme_option( 'wpft_free_vs_pro_cmb_enabled' ), FILTER_VALIDATE_BOOLEAN )
			) {
				Container::make( 'post_meta', __( 'Free vs Pro table', 'wpfactory' ) )
				         ->set_datastore( new Carbon_Fields_Post_Meta_Datastore() )
				         ->where( 'post_type', '=', 'product' )
				         ->add_fields( array(
					         Field::make( 'complex', 'wpft_free_vs_pro_table', '' )
					              ->set_collapsed( true )
					              ->add_fields(
						              array(
							              Field::make( 'text', 'text', __( 'Text', 'wpfactory' ) )->set_width( '100%' ),
							              Field::make( 'checkbox', 'free', __( 'Free', 'wpfactory' ) )->set_default_value( true )->set_width( '50%' ),
							              Field::make( 'checkbox', 'ignore_columns', __( 'Ignore free and pro columns', 'wpfactory' ) )->set_width( '50%' )
						              )
					              )
					              ->set_header_template( function () {
						              return '
									    <% if (text) { %>
									        <%- text %>
									        <% if (!ignore_columns) { %>
											    -<% if (free) { %>
											        Free [✔]
											    <% }else{ %>
											        Free [ ]
											    <% } %>
											        Pro [✔]
											<% } %>
									    <% } %>									    
							          ';
					              } )
					              ->setup_labels( array(
						              'plural_name'   => 'Rows',
						              'singular_name' => 'Row',
					              ) ),
				         ) );
			}
		}
	}
}




