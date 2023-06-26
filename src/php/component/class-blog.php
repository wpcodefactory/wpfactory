<?php
/**
 * WPFactory theme - Bog.
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  WPFactory
 */

namespace WPFactory\WPFactory_Theme\Component;

use Carbon_Fields\Container;
use Carbon_Fields\Field;
use WPFactory\WPFactory_Theme\Carbon_Fields\Carbon_Fields_Post_Meta_Datastore;
use WPFactory\WPFactory_Theme\Carbon_Fields\Carbon_Fields_User_Meta_Datastore;
use WPFactory\WPFactory_Theme\Theme_Component;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'WPFactory\WPFactory_Theme\Component\Blog' ) ) {

	class Blog implements Theme_Component {

		protected $category_terms_from_author = array();

		function init() {
			add_action( 'after_setup_theme', array( $this, 'setup_storefront' ) );
		}

		function setup_storefront() {
			// ----- Single post.
			// Remove actions.
			remove_all_actions( 'storefront_single_post' );
			remove_all_actions( 'storefront_single_post_bottom' );
			remove_action( 'storefront_post_content_before', 'storefront_post_thumbnail', 10 );
			// Add containers.
			add_action( 'storefront_single_post_top', array( $this, 'add_single_post_featured_image' ), 1 );
			add_action( 'storefront_single_post', array( $this, 'add_post_container' ), 5 );
			add_action( 'storefront_single_post_bottom', array( $this, 'close_post_container' ), 50 );
			// Add Post info.
			add_action( 'storefront_single_post', array( $this, 'add_post_info' ), 10 );
			// Add Post content.
			add_action( 'storefront_single_post', array( $this, 'add_post_content' ), 11 );

			//----- Blog.
			remove_all_actions( 'storefront_loop_post' );
			add_action( 'storefront_loop_post', array( $this, 'loop_post' ) );
			// User role field.
			add_action( 'carbon_fields_register_fields', array( $this, 'create_user_fields' ) );
			// Post meta fields.
			add_action( 'carbon_fields_register_fields', array( $this, 'create_post_fields' ) );
			// Blog header.
			//add_action( 'storefront_loop_before', array( $this, 'add_blog_header' ) );

			// Author
			add_action( 'storefront_loop_before', array( $this, 'add_author_header' ), 11 );
		}

		function add_author_header() {
			?>
			<?php if ( is_author() ) : ?>
                <div class="wpft-author-header">
                    <div class="columns">
                        <div class="column is-3">
							<?php echo $this->get_author( array(
								'is_v_centered' => true,
								'avatar_size'   => 72
							) ); ?>
                        </div>
                        <div class="column is-6">
                            <div class="wpft-blog-author-bio">
								<?php echo wpautop( get_the_author_meta( 'description' ) ) ?>
                            </div>
							<?php if ( ! empty( $terms = $this->get_category_terms_from_author() ) ) { ?>
								<?php foreach ( $terms as $term ) { ?>
									<?php
									$term_link = get_term_link( (int) $term['term_id'], 'category' );
									if ( ! is_wp_error( $term_link ) ) {
										$output = sprintf(
											'<a href="%s">%s</a>',
											$term_link,
											esc_html( $term['name'] ) . '(' . esc_html( $term['posts_count'] ) . ')'
										);
										echo '<span class="wpft-author-cat-term">' . $output . '</span>';
									}
									?>
								<?php } ?>
							<?php } ?>
                        </div>
                        <div class="column is-3">
                            <div class="columns is-mobile">
                                <div class="column is-narrow-mobile">
                                    <div class="wpft-author-info">
                                        Posts
                                        <div class="value"><?php echo count_user_posts( get_the_author_meta( 'ID' ) ) ?></div>
                                    </div>
                                </div>
								<?php if ( ! empty( $terms = $this->get_category_terms_from_author() ) ) { ?>
                                    <div class="column is-narrow-mobile">
                                        <div class="wpft-author-info">
                                            Topics
                                            <div class="value">
												<?php echo count( $terms ); ?>
                                            </div>
                                        </div>
                                    </div>
								<?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
			<?php endif; ?>
			<?php
		}

		function add_blog_header() {
			?>
            <div class="wpft-blog-header">
                <h2 class="entry-title"><?php wp_title( '' ) ?></h2>
            </div>
			<?php
		}

		function excerpt_more_link( $excerpt ) {
			$excerpt .= sprintf(
				' <a class="wpft-read-more" title="%s" href="%s">%s</a>',
				get_the_title(),
				esc_url( get_permalink() ),
				__( 'Read more' )
			);

			return $excerpt;
		}


		function create_post_fields() {
			Container::make( 'post_meta', 'Thumbnail' )
			         ->set_datastore( new Carbon_Fields_Post_Meta_Datastore() )
			         ->where( 'post_type', '=', 'post' )
			         ->set_context( 'side' )
			         ->set_priority( 'low' )
			         ->add_fields( array(
				         Field::make( 'image', 'wpft_featured_image', '' )->set_help_text( 'Image that will be displayed on the blog page' ),
			         ) );
		}

		function create_user_fields() {
			Container::make( 'user_meta', 'Blog data' )
			         ->set_datastore( new Carbon_Fields_User_Meta_Datastore() )
			         ->add_fields( array(
				         Field::make( 'text', 'wpft_blog_role', 'Blog role' ),
			         ) );
		}

		function custom_excerpt_length( $length ) {
			return 30;
		}

		function loop_post() {
			add_filter( 'the_excerpt', array( $this, 'excerpt_more_link' ), 21 );
			add_filter( 'excerpt_length', array( $this, 'custom_excerpt_length' ), 999 );
			//echo '<h2 class="entry-title">Blog</h2>';

			global $wp_query;
			if ( 0 === $wp_query->found_posts ) {
				return;
			}
			if ( 0 === $wp_query->current_post && ! is_author() ) {
				$this->first_blog_post();
			} else {
				$this->after_first_blog_post();
			}
		}

		function after_first_blog_post() {
			?>
            <div class="wpft-common-blog-post">
                <div class="columns is-multiline is-variable is-4">
                    <div class="column is-2-fullhd is-full-mobile">
                        <div class="columns is-mobile is-multiline">
                            <div class="column wpft-term-and-date is-6-mobile is-full-desktop">
								<?php echo $this->get_post_categories() ?>
								<?php echo $this->get_date(); ?>
                            </div>

							<?php if ( ! is_author() ): ?>
                                <div class="column is-6-mobile is-full-desktop">
									<?php echo $this->get_author(); ?>
                                </div>
							<?php endif ?>

                        </div>
                    </div>
                    <div class="column is-5 is-full-mobile">
                        <h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title() ?></a></h2>
						<?php the_excerpt(); ?>
                    </div>
                    <div class="column is-4 is-full-mobile">
						<?php
						$thumb_img_id = get_post_meta( get_the_ID(), '_wpft_featured_image', true );
						echo sprintf( '<a href="%s" title="%s">%s</a>',
							get_permalink(),
							get_the_title(),
							empty( $thumb_img_id ) && has_post_thumbnail() ? get_the_post_thumbnail( get_the_ID(), 'size-5' ) : wp_get_attachment_image( $thumb_img_id, 'size-5' )
						);
						?>
                    </div>
                </div>
            </div>
			<?php
		}

		function first_blog_post() {
			?>
            <div class="wpft-first-post">
                <div class="columns is-multiline is-variable is-5">
                    <div class="column is-6">
						<?php echo $this->get_post_categories() ?>
                        <h2 class="entry-title mb-5"><a href="<?php the_permalink(); ?>"><?php the_title() ?></a></h2>
						<?php echo $this->get_author(); ?>
                    </div>
                    <div class="column is-6">
						<?php echo $this->get_date(); ?>
						<?php the_excerpt(); ?>
                    </div>
                    <div class="column is-full">
						<?php
						if ( has_post_thumbnail() ) {
							echo sprintf( '<a href="%s" title="%s">%s</a>',
								get_permalink(),
								get_the_title(),
								get_the_post_thumbnail( get_the_ID(), 'size-4' )
							);
						}
						?>
                    </div>
                </div>
            </div>
			<?php
		}

		function get_post_categories( $args = null ) {
			$args              = wp_parse_args( $args, array(
				'title_li'  => '',
				'include'   => wp_list_pluck( get_the_category(), 'term_id' ),
				'style'     => '',
				'separator' => ', ',
				'echo'      => false,
				'orderby'   => 'name',
			) );
			$separator         = $args['separator'];
			$args['separator'] = '<br />';
			$terms             = wp_list_categories( $args );
			$terms             = rtrim( trim( str_replace( '<br />', $separator, $terms ) ), $separator );
			$terms             = '<div class="wpft-post-categories">' . $terms . '</div>';

			return $terms;
		}

		function get_category_terms_from_author( $args = null ) {
			global $wpdb;
			$args      = wp_parse_args( $args, array(
				'author_id' => get_the_author_meta( 'ID' )
			) );
			$author_id = intval( $args['author_id'] );
			$sql       = $wpdb->prepare( "
			SELECT t.term_id,t.name, COUNT(p.ID) AS posts_count
            FROM {$wpdb->terms} AS t
            JOIN {$wpdb->term_taxonomy} AS tt ON t.term_id = tt.term_id AND tt.taxonomy = 'category'
            JOIN {$wpdb->term_relationships} AS tr ON tr.term_taxonomy_id = tt.term_taxonomy_id
            JOIN {$wpdb->posts} AS p ON tr.object_id = p.ID AND p.post_author = %d
            GROUP BY t.term_id
			", $author_id );

			if ( empty( $this->category_terms_from_author ) ) {
				$this->category_terms_from_author = $wpdb->get_results( $sql, ARRAY_A );
			}

			return $this->category_terms_from_author;
		}

		function get_author( $args = null ) {
			$args              = wp_parse_args( $args, array(
				'avatar_size'   => 42,
				'is_v_centered' => false
			) );
			$avatar_size       = $args['avatar_size'];
			$is_v_centered_str = $args['is_v_centered'] ? 'is-vcentered' : '';

			// Author
			$author_url = get_author_posts_url( get_the_author_meta( 'ID' ) );

			if ( is_author() ) {
				$author = sprintf(
					'<div class="url fn" rel="author">%s</div>',
					esc_html( get_the_author() )
				);
			} else {
				$author = sprintf(
					'<a href="%s" class="url fn" rel="author">%s</a>',
					esc_url( $author_url ),
					esc_html( get_the_author() )
				);
			}

			return sprintf( '<div class="wpft-post-author-container columns is-variable is-1 is-mobile ' . $is_v_centered_str . '">%s%s</div>',
				'<div class="avatar-wrapper column is-narrow">' . get_avatar( get_the_author_meta( 'ID' ), $avatar_size ) . '</div>',
				'<div class="column"><div class="author-title is-narrow">' . $author . '</div><div class="author-blog-role">' . get_the_author_meta( '_wpft_blog_role' ) . '</div></div>',
				);
		}

		function get_date() {
			$post_date = get_the_date( 'F j, Y' );
			echo '<div class="wpft-post-date">' . $post_date . '</div>';
		}

		function add_post_info( $is_mobile = false ) {
			$columns_div_start = $is_mobile ? '<div class="columns is-mobile is-variable is-1">' : '';
			$columns_div_end   = $is_mobile ? '</div>' : '';
			?>
            <div class="column is-4 post-info">
				<?php echo $columns_div_start ?>
				<?php storefront_edit_post_link(); ?>

                <div class="wpft-possible-column column is-mobile">
					<?php echo $this->get_post_categories(); ?>
					<?php echo $this->get_date(); ?>
                </div>

                <div class="wpft-possible-column column is-mobile">
					<?php echo $this->get_author(); ?>
                </div>
				<?php echo $columns_div_end; ?>
            </div>
			<?php
		}

		function add_post_content() {
			?>
            <div class="column is-8 post-content">
				<?php
				if ( is_single() ) {
					the_title( '<h1 class="entry-title">', '</h1>' );
				} else {
					the_title( sprintf( '<h2 class="alpha entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' );
				}
				?>
                <div class="wptf-post-is-mobile">
                    <div class="columns">
						<?php $this->add_post_info( true ); ?>
                    </div>
                </div>
				<?php storefront_post_content(); ?>
				<?php storefront_display_comments() ?>
            </div>
			<?php

		}

		function add_post_container() {
			?>
            <div class="col-full">
            <div class="wpft-article-container">
            <div class="columns is-variable is-6">
			<?php
		}

		function close_post_container() {
			?>
            </div>
            </div>
            </div>
			<?php
		}

		function add_single_post_featured_image() {
			?>
            <div class="wpft-post-thumb-container">
				<?php
				if ( has_post_thumbnail() ) {
					the_post_thumbnail( 'size-4' );
				}
				?>
            </div>
			<?php
		}
	}
}