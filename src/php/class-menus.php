<?php
/**
 * WPFactory theme - Menus.
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  WPFactory
 */

namespace WPFactory\WPFactory_Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'WPFactory\WPFactory_Theme\Menus' ) ) {

	class Menus {
		//class Menus implements Theme_Component {

		/**
		 * Init.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return void
		 */
		function init() {
			add_action( 'after_setup_theme', array( $this, 'setup_menus' ) );
			add_action( 'wpfactory_header', array( $this, 'wpfactory_primary_navigation_wrapper' ), 42 );
			add_action( 'wpfactory_header', array( $this, 'wpfactory_primary_navigation' ), 50 );
			add_action( 'wpfactory_header', array( $this, 'wpfactory_top_right_menu' ), 80 );
			add_action( 'wpfactory_header', array( $this, 'site_navigation_menu_toggler' ), 81 );
			add_action( 'wpfactory_header', array( $this, 'handle_handheld_menu' ), 82 );
			add_action( 'wpfactory_header', array( $this, 'wpfactory_primary_navigation_wrapper_close' ), 68 );
			add_action( 'wpfactory_footer', array( $this, 'wpfactory_secondary_navigation' ), 30 );
			add_filter( 'wpft_frontend_js_info', array( $this, 'append_info_to_frontend_js' ) );
		}

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
		function append_info_to_frontend_js( $js_info ) {
			$js_info['handheld_toggler_selector'] = '#site-navigation-menu-toggle';
			$js_info['handheld_menu_selector'] = '.handheld-navigation';
			return $js_info;
		}

		/**
		 * handle_handheld_menu.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return void
		 */
		function handle_handheld_menu() {
			wp_nav_menu(
				array(
					'theme_location'  => 'handheld',
					'container_class' => 'handheld-navigation',
					'fallback_cb'     => '',
				)
			);
		}

		/**
		 * setup_menu.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return void
		 */
		function setup_menus() {
			register_nav_menus(
				apply_filters(
					'wpfactory_register_nav_menus',
					array(
						'top_left'  => __( 'Top left Menu', 'wpfactory' ),
						'top_right' => __( 'Top Right Menu', 'wpfactory' ),
						'footer'    => __( 'Footer Menu', 'wpfactory' ),
						'handheld'  => __( 'Handheld Menu', 'wpfactory' ),
					)
				)
			);
		}

		/**
		 * The primary navigation wrapper.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		function wpfactory_primary_navigation_wrapper() {
			echo '<div class="wpfactory-primary-navigation">';
			//echo '<div class="wpfactory-primary-navigation"><div class="wpf-container">';
		}

		/**
		 * The primary navigation wrapper close.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		function wpfactory_primary_navigation_wrapper_close() {
			echo '</div>';
		}

		/**
		 * Display Primary Navigation.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 * @return void
		 */
		function wpfactory_primary_navigation() {
			?>
            <nav id="site-navigation" class="main-navigation" role="navigation"
                 aria-label="<?php esc_attr_e( 'Primary Navigation', 'wpfactory' ); ?>">
				<?php
				wp_nav_menu(
					array(
						'theme_location'  => 'top_left',
						'container_class' => 'primary-navigation',
						'fallback_cb'     => '',
					)
				);
				?>
            </nav><!-- #site-navigation -->
			<?php
		}

		/**
		 * wpfactory_secondary_navigation.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return void
		 */
		function wpfactory_secondary_navigation() {

			?>
            <nav class="secondary-navigation" role="navigation"
                 aria-label="<?php esc_attr_e( 'Secondary Navigation', 'wpfactory' ); ?>">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'footer',
						'menu_class'     => 'menu',
						//'menu_class'     => 'menu is-justify-content-space-between',
						'fallback_cb'    => '',
					)
				);
				?>
            </nav><!-- #site-navigation -->
			<?php

		}

		/**
		 * wpfactory_top_right_menu.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return void
		 */
		function wpfactory_top_right_menu() {
			?>
            <div class="top-right-navigation">
                <nav class="top-right-menu" role="navigation">
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'top_right',
							'fallback_cb'    => '',
						)
					);
					?>
                </nav>
            </div>
			<?php
		}

		/**
		 * site_navigation_menu_toggler.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return void
		 */
		function site_navigation_menu_toggler() {
			?>
            <div class="site-navigation-menu-toggle-wrapper">
                <button id="site-navigation-menu-toggle" class="menu-toggle" aria-controls="site-navigation"
                        aria-expanded="false">
                    <span class="label"><?php echo esc_html( apply_filters( 'wpfactory_menu_toggle_text', __( 'Menu', 'wpfactory' ) ) ); ?></span>
                    <span class="style"></span>
                </button>
            </div>
			<?php
		}
	}
}