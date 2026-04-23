<?php

/**
 * BrightByte array of custom blocks
 * Just add the name of the block if you created a new one.
 */
$brightbyte_blocks = [
	'block-cta',
	'block-editor',
	'block-columns',
	'block-forms',
	'block-gallery',
	'block-hero',
	'block-image',
	'block-image-text',
	'block-quote',
	'block-slider',
	'block-video',
];

sort($brightbyte_blocks);

/**
 * Register blocks and scripts/styles.
 */
add_action('init', 'brightbyte_register_acf_blocks');

function brightbyte_register_acf_blocks(): void {
	// Register scripts to be used in blocks. Use the handle (defined here) in block.json if a block needs it.
	// Only register the script. Enqueuing will be handled by the block.

	
	// Use the $brightbyte_blocks array we created at the top of the file.
	global $brightbyte_blocks;
	
	// Set block folder path.
	$block_folder = __DIR__ . '/../templates/blocks/';
	
	// register_block_type() looks for block.json in the specified folder. Make sure it is present.
	if ( is_array($brightbyte_blocks) ) {
		foreach ( $brightbyte_blocks as $brightbyte_block_name ) {
			register_block_type($block_folder . $brightbyte_block_name);
		}
	}
}

/**
 * Add custom brightbyte category for our blocks
 */
add_filter('block_categories_all', 'brightbyte_block_categories_all');

function brightbyte_block_categories_all($block_categories) {
	// Add new category to the current array of categories
	$block_categories[] = [
		'slug'  => 'brightbyte-blocks',
		'title' => 'BrightByte Blocks',
	];
	
	return $block_categories;
}

/**
 * Only allow BrightByte blocks to be shown in the editor.
 * We don't want the default WordPress ones to be shown.
 */
add_filter('allowed_block_types_all', 'brightbyte_allowed_block_types', 10, 2);

function brightbyte_allowed_block_types(): array {
	// Use the $brightbyte_blocks array we created at the top of the file.
	global $brightbyte_blocks;
	
	// Create a new array, since we need to add 'acf/' to each name.
	$allowed_block_types = ['core/shortcode'];
	
	// Add 'acf/' to each block name
	foreach ( $brightbyte_blocks as $brightbyte_block_name ) {
		$allowed_block_types[] = 'acf/' . $brightbyte_block_name;
	}
	
	// Return the new array
	return $allowed_block_types;
}
