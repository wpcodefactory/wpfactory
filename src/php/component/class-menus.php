<?php
/**
 * WPFactory theme - Menus.
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

if ( ! class_exists( 'WPFactory\WPFactory_Theme\Component\Menus' ) ) {

	//class Menus {
	class Menus implements Theme_Component {

		/**
		 * Init.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return void
		 */
		function init() {
			// Setup menus.
			add_action( 'after_setup_theme', array( $this, 'setup_menus' ) );
			add_filter( 'storefront_register_nav_menus', array( $this, 'change_registered_menus' ), 20 );

			// Hide. nav menu item label.
			add_filter( 'wp_nav_menu_items', array( $this, 'hide_nav_menu_item_label' ) );
			add_filter( 'nav_menu_css_class', array( $this, 'add_nav_menu_item_hide_label_class' ), 10, 2 );

			// Nav menu item icons.
			add_filter( 'wpft_nav_menu_item_icons', array( $this, 'get_nav_menu_item_icons' ) );
			add_filter( 'nav_menu_css_class', array( $this, 'add_nav_menu_item_icon_class' ), 10, 2 );

			// Info to handheld menu.
			add_filter( 'wpft_frontend_js_info', array( $this, 'append_info_to_frontend_js' ) );
		}

		function change_registered_menus( $menus ) {
			$menus['top_left'] = __( 'Top left Menu', 'wpfactory' );
			//$menus['top_right'] = __( 'Top Right Menu', 'wpfactory' );
			$menus['footer'] = __( 'Footer Menu', 'wpfactory' );
			unset( $menus['primary'] );
			unset( $menus['secondary'] );

			//error_log(print_r($menus,true));
			return $menus;
		}

		function setup_menus() {
			// Primary navigation.
			remove_action( 'storefront_header', 'storefront_primary_navigation_wrapper', 42 );
			remove_action( 'storefront_header', 'storefront_primary_navigation', 50 );
			remove_action( 'storefront_header', 'storefront_primary_navigation_wrapper_close', 68 );
			add_action( 'storefront_header', array( $this, 'wpfactory_primary_navigation' ), 21 );

			// Secondary navigation.
			remove_action( 'storefront_header', 'storefront_secondary_navigation', 30 );
			add_action( 'storefront_footer', array( $this, 'footer_navigation' ), 15 );

			// Handheld naviagation.
			add_action( 'storefront_header', array( $this, 'handle_handheld_menu' ), 22 );
			add_action( 'storefront_header', array( $this, 'site_navigation_menu_toggler' ), 23 );

			// Top right menu.
			add_action( 'storefront_header', array( $this, 'top_right_menu' ), 24 );
		}

		function hide_nav_menu_item_label( $items ) {
			if ( 'yes' === wpf_get_option( '_wpft_hide_nav_menu_item_label', 'yes' ) ) {
				$items = preg_replace( '/(wpft-hide-label.*<a.*>)(.*)(<\/a>)/m', '$1<span class="hide">$3</span>', $items );
			}

			return $items;
		}

		/**
		 * my_special_nav_class.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param $classes
		 * @param $item
		 *
		 * @return mixed
		 */
		function add_nav_menu_item_icon_class( $classes, $item ) {
			if (
				'yes' === wpf_get_option( '_wpft_add_menu_icons', 'yes' ) &&
				! empty( $icon = carbon_get_nav_menu_item_meta( $item->ID, 'wpft_icon' ) )
			) {
				$classes[] = 'wpft-has-icon wpft-icon-' . $icon;
			}

			return $classes;
		}

		/**
		 * add_nav_menu_item_hide_label_class.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param $classes
		 * @param $item
		 *
		 * @return mixed
		 */
		function add_nav_menu_item_hide_label_class( $classes, $item ) {
			if ( true === filter_var( carbon_get_nav_menu_item_meta( $item->ID, 'wpft_hide_label' ), FILTER_VALIDATE_BOOLEAN ) ) {
				$classes[] = 'wpft-hide-label';
			}

			return $classes;
		}

		/**
		 * get_nav_menu_item_icons.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param $icons
		 *
		 * @return mixed
		 */
		function get_nav_menu_item_icons( $icons ) {
			$extra_icons = array(
				''          => __( 'None', 'wpfactory' ),
				'facebook'  => __( 'Facebook', 'wpfactory' ),
				'twitter'   => __( 'Twitter', 'wpfactory' ),
				'instagram' => __( 'Instagram', 'wpfactory' ),
				'linkedin'  => __( 'Linkedin', 'wpfactory' ),
				'youtube'   => __( 'Youtube', 'wpfactory' ),
				'all'       => __( 'Plugins - All', 'wpfactory' ),
				'order'     => __( 'Plugins - Order & Quantity', 'wpfactory' ),
				'coupon'    => __( 'Plugins - Coupons', 'wpfactory' ),
				'product'   => __( 'Plugins - Product', 'wpfactory' ),
				'report'    => __( 'Plugins - Report', 'wpfactory' ),
				'blog'      => __( 'Blog', 'wpfactory' ),
				'faq'       => __( 'FAQ', 'wpfactory' ),
				'support'   => __( 'Support', 'wpfactory' ),
				'search'    => __( 'Search', 'wpfactory' ),
				'cart'      => __( 'Cart', 'wpfactory' ),
				'account'   => __( 'Account', 'wpfactory' ),
			);

			return array_merge( $icons, $extra_icons );
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
			$js_info['handheld_menu_selector']    = '.wpft-handheld-navigation';

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
					'container_class' => 'wpft-handheld-navigation',
					'fallback_cb'     => '',
				)
			);
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
            <nav id="wpft-site-navigation" class="wpft-main-navigation" role="navigation"
                 aria-label="<?php esc_attr_e( 'Primary Navigation', 'wpfactory' ); ?>">
				<?php
				wp_nav_menu(
					array(
						'theme_location'  => 'top_left',
						'container_class' => 'wpft-primary-navigation',
						'fallback_cb'     => '',
					)
				);
				?>
            </nav><!-- #site-navigation -->
			<?php
		}

		/**
		 * footer_navigation.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return void
		 */
		function footer_navigation() {
			?>
            <div class="col-full">
                <nav class="wpft-secondary-navigation" role="navigation"
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
            </div>
            </nav><!-- #site-navigation -->
			<?php
		}

		function top_right_menu() {
			$items_html_arr = array(
				sprintf('<li class="wpft-icon-search wpft-has-icon wpft-hide-label"><a><label class="hide">%s</label></a></li>',__( 'Search', 'wpfactory' )),
				apply_filters('wpft_header_cart_li_html',''),
				sprintf( '<li class="wpft-icon-account wpft-has-icon wpft-hide-label"><a href="%s"><label class="hide">%s</label></a></li>', get_permalink( wc_get_page_id( 'myaccount' ) ), __( 'My Account', 'wpfactory' ) ),
			);
			$items_html = implode( $items_html_arr );
			?>
            <div class="top-right-navigation">
                <ul class="menu">
					<?php echo $items_html; ?>
                </ul>
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