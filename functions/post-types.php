<?php

/**
 * Remove Default WP Posttype
 */

function remove_default_post_type(): void
{
    remove_menu_page('edit.php');
    remove_submenu_page('edit.php', 'edit-tags.php?taxonomy=category');
}

add_action('admin_menu', 'remove_default_post_type');

function remove_post_tag(): void
{
    register_taxonomy('post_tag', array());
}

add_action('init', 'remove_post_tag');

/**
 * Register CPT Products
 */

function brightbyte_add_cpt_products(): void
{
    $labels = array(
        'name' => 'Products',
        'singular_name' => 'Product',
        'menu_name' => 'Products',
        'name_admin_bar' => 'Products',
        'archives' => 'Product Archives',
        'attributes' => 'Product Attributes',
        'parent_item_colon' => 'Parent Product',
        'all_items' => 'All Products',
        'add_new_item' => 'Add New Product',
        'add_new' => 'Add New',
        'new_item' => 'New Product',
        'edit_item' => 'Edit Product',
        'update_item' => 'Update Product',
        'view_item' => 'View Product',
        'view_items' => 'View Products',
        'search_items' => 'Search Products',
        'not_found' => 'Not Found',
        'not_found_in_trash' => 'Not Found In Trash',
        'featured_image' => 'Featured Image',
        'set_featured_image' => 'Set Featured Image',
        'remove_featured_image' => 'Remove Featured Image',
        'use_featured_image' => 'Use As Featured Image',
        'insert_into_item' => 'Insert Into Product',
        'uploaded_to_this_item' => 'Uploaded To This Product',
        'items_list' => 'Products List',
        'items_list_navigation' => 'Products List Navigation',
        'filter_item_list' => 'Filter Products List',
    );

    $args = array(
        'label' => 'Product',
        'labels' => $labels,
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'revisions'),
        'show_in_rest' => true,
        'hierarchical' => false,
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_position' => 50,
        'menu_icon' => 'dashicons-image-filter',
        'show_in_admin_bar' => true,
        'show_in_nav_menus' => true,
        'can_export' => true,
        'has_archive' => false,
        'exclude_from_search' => false,
        'publicly_queryable' => true,
        'capability_type' => 'page',
        'rewrite' => array('with_front' => false, 'slug' => 'products'),
    );

    register_post_type('cpt-products', $args);
}

add_action('init', 'brightbyte_add_cpt_products', 0);


/**
 * Register Taxonomy Kind
 */

function brightbyte_add_tax_kind(): void
{
    register_taxonomy(
        'tax-products-kind',
        'cpt-products',
        array(
            'label' => 'Kind',
            'hierarchical' => true,
            'show_in_rest' => true,
            'rewrite' => array(
                'slug' => 'kind',
                'with_front' => false
            ),
        )
    );
}

add_action('init', 'brightbyte_add_tax_kind');