<?php

// Composer.
require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

// Autoloader.
$autoloader = new \WPFactory\WPFactory_Autoloader\WPFactory_Autoloader();
$autoloader->add_namespace( '\WPFactory\WPFactory_Theme', plugin_dir_path( __FILE__ ) . '/src/php' );
$autoloader->init();

// Initializes the theme.
$theme = wpft_get_theme();
$theme->init();

/**
 * Cart Link
 * Displayed a link to the cart including the number of items present and the cart total
 *
 * @return void
 * @since  1.0.0
 */
function storefront_cart_link() {
	wpft_get_theme()->get_component('Cart')->storefront_cart_link();
}