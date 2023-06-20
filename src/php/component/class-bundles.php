<?php
/**
 * WPFactory theme - Bundles.
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

if ( ! class_exists( 'WPFactory\WPFactory_Theme\Component\Bundles' ) ) {

	class Bundles implements Theme_Component {

		protected $bundle_savings_totals_in_cart = 0;

		public function init() {
			// Timber.
			add_filter( 'timber/twig', array( $this, 'add_functions_to_twig' ) );

			// Searches wc_get_products by attribute.
			add_filter( 'woocommerce_product_data_store_cpt_get_products_query', array(
				$this,
				'find_variation_by_attributes'
			), 10, 2 );

			// Add bundle to cart.
			add_action( 'wp_loaded', array( $this, 'woocommerce_maybe_add_multiple_products_to_cart' ), 15 );
			add_action( 'template_redirect', array( $this, 'redirect_to_cart_on_bundle_add_to_cart' ) );

			// Apply bundle coupon discount programmatically
			add_action( 'woocommerce_before_calculate_totals', array( $this, 'apply_bundle_coupon_dynamically' ) );

			// Bundle discount.
			/*add_action( 'woocommerce_before_calculate_totals', array(
				$this,
				'change_bundle_products_price_on_cart'
			), 9999 );
			add_filter( 'woocommerce_cart_item_price', array(
				$this,
				'change_bunde_products_price_html_on_cart'
			), 10, 3 );
			add_action( 'woocommerce_cart_totals_before_order_total', array(
				$this,
				'add_fake_bundle_discount_info_on_cart_totals'
			) );
			add_action( 'woocommerce_review_order_before_order_total', array(
				$this,
				'add_fake_bundle_discount_info_on_cart_totals'
			) );*/

			// JS.
			/*add_filter( 'wpft_js_modules_required', array( $this, 'load_bundle_js' ) );
			add_filter( 'wpft_frontend_js_info', array( $this, 'append_info_to_frontend_js' ) );
			add_filter( 'wpft_frontend_js_deps', array( $this, 'add_select2_as_frontend_js_dependency' ) );
			add_filter( 'wpft_frontend_css_deps', array( $this, 'add_select2_as_frontend_css_dependency' ) );
			// Timber.
			add_filter( 'timber/twig', array( $this, 'add_functions_to_twig' ) );
			// AJAX.
			add_action( 'wp_ajax_' . 'get_bundle_products', array( $this, 'bundle_select_callback' ) );
			add_action( 'wp_ajax_nopriv_' . 'get_bundle_products', array( $this, 'bundle_select_callback' ) );
			*/
		}

		function apply_bundle_coupon_dynamically( $cart ) {
			if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
				return;
			}

			if ( did_action( 'woocommerce_before_calculate_totals' ) >= 2 ) {
				return;
			}

			// Your settings
			$coupon_code = $this->get_bundle_discount_coupon()->get_code();

			// Initializing variables
			$applied_coupons = $cart->get_applied_coupons();
			$coupon_code     = sanitize_text_field( $coupon_code );

			// Minimum bundle products necessary to give discount.
			$bundle_products_min = carbon_get_theme_option( 'wpft_bundle_products_min' );

			// Need to compare cart products with all plugins access product?
			$all_plugins_product_info = carbon_get_theme_option( 'wpft_all_plugins_access_product' );
			$need_to_compare_products = false;
			$all_plugins_product_id   = 0;
			if (
				true === filter_var( carbon_get_theme_option( 'wpft_all_plugins_access_enabled' ), FILTER_VALIDATE_BOOLEAN ) &&
				! empty( $all_plugins_product_info ) &&
				isset( $all_plugins_product_info[0]['id'] ) &&
				is_a( $all_plugins_product = wc_get_product( $all_plugins_product_info[0]['id'] ), 'WC_Product' ) ) {
				$all_plugins_product_id   = empty( $parent_id = $all_plugins_product->get_parent_id() ) ? $all_plugins_product->get_id() : $parent_id;
				$need_to_compare_products = true;
			}

			// Checks how many "All access products" are in cart.
			$all_plugins_products_in_cart_total = 0;
			foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) {
				if (
					$need_to_compare_products &&
					$this->do_products_match_id( $all_plugins_product_id, $cart_item['data'] )
				) {
					$all_plugins_products_in_cart_total ++;
				}
				$cart_item['wpft_bundle_discount']          = false;
				WC()->cart->cart_contents[ $cart_item_key ] = $cart_item;
			}

			// Gives discount to all products but "All access products".
			if (
				count( $cart->get_cart() ) - $all_plugins_products_in_cart_total >= $bundle_products_min &&
				! in_array( $coupon_code, $applied_coupons )
			) {
				$cart->add_discount( $coupon_code );
			} elseif ( count( $cart->get_cart() ) - $all_plugins_products_in_cart_total < $bundle_products_min ) {
				$cart->remove_coupon( $coupon_code );
			}
		}

		function do_products_match_id( $product_id, $product ) {
			return (int) $product_id === (int) ( empty( $parent_id = $product->get_parent_id() ) ? $product->get_id() : $parent_id );
		}

		function redirect_to_cart_on_bundle_add_to_cart() {
			if (
				isset( $_GET['add-to-cart'] ) &&
				false !== strpos( $_GET['add-to-cart'], ',' )
			) {
				exit( wp_redirect( wc_get_cart_url() ) );
			}
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
			/*$twig->addFunction( new \Timber\Twig_Function( 'wpft_get_bundle_select', array(
				$this,
				'wpft_get_bundle_select'
			) ) );*/

			$twig->addFunction( new \Timber\Twig_Function( 'wpft_get_bundle_products', array(
				$this,
				'wpft_get_bundle_products'
			) ) );

			$twig->addFunction( new \Timber\Twig_Function( 'wpft_calculate_bundle_price', array(
				$this,
				'wpft_calculate_bundle_price'
			) ) );

			return $twig;
		}

		function get_bundle_discount_coupon() {
			$coupon_info = carbon_get_theme_option( 'wpft_bundles_discount_coupon' );
			if (
				! empty( $coupon_info ) && isset( $coupon_info[0] ) &&
				! empty( $coupon = new \WC_Coupon( wc_get_coupon_code_by_id( $coupon_info[0]['id'] ) ) ) &&
				'percent' === $coupon->get_discount_type( '' )
			) {
				return $coupon;
			} else {
				return false;
			}
		}

		function wpft_calculate_bundle_price( $args = null ) {
			$args = wp_parse_args( $args, array(
				'bundle_products' => array(),
				'variation_info'  => array(),
				//'discount'        => carbon_get_theme_option( 'wpft_bundles_discount' )
				'discount'        => $this->get_bundle_discount_coupon()->get_amount() / 100
			) );

			$discount       = $args['discount'];
			$variation_info = $args['variation_info'];
			if ( empty( $variation_info ) ) {
				return '';
			}
			$bundle_products    = $args['bundle_products'];
			$original_variation = wc_get_product( $variation_info['variation_id'] );
			$variations         = array_merge( array( $original_variation ), $bundle_products );
			$original_price     = 0;
			foreach ( $variations as $variation ) {
				$original_price += $variation->get_price();
			}

			return array(
				'price'             => wc_price( $original_price * ( 1 - $discount ) ),
				'savings'           => $discount,
				'savings_formatted' => ( $discount * 100 ) . '%',
			);
			//$original_variation
		}

		function generate_bundle_add_to_cart_url( $variation, $bundle_products ) {
			$product_ids = array( $variation['variation_id'] );
			foreach ( $bundle_products as $product ) {
				$product_ids[] = $product->get_id();
			}
			$products_url_arr = array();
			foreach ( $product_ids as $id ) {
				$products_url_arr[] = $id . ':1';
			}

			return esc_url_raw( add_query_arg( array(
				'add-to-cart' => implode( ",", $product_ids ),
			), wc_get_cart_url() ) );
		}

		function get_all_variations_from_parents( $variation_ids ) {
			$variations_ids = array();
			foreach ( $variation_ids as $variations_id ) {
				$variation = wc_get_product( $variations_id );
				if ( is_a( $parent_product = wc_get_product( $variation->get_parent_id() ), 'WC_Product' ) ) {
					$available_variations = $parent_product->get_available_variations();
					$variations_ids       = array_merge( $variations_ids, wp_list_pluck( $available_variations, 'variation_id' ) );
				}
			}

			return $variations_ids;
		}

		function wpft_get_bundle_products( $variation = null, $all_plugins_access_variation = null ) {
			$exclude_ids = ! empty( $variation ) ? array( $variation['variation_id'] ) : array();
			if ( ! empty( $all_plugins_access_variation ) ) {
				$exclude_ids[] = $all_plugins_access_variation['variation_id'];
			}
			$exclude_ids                 = $this->get_all_variations_from_parents( $exclude_ids );
			$products_args               = array(
				'limit'   => 1,
				'type'    => 'variation',
				'exclude' => $exclude_ids,
				'orderby' => 'rand',
			);
			$variation                   = wc_get_product( $variation['variation_id'] );
			$products_args['attributes'] = $variation->get_variation_attributes();
			// Get products.
			$products                 = wc_get_products( $products_args );
			$products_args['exclude'] = array_merge( $exclude_ids, array( $products[0]->get_id() ) );
			$products                 = array_merge( $products, wc_get_products( $products_args ) );

			return $products;
		}

		/**
		 * woocommerce_maybe_add_multiple_products_to_cart.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return void|null
		 * @throws \Exception
		 * @link    https://stackoverflow.com/a/60012299/1193038
		 *
		 */
		function woocommerce_maybe_add_multiple_products_to_cart() {
			// Make sure WC is installed, and add-to-cart qauery arg exists, and contains at least one comma.
			if ( ! class_exists( 'WC_Form_Handler' ) || empty( $_REQUEST['add-to-cart'] ) || false === strpos( $_REQUEST['add-to-cart'], ',' ) ) {
				return;
			}

			remove_action( 'wp_loaded', array( 'WC_Form_Handler', 'add_to_cart_action' ), 20 );

			$product_ids = explode( ',', $_REQUEST['add-to-cart'] );
			$count       = count( $product_ids );
			$number      = 0;

			foreach ( $product_ids as $product_id ) {
				if ( ++ $number === $count ) {
					// Ok, final item, let's send it back to woocommerce's add_to_cart_action method for handling.
					$_REQUEST['add-to-cart'] = $product_id;

					return \WC_Form_Handler::add_to_cart_action();
				}

				$product_id        = apply_filters( 'woocommerce_add_to_cart_product_id', absint( $product_id ) );
				$was_added_to_cart = false;

				$adding_to_cart = wc_get_product( $product_id );

				if ( ! $adding_to_cart ) {
					continue;
				}

				if ( $adding_to_cart->is_type( 'simple' ) ) {

					// quantity applies to all products atm
					$quantity          = empty( $_REQUEST['quantity'] ) ? 1 : wc_stock_amount( $_REQUEST['quantity'] );
					$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity );

					if ( $passed_validation && false !== WC()->cart->add_to_cart( $product_id, $quantity ) ) {
						wc_add_to_cart_message( array( $product_id => $quantity ), true );
					}

				} else {

					$variation_id       = empty( $_REQUEST['variation_id'] ) ? '' : absint( wp_unslash( $_REQUEST['variation_id'] ) );
					$quantity           = empty( $_REQUEST['quantity'] ) ? 1 : wc_stock_amount( wp_unslash( $_REQUEST['quantity'] ) ); // WPCS: sanitization ok.
					$missing_attributes = array();
					$variations         = array();
					$adding_to_cart     = wc_get_product( $product_id );

					if ( ! $adding_to_cart ) {
						continue;
					}

					// If the $product_id was in fact a variation ID, update the variables.
					if ( $adding_to_cart->is_type( 'variation' ) ) {
						$variation_id   = $product_id;
						$product_id     = $adding_to_cart->get_parent_id();
						$adding_to_cart = wc_get_product( $product_id );

						if ( ! $adding_to_cart ) {
							continue;
						}
					}

					// Gather posted attributes.
					$posted_attributes = array();

					foreach ( $adding_to_cart->get_attributes() as $attribute ) {
						if ( ! $attribute['is_variation'] ) {
							continue;
						}
						$attribute_key = 'attribute_' . sanitize_title( $attribute['name'] );

						if ( isset( $_REQUEST[ $attribute_key ] ) ) {
							if ( $attribute['is_taxonomy'] ) {
								// Don't use wc_clean as it destroys sanitized characters.
								$value = sanitize_title( wp_unslash( $_REQUEST[ $attribute_key ] ) );
							} else {
								$value = html_entity_decode( wc_clean( wp_unslash( $_REQUEST[ $attribute_key ] ) ), ENT_QUOTES, get_bloginfo( 'charset' ) ); // WPCS: sanitization ok.
							}

							$posted_attributes[ $attribute_key ] = $value;
						}
					}

					// If no variation ID is set, attempt to get a variation ID from posted attributes.
					if ( empty( $variation_id ) ) {
						$data_store   = \WC_Data_Store::load( 'product' );
						$variation_id = $data_store->find_matching_product_variation( $adding_to_cart, $posted_attributes );
					}

					// Do we have a variation ID?
					if ( empty( $variation_id ) ) {
						throw new \Exception( __( 'Please choose product options&hellip;', 'woocommerce' ) );
					}

					// Check the data we have is valid.
					$variation_data = wc_get_product_variation_attributes( $variation_id );

					foreach ( $adding_to_cart->get_attributes() as $attribute ) {
						if ( ! $attribute['is_variation'] ) {
							continue;
						}

						// Get valid value from variation data.
						$attribute_key = 'attribute_' . sanitize_title( $attribute['name'] );
						$valid_value   = isset( $variation_data[ $attribute_key ] ) ? $variation_data[ $attribute_key ] : '';

						/**
						 * If the attribute value was posted, check if it's valid.
						 *
						 * If no attribute was posted, only error if the variation has an 'any' attribute which requires a value.
						 */
						if ( isset( $posted_attributes[ $attribute_key ] ) ) {
							$value = $posted_attributes[ $attribute_key ];

							// Allow if valid or show error.
							if ( $valid_value === $value ) {
								$variations[ $attribute_key ] = $value;
							} elseif ( '' === $valid_value && in_array( $value, $attribute->get_slugs() ) ) {
								// If valid values are empty, this is an 'any' variation so get all possible values.
								$variations[ $attribute_key ] = $value;
							} else {
								throw new \Exception( sprintf( __( 'Invalid value posted for %s', 'woocommerce' ), wc_attribute_label( $attribute['name'] ) ) );
							}
						} elseif ( '' === $valid_value ) {
							$missing_attributes[] = wc_attribute_label( $attribute['name'] );
						}
					}
					if ( ! empty( $missing_attributes ) ) {
						throw new \Exception( sprintf( _n( '%s is a required field', '%s are required fields', count( $missing_attributes ), 'woocommerce' ), wc_format_list_of_items( $missing_attributes ) ) );
					}

					$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity, $variation_id, $variations );

					if ( $passed_validation && false !== WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variations ) ) {
						wc_add_to_cart_message( array( $product_id => $quantity ), true );

						//global $wp;
						//$current_url = home_url(add_query_arg(array(), $wp->request))
					}
				}
			}
		}

		/*function change_bunde_products_price_html_on_cart( $price_html, $cart_item, $cart_item_key ) {
			if ( isset( $cart_item['wpft_bundle_discount'] ) && true === $cart_item['wpft_bundle_discount'] ) {
				$args = array();
				if ( WC()->cart->display_prices_including_tax() ) {
					$product_price = wc_get_price_including_tax( $cart_item['data'], $args );
				} else {
					$product_price = wc_get_price_excluding_tax( $cart_item['data'], $args );
				}
				$original_price = wc_price( $cart_item['data']->get_data()['price'] );

				return '<del>' . $original_price . '</del>' . ' ' . wc_price( $product_price );
			}

			return $price_html;
		}*/

		/*function add_fake_bundle_discount_info_on_cart_totals() {
			if ( $this->bundle_savings_totals_in_cart == 0 ) {
				return;
			}
			?>
			<tr class="tax-total">
				<th><?php echo __( 'Bundle discount', 'wpfactory' ) ?></th>
				<td>
					<?php
					echo '- ' . wc_price( $this->bundle_savings_totals_in_cart );
					?>
				</td>
			</tr>
			<?php
		}*/

		/*function change_bundle_products_price_on_cart( $cart ) {
			if (
				( is_admin() && ! defined( 'DOING_AJAX' ) ) ||
				did_action( 'woocommerce_before_calculate_totals' ) >= 2 ||
				true !== filter_var( carbon_get_theme_option( 'wpft_bundles_enabled' ), FILTER_VALIDATE_BOOLEAN )
			) {
				return;
			}

			// Minimum bundle products necessary to give discount.
			$bundle_products_min = carbon_get_theme_option( 'wpft_bundle_products_min' );

			// Need to compare cart products with all plugins access product?
			$all_plugins_product_info = carbon_get_theme_option( 'wpft_all_plugins_access_product' );
			$need_to_compare_products = false;
			$all_plugins_product_id   = 0;
			if (
				true === filter_var( carbon_get_theme_option( 'wpft_all_plugins_access_enabled' ), FILTER_VALIDATE_BOOLEAN ) &&
				! empty( $all_plugins_product_info ) &&
				isset( $all_plugins_product_info[0]['id'] ) &&
				is_a( $all_plugins_product = wc_get_product( $all_plugins_product_info[0]['id'] ), 'WC_Product' ) ) {
				$all_plugins_product_id   = empty( $parent_id = $all_plugins_product->get_parent_id() ) ? $all_plugins_product->get_id() : $parent_id;
				$need_to_compare_products = true;
			}

			// Checks how many "All access products" are in cart.
			$all_plugins_products_in_cart_total = 0;
			foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) {
				if (
					$need_to_compare_products &&
					$this->do_products_match_id( $all_plugins_product_id, $cart_item['data'] )
				) {
					$all_plugins_products_in_cart_total ++;
				}
				$cart_item['wpft_bundle_discount']          = false;
				WC()->cart->cart_contents[ $cart_item_key ] = $cart_item;
			}

			// Gives discount to all products but "All access products".
			if ( count( $cart->get_cart() ) - $all_plugins_products_in_cart_total >= $bundle_products_min ) {
				$discount = carbon_get_theme_option( 'wpft_bundles_discount' );
				foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) {
					if (
						$need_to_compare_products &&
						$this->do_products_match_id( $all_plugins_product_id, $cart_item['data'] )
					) {
						continue;
					}
					$product = $cart_item['data'];
					$price   = $product->get_price();
					$cart_item['data']->set_price( $price * ( 1 - $discount ) );
					$this->bundle_savings_totals_in_cart        += $price * $discount;
					$cart_item['wpft_bundle_discount']          = true;
					WC()->cart->cart_contents[ $cart_item_key ] = $cart_item;
				}
				WC()->cart->set_session();
			}

		}*/

		/*function bundle_select_callback() {
			check_ajax_referer( 'selectwoo_get_bundle_products', 'security' );
			if ( empty( $term ) && isset( $_GET['term'] ) ) {
				$term = (string) wc_clean( wp_unslash( $_GET['term'] ) );
			}
			if ( empty( $term ) ) {
				wp_die();
			}

			$exclude_ids          = ! empty( $_GET['exclude_ids'] ) ? array_map( 'absint', (array) wp_unslash( $_GET['exclude_ids'] ) ) : array();
			$initial_variation_id = ! empty( $exclude_ids ) ? $exclude_ids[0] : null;
			$products_args        = array(
				'type'    => 'variation',
				'exclude' => $exclude_ids,
				's'       => $term
			);
			if ( ! empty( $initial_variation_id ) ) {
				$variation = wc_get_product( $initial_variation_id );
				//$products_args['attributes'] = $variation->get_variation_attributes();
			}
			$products        = wc_get_products( $products_args );
			$products_result = array();
			foreach ( $products as $product ) {
				if ( ! wc_products_array_filter_readable( $product ) ) {
					continue;
				}
				$formatted_name                        = $product->get_title();
				$products_result[ $product->get_id() ] = rawurldecode( wp_strip_all_tags( $formatted_name ) );
			}
			wp_send_json( $products_result );
		}*/

		/*function wpft_get_bundle_select( $variation = null ) {
			ob_start();
			$exclude_ids = ! empty( $variation ) ? $variation['variation_id'] : '';
			?>
			<select data-max_selection_length="2" data-limit="15"
					data-exclude_ids="<?php echo esc_attr( $exclude_ids ); ?>"
					data-action="get_bundle_products"
					class="wpft-bundle-select" name="wpdt_bundle_select[]"
					multiple="multiple">
				<option value="AL">Alabama</option>
				<option value="WY">Wyoming</option>
			</select>
			<?php
			return ob_get_clean();
		}*/

		/*function add_select2_as_frontend_css_dependency( $deps ) {
			if ( is_product() ) {
				$deps[] = 'select2';
			}

			return $deps;
		}*/

		/*function add_select2_as_frontend_js_dependency( $deps ) {
			if ( is_product() ) {
				$deps[] = 'selectWoo';
			}

			return $deps;
		}*/

		/**
		 * load_bundle_js.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param $required_modules
		 *
		 * @return mixed
		 */
		/*function load_bundle_js( $required_modules ) {
			if ( is_product() ) {
				$required_modules[] = 'bundle';
			}

			return $required_modules;
		}*/

		/**
		 * append_info_to_frontend_js.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param $js_info
		 *
		 * @return mixed
		 */
		/*function append_info_to_frontend_js( $js_info ) {
			if ( is_product() ) {
				$js_info['ajaxURL']             = admin_url( 'admin-ajax.php' );
				$js_info['bundle_select_nonce'] = wp_create_nonce( 'selectwoo_get_bundle_products' );
				//wp_create_nonce( 'search-products' )
			}
			return $js_info;
		}*/
	}
}