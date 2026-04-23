<?php

if (is_admin()) {
    function brightbyte_disable_editor_fullscreen_by_default(): void
    {
        $script = "jQuery( window ).load(function() { const isFullscreenMode = wp.data.select( 'core/edit-post' ).isFeatureActive( 'fullscreenMode' ); if ( isFullscreenMode ) { wp.data.dispatch( 'core/edit-post' ).toggleFeature( 'fullscreenMode' ); } });";
        wp_add_inline_script('wp-blocks', $script);
    }

    add_action('enqueue_block_editor_assets', 'brightbyte_disable_editor_fullscreen_by_default');

    // Disable code editing in hte Gutenberg editor
    add_filter('block_editor_settings_all', static function (array $settings): array {
        $settings['codeEditingEnabled'] = false;
        return $settings;
    });
}
