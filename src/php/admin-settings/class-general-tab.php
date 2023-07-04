<?php
/**
 * WPFactory theme - General tab.
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

if ( ! class_exists( 'WPFactory\WPFactory_Theme\Admin_Settings\General_Tab' ) ) {

	class General_Tab extends Tab {

		/**
		 * init.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return void
		 */
		function init() {
			$container = $this->get_container()->add_tab( __( 'General', 'wpfactory' ), array(
				Field::make( 'separator', 'wpft_wc_separator_attributes', __( 'WooCommerce attributes', 'wpfactory' ) ),
				Field::make( 'complex', 'wpft_wc_attributes', 'Single pricing and Bundle products' )
				     ->set_collapsed( true )
				     ->add_fields(
					     array(
						     Field::make( 'text', 'attribute', __( 'Attribute', 'wpfactory' ) )->set_width( '50%' ),
						     Field::make( 'text', 'attribute_term', __( 'Attribute term', 'wpfactory' ) )->set_width( '50%' )
					     )
				     )
				     ->set_header_template( function () {
					     return '
						    <% if (attribute) { %>
						        <%- attribute %>
						    <% } %>
						    <% if (attribute_term) { %>
						        (<%- attribute_term %>)
						    <% } %>
				          ';
				     } )
				     ->setup_labels( array(
					     'plural_name'   => 'Attributes',
					     'singular_name' => 'Attribute',
				     ) ),
				Field::make( 'complex', 'wpft_wc_attributes_all_plugins_access', 'All plugins access' )
				     ->set_collapsed( true )
				     ->add_fields(
					     array(
						     Field::make( 'text', 'attribute', __( 'Attribute', 'wpfactory' ) )->set_width( '50%' ),
						     Field::make( 'text', 'attribute_term', __( 'Attribute term', 'wpfactory' ) )->set_width( '50%' )
					     )
				     )
				     ->set_header_template( function () {
					     return '
						    <% if (attribute) { %>
						        <%- attribute %>
						    <% } %>
						    <% if (attribute_term) { %>
						        (<%- attribute_term %>)
						    <% } %>
				          ';
				     } )
				     ->setup_labels( array(
					     'plural_name'   => 'Attributes',
					     'singular_name' => 'Attribute',
				     ) ),
				Field::make( 'separator', 'wpft_separator_bundles', __( 'Bundles', 'wpfactory' ) ),
				Field::make( 'checkbox', 'wpft_bundles_enabled', __( 'Enable Bundles', 'wpfactory' ) )->set_default_value( true ),
				//Field::make( 'text', 'wpft_bundles_discount', __( 'Discount', 'wpfactory' ) )->set_default_value( 0.8 )->set_attribute( 'type', 'number' )->set_attribute( 'step', '0.01' )->set_attribute( 'max', 1 )->set_attribute( 'min', 0 ),
				Field::make( 'association', 'wpft_bundles_discount_coupon', __( 'Discount coupon', 'wpfactory' ) )->set_help_text('<ul style="list-style: inside"><li>The coupon has to be set with a percentage discount.</li><li>The coupon has to exclude the All Plugins access product.</li></ul>')
				     ->set_types( array(
					array(
						'type'      => 'post',
						'post_type' => 'shop_coupon',
					)
				) ),
				Field::make( 'text', 'wpft_bundle_products_qty', __( 'Bundle products quantity', 'wpfactory' ) )->set_default_value( 3 )->set_attribute( 'type', 'number' )->set_attribute( 'step', '1' )->set_attribute( 'min', 0 ),
				Field::make( 'separator', 'wpft_separator_all_plugins', __( 'All plugins access', 'wpfactory' ) )->set_help_text('Special product that gives access to all plugins.'),
				Field::make( 'checkbox', 'wpft_all_plugins_access_enabled', __( 'Enable all plugins access product', 'wpfactory' ) )->set_default_value( true ),
				Field::make( 'association', 'wpft_all_plugins_access_product', '' )->set_types( array(
					array(
						'type'      => 'post',
						'post_type' => 'product',
					)
				) )
				->set_max( 1 ),
				Field::make( 'checkbox', 'wpft_hide_nav_menu_item_label', __( 'Add option to hide menu item label', 'wpfactory' ) )
				     ->set_default_value( true )
				     ->set_option_value( 'yes' )
				     ->set_help_text( sprintf( __( 'Allows hiding the <a href="%s">nav menu item</a> label.' ), admin_url( 'nav-menus.php' ) ) ),
				Field::make( 'separator', 'wpft_separator_free_vs_pro', __( 'Free vs Pro', 'wpfactory' ) ),
				Field::make( 'checkbox', 'wpft_free_vs_pro_cmb_enabled', __( 'Add a "Free vs Pro" meta box on admin products pages', 'wpfactory' ) )
				     ->set_default_value( true )
				     ->set_option_value( 'yes' ),
				     //->set_help_text( sprintf( __( 'Allows adding an icon on each <a href="%s">nav menu item</a>.' ), admin_url( 'nav-menus.php' ) ) ),
				Field::make( 'separator', 'wpft_separator', __( 'Menu', 'wpfactory' ) ),
				Field::make( 'checkbox', 'wpft_add_menu_icons', __( 'Add menu option to choose icons', 'wpfactory' ) )
				     ->set_default_value( true )
				     ->set_option_value( true )
				     ->set_help_text( sprintf( __( 'Allows adding an icon on each <a href="%s">nav menu item</a>.' ), admin_url( 'nav-menus.php' ) ) ),
			) );

			$this->handle_nav_menu_item_options();
		}

		/**
		 * handle_nav_menu_item_option.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return void
		 */
		function handle_nav_menu_item_options() {
			if ( 'yes' === wpft_get_option( '_wpft_add_menu_icons', 'yes' ) ) {
				Container::make( 'nav_menu_item', __( 'Menu Settings', 'wpfactory' ) )
				         ->add_fields( array(
					         Field::make( 'select', 'wpft_icon', __( 'Icon', 'wpfactory' ) )
					              ->add_options( apply_filters( 'wpft_nav_menu_item_icons', array() ) )
				         ) );
			}
			if ( 'yes' === wpft_get_option( '_wpft_hide_nav_menu_item_label', 'yes' ) ) {
				Container::make( 'nav_menu_item', __( 'Menu Settings', 'wpfactory' ) )
				         ->add_fields( array(
					         Field::make( 'checkbox', 'wpft_hide_label', __( 'Hide label', 'wpfactory' ) )
				         ) );
			}
		}
	}
}