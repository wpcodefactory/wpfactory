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

		protected $all_modules_posts = array();

		protected $modules_posts = array();

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
			// Generate Compressed ID from ID.
			add_action( 'updated_post_meta', array( $this, 'update_compressed_id_from_id' ), 10, 4 );
			add_action( 'added_post_meta', array( $this, 'update_compressed_id_from_id' ), 10, 4 );
			add_action( 'deleted_post_meta', array( $this, 'update_compressed_id_from_id' ), 10, 4 );
		}

		/**
		 * update_compressed_id_from_id
		 *
		 * @version 2.5.1
		 * @since   2.5.1
		 *
		 * @param $meta_id
		 * @param $post_id
		 * @param $meta_key
		 * @param $meta_value
		 */
		function update_compressed_id_from_id( $meta_id, $post_id, $meta_key, $meta_value ) {
			if (
				in_array( $meta_key, array(
					'_wpft_module_id',
				) ) &&
				'wpft_module' === get_post_type( $post_id ) &&
				! empty( $meta_value )
			) {
				$unique_id = $this->generate_unique_id( $meta_value );
				update_post_meta( $post_id, '_wpft_module_id_hashed', $unique_id );
			}
		}

		function generate_unique_id( $input, $length = 8 ) {
			// Create a raw binary sha256 hash and base64 encode it.
			$hash_base64 = base64_encode( hash( 'sha256', $input, true ) );
			// Replace non-urlsafe chars to make the string urlsafe.
			$hash_urlsafe = strtr( $hash_base64, '+/', '-.' );
			// Trim base64 padding characters from the end.
			$hash_urlsafe = rtrim( $hash_urlsafe, '=' );

			// Shorten the string before returning.
			return sanitize_title( strtolower( substr( $hash_urlsafe, 0, $length ) ) );
		}

		function manage_modules_displays_from_hook_settings() {
			$page_builder_settings = carbon_get_theme_option( 'wpft_pb_cpt_settings' );
			if ( empty( $page_builder_settings ) ) {
				return;
			}

			foreach ( $page_builder_settings as $cpt_key => $cpt_info ) {
				foreach ( $cpt_info['display_areas'] as $area_key => $area ) {
					$current_area_info             = $area;
					$current_area_info['area_key'] = $area_key;
					$current_area_info['cpt']      = array(
						'key' => $cpt_key,
						'id'  => wpft_get_option( '_wpft_pb_cpt_settings|cpt_relation|' . $cpt_key . '|0|value' ),
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
			$args             = wp_parse_args( $args, array(
				'module_id'        => null,
				'module_id_hashed' => null,
				'template_vars'    => null,
			) );
			$module_id        = $args['module_id'];
			$module_id_hashed = $args['module_id_hashed'];
			if ( empty( $module_id ) && ! empty( $module_id_hashed ) ) {
				$module = $this->get_module_post_from_id_hashed( $module_id_hashed );
				$module_id = $module->ID;
			}
			$module_id_string = get_post_meta( $module_id, '_wpft_module_id', true );
			$template_vars          = $args['template_vars'];
			$template               = carbon_get_post_meta( $module_id, 'wpft_template' );
			$module_classes         = array(
				'wpftpb-mod',
			);
			$module_wrapper_classes = array(
				'wpftpb-section',
				'wpftpb-mod-wrapper-' . $module_id,
				'wpftpb-mod-wrapper-' . $module_id_string,

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
			$template_vars = apply_filters( 'wpft_module_template_vars', $template_vars, $args );
			$template_vars = apply_filters( "wpft_module_{$module_id_string}_template_vars", $template_vars, $args );
			/*if ( ! empty( $extra_template_variables_filter = carbon_get_post_meta( $module_id, 'wpft_template_variables_filter' ) ) ) {
				$template_vars = apply_filters( $extra_template_variables_filter, $template_vars, $module_id );
			}*/
			$output = \Timber::compile_string( $template, $template_vars );
			if ( ! empty( trim( $output ) ) ) {
				$output = sprintf(
					'<section class="%s">%s<div class="%s">%s</div></section>',
					implode( " ", $this->sanitize_css_classes_array( apply_filters( 'wpft_module_wrapper_css_classes', $module_wrapper_classes ) ) ),
					apply_filters( "wpft_module_{$module_id_string}_before_content", '' ),
					implode( " ", $this->sanitize_css_classes_array( apply_filters( 'wpft_module_css_classes', $module_classes ) ) ),
					$output
				);
				echo $output;
			}
		}

		function sanitize_css_classes_array( $classes ) {
			return array_map( 'sanitize_html_class', $classes );
		}

		function init_timber_and_get_initial_context( $post_id ) {
			$timber          = new \Timber\Timber();
			$context         = Timber::context();
			$context['post'] = new \Timber\Post( $post_id );
			if ( 'product' === get_post_type( $post_id ) ) {
				$product            = wc_get_product( $context['post']->ID );
				$context['product'] = $product;
			}

			return $context;
		}

		function get_module_post_from_id_hashed( $id_hashed ) {
			$args = array(
				'post_type'  => 'wpft_module',
				'limit'      => 1,
				'meta_query' => array(
					array(
						'key'     => '_wpft_module_id_hashed',
						'value'   => $id_hashed,
						'compare' => '='
					)
				)
			);

			$args_serialized = md5( json_encode( $args ) );
			if ( ! isset( $this->modules_posts[ $args_serialized ] ) ) {
				$posts = get_posts( $args );
				if ( $posts ) {
					foreach ( $posts as $post ) {
						setup_postdata( $post );
						$this->modules_posts[ $args_serialized ] = $post;
					}
					wp_reset_postdata();
				} else {
					$this->modules_posts[ $args_serialized ] = false;
				}
			}

			return $this->modules_posts[ $args_serialized ];
		}

		//function get_template_vars_from_module( $module_id ) {
		function get_template_vars_from_module( $args = null ) {
			$args      = wp_parse_args( $args, array(
				'module_id'        => '',
				'module_id_hashed' => '',
			) );
			$module_id = (int) $args['module_id'];
			if ( empty( $module_id ) && ! empty( $args['module_id_hashed'] ) ) {
				$module    = $this->get_module_post_from_id_hashed( $args['module_id_hashed'] );
				$module_id = $module->ID;
			}
			$template_vars = wp_list_pluck( carbon_get_post_meta( $module_id, 'wpft_template_variables' ), 'default_value', 'var_id' );
			if ( ! empty( $inherited_modules = carbon_get_post_meta( $module_id, 'wpft_inherited_modules' ) ) ) {
				foreach ( $inherited_modules as $inherited_module_id ) {
					$template_vars = array_merge( $template_vars, wp_list_pluck( carbon_get_post_meta( $inherited_module_id, 'wpft_template_variables' ), 'default_value', 'var_id' ) );
				}
			}

			return $template_vars;
		}

		function display_modules( $current_area_info, $post_id = null ) {
			$post_id           = ! is_null( $post_id ) ? $post_id : get_the_ID();
			$modules_from_post = carbon_get_post_meta( $post_id, 'wpft_pb_' . $current_area_info['cpt']['id'] . '_' . $current_area_info['id'] );
			if ( ! empty( $before_area_modules = apply_filters( "wpft_before_area_{$current_area_info['id']}_modules", '' ) ) ) {
				echo wp_kses_post( $before_area_modules );
			}
			if ( empty( $modules_from_post ) && ! empty( $default_modules = $current_area_info['default_modules'] ) ) {
				$context = $this->init_timber_and_get_initial_context( $post_id );
				foreach ( $default_modules as $module ) {
					$template_vars = $this->get_template_vars_from_module( array( 'module_id_hashed' => $module['module_id'] ) );
					$this->display_module( array(
						'module_id_hashed' => $module['module_id'],
						'template_vars'    => array_merge( $context, $template_vars ),
					) );
				}
			} elseif ( ! empty( $modules_from_post ) ) {
				$context = $this->init_timber_and_get_initial_context( $post_id );
				foreach ( $modules_from_post as $module ) {
					$template_vars          = $this->sanitize_module_info( $module );
					$original_template_vars = $this->get_template_vars_from_module( array( 'module_id_hashed' => $module['module'] ) );
					$template_vars          = array_replace( $original_template_vars, $template_vars );
					$template_vars          = $this->maybe_convert_template_vars_to_timber_posts( $template_vars );
					$this->display_module( array(
						'module_id_hashed' => $module['module'],
						'template_vars'    => array_merge( $context, $template_vars ),
					) );
				}
			}
		}

		function maybe_convert_template_vars_to_timber_posts( $template_vars ) {
			$module_id              = $this->get_module_post_from_id_hashed( $template_vars['module'] )->ID;
			$original_template_vars = carbon_get_post_meta( $module_id, 'wpft_template_variables' );
			$post_type_vars         = wp_list_filter( $original_template_vars, array( 'var_type' => 'post_type' ) );
			foreach ( $post_type_vars as $post_type_var ) {
				if ( isset( $template_vars[ $post_type_var['var_id'] ] ) ) {
					if ( ! empty( $var = $template_vars[ $post_type_var['var_id'] ] ) ) {
						$template_vars[ $post_type_var['var_id'] ] = Timber::get_posts( array(
							'post_type'      => $var[0]['subtype'],
							'orderby'        => 'post__in',
							'post__in'       => wp_list_pluck( $var, 'id' ),
							'posts_per_page' => - 1
						) );
					} elseif ( 'get_posts_automatically' === $post_type_var['post_type_mode'] ) {
						$get_posts_args = array(
							'post_type'      => $post_type_var['post_type_slug'],
							'posts_per_page' => - 1
						);
						$tax_query      = array();
						if ( ! empty( $taxes = $template_vars[ $post_type_var['var_id'] . '_post_type_taxonomies' ] ) && is_array( $taxes ) ) {
							foreach ( $taxes as $chosen_tax_info ) {
								$tax_query[] = array(
									'taxonomy' => $chosen_tax_info['subtype'],
									'field'    => 'id',
									'terms'    => $chosen_tax_info['id'],
								);
							}
							if ( ! empty( $tax_query ) ) {
								$get_posts_args['tax_query'] = $tax_query;
							}
						}
						$posts                                     = Timber::get_posts( $get_posts_args );
						$template_vars[ $post_type_var['var_id'] ] = $posts;
					}
				}
			}

			return $template_vars;
		}

		function get_all_modules_posts( $args = null ) {
			$args            = wp_parse_args( $args, array(
				'post_type'   => 'wpft_module',
				'numberposts' => - 1,
				//'fields'      => 'ids',
			) );
			$args_serialized = md5( json_encode( $args ) );
			if ( ! isset( $this->all_modules_posts[ $args_serialized ] ) ) {
				$this->all_modules_posts[ $args_serialized ] = get_posts( $args );
			}

			return $this->all_modules_posts[ $args_serialized ];
		}

		function get_modules_posts_formatted( $args = null ) {
			$modules           = $this->get_all_modules_posts( $args );
			$modules_formatted = array();
			foreach ( $modules as $module ) {
				$modules_formatted[ get_post_meta( $module->ID, '_wpft_module_id_hashed', true ) ] = $module->post_title;
				//$modules_formatted[ $module->ID ] = $module->post_title;
			}
			wp_reset_postdata();

			return $modules_formatted;
		}

		function create_module_fields_on_custom_cpt() {
			$page_builder_cpt_settings = carbon_get_theme_option( 'wpft_pb_cpt_settings' );
			//error_log(print_r($page_builder_cpt_settings,true));
			if ( empty( $page_builder_cpt_settings ) ) {
				return;
			}
			foreach ( $page_builder_cpt_settings as $cpt_key => $cpt_info ) {
				$cpt_relation = wpft_get_option( '_wpft_pb_cpt_settings|cpt_relation|' . $cpt_key . '|0|value' );
				foreach ( $cpt_info['display_areas'] as $area_key => $area ) {
					if (
						isset( $area['editable'] ) &&
						false === $area['editable']
					) {
						continue;
					}

					$container = Container::make( 'post_meta', 'wpft_cpt_' . $cpt_relation . '_' . $area['id'], 'Page builder area: ' . $area['area_name'] )
					                      ->set_datastore( new Carbon_Fields_Post_Meta_Datastore() )
					                      ->where( 'post_type', 'CUSTOM', function ( $post_type ) use ( $cpt_key, $cpt_info ) {
						                      $post_types = carbon_get_theme_option( 'wpft_pb_cpt_settings' );

						                      return $post_types[ $cpt_key ]['cpt_relation'] === $post_type;
					                      } );

					$container->add_fields( array(
						Field::make( 'complex', 'wpft_pb_' . $cpt_relation . '_' . $area['id'], '' )
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
							     $this->get_dynamic_fields( array_keys( $this->get_modules_posts_formatted() ), 'wpft_modules_' . $cpt_info['cpt_relation'] . '_' . $area_key, $container )
						     ) )
						     ->set_header_template( function () {
							     $modules_formatted_json = json_encode( $this->get_modules_posts_formatted() );

							     return '<%-' . $modules_formatted_json . '[module] %>  <%- module_label ? "- " + module_label : "" %>';
						     } )
						     ->setup_labels( array(
							     'plural_name'   => 'Modules',
							     'singular_name' => 'Module',
						     ) )
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
				'url'        => 'text',
				'repeatable' => 'complex',
				'number'     => 'text',
				'post_type'  => 'association',
				'taxonomy'   => 'association'
			);
			$type         = isset( $id_converted[ $type ] ) ? $id_converted[ $type ] : $type;

			return $type;
		}

		function get_dynamic_fields( $modules_ids_hashed, $complex_field_id = null, $container = null ) {
			$fields   = array();
			$ids_used = array();
			foreach ( $modules_ids_hashed as $module_id_hashed ) {
				$module_id          = $this->get_module_post_from_id_hashed( $module_id_hashed )->ID;
				$template_variables = carbon_get_post_meta( $module_id, 'wpft_template_variables' );
				if ( ! empty( $inherited_modules = carbon_get_post_meta( $module_id, 'wpft_inherited_modules' ) ) ) {
					foreach ( $inherited_modules as $inherited_module_id ) {
						$template_variables = array_merge( $template_variables, carbon_get_post_meta( $inherited_module_id, 'wpft_template_variables' ) );
					}
				}
				foreach ( $template_variables as $variable ) {
					$can_add_field = true;
					$field_id      = $variable['var_id'];
					if (
						empty( $field_id ) ||
						false === $variable['editable']
					) {
						continue;
					}
					$field_id = 'mod_' . $module_id_hashed . '_' . $field_id;
					if ( isset( $ids_used[ $field_id ] ) ) {
						continue;
					}
					$ids_used[ $field_id ] = $field_id;
					$field                 = Field::make( $this->get_dynamic_field_type( $variable ), $field_id, $variable['var_label'] );
					$extra_fields          = array();
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
										'post_type' => $variable['post_type_slug'],
									)
								) );
								if ( isset( $variable['post_type_taxonomies'] ) && ! empty( $variable['post_type_taxonomies'] ) ) {
									/*add_filter( 'carbon_fields_association_field_options_' . $field_id . '_' . 'post' . '_' . $variable['post_type_slug'], function ( $args ) use ( $complex_field_id, $field_id, $container ) {
										@$post_id = $_REQUEST['post'] ?? $_REQUEST['id'] ?? $_REQUEST['post_ID'];
										$unique_id = $container->get_id() . '_' . $complex_field_id . '_' . $field_id;
										$tax_query = array();
										if ( ! empty( $post_id ) ) {
											$fields = carbon_get_post_meta( $post_id, $complex_field_id, $container->get_id() );
											foreach ( $fields as $k => $field ) {
												if ( isset( $args[ $unique_id . '_' . $k ] ) ) {
													continue;
												}
												if ( isset( $field[ $field_id . '_post_type_taxonomies' ] ) && ! empty( $tax_values = $field[ $field_id . '_post_type_taxonomies' ] ) ) {
													foreach ( $tax_values as $other_key => $chosen_tax_info ) {
														$tax_query[] = array(
															'taxonomy' => $chosen_tax_info['subtype'],
															'field'    => 'id',
															'terms'    => $chosen_tax_info['id'],
														);
													}
													if ( ! empty( $tax_query ) ) {
														$args[ $unique_id . '_' . $k ] = $k;
														$args['tax_query']             = $tax_query;
													}
												}
											}
										}

										return $args;
									} );*/
									add_filter( 'carbon_fields_association_field_options_' . $field_id . '_' . 'post_type_taxonomies_term_product_cat', function ( $args ) use ( $complex_field_id ) {
										$args['orderby'] = '';

										return $args;
									} );
									foreach ( $variable['post_type_taxonomies'] as $tax_info ) {
										$field_tax_terms =
											//Field::make( 'association', $field_id . '_'.'post_type_taxonomy_' . $tax_info['tax'], sprintf( 'Taxonomy (%s)', $tax_info['tax'] ) )
											//carbon_fields_association_field_options_mod_347_faq_posts_post_type_taxonomies_term_wpft_faq_category
											Field::make( 'association', $field_id . '_' . 'post_type_taxonomies', sprintf( 'Filter posts by taxonomy (%s)', $tax_info['tax'] ) )
											     ->set_types( array(
												     array(
													     'type'     => 'term',
													     'taxonomy' => $tax_info['tax'],
												     )
											     ) )
											     ->set_conditional_logic( array(
												     array(
													     'field'   => 'module',
													     'value'   => $module_id_hashed,
													     'compare' => '=',
												     )
											     ) );
										if ( ! empty( $default_tax_term = $tax_info['default_tax_term'] ) ) {
											if ( ! is_numeric( $default_tax_term ) ) {
												$term = $this->get_term_by_slug_via_db( $tax_info['tax'], $default_tax_term );
												if ( false !== $term ) {
													$default_tax_term = $term->term_id;
												}
											}
											$field_tax_terms->set_default_value( array( 'term:' . $tax_info['tax'] . ':' . $default_tax_term ) );
										}
										//$fields[] = $field_tax_terms;
										$extra_fields[] = $field_tax_terms;
									}
								}
								$field->set_width( '100%' );

							}
							//$field->set_types( array( 'type', 'post', 'post_type' => $variable['post_type_slug'] ) );
							break;
						case 'taxonomy':
							//carbon_fields_association_field_options_mod_393_product_cat_term_product_cat
							//error_log(print_r('carbon_fields_association_field_options_' . $field_id . '_' . 'product_cat_term_product_cat',true));
							add_filter( 'carbon_fields_association_field_options_' . $field_id . '_' . 'term_product_cat', function ( $args ) use ( $complex_field_id ) {
								$args['orderby'] = '';

								return $args;
							} );
							//error_log(print_r($variable,true));
							if ( ! empty( $variable['var_id'] ) ) {
								//error_log(print_r($variable,true));
								$field->set_types( array(
									array(
										'type'     => 'term',
										'taxonomy' => $variable['var_id'],
									)
								) );
								if ( ! empty( $default_value = $variable['default_value'] ) ) {
									if ( ! is_numeric( $default_value ) ) {
										$term = $this->get_term_by_slug_via_db( $variable['var_id'], $default_value );
										if ( false !== $term ) {
											$default_value = $term->term_id;
										}
									}
									$field->set_default_value( array( 'term:' . $variable['var_id'] . ':' . $default_value ) );
								}
							}
							break;
						case 'repeatable':
							$field->set_width( '100%' );
							if ( isset( $variable['repeatable_group'] ) && ! empty( $variable['repeatable_group'] ) ) {
								$fields_to_add = array();
								foreach ( $variable['repeatable_group'] as $group_field ) {
									$fields_to_add[] = Field::make( $group_field['repeatable_group_field_type'], $group_field['repeatable_group_field_id'], $group_field['repeatable_group_field_label'] )->set_width( '50%' );
								}
								$field
									->set_collapsed( true )
									->add_fields( $fields_to_add )
									->set_layout( 'tabbed-vertical' );
							}
							break;
					}
					$field->set_conditional_logic( array(
						array(
							'field'   => 'module',
							'value'   => $module_id_hashed,
							'compare' => '=',
						)
					) );
					if ( $can_add_field ) {
						$fields[] = $field;
					}
					$fields = array_merge( $fields, $extra_fields );
				}
			}

			return $fields;
		}

		function get_term_by_slug_via_db( $taxonomy, $term_slug ) {
			global $wpdb;
			$query = $wpdb->prepare(
				"SELECT t.*
			    FROM {$wpdb->terms} AS t
			    INNER JOIN {$wpdb->term_taxonomy} AS tt ON t.term_id = tt.term_id
			    WHERE tt.taxonomy = %s
			    AND t.slug = %s
			    LIMIT 1",
				$taxonomy,
				$term_slug
			);

			$term = $wpdb->get_results( $query );
			if ( $term && ! empty( $term ) ) {
				return $term[0];
			} else {
				return false;
			}
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
						     Field::make( 'text', 'default_value', 'Default value' )->set_width( '30%' ),
						     Field::make( 'checkbox', 'editable', 'Editable' )->set_width( '50%' )->set_default_value( true ),
						     Field::make( 'select', 'var_type', 'Type' )->set_width( '50%' )
						          ->add_options( array(
							          'text'       => 'Text',
							          'url'        => 'URL',
							          'textarea'   => 'Textarea',
							          'number'     => 'Number',
							          'select'     => 'Select',
							          'image'      => 'Image',
							          'post_type'  => 'Post type',
							          'taxonomy'   => 'Taxonomy term(s)',
							          'repeatable' => 'Repeatable field'
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
						     Field::make( 'select', 'post_type_mode', 'Mode' )
						          ->set_options( function () {
							          return array(
								          'select_posts_manually'   => 'Get only posts manually selected',
								          'get_posts_automatically' => 'Get posts automatically if none is selected',
							          );
						          } )
						          ->set_width( '50%' )
						          ->set_conditional_logic( $this->get_var_type_conditional( 'post_type' ) ),
						     Field::make( 'text', 'post_type_slug', 'Post type slug' )
						          ->set_width( '50%' )
						          ->set_conditional_logic( $this->get_var_type_conditional( 'post_type' ) ),
						     Field::make( 'complex', 'post_type_taxonomies', 'Filter posts by taxonomies' )
						          ->set_collapsed( true )
						          ->add_fields( array(
								          Field::make( 'text', 'tax', 'Taxonomy' )->set_width( '50%' ),
								          Field::make( 'text', 'default_tax_term', 'Default taxonomy term' )->set_width( '50%' ),
							          )
						          )
						          ->set_conditional_logic( $this->get_var_type_conditional( 'select' ) )
						          ->set_header_template( '
									    <% if (tax) { %>
									        <%- tax %>
									    <% } %>
							          ' )
						          ->set_conditional_logic( $this->get_var_type_conditional( 'post_type' ) ),
						     Field::make( 'complex', 'repeatable_group', 'Field group' )
						          ->set_collapsed( true )
						          ->add_fields( array(
							          Field::make( 'text', 'repeatable_group_field_label', 'Label' )->set_width( '30%' ),
							          Field::make( 'text', 'repeatable_group_field_id', 'ID' )->set_width( '30%' ),
							          Field::make( 'select', 'repeatable_group_field_type', 'Type' )->set_width( '30%' )
							               ->set_options( function () {
								               return array(
									               'text'     => 'Text',
									               'textarea' => 'Textarea',
								               );
							               } )
						          ,
						          ) )
						          ->set_header_template( '
									    <% if (repeatable_group_field_label) { %>
									        <%- repeatable_group_field_label %>
									    <% } %>
							          ' )
						          ->set_conditional_logic( $this->get_var_type_conditional( 'repeatable' ) ),
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
				         Field::make( 'text', 'wpft_module_id', __( 'Module ID', 'wpfactory' ) )->set_attribute( 'maxLength', 30 ),
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
			         /*->add_tab( __( 'PHP', 'wpfactory' ), array(
				         Field::make( 'text', 'wpft_template_variables_filter', 'Template variables filter' )->set_help_text( 'Custom hook filter used to send info to template as an associative array. ' ),
			         ) );*/
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