<?php
/**
 * Customizer Asset Enqueuer - Top Bar Settings
 * Handles enqueuing customizer scripts and styles
 */

if (!defined('ABSPATH')) exit;

/**
 * Enqueue customizer assets
 */
function ross_enqueue_customizer_assets($hook) {
    // Enqueue customizer CSS
    $customizer_css = get_template_directory() . '/assets/css/admin/customizer-topbar.css';
    if (file_exists($customizer_css)) {
        wp_enqueue_style(
            'ross-customizer-topbar',
            get_template_directory_uri() . '/assets/css/admin/customizer-topbar.css',
            array(),
            filemtime($customizer_css)
        );
    }

    // Enqueue customizer preview JS (only on customizer page)
    if (false && is_customize_preview()) {
        $preview_js = get_template_directory() . '/assets/js/admin/customizer-topbar-preview.js';
        if (file_exists($preview_js)) {
            wp_enqueue_script(
                'ross-customizer-topbar-preview',
                get_template_directory_uri() . '/assets/js/admin/customizer-topbar-preview.js',
                array('jquery', 'customize-preview'),
                filemtime($preview_js),
                true
            );
        }
    }

    // Enqueue FontAwesome for icon display (if not already included)
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');
}
add_action('customize_enqueue_scripts', 'ross_enqueue_customizer_assets');
