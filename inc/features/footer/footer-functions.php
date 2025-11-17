<?php
/**
 * Footer Functions Module
 * Handles footer display logic based on options
 */

function ross_theme_get_footer_layout() {
    $footer_options = get_option('ross_theme_footer_options');
    $layout = isset($footer_options['footer_style']) ? $footer_options['footer_style'] : 'default';
    
    return $layout;
}

function ross_theme_display_footer() {
    $layout = ross_theme_get_footer_layout();
    
    switch($layout) {
        case 'minimal':
            get_template_part('template-parts/footer/footer-minimal');
            break;
        case 'cta':
            get_template_part('template-parts/footer/footer-cta');
            break;
        default:
            get_template_part('template-parts/footer/footer-default');
            break;
    }
}

function ross_theme_should_show_footer_widgets() {
    $footer_options = get_option('ross_theme_footer_options');
    return isset($footer_options['enable_widgets']) ? $footer_options['enable_widgets'] : true;
}

function ross_theme_should_show_footer_cta() {
    $footer_options = get_option('ross_theme_footer_options');
    return isset($footer_options['enable_footer_cta']) ? $footer_options['enable_footer_cta'] : false;
}

function ross_theme_should_show_social_icons() {
    $footer_options = get_option('ross_theme_footer_options');
    return isset($footer_options['enable_social_icons']) ? $footer_options['enable_social_icons'] : true;
}

function ross_theme_get_copyright_text() {
    $footer_options = get_option('ross_theme_footer_options');
    return isset($footer_options['copyright_text']) ? $footer_options['copyright_text'] : '© ' . date('Y') . ' ' . get_bloginfo('name') . '. All rights reserved.';
}