<?php
/**
 * Header Functions Module
 * Handles header display logic based on options
 */

if (!defined('ABSPATH')) exit;

/**
 * Get header options with fallbacks
 */
function ross_theme_get_header_options() {
    $options = get_option('ross_theme_header_options', array());
    
    // Set defaults if options don't exist
    $defaults = array(
        'header_style' => 'default',
        'header_width' => 'contained',
            'header_center' => 0,
            'header_padding_top' => '20',
            'header_padding_right' => '0',
            'header_padding_bottom' => '20',
            'header_padding_left' => '0',
            'header_margin_top' => '0',
            'header_margin_right' => '0',
            'header_margin_bottom' => '0',
            'header_margin_left' => '0',
        'sticky_header' => 0,
        'header_height' => '80',
        'logo_upload' => '',
        'logo_width' => '200',
        'show_site_title' => 1,
        'enable_topbar' => 0,
        'topbar_left_content' => '',
        'topbar_bg_color' => '#001946',
        'topbar_text_color' => '#ffffff',
        'topbar_icon_color' => '#ffffff',
        // new topbar defaults
        'enable_social' => 0,
        'social_facebook' => '',
        'social_twitter' => '',
        'social_linkedin' => '',
        'phone_number' => '',
        'enable_announcement' => 0,
            'announcement_text' => '',
            'enable_topbar_left' => 1,
            'announcement_animation' => 'marquee',
            'social_links' => array(),
        'color_palette' => 'professional',
        'menu_alignment' => 'left',
        'menu_font_size' => '16',
        'active_item_color' => '#E5C902',
        'enable_search' => 1,
        'enable_cta_button' => 1,
        'cta_button_text' => 'Get Free Consultation',
        'cta_button_color' => '#E5C902',
        'header_bg_color' => '#ffffff',
        'header_text_color' => '#333333',
        'header_link_hover_color' => '#E5C902',
        'transparent_homepage' => 0,
        // Style enhancements
        'topbar_shadow_enable' => 0,
        'topbar_gradient_enable' => 0,
        'topbar_gradient_color1' => '#001946',
        'topbar_gradient_color2' => '#003d7a',
        'topbar_border_color' => '#E5C902',
        'topbar_border_width' => 0,
        'topbar_custom_icon_links' => array(),
    );
    
    return wp_parse_args($options, $defaults);
}

/**
 * Get header layout from options
 */
function ross_theme_get_header_layout() {
    $options = ross_theme_get_header_options();
    return $options['header_style'];
}

/**
 * Display the appropriate header
 */
function ross_theme_display_header() {
    $layout = ross_theme_get_header_layout();
    $options = ross_theme_get_header_options();
    
    // Debug output
    if (current_user_can('manage_options')) {
        echo "<!-- Ross Theme Header: " . esc_html($layout) . " -->\n";
        echo "<!-- Logo URL: " . esc_html($options['logo_upload']) . " -->\n";
    }
    
    // Load header template
    get_template_part('template-parts/header/header', $layout);
}

/**
 * Get header CSS classes
 */
function ross_theme_header_classes() {
    $options = ross_theme_get_header_options();
    $classes = array('site-header');
    
    // Header style
    $classes[] = 'header-' . $options['header_style'];
    
    // Sticky header
    if ($options['sticky_header']) {
        $classes[] = 'header-sticky';
    }
    
    // Header width
    $classes[] = 'header-' . $options['header_width'];
    // Centered layout flag
    if (!empty($options['header_center'])) {
        $classes[] = 'header-center';
    }
    
    return implode(' ', $classes);
}

/**
 * Check if header feature is enabled
 */
function ross_theme_header_feature_enabled($feature) {
    $options = ross_theme_get_header_options();
    
    switch($feature) {
        case 'topbar':
            return !empty($options['enable_topbar']);
        case 'search':
            return !empty($options['enable_search']);
        case 'cta_button':
            return !empty($options['enable_cta_button']);
        case 'sticky':
            return !empty($options['sticky_header']);
        default:
            return false;
    }
}

/**
 * Build header inline style from options
 * Returns a safe CSS string for header height, padding and margin
 */
function ross_theme_get_header_inline_style() {
    $options = ross_theme_get_header_options();
    
    // Sanitize each value to ensure it's numeric
    $height = intval($options['header_height'] ?? 80);
    $pt = intval($options['header_padding_top'] ?? 20);
    $pr = intval($options['header_padding_right'] ?? 0);
    $pb = intval($options['header_padding_bottom'] ?? 20);
    $pl = intval($options['header_padding_left'] ?? 0);
    $mt = intval($options['header_margin_top'] ?? 0);
    $mr = intval($options['header_margin_right'] ?? 0);
    $mb = intval($options['header_margin_bottom'] ?? 0);
    $ml = intval($options['header_margin_left'] ?? 0);
    
    $bg_color = isset($options['header_bg_color']) ? sanitize_hex_color($options['header_bg_color']) : '#ffffff';
    $text_color = isset($options['header_text_color']) ? sanitize_hex_color($options['header_text_color']) : '#333333';
    
    return sprintf(
        'background: %s; color: %s; min-height: %dpx; padding: %dpx %dpx %dpx %dpx; margin: %dpx %dpx %dpx %dpx;',
        esc_attr($bg_color),
        esc_attr($text_color),
        $height,
        $pt, $pr, $pb, $pl,
        $mt, $mr, $mb, $ml
    );
}

/**
 * Render top bar if enabled
 */
