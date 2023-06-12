<?php
/**
 * WPFactory theme - Page builder modules.
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  WPFactory
 */

namespace WPFactory\WPFactory_Theme\Component\Page_Builder;

use Carbon_Fields\Container;
use Carbon_Fields\Field;
use Carbon_Fields\Helper\Helper;
use \Timber\Timber;
use WPFactory\WPFactory_Theme\Carbon_Fields\Carbon_Fields_Post_Meta_Datastore;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'WPFactory\WPFactory_Theme\Component\Page_Builder\Modules' ) ) {

	class Modules {

		protected $modules_posts = null;

		public function __construct() {

		}

		function init() {
			add_action( 'init', array( $this, 'create_module_post_type' ) );
			add_action( 'admin_menu', array( $this, 'add_modules_cpt_as_wpft_submenu' ), 20 );
			add_filter( 'parent_file', array( $this, 'highlight_wpfactory_menu_on_module_editing' ) );
			add_action( 'carbon_fields_register_fields', array( $this, 'create_module_fields' ) );
			add_action( 'carbon_fields_register_fields', array( $this, 'create_module_fields_on_custom_cpt' ), 22 );
			add_action( 'carbon_fields_fields_registered', array(
				$this,
				'manage_modules_displays_from_hook_settings'
			) );
		}

		function manage_modules_displays_from_hook_settings() {
			$page_builder_settings = carbon_get_theme_option( 'wpft_page_builder_settings' );
			if ( empty( $page_builder_settings ) ) {
				return;
			}

			foreach ( $page_builder_settings as $cpt_key => $cpt_info ) {
				foreach ( $cpt_info['display_areas'] as $area_key => $area ) {
					$current_area_info             = $area;
					$current_area_info['area_key'] = $area_key;
					$current_area_info['cpt']      = array(
						'key' => $cpt_key,
						'id'  => $cpt_info['cpt_relation'],
					);
					add_action( $area['hook'], function ( $post_id = null ) use ( $current_area_info ) {
						$post_id = null != $post_id ? $post_id : get_the_ID();
						$this->display_modules( $current_area_info, $post_id );
					}, $area['hook_priority'] );
				}
			}
		}

		function sanitize_module_info( $module_info ) {
			$module_info_without_wrong_mods = array();
			foreach ( $module_info as $key => $value ) {
				if ( false !== strpos( $key, 'mod_' . $module_info['module'] ) ) {
					$module_info_without_wrong_mods[ $key ] = $value;
				}
			}
			$module_info_sanitized = array();
			foreach ( $module_info_without_wrong_mods as $k => $v ) {
				$new_key                           = str_replace( 'mod_' . $module_info['module'] . '_', '', $k );
				$module_info_sanitized[ $new_key ] = $v;
			}
			$module_info_sanitized['module'] = $module_info['module'];

			return $module_info_sanitized;
		}

		function get_current_priority() {
			global $wp_filter, $wp_current_filter;

			// Find the currently running WP action/filter name.
			$action = end( $wp_current_filter );

			// Get the corresponding WP_Hook object of that filter.
			$filter = $wp_filter[ $action ];

			// Determine the priority of the current filter callback.
			$prio = $filter->current_priority();

			return $prio;
		}

		function display_module( $args = null ) {
			$args                      = wp_parse_args( $args, array(
				'module_id'     => null,
				'template_vars' => null,
			) );
			$module_id                 = $args['module_id'];
			$template_vars             = $args['template_vars'];
			$template                  = carbon_get_post_meta( $module_id, 'wpft_template' );
			$module_classes            = array(
				'wpftpb-mod',
			);
			$module_wrapper_classes    = array(
				'wpftpb-section',
				'wpftpb-mod-wrapper-' . $module_id,
			);
			$static_css_classes_option = carbon_get_post_meta( $module_id, 'wpft_css_classes' );
			if ( ! empty( $static_css_classes_option ) ) {
				$module_wrapper_classes = array_merge( $module_wrapper_classes, explode( " ", $static_css_classes_option ) );
			}
			if ( ! empty( $vars_to_css = carbon_get_post_meta( $module_id, 'wpft_template_vars_to_css' ) ) ) {
				$vars_to_css            = array_map( 'trim', explode( ',', $vars_to_css ) );
				$intersection_keys      = array_intersect( $vars_to_css, array_keys( $template_vars ) );
				$result                 = array_intersect_key( $template_vars, array_flip( $intersection_keys ) );
				$result                 = array_map( 'sanitize_title_with_dashes', $result );
				$module_wrapper_classes = array_merge( $module_wrapper_classes, $result );
			}
			$template = sprintf(
				'<section class="%s"><div class="%s">%s</div></section>',
				implode( " ", $this->sanitize_css_classes_array( apply_filters( 'wpft_module_wrapper_css_classes', $module_wrapper_classes ) ) ),
				implode( " ", $this->sanitize_css_classes_array( apply_filters( 'wpft_module_css_classes', $module_classes ) ) ),
				$template
			);
			$output   = \Timber::compile_string( $template, $template_vars );
			echo $output;
		}

		function sanitize_css_classes_array( $classes ) {
			return array_map( 'sanitize_html_class', $classes );
		}

		function init_timber_and_get_initial_context( $post_id ) {
			$timber          = new \Timber\Timber();
			$context         = Timber::context();
			$context['post'] = new \Timber\Post( $post_id );
			if ( 'product' === get_post_type( $post_id ) ) {
				$product = wc_get_product( $context['post']->ID );
				$context['product'] = $product;
				//$variations = $product->get_available_variations();
				//$test = $product->get_children();
				//error_log(print_r($test,true));
				//$test = wc_get_formatted_variation
			}
			return $context;
		}

		function display_modules( $current_area_info, $post_id = null ) {
			$post_id           = ! is_null( $post_id ) ? $post_id : get_the_ID();
			$modules_from_post = carbon_get_post_meta( $post_id, 'wpft_modules_' . $current_area_info['cpt']['key'] . '_' . $current_area_info['area_key'] );
			if ( empty( $modules_from_post ) && ! empty( $default_modules = $current_area_info['default_modules'] ) ) {
				$context = $this->init_timber_and_get_initial_context( $post_id );
				foreach ( $default_modules as $module ) {
					$template_vars = wp_list_pluck( carbon_get_post_meta( $module['id'], 'wpft_template_variables' ), 'default_value', 'var_id' );
					if ( ! empty( $inherited_modules = carbon_get_post_meta( $module['id'], 'wpft_inherited_modules' ) ) ) {
						foreach ( $inherited_modules as $inherited_module_id ) {
							$template_vars = array_merge( $template_vars, wp_list_pluck( carbon_get_post_meta( $inherited_module_id, 'wpft_template_variables' ), 'default_value', 'var_id' ) );
						}
					}
					$this->display_module( array(
						'module_id'     => $module['id'],
						'template_vars' => array_merge( $context, $template_vars ),
					) );
				}
			} elseif ( ! empty( $modules_from_post ) ) {
				$context = $this->init_timber_and_get_initial_context( $post_id );
				foreach ( $modules_from_post as $module ) {
					$template_vars = $this->sanitize_module_info( $module );
					if ( false !== ( $timber_posts = $this->maybe_get_timber_posts( $template_vars ) ) ) {
						$template_vars[ array_keys( $timber_posts )[0] ] = array_values( $timber_posts )[0];
					}
					$this->display_module( array(
						'module_id'     => $module['module'],
						'template_vars' => array_merge( $context, $template_vars ),
					) );
				}
			}
		}

		function maybe_get_timber_posts( $module ) {
			foreach ( $module as $k => $v ) {
				if ( is_array( $v ) && ! empty( $v ) && 'post' === $v[0]['type'] ) {
					return array(
						$k => Timber::get_posts( array(
							'post_type'      => $v[0]['subtype'],
							'orderby'        => 'post__in',
							'post__in'       => wp_list_pluck( $v, 'id' ),
							'posts_per_page' => - 1
						) )
					);
				}
			}

			return false;
		}

		function get_modules_posts( $args = null ) {
			$args            = wp_parse_args( $args, array(
				'post_type'   => 'wpft_module',
				'numberposts' => - 1,
				//'fields'      => 'ids',
			) );
			$args_serialized = md5( json_encode( $args ) );
			if ( ! isset( $this->modules_posts[ $args_serialized ] ) ) {
				$this->modules_posts[ $args_serialized ] = get_posts( $args );
			}

			return $this->modules_posts[ $args_serialized ];
		}

		function get_modules_posts_formatted( $args = null ) {
			$modules           = $this->get_modules_posts( $args );
			$modules_formatted = array();
			foreach ( $modules as $module ) {
				$modules_formatted[ $module->ID ] = $module->post_title;
			}
			wp_reset_postdata();

			return $modules_formatted;
		}

		function create_module_fields_on_custom_cpt() {
			$page_builder_settings = carbon_get_theme_option( 'wpft_page_builder_settings' );
			if ( empty( $page_builder_settings ) ) {
				return;
			}
			/*$fields = array(
				Field::make( 'select', 'module', __( 'Module', 'wpfactory' ) )->set_options( function () {
					return $this->get_modules_posts_formatted();
				} )
			);*/

			//$fields = array_merge( $fields, $this->get_dynamic_fields( array_keys( $this->get_modules_posts_formatted() ) ) );
			foreach ( $page_builder_settings as $key => $cpt_info ) {
				foreach ( $cpt_info['display_areas'] as $area_key => $area ) {
					Container::make( 'post_meta', $area['area_name'] )
					         ->set_datastore( new Carbon_Fields_Post_Meta_Datastore() )
					         ->where( 'post_type', 'CUSTOM', function ( $post_type ) use ( $key, $cpt_info ) {
						         $post_types = carbon_get_theme_option( 'wpft_page_builder_settings' );

						         return $post_types[ $key ]['cpt_relation'] === $post_type;
						         //return in_array( $post_type, array('product') );
					         } )
					         ->add_fields( array(
						         //Field::make( 'text', 'test_'.$key.'_'.$area_key, __( 'Test' ) )
						         Field::make( 'complex', 'wpft_modules_' . $key . '_' . $area_key, '' )
						              ->set_collapsed( true )
						              ->add_fields( array_merge(
							              array(
								              Field::make( 'select', 'module', __( 'Module', 'wpfactory' ) )
								                   ->set_width( '50%' )
								                   ->set_options( function () {
									                   return $this->get_modules_posts_formatted();
								                   } ),
								              Field::make( 'text', 'module_label', __( 'Module label', 'wpfactory' ) )
								                   ->set_width( '50%' )
							              ),
							              $this->get_dynamic_fields( array_keys( $this->get_modules_posts_formatted() ) )
						              ) )
						              ->set_header_template( function () {
							              $modules_formatted_json = json_encode( $this->get_modules_posts_formatted() );

							              return '<%-' . $modules_formatted_json . '[module] %>  <%- module_label ? "- " + module_label : "" %>';
						              } )
						              ->setup_labels( array(
							              'plural_name'   => 'Modules',
							              'singular_name' => 'Module',
						              ) )

						         /*Field::make( 'complex', 'wpft_modules_'.$key.'_'.$area_key, __( 'Modules' ) )
						              ->set_collapsed( true )
						              ->add_fields( $fields )
						              ->set_header_template( function () {
							              $modules_formatted_json = json_encode( $this->get_modules_posts_formatted() );

							              return '<%-' . $modules_formatted_json . '[module] %>';
						              } )*/
					         ) );
				}
			}
		}

		function highlight_wpfactory_menu_on_module_editing( $parent_file ) {
			global $pagenow;
			if ( 'post.php' === $pagenow && isset( $_GET['post'] ) && 'wpft_module' === get_post_type( $_GET['post'] ) ) {
				return 'crb_carbon_fields_container_wpfactory_settings.php';
			}

			return $parent_file;
		}

		function get_dynamic_field_type( $variable ) {
			$type         = $variable['var_type'];
			$id_converted = array(
				'url'       => 'text',
				'number'    => 'text',
				'post_type' => 'association'
			);
			$type         = isset( $id_converted[ $type ] ) ? $id_converted[ $type ] : $type;

			return $type;
		}

		function get_dynamic_fields( $modules_ids ) {
			$fields   = array();
			$ids_used = array();
			foreach ( $modules_ids as $module_id ) {
				$template_variables = carbon_get_post_meta( $module_id, 'wpft_template_variables' );
				if ( ! empty( $inherited_modules = carbon_get_post_meta( $module_id, 'wpft_inherited_modules' ) ) ) {
					foreach ( $inherited_modules as $inherited_module_id ) {
						$template_variables = array_merge( $template_variables, carbon_get_post_meta( $inherited_module_id, 'wpft_template_variables' ) );
					}
				}
				foreach ( $template_variables as $variable ) {
					$field_id = $variable['var_id'];
					if (
						empty( $field_id )
					) {
						continue;
					}
					$field_id = 'mod_' . $module_id . '_' . $field_id;
					if ( isset( $ids_used[ $field_id ] ) ) {
						continue;
					}
					$ids_used[ $field_id ] = $field_id;
					$field                 = Field::make( $this->get_dynamic_field_type( $variable ), $field_id, $variable['var_label'] );
					if ( 'select' === $variable['var_type'] ) {
						$field->set_options( wp_list_pluck( $variable['select_options'], 'option_label', 'option_id' ) );
					}
					if ( ! empty( $default_value = $variable['default_value'] ) ) {
						$field->set_default_value( $default_value );
					}

					$field->set_width( '33%' );
					switch ( $variable['var_type'] ) {
						case 'url':
							$field->set_attribute( 'type', 'url' );
							break;
						case 'number':
							$field->set_attribute( 'type', 'number' );
							break;
						case 'post_type':
							if ( ! empty( $variable['post_type_slug'] ) ) {
								$field->set_types( array(
									array(
										'type'      => 'post',
										'post_type' => $variable['post_type_slug']
									)
								) );
								$field->set_width( '100%' );
							}
							//$field->set_types( array( 'type', 'post', 'post_type' => $variable['post_type_slug'] ) );
							break;
					}
					$field->set_conditional_logic( array(
						array(
							'field'   => 'module',
							'value'   => $module_id,
							'compare' => '=',
						)
					) );
					$fields[] = $field;
				}
			}

			return $fields;
		}

		function get_var_type_conditional( $type = 'select' ) {
			return array(
				array(
					'field'   => 'var_type',
					'value'   => $type,
					'compare' => '=',
				)
			);
		}

		function create_module_fields() {
			Container::make( 'post_meta', __( 'Template variables', 'wpfactory' ) )
			         ->set_datastore( new Carbon_Fields_Post_Meta_Datastore() )
			         ->where( 'post_type', '=', 'wpft_module' )
				//->set_priority( 'high' )
				     ->add_fields( array(
					Field::make( 'complex', 'wpft_template_variables', '' )
					     ->set_collapsed( true )
					     ->add_fields( array(
						     Field::make( 'text', 'var_label', 'Label' )->set_width( '30%' ),
						     Field::make( 'text', 'var_id', 'ID' )->set_width( '30%' ),
						     Field::make( 'text', 'default_value', 'default_value' )->set_width( '30%' ),
						     Field::make( 'select', 'var_type', 'Type' )->set_width( '50%' )
						          ->add_options( array(
							          'text'      => 'Text',
							          'url'       => 'URL',
							          'textarea'  => 'Textarea',
							          'number'    => 'Number',
							          'select'    => 'Select',
							          'image'     => 'Image',
							          'post_type' => 'Post type'
						          ) ),

						     Field::make( 'complex', 'select_options', __( 'Select options' ) )
						          ->set_collapsed( true )
						          ->add_fields( array(
								          Field::make( 'text', 'option_label', 'Label' ),
								          Field::make( 'text', 'option_id', 'ID' ),
							          )
						          )
						          ->set_conditional_logic( $this->get_var_type_conditional( 'select' ) )
						          ->set_header_template( '
									    <% if (option_label) { %>
									        <%- option_label %>
									    <% } %>
							          ' ),
						     Field::make( 'text', 'post_type_slug', 'Post type slug' )
						          ->set_conditional_logic( $this->get_var_type_conditional( 'post_type' ) ),
					     ) )
					     ->set_header_template( '
							    <% if (var_label) { %>
							        <%- var_label %>
							    <% } %>
					          ' ),

				) );

			Container::make( 'post_meta', __( 'Template', 'wpfactory' ) )
			         ->set_datastore( new Carbon_Fields_Post_Meta_Datastore() )
			         ->where( 'post_type', '=', 'wpft_module' )
			         ->add_fields( array(
				         Field::make( 'textarea', 'wpft_template', '' )
				              ->set_rows( 20 )
			         ) );

			Container::make( 'post_meta', __( 'Module options', 'wpfactory' ) )
			         ->set_datastore( new Carbon_Fields_Post_Meta_Datastore() )
			         ->where( 'post_type', '=', 'wpft_module' )
			         ->add_tab( __( 'General', 'wpfactory' ), array(
				         Field::make( 'set', 'wpft_inherited_modules', __( 'Inherit module(s)', 'wpfactory' ) )->set_options( function () {
					         $args              = array();
					         $modules_formatted = $this->get_modules_posts_formatted( $args );
					         if ( ! empty( $post_id = $this->get_post_id_from_request() ) ) {
						         unset( $modules_formatted[ $post_id ] );
					         }

					         return $modules_formatted;
				         } )
				              ->limit_options( 5 ),
			         ) )
			         ->add_tab( __( 'CSS', 'wpfactory' ), array(
				         Field::make( 'text', 'wpft_css_classes', 'CSS classes' )->set_help_text( 'Adds extra CSS classes to the module wrapper. Separate by space.' ),
				         Field::make( 'text', 'wpft_template_vars_to_css', __( 'Template variables to CSS', 'wpfactory' ) )->set_help_text( 'Adds extra CSS classes from the Template variables to the module wrapper. Separate by comma. Use the variable Id.' )
			         ) );
		}

		function get_post_id_from_request() {
			if (
				( isset( $_REQUEST['post'] ) && ! empty( $post_id = $_REQUEST['post'] ) ) ||
				( isset( $_REQUEST['post_ID'] ) && ! empty( $post_id = $_REQUEST['post_ID'] ) )
			) {
				return $post_id;
			}

			return '';
		}

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

	}
}