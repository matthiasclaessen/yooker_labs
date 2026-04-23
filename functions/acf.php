<?php

/**
 * ACF Options Pages
 */

$site_name = get_bloginfo('name') . ' Settings';

if (function_exists('acf_add_options_page')) {
    $option_page = acf_add_options_page(array(
        'page_title' => 'Theme Settings',
        'menu_title' => $site_name,
        'menu_slug' => 'theme-settings',
        'capability' => 'edit_posts',
        'redirect' => false,
        'icon_url' => 'dashicons-layout',
    ));
}

/**
 * Set ACF WYSIWYG Media Fields To 0
 */

function acf_set_media_upload_wysiwyg_false($field)
{
    if ($field['type'] === 'wysiwyg') {
        $field['media_upload'] = 0;
    }

    return $field;
}

add_filter('acf/get_valid_field', 'acf_set_media_upload_wysiwyg_false');
