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
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 10 );
			// Initializes theme components.
			$this->initialize_theme_components();
			// Admin settings.
			$admin_settings = new Admin_Settings();
			$admin_settings->init();
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
				'\\WPFactory\\WPFactory_Theme\\Component\\Footer',
			);
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

			// Main frontend style.
			wp_enqueue_style( 'wpfactory-style', get_theme_file_uri( '/assets/css/frontend' . $suffix . '.css' ), '', $version );

			// Main frontend script.
			wp_enqueue_script( 'wpfactory-frontend-js',
				get_template_directory_uri() . '/assets/js/frontend' . $suffix . '.js',
				array( 'jquery' ),
				$version,
				false
			);
			wp_add_inline_script( 'wpfactory-frontend-js', 'const WPFTFEJS = ' . json_encode( apply_filters( 'wpft_frontend_js_info', array(
					'themeURI'        => get_theme_file_uri(),
					'modulesRequired' => apply_filters( 'wpft_js_modules_required', array( 'menus' ) )
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
			foreach ( $this->theme_components as $component ) {
				$component_name = get_class( $component );
				if ( $component_name === $class_name || trim( $component_name, "\\WPFactory\\WPFactory_Theme\\" ) === $class_name ) {
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