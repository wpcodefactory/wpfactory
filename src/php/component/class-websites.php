<?php
/**
 * WPFactory theme - Websites.
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

if ( ! class_exists( 'WPFactory\WPFactory_Theme\Component\Websites' ) ) {

	class Websites implements Theme_Component {
		public function init() {
			add_action( 'admin_menu', array( $this, 'add_modules_cpt_as_wpft_submenu' ), 20 );
			add_filter( 'parent_file', array( $this, 'highlight_wpfactory_menu_on_module_editing' ) );
			add_action( 'init', array( $this, 'create_websites_post_type' ) );
			add_action( 'carbon_fields_register_fields', array( $this, 'create_websites_fields' ) );
		}

		function create_websites_fields() {
			Container::make( 'post_meta', __( 'Website info', 'wpfactory' ) )
			         ->set_datastore( new Carbon_Fields_Post_Meta_Datastore() )
			         ->where( 'post_type', '=', 'wpft_website' )
				//->set_priority( 'high' )
				     ->add_fields( array(
					Field::make( 'text', 'wpft_url', 'URL' )->set_attribute( 'type', 'url' )
				) );
		}

		function add_modules_cpt_as_wpft_submenu() {
			add_submenu_page(
			//'wpft',
				'crb_carbon_fields_container_wpfactory_settings.php',
				'Websites',
				'Websites',
				'manage_options',
				'edit.php?post_type=wpft_website'
			);
			//add_submenu_page( 'my-top-level-slug', 'My Custom Page', 'My Custom Page','manage_options', 'my-top-level-slug' );
		}

		function highlight_wpfactory_menu_on_module_editing( $parent_file ) {
			global $pagenow;
			if ( 'post.php' === $pagenow && isset( $_GET['post'] ) && 'wpft_website' === get_post_type( $_GET['post'] ) ) {
				return 'crb_carbon_fields_container_wpfactory_settings.php';
			}

			return $parent_file;
		}

		function create_websites_post_type() {
			$labels = array(
				'name'                  => _x( 'Websites', 'Post type general name', 'textdomain' ),
				'singular_name'         => _x( 'Website', 'Post type singular name', 'textdomain' ),
				'menu_name'             => _x( 'Websites', 'Admin Menu text', 'textdomain' ),
				'name_admin_bar'        => _x( 'Website', 'Add New on Toolbar', 'textdomain' ),
				'add_new'               => __( 'Add New', 'textdomain' ),
				'add_new_item'          => __( 'Add New Website', 'textdomain' ),
				'new_item'              => __( 'New Website', 'textdomain' ),
				'edit_item'             => __( 'Edit Website', 'textdomain' ),
				'view_item'             => __( 'View Website', 'textdomain' ),
				'all_items'             => __( 'All Websites', 'textdomain' ),
				'search_items'          => __( 'Search Websites', 'textdomain' ),
				'parent_item_colon'     => __( 'Parent Websites:', 'textdomain' ),
				'not_found'             => __( 'No Websites found.', 'textdomain' ),
				'not_found_in_trash'    => __( 'No Websites found in Trash.', 'textdomain' ),
				'featured_image'        => _x( 'Website logo', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'textdomain' ),
				'set_featured_image'    => _x( 'Set logo', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'textdomain' ),
				'remove_featured_image' => _x( 'Remove logo', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'textdomain' ),
				'use_featured_image'    => _x( 'Use as logo', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'textdomain' ),
				'archives'              => _x( 'Website archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'textdomain' ),
				'insert_into_item'      => _x( 'Insert into Website', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'textdomain' ),
				'uploaded_to_this_item' => _x( 'Uploaded to this Website', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'textdomain' ),
				'filter_items_list'     => _x( 'Filter Websites list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'textdomain' ),
				'items_list_navigation' => _x( 'Websites list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'textdomain' ),
				'items_list'            => _x( 'Websites list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'textdomain' ),
			);

			$args = array(
				'labels'          => $labels,
				'public'          => false,
				'show_ui'         => true,
				'show_in_menu'    => false,
				'rewrite'         => array( 'slug' => 'wpft_website' ),
				'capability_type' => 'post',
				'has_archive'     => false,
				'hierarchical'    => false,
				'menu_position'   => null,
				'supports'        => array( 'title', 'thumbnail' ),
			);

			register_post_type( 'wpft_website', $args );
		}
	}
}