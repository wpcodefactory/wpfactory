<?php
/**
 * WPFactory theme - FAQ.
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  WPFactory
 */

namespace WPFactory\WPFactory_Theme\Component;

use Carbon_Fields\Container;
use Carbon_Fields\Field;
use WPFactory\WPFactory_Theme\Carbon_Fields\Carbon_Fields_Post_Meta_Datastore;
use WPFactory\WPFactory_Theme\Theme_Component;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'WPFactory\WPFactory_Theme\Component\FAQ' ) ) {

	class FAQ implements Theme_Component {
		public function init() {
			add_action( 'admin_menu', array( $this, 'add_cpt_as_wpft_submenu' ), 20 );
			add_action( 'admin_menu', array( $this, 'add_tax_as_wpft_submenu' ), 20 );
			add_filter( 'parent_file', array( $this, 'highlight_wpfactory_menu_on_cpt_editing' ) );
			add_filter( 'parent_file', array( $this, 'highlight_wpfactory_menu_on_tax_editing' ) );
			add_action( 'init', array( $this, 'create_websites_post_type' ) );
			add_action( 'init', array( $this, 'register_private_taxonomy' ) );
			//add_action( 'carbon_fields_register_fields', array( $this, 'create_websites_fields' ) );

			//edit-tags.php?taxonomy=wpft_faq_category&post_type=wpft_faq
		}

		function add_cpt_as_wpft_submenu() {
			add_submenu_page(
			//'wpft',
				'crb_carbon_fields_container_wpfactory_settings.php',
				'FAQ',
				'FAQ',
				'manage_options',
				'edit.php?post_type=wpft_faq'
			);

		}

		function add_tax_as_wpft_submenu() {
			add_submenu_page(
			//'wpft',
				'crb_carbon_fields_container_wpfactory_settings.php',
				'FAQ',
				'FAQ categories',
				'manage_options',
				'edit-tags.php?taxonomy=wpft_faq_category&post_type=wpft_faq'
			);

		}

		function is_cpt_admin_edit_url( $cpt_slug ) {
			global $pagenow;

			return (
				       'post.php' === $pagenow &&
				       isset( $_GET['post'] ) && $cpt_slug === get_post_type( $_GET['post'] )
			       ) ||
			       (
				       'post-new.php' === $pagenow &&
				       isset( $_GET['post_type'] ) && $cpt_slug === ( $_GET['post_type'] )
			       );
		}

		function highlight_wpfactory_menu_on_tax_editing( $parent_file ) {
			if (
				isset( $_GET['taxonomy'] ) && 'wpft_faq_category' === $_GET['taxonomy']

			) {
				//return 'edit-tags.php?taxonomy=wpft_faq_category';
				return 'crb_carbon_fields_container_wpfactory_settings.php';
			}

			return $parent_file;
		}

		function highlight_wpfactory_menu_on_cpt_editing( $parent_file ) {
			if ( $this->is_cpt_admin_edit_url( 'wpft_faq' ) ) {
				return 'crb_carbon_fields_container_wpfactory_settings.php';
			}

			return $parent_file;
		}

		function create_websites_post_type() {
			$labels = array(
				'name'                  => _x( 'Questions', 'Post type general name', 'textdomain' ),
				'singular_name'         => _x( 'Question', 'Post type singular name', 'textdomain' ),
				'menu_name'             => _x( 'Questions', 'Admin Menu text', 'textdomain' ),
				'name_admin_bar'        => _x( 'Question', 'Add New on Toolbar', 'textdomain' ),
				'add_new'               => __( 'Add New', 'textdomain' ),
				'add_new_item'          => __( 'Add New Question', 'textdomain' ),
				'new_item'              => __( 'New Question', 'textdomain' ),
				'edit_item'             => __( 'Edit Question', 'textdomain' ),
				'view_item'             => __( 'View Question', 'textdomain' ),
				'all_items'             => __( 'All Questions', 'textdomain' ),
				'search_items'          => __( 'Search Questions', 'textdomain' ),
				'parent_item_colon'     => __( 'Parent Questions:', 'textdomain' ),
				'not_found'             => __( 'No Questions found.', 'textdomain' ),
				'not_found_in_trash'    => __( 'No Questions found in Trash.', 'textdomain' ),
				'featured_image'        => _x( 'Question logo', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'textdomain' ),
				'set_featured_image'    => _x( 'Set logo', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'textdomain' ),
				'remove_featured_image' => _x( 'Remove logo', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'textdomain' ),
				'use_featured_image'    => _x( 'Use as logo', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'textdomain' ),
				'archives'              => _x( 'Question archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'textdomain' ),
				'insert_into_item'      => _x( 'Insert into Question', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'textdomain' ),
				'uploaded_to_this_item' => _x( 'Uploaded to this Question', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'textdomain' ),
				'filter_items_list'     => _x( 'Filter Questions list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'textdomain' ),
				'items_list_navigation' => _x( 'Questions list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'textdomain' ),
				'items_list'            => _x( 'Questions list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'textdomain' ),
			);

			$args = array(
				'labels'          => $labels,
				'public'          => false,
				'show_ui'         => true,
				'show_in_menu'    => false,
				'rewrite'         => array( 'slug' => 'wpft_faq' ),
				'capability_type' => 'post',
				'has_archive'     => false,
				'hierarchical'    => false,
				'menu_position'   => null,
				'supports'        => array( 'title', 'editor' ),
			);

			register_post_type( 'wpft_faq', $args );
		}

		function register_private_taxonomy() {
			$args = array(
				'label'        => __( 'Category', 'wpfactory' ),
				'public'       => false,
				'show_ui'      => true,
				'show_in_menu' => false,
				'rewrite'      => false,
				'hierarchical' => true
			);

			register_taxonomy( 'wpft_faq_category', 'wpft_faq', $args );
		}

		/*function create_websites_fields() {
			Container::make( 'post_meta', __( 'Answwer', 'wpfactory' ) )
			         ->set_datastore( new Carbon_Fields_Post_Meta_Datastore() )
			         ->where( 'post_type', '=', 'wpft_website' )
				//->set_priority( 'high' )
				     ->add_fields( array(
					Field::make( 'text', 'wpft_url', 'URL' )->set_attribute( 'type', 'url' )
				) );
		}*/
	}
}