function ross_theme_render_topbar() {
    $options = ross_theme_get_header_options();
    if (empty($options['enable_topbar'])) {
        return;
    }

    $bg = isset($options['topbar_bg_color']) ? esc_attr($options['topbar_bg_color']) : '#001946';
    $color = isset($options['topbar_text_color']) ? esc_attr($options['topbar_text_color']) : '#ffffff';

    // Left content (can contain simple HTML from admin)
    $left = !empty($options['topbar_left_content']) ? $options['topbar_left_content'] : '';
    $enable_left = isset($options['enable_topbar_left']) ? (bool) $options['enable_topbar_left'] : true;

    // Phone and social
    $phone = !empty($options['phone_number']) ? esc_html($options['phone_number']) : '';
    $enable_social = !empty($options['enable_social']);
    // Support repeater social_links (preferred) or legacy fb/tw/li fields
    $social_links = array();
    if (!empty($options['social_links']) && is_array($options['social_links'])) {
        foreach ($options['social_links'] as $s) {
            if (!empty($s['url'])) {
                $social_links[] = array('icon' => isset($s['icon']) ? $s['icon'] : '', 'url' => esc_url($s['url']));
            }
        }
    } else {
        $fb = !empty($options['social_facebook']) ? esc_url($options['social_facebook']) : '';
        $tw = !empty($options['social_twitter']) ? esc_url($options['social_twitter']) : '';
        $li = !empty($options['social_linkedin']) ? esc_url($options['social_linkedin']) : '';
        if ($fb) $social_links[] = array('icon' => '🔵', 'url' => $fb);
        if ($tw) $social_links[] = array('icon' => '🐦', 'url' => $tw);
        if ($li) $social_links[] = array('icon' => '🔗', 'url' => $li);
    }

    // Announcement (allow HTML)
    $announcement = (!empty($options['enable_announcement']) && !empty($options['announcement_text'])) ? $options['announcement_text'] : '';
    $announcement_animation = isset($options['announcement_animation']) ? $options['announcement_animation'] : 'marquee';

    // Render markup
    // Topbar container render with layout variations based on enabled areas
    $classes = array('site-topbar');
    if (!$enable_left) $classes[] = 'topbar--no-left';
    if (empty($social_links) || !$enable_social) $classes[] = 'topbar--no-right';

    echo '<div class="' . implode(' ', $classes) . '" style="background:' . $bg . '; color:' . $color . ';">';
    echo '<div class="container topbar-inner">';

    // Left
    if ($enable_left) {
        echo '<div class="topbar-left">' . wp_kses_post($left) . '</div>';
    } else {
        echo '<div class="topbar-left"></div>';
    }

    // Center announcement - when left disabled, center can expand (CSS handles flex)
    if ($announcement) {
        $anim_class = 'announce-' . esc_attr($announcement_animation);
        echo '<div class="topbar-center"><div class="topbar-announcement ' . $anim_class . '" aria-hidden="false">' . wp_kses_post($announcement) . '</div></div>';
    } else {
        echo '<div class="topbar-center"></div>';
    }

    // Right
    echo '<div class="topbar-right">';
    if ($phone) {
        echo '<a class="topbar-phone" href="tel:' . esc_attr($phone) . '" style="color:' . $color . ';">' . esc_html($phone) . '</a>';
    }
    if ($enable_social && !empty($social_links)) {
        $icon_color = isset($options['topbar_icon_color']) ? esc_attr($options['topbar_icon_color']) : $color;
        echo '<div class="topbar-social">';
        foreach ($social_links as $s) {
            $icon_html = !empty($s['icon']) ? esc_html($s['icon']) : '�';
            echo '<a class="social-link" href="' . esc_url($s['url']) . '" target="_blank" rel="noopener noreferrer" style="color:' . $icon_color . ';">' . $icon_html . '</a>';
        }
        echo '</div>';
    }

    // Custom icon links (configured in admin)
    if (!empty($options['topbar_custom_icon_links']) && is_array($options['topbar_custom_icon_links'])) {
        $icon_color = isset($options['topbar_icon_color']) ? esc_attr($options['topbar_icon_color']) : $color;
        echo '<div class="topbar-custom-icons">';
        foreach ($options['topbar_custom_icon_links'] as $c) {
            if (empty($c['url']) || empty($c['icon'])) continue;
            $title = isset($c['title']) ? esc_attr($c['title']) : '';
            $icon_html = esc_html($c['icon']);
            echo '<a class="topbar-custom-icon" href="' . esc_url($c['url']) . '" title="' . $title . '" style="color:' . $icon_color . ';">' . $icon_html . '</a>';
        }
        echo '</div>';
    }

    echo '</div>'; // topbar-right

    echo '</div></div>';
}

/**
 * Output dynamic CSS for top bar enhancements
 */
function ross_theme_topbar_dynamic_css() {
    $options = ross_theme_get_header_options();
    
    if (empty($options['enable_topbar'])) {
        return;
    }

    $base_color = isset($options['topbar_text_color']) ? esc_attr($options['topbar_text_color']) : '#ffffff';
    $use_gradient = !empty($options['topbar_gradient_enable']);
    $shadow_enable = !empty($options['topbar_shadow_enable']);
    $border_width = isset($options['topbar_border_width']) ? absint($options['topbar_border_width']) : 0;
    $border_color = isset($options['topbar_border_color']) ? sanitize_hex_color($options['topbar_border_color']) : '#E5C902';
    
    echo '<style id="ross-topbar-dynamic-css">';
    
    echo '.site-topbar { transition: all 0.3s ease; }';
    echo '.topbar-right { display: flex; gap: 20px; align-items: center; }';
    echo '.topbar-phone, .social-link, .topbar-custom-icon { transition: all 0.2s ease; }';
    echo '.topbar-phone:hover, .social-link:hover, .topbar-custom-icon:hover { opacity: 0.8; transform: scale(1.1); }';
    
    echo '</style>';
}
add_action('wp_head', 'ross_theme_topbar_dynamic_css', 999);