<?php
/**
 * WPFactory theme - Main class.
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  WPFactory
 */

namespace WPFactory\WPFactory_Theme;

use WPFactory\WPFactory_Theme\Admin_Settings\Admin_Settings;
use WPFactory\WPFactory_Theme\Page_Builder\Page_Builder;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'WPFactory\WPFactory_Theme\WPFactory_Theme' ) ) {

	class WPFactory_Theme extends Singleton {

		/**
		 * $version.
		 *
		 * @since 1.0.0
		 */
		private $version = null;

		/**
		 * Theme components.
		 *
		 * @since 1.0.0
		 *
		 * @var array
		 */
		protected $theme_components = array();

		/**
		 * Options.
		 *
		 * @since 1.0.0
		 *
		 * @var Options
		 */
		protected $options;

		/**
		 * Init.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return void
		 */
		function init() {
			// Options.
			$this->options = new Options();
			$this->options->init();
			// General setup.
			add_action( 'after_setup_theme', array( $this, 'general_setup' ) );
			add_action( 'init', array( $this, 'general_setup' ) );
			// Enqueue scripts.
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 11 );
			// Initializes theme components.
			$this->initialize_theme_components();
			// Admin settings.
			$admin_settings = new Admin_Settings();
			$admin_settings->init();
			// Timber
			add_filter( 'timber/twig', array( $this, 'add_functions_to_twig' ), 9 );
		}

		/**
		 * get_theme_component_classes.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return string[]
		 */
		function get_theme_component_classes() {
			return array(
				'\\WPFactory\\WPFactory_Theme\\Component\\Menus',
				'\\WPFactory\\WPFactory_Theme\\Component\\Logo',
				'\\WPFactory\\WPFactory_Theme\\Component\\Credit',
				'\\WPFactory\\WPFactory_Theme\\Component\\Search',
				'\\WPFactory\\WPFactory_Theme\\Component\\Cart',
				'\\WPFactory\\WPFactory_Theme\\Component\\Sidebar',
				'\\WPFactory\\WPFactory_Theme\\Component\\Home',
				'\\WPFactory\\WPFactory_Theme\\Component\\Products',
				'\\WPFactory\\WPFactory_Theme\\Component\\Websites',
				'\\WPFactory\\WPFactory_Theme\\Component\\Blog',
				'\\WPFactory\\WPFactory_Theme\\Component\\Content_Header',
				'\\WPFactory\\WPFactory_Theme\\Component\\Bundles',
				'\\WPFactory\\WPFactory_Theme\\Component\\Pricing_Module',
				'\\WPFactory\\WPFactory_Theme\\Component\\FAQ',
				'\\WPFactory\\WPFactory_Theme\\Component\\Page_Builder\\Page_Builder',
			);
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
			$twig->addFunction( new \Timber\Twig_Function( 'wpft_get_wc_url', array( $this, 'wpft_get_wc_url' ) ) );
			$twig->addFunction( new \Timber\Twig_Function( 'sprintf', 'sprintf' ) );
			$twig->addFunction( new \Timber\Twig_Function( 'wpft_add_to_cart_url', array(
				$this,
				'wpft_add_to_cart_url'
			) ) );
			$twig->addFunction( new \Timber\Twig_Function( 'wpft_exit', array( $this, 'wpft_exit' ) ) );

			return $twig;
		}

		function wpft_exit() {
			die();
		}

		/**
		 * add_to_cart_url.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param $prod_id
		 *
		 * @return string
		 */
		function wpft_add_to_cart_url( $prod_id ) {
			return do_shortcode( '[add_to_cart_url id="' . $prod_id . '"]' );
		}

		function wpft_get_wc_url( $wc_page_label = '' ) {
			return get_permalink( wc_get_page_id( $wc_page_label ) );
		}

		/**
		 * enqueue_scripts.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return void
		 */
		function enqueue_scripts() {
			$suffix  = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			$version = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? current_time( 'timestamp' ) : $this->get_version();

			//wp_deregister_style('storefront-style');
			//wp_deregister_style('storefront-woocommerce-style');
			//wp_dequeue_style('storefront-style');

			// Splide
			wp_enqueue_style( 'wpft-splide-css', 'https://cdnjs.cloudflare.com/ajax/libs/splidejs/4.1.4/css/splide.min.css', array(), false );
			wp_enqueue_script( 'wpft-splide-js',
				'https://cdnjs.cloudflare.com/ajax/libs/splidejs/4.1.4/js/splide.js',
				array(),
				false,
				false
			);

			//<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/splidejs/4.1.4/css/splide.min.css" integrity="sha512-KhFXpe+VJEu5HYbJyKQs9VvwGB+jQepqb4ZnlhUF/jQGxYJcjdxOTf6cr445hOc791FFLs18DKVpfrQnONOB1g==" crossorigin="anonymous" referrerpolicy="no-referrer" />

			// Main frontend style.
			//wp_enqueue_style( 'wpfactory-style', get_theme_file_uri( '/assets/css/frontend' . $suffix . '.css' ), array(), $version );
			wp_enqueue_style( 'wpfactory-style', get_theme_file_uri( '/assets/css/frontend' . $suffix . '.css' ), apply_filters( 'wpft_frontend_css_deps', array( 'storefront-woocommerce-style' ) ), $version );
			//wp_style_add_data( 'wpfactory-style', 'rtl', 'replace' );
			//wp_enqueue_style( 'wpfactory-style', get_theme_file_uri( '/assets/css/frontend' . $suffix . '.css' ), array('storefront-woocommerce-style'), $version );
			//wp_enqueue_style( 'wpfactory-style', get_theme_file_uri( '/assets/css/frontend' . $suffix . '.css' ), '', $version );

			// Main frontend script.
			wp_enqueue_script( 'wpfactory-frontend-js',
				get_theme_file_uri( '/assets/js/frontend' . $suffix . '.js' ),
				apply_filters( 'wpft_frontend_js_deps', array( 'jquery','wpft-splide-js' ) ),
				$version,
				true
			);
			wp_add_inline_script( 'wpfactory-frontend-js', 'const WPFTFEJS = ' . json_encode( apply_filters( 'wpft_frontend_js_info', array(
					'themeURI'        => get_theme_file_uri(),
					'modulesRequired' => apply_filters( 'wpft_js_modules_required', array(
						'smooth-scroll',
						'slider',
						'modal'
					) )
				) ) ), 'before'
			);

			// Google fonts.
			wp_enqueue_style( 'add_google_fonts', 'https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;800&display=swap', array(), null );
		}

		/**
		 * general_setup.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return void
		 */
		function general_setup() {
			switch ( current_filter() ) {
				case 'after_setup_theme':
					// Translation.
					load_theme_textdomain( 'wpfactory', get_template_directory() . '/languages' );
					// Disable Gutenberg on the back end.
					add_filter( 'use_block_editor_for_post', '__return_false' );
					add_image_size( 'size-1', 104, 112 ); // Product icon
					add_image_size( 'size-2', 384 ); // Product feature images
					add_image_size( 'size-3', 160, 40 ); // Product feature images
					add_image_size( 'size-4', 1920, 546, array( 'center', 'top' ) ); // Post featured images
					add_image_size( 'size-5', 382, 186, array( 'center', 'top' ) ); // Blog thumbnail images
					// Body classes.
					add_filter( 'body_class', array( $this, 'set_full_width_css' ) );
					add_action( 'wpft_col_full_start', array( $this, 'open_main_col_full' ) );
					add_action( 'wpft_col_full_close', array( $this, 'close_main_col_full' ) );
					// Content header.
					add_action( 'storefront_loop_before', array( $this, 'handle_content_header' ) );
					// Footer.
					remove_action( 'storefront_after_footer', 'storefront_sticky_single_add_to_cart', 999 );
					break;
				case 'init':
					// Remove global styles and front SVG.
					remove_action( 'wp_enqueue_scripts', 'wp_enqueue_global_styles' );
					remove_action( 'wp_body_open', 'wp_global_styles_render_svg_filters' );
					// Remove emojis.
					$this->disable_emojis();
					break;
			}
		}

		function handle_content_header() {
			//<h2 class="entry-title"><?php wp_title( '' ) </h2>
			?>
            <header class="wpft-page-header">
				<?php
				if ( is_archive() ) {
					if ( is_author() ) {
						echo '<h1 class="entry-title">' . __( 'Author', 'wpfactory' ) . '</h1>';
					} else {
						the_archive_title( '<h1 class="entry-title">', '</h1>' );
					}

					//the_archive_description( '<div class="taxonomy-description">', '</div>' );
				} else {
					echo '<h1 class="entry-title">' . wp_title( '', false ) . '</h1>';
				}
				?>
            </header><!-- .page-header -->
			<?php
		}

		function open_main_col_full() {
			if ( ! wpft_is_current_page_full_width_content() ) {
				echo '<div class="col-full">';
			}
		}

		function close_main_col_full() {
			if ( ! wpft_is_current_page_full_width_content() ) {
				echo '</div>';
			}
		}

		/**
		 * Sets full width in all pages but shop and product.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param $classes
		 *
		 * @return mixed
		 */
		function set_full_width_css( $classes ) {
			if (
				wpft_is_current_page_full_width_content()
				//!wpft_does_current_page_have_sidebar()
			) {
				$classes[] = 'storefront-full-width-content';
			}

			return $classes;
		}

		/**
		 * Disable the emoji's.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		function disable_emojis() {
			remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
			remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
			remove_action( 'wp_print_styles', 'print_emoji_styles' );
			remove_action( 'admin_print_styles', 'print_emoji_styles' );
			remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
			remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
			remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
			// Remove from TinyMCE.
			add_filter( 'tiny_mce_plugins', array( $this, 'disable_emojis_tinymce' ) );
			add_filter( 'wp_resource_hints', array( $this, 'disable_emojis_remove_dns_prefetch' ), 10, 2 );
		}

		/**
		 * Filter function used to remove the tinymce emoji plugin.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param   array  $plugins
		 *
		 * @return array Difference betwen the two arrays
		 */
		function disable_emojis_tinymce( $plugins ) {
			if ( is_array( $plugins ) ) {
				return array_diff( $plugins, array( 'wpemoji' ) );
			} else {
				return array();
			}
		}

		/**
		 * Remove emoji CDN hostname from DNS prefetching hints.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param   array   $urls           URLs to print for resource hints.
		 * @param   string  $relation_type  The relation type the URLs are printed for.
		 *
		 * @return array Difference betwen the two arrays.
		 */
		function disable_emojis_remove_dns_prefetch( $urls, $relation_type ) {
			if ( 'dns-prefetch' == $relation_type ) {
				/** This filter is documented in wp-includes/formatting.php */
				$emoji_svg_url = apply_filters( 'emoji_svg_url', 'https://s.w.org/images/core/emoji/2/svg/' );

				$urls = array_diff( $urls, array( $emoji_svg_url ) );
			}

			return $urls;
		}

		/**
		 * handle_translation.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return void
		 */
		function handle_translation() {
			load_theme_textdomain( 'wpfactory', get_template_directory() . '/languages' );
		}

		/**
		 * Gets theme version from style.css.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return string
		 */
		function get_version() {
			if ( is_null( $this->version ) ) {
				$theme             = wp_get_theme( 'wpfactory' );
				$wpfactory_version = $theme['Version'];
				$this->version     = $wpfactory_version;
			}

			return $this->version;
		}

		/**
		 * get_theme_components.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return array
		 */
		function get_theme_components() {
			return $this->theme_components;
		}

		/**
		 * initialize_theme_components.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return void
		 */
		function initialize_theme_components() {
			$this->theme_components = array();
			foreach ( $this->get_theme_component_classes() as $component_class ) {
				$type                     = $component_class;
				$component                = new $type();
				$this->theme_components[] = $component;
				call_user_func( array( $component, 'init' ) );
			}
		}

		/**
		 * Get component.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param $class_name
		 *
		 * @return false|mixed
		 */
		function get_component( $class_name ) {
			foreach ( $this->get_theme_components() as $component ) {
				$component_name                   = get_class( $component );
				$component_name_without_namespace = substr( $component_name, strrpos( $component_name, "\\" ) + 1 );
				if ( $component_name === $class_name || $component_name_without_namespace === $class_name ) {
					return $component;
				}
			}

			return false;
		}

		/**
		 * get_options.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return Options
		 */
		public function get_options(): Options {
			return $this->options;
		}


	}
}