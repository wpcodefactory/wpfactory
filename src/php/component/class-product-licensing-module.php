<?php
/**
 * WPFactory theme - Product Licensing Module.
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

if ( ! class_exists( 'WPFactory\WPFactory_Theme\Component\Product_Licensing_Module' ) ) {

	//class Menus {
	class Product_Licensing_Module implements Theme_Component {
		public function init() {
			add_filter( 'wpft_module_prod_licensing_template_vars', array( $this, 'add_extra_template_vars' ), 10, 2 );
		}

		function are_required_keys_present( $array, $required_keys ) {
			return count( array_intersect_key( array_flip( $required_keys ), $array ) ) === count( $required_keys );
		}

		function add_extra_template_vars( $vars, $module_id ) {
			if (
				! isset( $vars['product'] ) ||
				empty( $attributes = carbon_get_theme_option( 'wpft_wc_attributes' ) )
			) {
				return $vars;
			}

			// Products component.
			$productsComponent = wpft_get_theme()->get_component( 'Products' );

			// All plugins access.
			$all_plugins_variation = array();
			if (
				false !== ( $all_plugins_access_id = wpft_get_theme()->get_component( 'Products' )->get_all_plugins_access_id() )
			) {
				$vars['all_plugins_access_enabled']    = true;
				$info                                  = $productsComponent->get_all_plugins_access_info( $all_plugins_access_id );
				$vars['all_plugins_variation']         = $info['all_plugins_variation'];
				$vars['all_plugins_parent']            = $info['all_plugins_parent'];
				$all_plugins_variation                 = $info['all_plugins_variation'];
				$vars['all_plugins_savings_formatted'] = $info['all_plugins_savings_formatted'];
				if ( (int) $vars['product']->get_id() === $all_plugins_access_id ) {
					return $vars;
				}
			}

			// Gets single price variation.
			$variation = $productsComponent->wpft_get_prod_variation_by_attributes( $vars['product'], wp_list_pluck( $attributes, 'attribute_term', 'attribute' ) );
			if ( ! empty( $variation ) ) {
				$vars['variation'] = $variation;
			}

			// Handle bundle.
			if (
				! empty( $variation ) &&
				true === filter_var( carbon_get_theme_option( 'wpft_bundles_enabled' ), FILTER_VALIDATE_BOOLEAN )
			) {
				$vars['bundles_enabled'] = true;

				// Gets bundle products.
				$bundles_class           = wpft_get_theme()->get_component( 'Bundles' );
				$bundle_products         = $bundles_class->wpft_get_bundle_products( $variation, $all_plugins_variation );
				$vars['bundle_products'] = $bundle_products;

				// Bundle price.
				$bundle_price_info         = $bundles_class->wpft_calculate_bundle_price( array(
					'bundle_products' => $bundle_products,
					'variation_info'  => $variation,
				) );
				$vars['bundle_price_info'] = $bundle_price_info;

				// Bundle add to cart url.
				$url                            = $bundles_class->generate_bundle_add_to_cart_url( $variation, $bundle_products );
				$vars['bundle_add_to_cart_url'] = $url;
			}

			// Free plugin url.
			if ( ! empty( $free_version_slug = get_post_meta( $vars['product']->get_id(), 'wp_org_free_version_slug', true ) ) ) {
				$vars['free_plugin_url'] = 'https://wordpress.org/plugins/' . $free_version_slug;
			}

			// Free vs Pro.
			if (
				true === filter_var( carbon_get_theme_option( 'wpft_free_vs_pro_cmb_enabled' ), FILTER_VALIDATE_BOOLEAN ) &&
				! empty( $free_vs_pro_table = carbon_get_post_meta( $vars['product']->get_id(), 'wpft_free_vs_pro_table' ) )
			) {
				//error_log(print_r($free_vs_pro_table,true));
				$vars['free_vs_pro_table'] = $free_vs_pro_table;
			}

			return $vars;
		}
	}
}