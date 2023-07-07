<?php
/**
 * WPFactory theme - Reviews and statistics Module.
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

if ( ! class_exists( 'WPFactory\WPFactory_Theme\Component\Product_Reviews_Module' ) ) {

	//class Menus {
	class Product_Reviews_Module implements Theme_Component {
		public function init() {
			add_filter( 'wpft_module_prod_reviews_template_vars', array( $this, 'add_extra_template_vars' ), 10, 2 );
		}

		function add_extra_template_vars( $vars ) {
			if (
				! isset( $vars['product'] )
			) {
				return $vars;
			}
			$product = $vars['product'];
			$vars    = wp_parse_args( $vars, array(
				'initial_reviews_total' => 3
			) );

			// Reviews.
			$initial_reviews_total = (int) $vars['initial_reviews_total'];
			$args                  = array(
				'type'    => 'review',
				'orderby' => 'comment_date_gmt',
				'order'   => 'DESC',
				'post_id' => $product->get_id(),   // Use post_id, not post_ID
				'number'  => $initial_reviews_total // Return only the count
			);
			$reviews               = get_comments( $args );
			foreach ( $reviews as $review ) {
				$rating = get_comment_meta( $review->comment_ID, 'rating', true );
				// Rating.
				$rating_label            = 'rating';
				$review->{$rating_label} = $rating;
				// Avatar.
				$avatar_label            = 'avatar';
				$review->{$avatar_label} = get_avatar( $review, 48 );
				// Review title.
				/*$review_title_label      = 'title';
				$review->{$review_title_label} = 'A test title';*/

			}
			$vars['reviews'] = $reviews;

			// Statistics.
			if ( '' != ( $item_slug = get_post_meta( $product->get_id(), '_item_slug', true ) ) ) {
				$item_data = get_option( 'alg_saved_items_data', array() );
				if ( isset( $item_data[ $item_slug ] ) ) {
					$vars['stats_data'] = $item_data[ $item_slug ];
				}
				$vars['stats_data']->installations_total = get_post_meta( $product->get_id(), 'total_sales', true );
				//$saved_items_data[ $item_slug ]->sections->changelog
			}

			return $vars;
		}
	}
}