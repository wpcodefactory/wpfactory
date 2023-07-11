<?php
/**
 * WPFactory theme - Reviews and statistics Module.
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  WPFactory
 */

namespace WPFactory\WPFactory_Theme\Component;

use Timber\Timber;
use WPFactory\WPFactory_Theme\Theme_Component;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'WPFactory\WPFactory_Theme\Component\Product_Reviews_Module' ) ) {

	//class Menus {
	class Product_Reviews_Module implements Theme_Component {
		public function init() {
			add_filter( 'wpft_module_prod_reviews_template_vars', array( $this, 'add_extra_template_vars' ), 10, 2 );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 11 );
			add_action( 'wp_footer', array( $this, 'handle_reviews_loading' ) );
			// AJAX
			add_action( 'wp_ajax_load_all_reviews', array( $this, 'load_all_reviews_via_ajax' ) );
			add_action( 'wp_ajax_nopriv_load_all_reviews', array( $this, 'load_all_reviews_via_ajax' ) );
		}

		function load_all_reviews_via_ajax() {
			check_ajax_referer( 'load_all_reviews_ajax', 'nonce' );
			ob_clean();
			$product_id = intval( $_POST['product_id'] );
			$args       = array(
				'type'    => 'review',
				'orderby' => 'comment_date_gmt',
				'order'   => 'DESC',
				'post_id' => $product_id,   // Use post_id, not post_ID
				'number'  => ''
			);
			$reviews    = get_comments( $args );
			$timber     = new \Timber\Timber();
			$html       = \Timber::compile_string( $this->get_all_reviews_html(), array( 'reviews' => $reviews ) );
			wp_send_json_success( array(
				'html'        => $html,
				'items_count' => count( $reviews )
			), 200 );
		}

		function get_all_reviews_html() {
			ob_start();
			?>
            {% for review in reviews %}
            <div class="review-box">
                <div class="columns is-variable is-2 is-mobile">
                    <div class="column is-narrow">
                        <div class="avatar">{{ fn('get_avatar',review,48) }}</div>
                    </div>
                    <div class="column has-text-left">
                        <div class="review-author">
                            {{review.comment_author}}
                        </div>
                        <span class="review-rating">
                            {% set rating = fn('get_comment_meta',review.comment_ID,'rating',true) %}
                            {% for i in 1..rating %}
                            <span class="star"></span>
                            {% endfor %}
                        </span>
                        <span class="time-ago">
                            {{review.comment_date_gmt|time_ago}}
                        </span>
                    </div>
                </div>
                <div class="review-content">
                    {{review.comment_content|wpautop}}
                </div>
            </div>
            {% endfor %}
			<?php
			$html_content = ob_get_contents();
			ob_end_clean();

			return $html_content;
		}

		function handle_reviews_loading() {
			if ( ! is_product() ) {
				return;
			}
			$php_to_js = array(
				'ajaxURL' => admin_url( 'admin-ajax.php' ),
				'action'  => 'load_all_reviews',
				'nonce'   => wp_create_nonce( 'load_all_reviews_ajax' )
			);
			?>
            <script>
                jQuery(document).ready(function ($) {
                    let jsData = <?php echo json_encode( $php_to_js );?>;

                    jQuery('.see-all-reviews-btn').on('click', showAllReviews);
                    let reviewsLoaded = false;

                    function showAllReviews() {
                        if (!reviewsLoaded) {
                            let targetSelector = jQuery(this).data('target');
                            let bkgLoader = jQuery(targetSelector).find('.bkg-loader');
                            bkgLoader.addClass('is-loading');
                            reviewsLoaded = true;
                            let ajaxData = {
                                action: jsData.action,
                                nonce: jsData.nonce,
                                product_id: jQuery(this).data('product-id')
                            };
                            jQuery.post(jsData.ajaxURL, ajaxData, function (response) {
                                jQuery('.modal-reviews .reviews-container').html(response.data.html)
                                let magicGrid = new MagicGrid({
                                    container: ".reviews-container", // Required. Can be a class, id, or an HTMLElement.
                                    static: true,
                                    animate: true,

                                });
                                magicGrid.listen();
                                magicGrid.positionItems();
                                bkgLoader.removeClass('is-loading');
                            });
                        }
                    }
                });
            </script>
			<?php
		}

		function enqueue_scripts() {
			if ( ! is_product() ) {
				return;
			}

			// Magic grid
			wp_enqueue_script( 'wpft-magic-grid-js',
				'https://unpkg.com/magic-grid/dist/magic-grid.min.js',
				array(),
				false,
				true
			);
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