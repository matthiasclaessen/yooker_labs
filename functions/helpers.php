<?php

/**
 * Remove Comments From Backend Menu
 */

function remove_admin_menus(): void {
	remove_menu_page('edit-comments.php');
}

add_action('admin_init', 'remove_admin_menus');

function remove_comments_topbar(): void {
	global $wp_admin_bar;
	$wp_admin_bar->remove_menu('comments');
}

add_action('wp_before_admin_bar_render', 'remove_comments_topbar');

function remove_comment_support(): void {
	remove_post_type_support('post', 'comments');
	remove_post_type_support('page', 'comments');
}

add_action('init', 'remove_comment_support', 100);

/**
 * Disable The Emoji's
 */

function disable_emojis(): void {
	remove_action('wp_head', 'print_emoji_detection_script', 7);
	remove_action('admin_print_scripts', 'print_emoji_detection_script');
	remove_action('wp_print_styles', 'print_emoji_styles');
	remove_action('admin_print_styles', 'print_emoji_styles');
	remove_action('the_content_feed', 'wp_staticize_emoji');
	remove_action('comment_text_rss', 'wp_staticize_emoji');
	remove_action('wp_mail', 'wp_staticize_emoji_for_email');
}

add_action('init', 'disable_emojis');

/**
 * Delete This If You Don't Want To Use The Embed Options In WordPress
 */

function my_deregister_script(): void {
	wp_deregister_script('wp-embed');
}

add_action('wp_footer', 'my_deregister_script');

/**
 * Get User's IP Address
 */

function get_user_ip() {
	if ( ! empty($_SERVER['HTTP_CLIENT_IP']) ) {
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif ( ! empty($_SERVER['HTTP_X_FORWARDED_FOR']) ) {
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	
	return apply_filters('wpb_get_ip', $ip);
}

add_shortcode('show_ip', 'get_user_ip');

/**
 * Inline an SVG from the build/images folder (production)
 * or directly from src/images during Vite dev server.
 *
 * Usage: <?= brightbyte_sprite('logo'); ?>
 */
function brightbyte_sprite(string $icon_name): string {
	$theme_dir = get_stylesheet_directory();
	$is_dev    = file_exists($theme_dir . '/hot');
	
	// In dev mode SVGs haven't been copied to /build yet — read from src directly
	$svg_path = $is_dev ? $theme_dir . '/src/images/' . $icon_name . '.svg' : $theme_dir . '/build/images/' . $icon_name . '.svg';
	
	if ( ! file_exists($svg_path) ) {
		return '';
	}
	
	return file_get_contents($svg_path);
}
