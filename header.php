<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<?php wp_body_open(); ?>

<?php do_action( 'wpfactory_before_site' ); ?>

<div id="page" class="hfeed site">
	<?php do_action( 'wpfactory_before_header' ); ?>

    <header id="masthead" class="site-header" role="banner">
        <div class="wpf-container">
			<?php
			do_action( 'wpfactory_header' );
			?>
        </div>
    </header><!-- #masthead -->

	<?php
	do_action( 'wpfactory_before_content' );
	?>

    <div id="content" class="site-content" tabindex="-1">
        <!--<div class="wpf-container">-->

<?php
do_action( 'wpfactory_content_top' );
