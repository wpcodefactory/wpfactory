<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package storefront
 */

?>

<!--</div>--><!-- .col-full -->
<?php do_action( 'wpft_col_full_close' ); ?>
</div><!-- #content -->

<?php do_action( 'storefront_before_footer' ); ?>

<footer id="colophon" class="site-footer" role="contentinfo">
	<!--<div class="col-full">-->

		<?php
		/**
		 * Functions hooked in to storefront_footer action
		 *
		 * @hooked storefront_footer_widgets - 10
		 * @hooked storefront_credit         - 20
		 */
		do_action( 'storefront_footer' );
		?>

	<!--</div>--><!-- .col-full -->
</footer><!-- #colophon -->

<?php do_action( 'storefront_after_footer' ); ?>

</div><!-- #page -->

<?php wp_footer(); ?>

<div class="main-bkg">
    <svg width="1183" height="711" viewBox="0 0 1183 711" fill="none" xmlns="http://www.w3.org/2000/svg">
        <circle cx="810" cy="392" r="89" stroke="#1A2DC9" stroke-opacity="0.06" stroke-width="2"/>
        <circle cx="810" cy="392" r="149" stroke="#1A2DC9" stroke-opacity="0.06" stroke-width="2"/>
        <circle cx="810" cy="392" r="209" stroke="#1A2DC9" stroke-opacity="0.06" stroke-width="2"/>
        <circle cx="810" cy="392" r="269" stroke="#1A2DC9" stroke-opacity="0.06" stroke-width="2"/>
        <circle cx="810" cy="392" r="329" stroke="#1A2DC9" stroke-opacity="0.06" stroke-width="2"/>
        <circle cx="810" cy="392" r="389" stroke="#1A2DC9" stroke-opacity="0.06" stroke-width="2"/>
        <circle opacity="0.6" cx="810" cy="392" r="449" stroke="#1A2DC9" stroke-opacity="0.06" stroke-width="2"/>
        <circle opacity="0.48" cx="810" cy="392" r="509" stroke="#1A2DC9" stroke-opacity="0.06" stroke-width="2"/>
        <circle opacity="0.36" cx="810" cy="392" r="569" stroke="#1A2DC9" stroke-opacity="0.06" stroke-width="2"/>
        <circle opacity="0.24" cx="810" cy="392" r="629" stroke="#1A2DC9" stroke-opacity="0.06" stroke-width="2"/>
        <circle opacity="0.12" cx="810" cy="392" r="689" stroke="#1A2DC9" stroke-opacity="0.06" stroke-width="2"/>
        <circle opacity="0.12" cx="810" cy="392" r="749" stroke="#1A2DC9" stroke-opacity="0.06" stroke-width="2"/>
        <circle opacity="0.12" cx="810" cy="392" r="809" stroke="#1A2DC9" stroke-opacity="0.06" stroke-width="2"/>
    </svg>
</div>
<div class="blog-bkg"></div>


</body>
</html>
