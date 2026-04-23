<?php

/**
 * Removing Dashboard Widgets
 */

function brightbyte_remove_dashboard_widgets(): void
{
    // Remove 'Welcome' Panel
    remove_action('welcome_panel', 'wp_welcome_panel');

    // Remove 'At A Glance' Metabox
    remove_meta_box('dashboard_right_now', 'dashboard', 'normal');

    // Remove 'Activity' Metabox
    remove_meta_box('dashboard_activity', 'dashboard', 'normal');

    // Remove 'WordPress News' Metabox
    remove_meta_box('dashboard_primary', 'dashboard', 'side');

    // Remove 'Quick Draft' Metabox
    remove_meta_box('dashboard_quick_press', 'dashboard', 'side');

    // Remove 'Pagebuilder' Metabox
    remove_meta_box('so-dashboard-news', 'dashboard', 'side');

    // Remove 'Yoast' Metabox
    remove_meta_box('wpseo-dashboard-overview', 'dashboard', 'side');

    // Remove 'Gravity Forms' Metabox
    remove_meta_box('rg_forms_dashboard', 'dashboard', 'side');

    // Remove 'Wordfence' Metabox
    remove_meta_box('wordfence_activity_report_widget', 'dashboard', 'side');

    // Removoe Site Health Metabox
    remove_meta_box('dashboard_site_health', 'dashboard', 'normal');
}

add_action('admin_init', 'brightbyte_remove_dashboard_widgets');

/**
 * Add Custom Dashboard Widget 'Website Info'
 */

function dashboard_widget_website_info(): void
{
    global $wp_meta_boxes;

    wp_add_dashboard_widget('dashboard_widget_website_info', 'Website Information', 'brightbyte_dashboard_widget_website_info');
}

function brightbyte_dashboard_widget_website_info(): void
{
?>
    <ul>
        <li>
            <strong>Name</strong>: <?php bloginfo('name') ?>
        </li>
        <li>
            <strong>URL</strong>: <?php bloginfo('url') ?>
        </li>
        <li>
            <strong>Posts</strong>:
            <?= wp_count_posts()->publish; ?>
        </li>
        <li>
            <strong>Pages</strong>:
            <?= wp_count_posts('page')->publish; ?>
        </li>

        <?php
        $post_types = get_post_types(array(
            'public' => true,
            '_builtin' => false,
        ), 'objects');

        foreach ($post_types as $post_type) {
            echo '<li>';
            echo '<strong>' . $post_type->labels->menu_name . ': </strong>';
            echo wp_count_posts($post_type->name)->publish;
            echo '</li>';
        }
        ?>
    </ul>

<?php
}

add_action('wp_dashboard_setup', 'dashboard_widget_website_info');
