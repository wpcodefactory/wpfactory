<?php
get_header(); ?>


    <div id="primary" class="content-area">
        <main id="main" class="site-main" role="main">
            <div class="wpf-container">
	            <h1 class="entry-title"><?php single_post_title(); ?></h1>
				<?php
				if ( have_posts() ) :

					get_template_part( 'loop' );

				else :

					get_template_part( 'content', 'none' );

				endif;
				?>
            </div>
        </main><!-- #main -->
    </div><!-- #primary -->

<?php
do_action( 'wpfactory_sidebar' );
get_footer();