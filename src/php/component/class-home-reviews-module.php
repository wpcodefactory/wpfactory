<?php
/**
 * WPFactory theme - Home reviews.
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

if ( ! class_exists( 'WPFactory\WPFactory_Theme\Component\Home_Reviews_Module' ) ) {


	//class Menus {
	class Home_Reviews_Module implements Theme_Component {

		public function init() {
			add_filter( 'wpft_module_home_reviews_template_vars', array( $this, 'add_extra_template_vars' ), 10, 2 );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 11 );
			add_action( 'wp_footer', array( $this, 'handle_magic_grid_on_initial_reviews' ) );
			add_action( 'wp_footer', array( $this, 'handle_modal_reviews_loading' ) );
			// AJAX.
			add_action( 'wp_ajax_load_reviews', array( $this, 'load_reviews_via_ajax' ) );
			add_action( 'wp_ajax_nopriv_load_reviews', array( $this, 'load_reviews_via_ajax' ) );
		}

		function handle_magic_grid_on_initial_reviews() {
			if (
				! is_page_template( 'template-homepage.php' ) &&
				! is_front_page()
			) {
				return;
			}
			?>
            <script>
                jQuery(document).ready(function ($) {
                    let magicGrid = new MagicGrid({
                        container: ".initial-reviews.reviews-container",
                        static: true,
                        animate: true, // Optional.

                    });
                    magicGrid.listen();
                    magicGrid.positionItems();
                });
            </script>
			<?php
		}

		function load_reviews_via_ajax() {
			check_ajax_referer( 'load_reviews_ajax', 'nonce' );
			ob_clean();
			$number  = intval( $_POST['number'] );
			$paged   = intval( $_POST['paged'] );
			$offset  = ( $paged - 1 ) * $number;
			$args    = array(
				'type'          => 'review',
				'orderby'       => 'comment_date_gmt',
				'order'         => 'DESC',
				'no_found_rows' => false,
				'number'        => $number,
				'offset'        => $offset,
				'meta_query'    => array(
					array(
						'key'     => 'rating',
						'value'   => '5',
						'compare' => '>='
					),
				),
			);
			$reviews = new \WP_Comment_Query( $args );
			$timber  = new \Timber\Timber();
			$html    = \Timber::compile_string( $this->get_all_reviews_html(), array( 'reviews' => $reviews->comments ) );
			wp_send_json_success( array(
				'html'          => $html,
				'max_num_pages' => $reviews->max_num_pages,
				'items_count'   => $reviews->found_comments
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
                <div class="review-reference">
                    {% set product = fn('wc_get_product',review.comment_post_ID) %}
                    <h4 class="product-title"><a href="{{ product.get_permalink() }}">{{ product.get_title() }}</a></h4>
                    {{ __('review', 'wpfactory') }}
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

		function handle_modal_reviews_loading() {
			if (
				! is_page_template( 'template-homepage.php' ) &&
				! is_front_page()
			) {
				return;
			}
			$php_to_js = array(
				'ajaxURL' => admin_url( 'admin-ajax.php' ),
				'action'  => 'load_reviews',
				'nonce'   => wp_create_nonce( 'load_reviews_ajax' )
			);
			?>
            <script>
                jQuery(document).ready(function ($) {
                    let jsData = <?php echo json_encode( $php_to_js );?>;
                    let needToLoadInitialReviews = true;
                    let magicGrid = null;
                    let paged = 1;
                    let needToLoadMoreReviews = true;
                    let itemsAmount = jQuery('.load-more-reviews-btn').data('more-reviews-amount');
                    let ajaxData = {
                        action: jsData.action,
                        nonce: jsData.nonce,
                        paged: paged,
                        number: itemsAmount
                    };
                    jQuery('.see-all-reviews-btn').on('click', getInitialModalReviews);

                    function handleMoreReviewsBtn(response) {
                        if (response.data.items_count < itemsAmount || response.data.max_num_pages == paged) {
                            needToLoadMoreReviews = false;
                            jQuery('.load-more-reviews-btn').attr('disabled', 'disabled');
                        } else {
                            needToLoadMoreReviews = true;
                        }
                    }

                    function getInitialModalReviews() {
                        if (needToLoadInitialReviews) {
                            needToLoadInitialReviews = false;
                            jQuery.post(jsData.ajaxURL, ajaxData, function (response) {
                                jQuery(response.data.html).appendTo('.modal-wpf-reviews .reviews-container');
                                magicGrid = new MagicGrid({
                                    container: ".modal-wpf-reviews .reviews-container",
                                    static: false,
                                    items: paged,
                                    animate: true, // Optional.
                                });
                                magicGrid.listen();
                                magicGrid.positionItems();
                                handleMoreReviewsBtn(response);
                            });
                        }
                    }

                    jQuery('.load-more-reviews-btn').on('click', getMoreModalReviews);

                    function getMoreModalReviews() {
                        if (needToLoadMoreReviews) {
                            let clickedBtn = jQuery(this);
                            clickedBtn.addClass('is-loading');
                            needToLoadMoreReviews = false;
                            paged++;
                            ajaxData.paged = paged;
                            jQuery.post(jsData.ajaxURL, ajaxData, function (response) {
                                jQuery(response.data.html).appendTo('.modal-wpf-reviews .reviews-container');
                                magicGrid = new MagicGrid({
                                    container: ".modal-wpf-reviews .reviews-container",
                                    static: false,
                                    items: paged,
                                    animate: true, // Optional.
                                });
                                magicGrid.positionItems();
                                handleMoreReviewsBtn(response);
                                clickedBtn.removeClass('is-loading');
                            });
                        }
                    }
                });
            </script>
			<?php
		}

		function enqueue_scripts() {
			if (
				! is_page_template( 'template-homepage.php' ) &&
				! is_front_page()
			) {
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
			$vars                  = wp_parse_args( $vars, array(
				'initial_reviews_total' => 6
			) );
			$initial_reviews_total = (int) $vars['initial_reviews_total'];
			$args                  = array(
				'type'       => 'review',
				'orderby'    => 'comment_date_gmt',
				'order'      => 'DESC',
				'meta_query' => array(
					array(
						'key'     => 'rating',
						'value'   => '5',
						'compare' => '>='
					),
				),
				'number'     => $initial_reviews_total // Return only the count
			);
			$reviews               = get_comments( $args );
			$vars['reviews']       = $reviews;

			return $vars;
		}
	}
}