<?php

// Composer.
require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

// Autoloader.
$autoloader = new \WPFactory\WPFactory_Autoloader\WPFactory_Autoloader();
$autoloader->add_namespace( '\WPFactory\WPFactory_Theme', plugin_dir_path( __FILE__ ) . '/src/php' );
$autoloader->init();

// Initializes the theme.
$theme = wpf_get_theme();
$theme->init();