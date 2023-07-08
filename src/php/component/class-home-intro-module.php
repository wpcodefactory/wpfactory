<?php
/**
 * WPFactory theme - Home intro - Module
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  WPFactory
 */

namespace WPFactory\WPFactory_Theme\Component;

use WPFactory\WPFactory_Theme\Theme_Component;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'WPFactory\WPFactory_Theme\Component\Home_Intro_Module' ) ) {

	//class Menus {
	class Home_Intro_Module implements Theme_Component {
		public function init() {
			add_filter( 'wpft_module_home_intro_template_vars', array( $this, 'add_extra_template_vars' ), 10, 2 );
		}
		function add_extra_template_vars( $vars ) {
			$products_component = wpft_get_theme()->get_component( 'Products' );
			if ( '' === $vars['plugins_total'] ) {
				$vars['plugins_total'] = $products_component->get_products_amount();
			}
			if ( '' === $vars['active_installs'] ) {
				$vars['active_installs'] = $products_component->get_all_products_active_installs();
			}

			return $vars;
		}
	}
}