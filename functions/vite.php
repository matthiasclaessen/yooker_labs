<?php

if ( file_exists(get_template_directory() . '/hot') ) {
	add_action('wp_enqueue_scripts', 'vite_serve_assets');
} else {
	add_action('wp_enqueue_scripts', 'vite_build_assets');
}

function vite_serve_assets(): void {
	$vite_server  = file_exists(get_template_directory() . '/hot') ? file_get_contents(get_template_directory() . '/hot') : '';
	$entry_points = [ 'src/scss/main.scss', 'src/js/main.js' ];
	
	// Add Vite client to <head> tag
	add_action('wp_head', function () use ($vite_server) {
		echo '<script type="module" src="' . $vite_server . '/@vite/client' . '"></script>';
	});
	
	foreach ( $entry_points as $entry_point ) {
		if ( str_ends_with($entry_point, '.scss') ) {
			add_action('wp_head', function () use ($vite_server, $entry_point) {
				echo '<link rel="stylesheet" type="text/css" href="' . $vite_server . '/' . $entry_point . '" />';
			});
		}
		
		if ( str_ends_with($entry_point, '.js') ) {
			add_action('wp_head', function () use ($vite_server, $entry_point) {
				echo '<script type="module" crossorigin src="' . $vite_server . '/' . $entry_point . '"></script>';
			});
		}
	}
}

function vite_build_assets(): void {
	$manifest = file_exists(get_template_directory() . '/build/manifest.json') ? json_decode(file_get_contents(get_template_directory() . '/build/manifest.json'), true) : '';
	
	if ( is_array($manifest) ) {
		foreach ( $manifest as $entry ) {
			$file = $entry['file'];
			$path = get_template_directory_uri() . '/build/' . $file;
			
			if ( str_ends_with($file, '.css') ) {
				wp_enqueue_style('main', $path, [], false, false);
			}
			
			if ( str_ends_with($file, '.js') ) {
				wp_enqueue_script('main', $path, [], false, true);
			}
		}
	}
}
