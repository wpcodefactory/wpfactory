<?php
/**
 * WPFactory theme - Footer.
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

if ( ! class_exists( 'WPFactory\WPFactory_Theme\Component\Footer' ) ) {

	class Footer implements Theme_Component {

		/**
		 * Init.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return void
		 */
		function init() {
			add_action( 'wpfactory_after_footer', array( $this, 'wpfactory_credit' ), 40 );
		}

		/**
		 * Displays the theme credit.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return void
		 */
		function wpfactory_credit() {
			$output = '';

			if ( apply_filters( 'wpfactory_credit_link', true ) ) {
				$output .= esc_html__( 'All rights reserved.', 'wpfactory' );
			}

			$output = apply_filters( 'wpfactory_credit_links_output', $output );
			?>
            <div class="site-info-credits">
                <div class="wpf-container">
					<?php echo esc_html( apply_filters( 'wpfactory_copyright_text', $content = '&copy; ' . gmdate( 'Y' ) . ' ' . get_bloginfo( 'name' ) . '.' ) ); ?>
					<?php //echo esc_html( apply_filters( 'wpfactory_copyright_text', $content = '&copy; ' . get_bloginfo( 'name' ) . ' ' . gmdate( 'Y' ) ) ); ?>
					<?php if ( ! empty( $output ) ) { ?>
						<?php echo wp_kses_post( $output ); ?>
					<?php } ?>
                </div>
            </div><!-- .site-info -->
			<?php
		}
	}
}