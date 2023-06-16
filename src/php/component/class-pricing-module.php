<?php
/**
 * WPFactory theme - Pricing Module.
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

if ( ! class_exists( 'WPFactory\WPFactory_Theme\Component\Pricing_Module' ) ) {

	//class Menus {
	class Pricing_Module implements Theme_Component {
		public function init() {
			add_filter( 'wpft_pricing_template_info', array( $this, 'add_extra_template_vars' ), 10, 2 );
			add_filter( 'timber/twig', array( $this, 'add_functions_to_twig' ) );
		}

		/**
		 * add_functions_to_twig.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param $twig
		 *
		 * @return mixed
		 */
		function add_functions_to_twig( $twig ) {
			$twig->addFunction( new \Timber\Twig_Function( 'wpft_get_prod_variation_by_attributes', array(
				$this,
				'wpft_get_prod_variation_by_attributes'
			) ) );

			return $twig;
		}

		/**
		 * wpft_get_prod_variation_by_attributes.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param $product
		 * @param $required_attributes
		 *
		 * @return mixed|string
		 */
		function wpft_get_prod_variation_by_attributes( $product, $required_attributes ) {
			$returned_variation = '';
			if ( 'variable' === $product->get_type() && ! empty( $variations = $product->get_available_variations() ) ) {
				foreach ( $variations as $variation ) {
					$formatted_attributes = call_user_func_array( 'array_merge', array_map( function ( $k, $v ) {
						return array( preg_replace( '/^attribute_pa_/', '', $k ) => $v );
					}, array_keys( $variation['attributes'] ), $variation['attributes'] ) );
					if ( count( $required_attributes ) === count( array_intersect_assoc( $formatted_attributes, $required_attributes ) ) ) {
						$returned_variation = $variation;
						break;
					}
				}
			}

			return $returned_variation;
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

			// Gets single price variation.
			$variation = $this->wpft_get_prod_variation_by_attributes( $vars['product'], wp_list_pluck( $attributes, 'attribute_term', 'attribute' ) );
			if ( ! empty( $variation ) ) {
				$vars['variation'] = $variation;
			}
			if (
				! empty( $variation ) &&
				true === filter_var( carbon_get_theme_option( 'wpft_bundles_enabled' ), FILTER_VALIDATE_BOOLEAN )
			) {
				$vars['bundles_enabled'] = true;

				// Gets bundle products.
				$bundles_class           = wpft_get_theme()->get_component( 'Bundles' );
				$bundle_products         = $bundles_class->wpft_get_bundle_products( $variation );
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

			return $vars;
		}
	}
}