<?php

function brightbyte_styles_and_scripts(): void {
	// Remove default jQuery
	wp_deregister_script('jquery');
	
	// Remove Gutenberg blocks CSS & WooCommerce blocks CSS
	wp_dequeue_style('wp-block-library');
	wp_dequeue_style('wp-block-library-theme');
	wp_dequeue_style('wc-block-style');
	
	wp_register_script('main-php-vars', '', null, null, [ 'in_footer' => true, 'strategy' => 'defer' ]);
	wp_enqueue_script('main-php-vars');
	
	// Add PHP vars to main.js
	$vars = [
		'ajaxurl'     => admin_url('admin-ajax.php'),
		'templateurl' => get_stylesheet_directory_uri(),
		'siteurl'     => get_site_url(),
	];
	
	wp_localize_script('main-php-vars', 'vars', $vars);
	
	// Register jQuery to load from WordPress's included library and set it to load in the footer
	wp_register_script('jquery', includes_url('/js/jquery/jquery.min.js'), [], get_bloginfo('version'), [ 'in_footer' => true ]);
	
	// Enqueue the re-registered jQuery script
	wp_enqueue_script('jquery');
	
	$woocommerce_vars = [
		'ajax_url'        => admin_url('admin-ajax.php'),
		'nonce'           => wp_create_nonce('brightbyte_woo_nonce'),
		'cart_url'        => wc_get_cart_url(),
		'checkout_url'    => wc_get_checkout_url(),
		'currency_symbol' => get_woocommerce_currency_symbol(),
		'is_checkout'     => is_checkout() ? 'yes' : 'no',
		'is_cart'         => is_cart() ? 'yes' : 'no',
	];
	
	// Pass WooCommerce-specific PHP vars to JS
	wp_localize_script('main-php-vars', 'woocommerce_vars', $woocommerce_vars);
}

add_action('wp_enqueue_scripts', 'brightbyte_styles_and_scripts');
