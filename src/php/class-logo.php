<?php
/**
 * WPFactory theme - Logo.
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  WPFactory
 */

namespace WPFactory\WPFactory_Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'WPFactory\WPFactory_Theme\Logo' ) ) {
	class Logo {

		/**
		 * Init.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return void
		 */
		function init() {
			add_action( 'wpfactory_header', array( $this, 'wpfactory_site_branding' ), 20 );
			add_action( 'after_setup_theme', array( $this, 'add_logo_support' ) );
		}

		/**
		 * add_logo_support.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return void
		 */
		function add_logo_support() {
			// Logo.
			add_theme_support(
				'custom-logo',
				apply_filters(
					'wpfactory_custom_logo_args',
					array(
						'height'      => 48,
						'width'       => 201,
						'flex-width'  => true,
						'flex-height' => true,
					)
				)
			);
		}

		/**
		 * Site branding wrapper and display.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
         *
		 * @return void
		 */
		function wpfactory_site_branding() {
			?>
            <div class="site-branding">
				<?php $this->wpfactory_site_title_or_logo(); ?>
            </div>
			<?php
		}

		/**
		 * Display the site title or logo.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param   bool  $echo  Echo the string or return it.
		 *
		 * @return string
		 */
		function wpfactory_site_title_or_logo( $echo = true ) {
			if ( function_exists( 'the_custom_logo' ) && has_custom_logo() ) {
				$logo = get_custom_logo();
				$html = is_home() ? '<h1 class="logo">' . $logo . '</h1>' : $logo;
			} else {
				$tag = is_home() ? 'h1' : 'div';

				$html = '<' . esc_attr( $tag ) . ' class="beta site-title"><a href="' . esc_url( home_url( '/' ) ) . '" rel="home">' . esc_html( get_bloginfo( 'name' ) ) . '</a></' . esc_attr( $tag ) . '>';

				if ( '' !== get_bloginfo( 'description' ) ) {
					$html .= '<p class="site-description">' . esc_html( get_bloginfo( 'description', 'display' ) ) . '</p>';
				}
			}

			if ( ! $echo ) {
				return $html;
			}

			echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

	}
}