<?php
/**
 * WPFactory theme - Page builder modules.
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  WPFactory
 */

namespace WPFactory\WPFactory_Theme\Page_Builder;

use Carbon_Fields\Container;
use Carbon_Fields\Field;
use \Timber\Timber;
use WPFactory\WPFactory_Theme\Carbon_Fields\Carbon_Fields_Post_Meta_Datastore;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'WPFactory\WPFactory_Theme\Page_Builder\Modules' ) ) {

	class Modules {

		public function __construct() {

		}

		function init() {
			add_action( 'init', array( $this, 'create_module_post_type' ) );
			add_action( 'admin_menu', array( $this, 'add_modules_cpt_as_wpft_submenu' ), 20 );
			add_action( 'carbon_fields_register_fields', array( $this, 'create_module_fields' ) );
			add_action( 'carbon_fields_register_fields', array( $this, 'create_module_fields_on_custom_cpt' ), 20 );
			add_filter( 'parent_file', array( $this, 'highlight_wpfactory_menu_on_module_editing' ) );
			//add_filter( 'init', array( $this, 'create_fields_displaying_hooks' ) );
			add_action( 'init', array( $this, 'create_fields_displaying_hooks' ) );
			//add_action('');
		}

		function create_fields_displaying_hooks() {

			$hooks = carbon_get_theme_option( 'wpft_modules_position_hooks' );
			foreach ( $hooks as $hook ) {
				add_action( $hook['action_hook'], array( $this, 'display_fields' ) );
			}
		}

		function display_fields( $post_id = null ) {
			$post_id         = ! is_null( $post_id ) ? $post_id : get_the_ID();
			$timber          = new \Timber\Timber();
			$context['post'] = new \Timber\Post( $post_id );
			foreach ( carbon_get_post_meta( $post_id, 'wpft_modules' ) as $module_info ) {
				$template        = carbon_get_post_meta( $module_info['module'], 'wpft_template' );
				$module_classes = array(
					'wpftpb-module',
					'wpftpb-module-'.$module_info['module'],
				);
				$template        = '<section class="wpftpb-section"><div class="' . implode( " ", array_map( 'sanitize_html_class', $module_classes ) ) . '">' . $template . '</div></section>';
				$output          = \Timber::compile_string( $template, array_merge( $context, $module_info ) );
				echo $output;
			}
		}

		function get_modules() {
			$modules           = get_posts( array(
				'post_type'   => 'wpft_module',
				'numberposts' => - 1,
				//'fields'      => 'ids',
			) );
			$modules_formatted = array();
			foreach ( $modules as $module ) {
				$modules_formatted[ $module->ID ] = $module->post_title;
			}
			wp_reset_postdata();

			return $modules_formatted;
		}

		function create_module_fields_on_custom_cpt() {
			$modules_formatted      = $this->get_modules();
			$modules_formatted_json = json_encode( $modules_formatted );
			$fields                 = array(
				Field::make( 'select', 'module', __( 'Module', 'wpfactory' ) )->set_options( function () use ( $modules_formatted ) {

					return $modules_formatted;
				} )
			);
			$fields                 = array_merge( $fields, $this->get_dynamic_fields( array_keys( $modules_formatted ) ) );
			Container::make( 'post_meta', __( 'Page builder', 'wpfactory' ) )
			         ->set_datastore( new Carbon_Fields_Post_Meta_Datastore() )
			         ->where( 'post_type', 'CUSTOM', function ( $post_type ) {
				         $post_types = carbon_get_theme_option( 'wpft_modules_admin_cpt' );

				         return in_array( $post_type, $post_types );
			         } )
			         ->add_fields( array(
				         Field::make( 'complex', 'wpft_modules', __( 'Modules' ) )
				              ->set_collapsed( true )
				              ->add_fields( $fields )
				              ->set_header_template( '<%-' . $modules_formatted_json . '[module] %>' )
			         ) );
		}

		function highlight_wpfactory_menu_on_module_editing( $parent_file ) {
			global $pagenow;
			if ( 'post.php' === $pagenow && isset( $_GET['post'] ) && 'wpft_module' === get_post_type( $_GET['post'] ) ) {
				return 'crb_carbon_fields_container_wpfactory_settings.php';
			}

			return $parent_file;
		}

		function get_dynamic_fields( $modules_ids ) {
			$fields = array();
			foreach ( $modules_ids as $module_id ) {
				foreach ( carbon_get_post_meta( $module_id, 'wpft_template_variables' ) as $variable ) {
					//$field_id = 'wpft' . '_' . $module_id . '_' . $variable['var_id'];
					$field_id = $variable['var_id'];
					$field    = Field::make( $variable['var_type'], $field_id, $variable['var_label'] );
					$field->set_width( '33%' );
					$field->set_conditional_logic( array(
						array(
							'field'   => 'module',
							'value'   => $module_id,
							'compare' => '=',
						)
					) );

					if ( 'url' === $variable['txt_attribute'] ) {
						$field->set_attribute( 'type', 'url' );
					}
					$fields[] = $field;
				}
			}

			return $fields;
		}

		function create_module_fields() {
			Container::make( 'post_meta', __( 'Template data', 'wpfactory' ) )
			         ->set_datastore( new Carbon_Fields_Post_Meta_Datastore() )
			         ->where( 'post_type', '=', 'wpft_module' )
			         ->add_fields( array(
				         Field::make( 'complex', 'wpft_template_variables', __( 'Template variables' ) )
				              ->set_collapsed( true )
				              ->add_fields( array(
					              Field::make( 'text', 'var_label', 'Label' ),
					              Field::make( 'text', 'var_id', 'ID' ),
					              Field::make( 'select', 'var_type', 'Type' )->set_width( '50%' )
					                   ->add_options( array(
						                   'text'  => 'Text',
						                   'image' => 'Image'
					                   ) ),
					              Field::make( 'select', 'txt_attribute', 'Attribute' )->set_width( '50%' )
					                   ->add_options( array(
						                   'none'   => 'None',
						                   'url'    => 'URL',
						                   'number' => 'Number'
					                   ) )
					                   ->set_conditional_logic( array(
						                   'relation' => 'AND', // Optional, defaults to "AND"
						                   array(
							                   'field'   => 'var_type',
							                   'value'   => 'text',
							                   // Optional, defaults to "". Should be an array if "IN" or "NOT IN" operators are used.
							                   'compare' => '=',
							                   // Optional, defaults to "=". Available operators: =, <, >, <=, >=, IN, NOT IN
						                   )
					                   ) ),
				              ) )
				              ->set_header_template( '
							    <% if (var_label) { %>
							        <%- var_label %>
							    <% } %>
					          ' ),
				         Field::make( 'textarea', 'wpft_template', __( 'Template content' ) )
			         ) );
		}

		/*function create_module_fields_old() {
			$templates_info = carbon_get_theme_option( 'wpft_modules' );
			error_log( print_r( $templates_info, true ) );
			$templates = wp_list_pluck( $templates_info, 'title' );

			$fields = array( Field::make( 'select', 'wpft_template', __( 'Template', 'wpfactory' ) )->add_options( $templates ) );

			//error_log(print_r($test,true));
			$fields = array_merge( $fields, $this->get_dynamic_fields( $templates_info ) );

			Container::make( 'post_meta', __( 'Module data', 'wpfactory' ) )
			         ->where( 'post_type', '=', 'wpft_module' )
			         ->add_fields( $fields );
		}*/

		function add_modules_cpt_as_wpft_submenu() {
			add_submenu_page(
			//'wpft',
				'crb_carbon_fields_container_wpfactory_settings.php',
				'Modules',
				'Modules',
				'manage_options',
				'edit.php?post_type=wpft_module'
			);
			//add_submenu_page( 'my-top-level-slug', 'My Custom Page', 'My Custom Page','manage_options', 'my-top-level-slug' );
		}

		function create_module_post_type() {
			$labels = array(
				'name'                  => _x( 'Modules', 'Post type general name', 'textdomain' ),
				'singular_name'         => _x( 'Module', 'Post type singular name', 'textdomain' ),
				'menu_name'             => _x( 'Modules', 'Admin Menu text', 'textdomain' ),
				'name_admin_bar'        => _x( 'Module', 'Add New on Toolbar', 'textdomain' ),
				'add_new'               => __( 'Add New', 'textdomain' ),
				'add_new_item'          => __( 'Add New Module', 'textdomain' ),
				'new_item'              => __( 'New Module', 'textdomain' ),
				'edit_item'             => __( 'Edit Module', 'textdomain' ),
				'view_item'             => __( 'View Module', 'textdomain' ),
				'all_items'             => __( 'All Modules', 'textdomain' ),
				'search_items'          => __( 'Search Modules', 'textdomain' ),
				'parent_item_colon'     => __( 'Parent Modules:', 'textdomain' ),
				'not_found'             => __( 'No Modules found.', 'textdomain' ),
				'not_found_in_trash'    => __( 'No Modules found in Trash.', 'textdomain' ),
				'featured_image'        => _x( 'Module Cover Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'textdomain' ),
				'set_featured_image'    => _x( 'Set cover image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'textdomain' ),
				'remove_featured_image' => _x( 'Remove cover image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'textdomain' ),
				'use_featured_image'    => _x( 'Use as cover image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'textdomain' ),
				'archives'              => _x( 'Module archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'textdomain' ),
				'insert_into_item'      => _x( 'Insert into Module', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'textdomain' ),
				'uploaded_to_this_item' => _x( 'Uploaded to this Module', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'textdomain' ),
				'filter_items_list'     => _x( 'Filter Modules list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'textdomain' ),
				'items_list_navigation' => _x( 'Modules list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'textdomain' ),
				'items_list'            => _x( 'Modules list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'textdomain' ),
			);

			$args = array(
				'labels'          => $labels,
				'public'          => false,
				'show_ui'         => true,
				'show_in_menu'    => false,
				'rewrite'         => array( 'slug' => 'wpft_module' ),
				'capability_type' => 'post',
				'has_archive'     => false,
				'hierarchical'    => false,
				'menu_position'   => null,
				'supports'        => array( 'title' ),
			);

			register_post_type( 'wpft_module', $args );
		}

		function create_custom_post_type() {
			register_post_type( $this->get_args()['cpt_args'] );
		}
	}
}