<?php
/**
 * WPFactory theme - Admin Settings.
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
				Field::make( 'separator', 'wpft_separator', __( 'Menu', 'wpfactory' ) ),
				Field::make( 'checkbox', 'wpft_add_menu_icons', __( 'Add menu option to choose icons', 'wpfactory' ) )
				     ->set_default_value( true )
				     ->set_option_value( 'yes' )
				     ->set_help_text( sprintf( __( 'Allows adding an icon on each <a href="%s">nav menu item</a>.' ), admin_url( 'nav-menus.php' ) ) ),
				Field::make( 'checkbox', 'wpft_hide_nav_menu_item_label', __( 'Add option to hide menu item label', 'wpfactory' ) )
				     ->set_default_value( true )
				     ->set_option_value( 'yes' )
				     ->set_help_text( sprintf( __( 'Allows hiding the <a href="%s">nav menu item</a> label.' ), admin_url( 'nav-menus.php' ) ) ),
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
			if ( 'yes' === wpf_get_option( '_wpft_add_menu_icons', 'yes' ) ) {
				Container::make( 'nav_menu_item', __( 'Menu Settings', 'wpfactory' ) )
				         ->add_fields( array(
					         Field::make( 'select', 'wpft_icon', __( 'Icon', 'wpfactory' ) )
					              ->add_options( apply_filters( 'wpft_nav_menu_item_icons', array() ) )
				         ) );
			}
			if ( 'yes' === wpf_get_option( '_wpft_hide_nav_menu_item_label', 'yes' ) ) {
				Container::make( 'nav_menu_item', __( 'Menu Settings', 'wpfactory' ) )
				         ->add_fields( array(
					         Field::make( 'checkbox', 'wpft_hide_label', __( 'Hide label', 'wpfactory' ) )
				         ) );
			}
		}
	}
}