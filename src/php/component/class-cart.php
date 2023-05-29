<?php
/**
 * WPFactory theme - Cart.
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

if ( ! class_exists( 'WPFactory\WPFactory_Theme\Component\Cart' ) ) {

	class Cart implements Theme_Component {

		/**
		 * Init.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return void
		 */
		function init() {
			add_action( 'after_setup_theme', array( $this, 'setup_storefront' ) );
			add_filter( 'wpft_header_cart_li_html', array( $this, 'get_header_cart_li_html' ) );
		}

		/**
         * storefront_cart_link.
         *
		 * @version 1.0.0
		 * @since   1.0.0
         *
         * @see \storefront_cart_link
         *
		 * @return void
		 */
		function storefront_cart_link() {
			if ( ! storefront_woo_cart_available() ) {
				return;
			}
			?>
            <a class="cart-contents" href="<?php echo esc_url( wc_get_cart_url() ); ?>"
               title="<?php esc_attr_e( 'View your shopping cart', 'storefront' ); ?>">
                <div class="cart-info">
					<?php /* translators: %d: number of items in cart */ ?>
					<?php echo wp_kses_post( WC()->cart->get_cart_subtotal() ); ?> <span
                            class="count"><?php echo wp_kses_data( WC()->cart->get_cart_contents_count() ) ?></span>
                </div>
            </a>
			<?php
		}

		/**
		 * get_header_cart_li_html.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param $cart_html
		 *
		 * @return false|string
		 */
		function get_header_cart_li_html( $cart_html ) {
			ob_start();
			?>
            <li class="wpft-is-header-cart wpft-icon-cart wpft-has-icon wpft-hide-label">
                <?php $this->storefront_cart_link(); ?>
                <div class="site-header-cart">
					<?php the_widget( 'WC_Widget_Cart', 'title=' ); ?>
                </div>
            </li>
			<?php
			return ob_get_clean();
		}

		/**
		 * setup_storefront.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return void
		 */
		function setup_storefront() {
			remove_action( 'storefront_header', 'storefront_header_cart', 60 );
		}
	}
}