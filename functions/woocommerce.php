<?php

/*
 * Setup global Woocommerce container
 */

remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
remove_action('woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);

add_action('woocommerce_before_main_content', 'brightbyte_wrapper_start', 10);
add_action('woocommerce_after_main_content', 'brightbyte_wrapper_end', 10);

function brightbyte_wrapper_start(): void {
  echo '<section class="c-woocommerce"><div class="container">';
}

function brightbyte_wrapper_end(): void {
  echo '</div></section>';
}

/*
 * Add theme support for WooCommerce
 */

add_action('after_setup_theme', 'woocommerce_support');

function woocommerce_support(): void {
  add_theme_support('woocommerce');
}

/*
 * Add Excerpt to product overview page
 */

function add_excerpt_to_overview(): void {
  $excerpt = get_the_excerpt();
  echo '<span class="short-description">' . wp_trim_words($excerpt, 10) . '</span>';
}

add_action('woocommerce_after_shop_loop_item_title', 'add_excerpt_to_overview', 40);

/*
 * Remove default WooCommerce cart
 */
add_filter('woocommerce_blocks_enable_cart', '__return_false');

/*
 * Remove default WooCommerce stylesheet
 */

add_filter('woocommerce_enqueue_styles', '__return_empty_array');

/*
 * Uncheck ship to different address
 */

add_filter('woocommerce_ship_to_different_address_checked', '__return_false');

/*
 * Enabling support for WooCommerce jQuery Slider
 */

add_theme_support('wc-product-gallery-zoom');
add_theme_support('wc-product-gallery-slider');

/*
 * Remove WooCommerce breadcrumbs
 */

function woo_remove_wc_breadcrumbs(): void {
  remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0);
}

add_action('init', 'woo_remove_wc_breadcrumbs');

add_filter('woocommerce_localize_vars', function (array $vars): array {
  $vars['filter_nonce'] = wp_create_nonce('brightbyte_filter_nonce');
  $vars['filter_url']   = admin_url('admin-ajax.php');

  return $vars;
});

/**
 * When developing behind an ngrok tunnel, add a header to skip the
 * "abuse warning" interstitial so programmatic requests (webhook tests,
 * Mollie callbacks, etc.) receive the actual response instead of HTML.
 *
 * Remove in production.
 */
add_filter('http_request_args', function (array $args, string $url): array {
  if ( str_contains($url, 'ngrok-free.dev') || str_contains($url, 'ngrok-free.app') ) {
    $args['headers']['ngrok-skip-browser-warning'] = 'true';
  }

  return $args;
}, 10, 2);
