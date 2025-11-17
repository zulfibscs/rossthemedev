<?php
/**
 * Dynamic CSS Output Module
 */
function ross_theme_dynamic_css() {
    $header_options = get_option('ross_theme_header_options', array());
    $general_options = get_option('ross_theme_general_options', array());
    
    echo '<style type="text/css" id="ross-theme-dynamic-css">';
    
    // Force apply header styles
    if (!empty($header_options['header_bg_color'])) {
        echo '.site-header { background-color: ' . esc_attr($header_options['header_bg_color']) . ' !important; }';
    }
    
    if (!empty($header_options['header_text_color'])) {
        echo '.site-header, .site-header a { color: ' . esc_attr($header_options['header_text_color']) . ' !important; }';
    }
    
    if (!empty($header_options['header_link_hover_color'])) {
        echo '.site-header a:hover { color: ' . esc_attr($header_options['header_link_hover_color']) . ' !important; }';
    }
    
    if (!empty($header_options['active_item_color'])) {
        echo '.primary-menu .current-menu-item a { color: ' . esc_attr($header_options['active_item_color']) . ' !important; }';
    }
    
    // Apply general colors
    if (!empty($general_options['primary_color'])) {
        echo ':root { --primary-color: ' . esc_attr($general_options['primary_color']) . '; }';
        echo '.primary-color { color: ' . esc_attr($general_options['primary_color']) . ' !important; }';
    }
    
    if (!empty($general_options['secondary_color'])) {
        echo ':root { --secondary-color: ' . esc_attr($general_options['secondary_color']) . '; }';
    }
    
    echo '</style>';
}
add_action('wp_head', 'ross_theme_dynamic_css', 999); // High priority