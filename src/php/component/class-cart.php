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

		function get_header_cart_li_html( $cart_html ) {
			ob_start();
			?>
			<li class="wpft-is-header-cart wpft-icon-cart wpft-has-icon wpft-hide-label">
				<a href="<?php echo esc_url( wc_get_cart_url() ); ?>"
				   title="<?php esc_attr_e( 'View your shopping cart', 'storefront' ); ?>">
					<label class="hide"><?php esc_html( __( 'Cart', 'wpfactory' ) ); ?></label>
				</a>
				<div class="site-header-cart">
					<?php the_widget( 'WC_Widget_Cart', 'title=' ); ?>
				</div>
			</li>
			<?php
			return ob_get_clean();
		}

		function setup_storefront() {
			remove_action( 'storefront_header', 'storefront_header_cart', 60 );
			//add_action( 'storefront_header', 'storefront_header_cart', 25 );
		}
	}
}