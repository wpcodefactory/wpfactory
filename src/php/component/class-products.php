<?php
/**
 * WPFactory theme - Products.
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

if ( ! class_exists( 'WPFactory\WPFactory_Theme\Component\Products' ) ) {

	//class Menus {
	class Products implements Theme_Component {

		protected $all_plugins_access_id = null;

		public function init() {
			add_action( 'after_setup_theme', array( $this, 'setup_woocommerce_product' ) );
			add_filter( 'woocommerce_add_to_cart_redirect', function ( $url, $adding_to_cart ) {
				return wc_get_cart_url();
			}, 10, 2 );
			// Searches wc_get_products by attribute.
			add_filter( 'woocommerce_product_data_store_cpt_get_products_query', array(
				$this,
				'find_variation_by_attributes'
			), 10, 2 );
			// Add product loop container.
			add_filter( 'woocommerce_before_shop_loop_item', array( $this, 'start_shop_loop_product_wrapper' ), 1 );
			add_filter( 'woocommerce_after_shop_loop_item', array( $this, 'close_shop_loop_product_wrapper' ), 100 );
			// Product loop excerpt.
			add_filter( 'woocommerce_after_shop_loop_item_title', array( $this, 'add_shop_loop_excerpt' ), 5 );
			add_filter( 'woocommerce_short_description', array( $this, 'handle_loop_excerpt_length' ) );
			// Product loop tags.
			add_filter( 'woocommerce_after_shop_loop_item_title', array( $this, 'add_shop_loop_tags' ), 6 );
			add_filter( 'term_links-product_tag', array( $this, 'add_tag_class_to_product_tags' ) );
			// Product Loop Thumbnail size.
			add_filter( 'single_product_archive_thumbnail_size', array(
				$this,
				'change_product_archive_thumbnail_size'
			) );
			// Product columns total.
			add_filter( 'loop_shop_columns', array( $this, 'loop_shop_columns_total' ), 999 );
			add_filter( 'woocommerce_post_class', array( $this, 'add_css_classes_to_product_loop' ) );
			// All plugins access info
			add_filter( 'woocommerce_after_shop_loop_item_title', array(
				$this,
				'add_all_plugins_access_info_to_loop'
			), 7 );
			// Add functions to Timber.
			add_filter( 'timber/twig', array( $this, 'add_functions_to_twig' ) );
			// Delete products total meta
			add_action( 'updated_post_meta', array( $this, 'delete_products_total_meta' ), 10, 4 );
			add_action( 'added_post_meta', array( $this, 'delete_products_total_meta' ), 10, 4 );
			add_action( 'deleted_post_meta', array( $this, 'delete_products_total_meta' ), 10, 4 );
		}

		/**
		 * Delelte products total meta when some price changes.
		 *
		 * @version 2.5.1
		 * @since   2.5.1
		 *
		 * @param $meta_id
		 * @param $post_id
		 * @param $meta_key
		 * @param $meta_value
		 */
		function delete_products_total_meta( $meta_id, $post_id, $meta_key, $meta_value ) {
			if (
				in_array( $meta_key, array(
					'_price',
					'_sale_price',
				) ) &&
				is_a( $product = wc_get_product( $post_id ), 'WC_Product' )
			) {
				global $wpdb;
				$prefix           = '_transient_wpft_products_total_'; // Replace with your desired prefix
				$meta_key_pattern = $wpdb->esc_like( $prefix ) . '%';
				$query            = $wpdb->prepare( "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s", $meta_key_pattern );
				$wpdb->query( $query );
			}
		}

		function get_all_plugins_access_id() {
			if ( ! is_null( $this->all_plugins_access_id ) ) {
				return $this->all_plugins_access_id;
			}
			if (
				true === filter_var( carbon_get_theme_option( 'wpft_all_plugins_access_enabled' ), FILTER_VALIDATE_BOOLEAN ) &&
				! empty( $all_plugins_product_info = carbon_get_theme_option( 'wpft_all_plugins_access_product' ) )
			) {
				$this->all_plugins_access_id = $all_plugins_product_info[0]['id'];
			} else {
				$this->all_plugins_access_id = false;
			}

			return $this->all_plugins_access_id;
		}

		function add_css_classes_to_product_loop( $classes ) {
			if ( is_shop() ) {
				$classes = array_merge( $classes, array(
					'column',
					'is-4',
				) );
				if (
					false !== ( $all_plugins_access_id = $this->get_all_plugins_access_id() )
				) {
					global $product;
					$all_plugins_access_css = $product->get_id() === (int) $all_plugins_access_id ? 'all-plugins-access-loop' : '';
					if ( ! empty( $all_plugins_access_css ) ) {
						$classes = array_merge( $classes, array( $all_plugins_access_css ) );
					}
				}
			}

			return $classes;
		}

		function add_all_plugins_access_info_to_loop() {
			global $product;
			if (
				false !== ( $all_plugins_access_id = $this->get_all_plugins_access_id() ) &&
				$product->get_id() === (int) $all_plugins_access_id
			) {
				$vars['all_plugins_access_enabled'] = true;
				$info                               = $this->get_all_plugins_access_info( $all_plugins_access_id );
				echo sprintf( '<div class="savings">%s</div>', sprintf( __( 'Save %s', 'wpfactory' ), $info['all_plugins_savings_formatted'] ) );
				echo wc_get_formatted_variation( $info['all_plugins_variation']['attributes'] );
				echo sprintf( '<div class="round-check-list-item-type2">%s</div>', __( '30-day money-back guarantee', 'wpfactory' ) );
			}
		}

		function get_all_plugins_access_info( $all_plugins_access_id ) {
			$info                                  = array();
			$all_plugins_product                   = wc_get_product( $all_plugins_access_id );
			$all_plugins_attributes                = carbon_get_theme_option( 'wpft_wc_attributes_all_plugins_access' );
			$all_plugins_attributes_flat           = wp_list_pluck( $all_plugins_attributes, 'attribute_term', 'attribute' );
			$all_plugins_attributes_formatted      = call_user_func_array( 'array_merge', array_map( function ( $k, $v ) {
				return array( str_replace( $k, 'attribute_pa_' . $k, $k ) => $v );
			}, array_keys( $all_plugins_attributes_flat ), $all_plugins_attributes_flat ) );
			$all_plugins_variation                 = $this->wpft_get_prod_variation_by_attributes( $all_plugins_product, $all_plugins_attributes_flat );
			$variations_price_total                = $this->get_products_prices_total( array(
				'attributes' => $all_plugins_attributes_formatted,
				'exclude'    => array( $all_plugins_variation['variation_id'] )
			) );
			$info['all_plugins_parent']            = $all_plugins_product;
			$info['all_plugins_variation']         = $all_plugins_variation;
			$info['all_plugins_savings_formatted'] = round( ( ( ( $variations_price_total - $all_plugins_variation['display_price'] ) / $variations_price_total ) ) * 100 ) . '%';

			return $info;
		}

		function loop_shop_columns_total() {
			return 3; // 3 products per row
		}

		function change_product_archive_thumbnail_size( $size ) {
			$size = 'size-1';

			return $size;
		}

		function start_shop_loop_product_wrapper() {
			echo '<div class="wpft-product-loop-content">';
		}

		function close_shop_loop_product_wrapper() {
			echo '</div>';
		}

		function add_tag_class_to_product_tags( $term_links ) {
			return array_map( function ( $v ) {
				return str_replace( '<a ', '<a class="tag"', $v );
			}, $term_links );
		}

		function find_variation_by_attributes( $query, $query_vars ) {
			if ( ! empty( $query_vars['attributes'] ) ) {
				$meta_query_data = array();
				foreach ( $query_vars['attributes'] as $k => $v ) {
					$meta_query_data[] = array(
						'key'     => $k,
						'value'   => $v,
						'compare' => '='
					);
				}
				$query['meta_query'][] = array( $meta_query_data );

			}

			return $query;
		}

		function setup_woocommerce_product() {
			remove_all_actions( 'woocommerce_before_single_product' );
			remove_all_actions( 'woocommerce_before_single_product_summary' );
			remove_all_actions( 'woocommerce_single_product_summary' );
			remove_all_actions( 'woocommerce_after_single_product_summary' );
			remove_all_actions( 'woocommerce_after_single_product' );
			// Remove add to cart button from loop.
			remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart' );
			// Remove loop review.
			remove_filter( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
			// Remove loop price.
			remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
			// Add id on features area.
			//add_action( 'woocommerce_after_single_product_summary', array( $this, 'add_id_on_features_area' ), 12 );
			//add_filter( 'wpft_module_prod_feature_before_content', array( $this, 'add_id_on_features_area' ) );
		}


		function add_shop_loop_tags() {
			global $product;
			$tags_string = wc_get_product_tag_list( $product->get_id(), ' ' );
			if ( ! empty( $tags_string ) ) {
				echo '<div class="wpft-product-tags">' . $tags_string . '</div>';
			}
		}

		function add_shop_loop_excerpt() {
			global $product;
			echo '<div class="wpft-product-loop-short-description">' . apply_filters( 'woocommerce_short_description', $product->get_short_description() ) . '</div>';
		}

		function handle_loop_excerpt_length( $post_post_excerpt ) {
			if ( ! is_product() ) { // add in conditionals
				$text  = $post_post_excerpt;
				$words = 10; // change word length
				$more  = '…'; // add a more cta

				$post_post_excerpt = wp_trim_words( $text, $words, $more );
			}

			return $post_post_excerpt;
		}

		function get_products_prices_total( $args = null ) {
			$args           = wp_parse_args( $args, array(
				'limit' => - 1,
				'type'  => 'variation',
			) );
			$transient_name = 'wpft_products_total_' . md5( serialize( $args ) );
			if ( false === ( $prices_total = get_transient( $transient_name ) ) ) {
				$products     = wc_get_products( $args );
				$prices_total = 0;
				foreach ( $products as $product ) {
					$prices_total += $product->get_price();
				}
				set_transient( $transient_name, $prices_total );
			}

			return $prices_total;
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
		 * @return array|string
		 */
		function wpft_get_prod_variation_by_attributes( $product, $required_attributes ) {
			$required_attributes = $this->maybe_remove_attributes_prefix( $required_attributes );
			$returned_variation  = '';
			if ( 'variable' === $product->get_type() && ! empty( $variations = $product->get_available_variations() ) ) {
				foreach ( $variations as $variation ) {
					$formatted_attributes = $this->maybe_remove_attributes_prefix( $variation['attributes'] );
					//error_log('---');
					//error_log(print_r($formatted_attributes,true));
					//error_log(print_r($required_attributes,true));

					if ( count( $required_attributes ) === count( array_intersect_assoc( $formatted_attributes, $required_attributes ) ) ) {
						$returned_variation = $variation;
						break;
					}
				}
			}

			return $returned_variation;
		}

		function maybe_remove_attributes_prefix( $attributes ) {
			return call_user_func_array( 'array_merge', array_map( function ( $k, $v ) {
				return array( preg_replace( '/^attribute_pa_/', '', $k ) => $v );
			}, array_keys( $attributes ), $attributes ) );
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

			$twig->addFunction( new \Timber\Twig_Function( 'wpft_get_prod_docs_page_url', array(
				$this,
				'wpft_get_prod_docs_page_url'
			) ) );

			$twig->addFunction( new \Timber\Twig_Function( 'wpft_get_prod_support_page_url', array(
				$this,
				'wpft_get_prod_support_page_url'
			) ) );

			return $twig;
		}

		function wpft_get_prod_docs_page_url( $product_id = null ) {
			global $product;
			$product_id = is_null( $product_id ) ? $product->get_id() : $product_id;
			$product    = ! is_a( $product, 'WC_Product' ) ? wc_get_product( $product_id ) : $product;
			$permalink  = trailingslashit( site_url() ) . 'docs' . '/' . $product->get_slug();

			return esc_url( $permalink );
		}

		function wpft_get_prod_support_page_url( $product_id = null ) {
			global $product;
			$product_id     = is_null( $product_id ) ? $product->get_id() : $product_id;
			$prod_permalink = get_permalink( $product_id );
			$array_from_to  = array(
				site_url() => trailingslashit( site_url() ) . 'support'
			);
			$permalink      = str_replace( array_keys( $array_from_to ), $array_from_to, $prod_permalink );

			return esc_url( $permalink );
		}

		/*function setup_page_builder() {
			do_action( 'wpft_product_modules_wrapper' );
		}*/
	}
}