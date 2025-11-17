<?php
/**
 * WordPress Theme Customizer - Top Bar Settings
 * Handles all top bar customization options via the WordPress Customizer
 */

if (!defined('ABSPATH')) exit;

/**
 * Add Top Bar Settings to Customizer
 */
function ross_theme_customize_register($wp_customize) {
    
    // ============================================
    // SECTION: Top Bar Settings
    // ============================================
    $wp_customize->add_section('ross_topbar_section', array(
        'title'       => __('🎯 Top Bar Settings', 'ross-theme'),
        'description' => __('Configure top bar layout, colors, and social links', 'ross-theme'),
        'priority'    => 30,
        'capability'  => 'edit_theme_options',
    ));

    // ============================================
    // PANEL: General Options (Left Column)
    // ============================================
    
    // Enable Top Bar
    $wp_customize->add_setting('ross_topbar_enable', array(
        'default'              => false,
        'sanitize_callback'    => 'ross_sanitize_checkbox',
        'type'                 => 'theme_mod',
        'transport'            => 'postMessage',
    ));
    $wp_customize->add_control('ross_topbar_enable', array(
        'label'       => __('✅ Enable Top Bar', 'ross-theme'),
        'section'     => 'ross_topbar_section',
        'type'        => 'checkbox',
        'priority'    => 10,
    ));

    // Left Section Content
    $wp_customize->add_setting('ross_topbar_left_content', array(
        'default'              => '',
        'sanitize_callback'    => 'wp_kses_post',
        'type'                 => 'theme_mod',
        'transport'            => 'postMessage',
    ));
    $wp_customize->add_control('ross_topbar_left_content', array(
        'label'       => __('📝 Left Section Content', 'ross-theme'),
        'description' => __('Accepts text, phone, email, or custom HTML', 'ross-theme'),
        'section'     => 'ross_topbar_section',
        'type'        => 'textarea',
        'priority'    => 20,
    ));

    // Show Left Section
    $wp_customize->add_setting('ross_topbar_show_left', array(
        'default'              => true,
        'sanitize_callback'    => 'ross_sanitize_checkbox',
        'type'                 => 'theme_mod',
        'transport'            => 'postMessage',
    ));
    $wp_customize->add_control('ross_topbar_show_left', array(
        'label'       => __('✅ Show Left Section', 'ross-theme'),
        'section'     => 'ross_topbar_section',
        'type'        => 'checkbox',
        'priority'    => 30,
    ));

    // Phone Number
    $wp_customize->add_setting('ross_topbar_phone', array(
        'default'              => '',
        'sanitize_callback'    => 'ross_sanitize_phone',
        'type'                 => 'theme_mod',
        'transport'            => 'postMessage',
    ));
    $wp_customize->add_control('ross_topbar_phone', array(
        'label'       => __('📞 Phone Number', 'ross-theme'),
        'section'     => 'ross_topbar_section',
        'type'        => 'text',
        'priority'    => 40,
    ));

    // Email Address
    $wp_customize->add_setting('ross_topbar_email', array(
        'default'              => '',
        'sanitize_callback'    => 'sanitize_email',
        'type'                 => 'theme_mod',
        'transport'            => 'postMessage',
    ));
    $wp_customize->add_control('ross_topbar_email', array(
        'label'       => __('✉️ Email Address', 'ross-theme'),
        'section'     => 'ross_topbar_section',
        'type'        => 'email',
        'priority'    => 50,
    ));

    // Announcement Text
    $wp_customize->add_setting('ross_topbar_announcement', array(
        'default'              => '',
        'sanitize_callback'    => 'wp_kses_post',
        'type'                 => 'theme_mod',
        'transport'            => 'postMessage',
    ));
    $wp_customize->add_control('ross_topbar_announcement', array(
        'label'       => __('📰 Announcement Text', 'ross-theme'),
        'description' => __('Text displayed in top bar announcement area', 'ross-theme'),
        'section'     => 'ross_topbar_section',
        'type'        => 'textarea',
        'priority'    => 60,
    ));

    // Marquee Toggle
    $wp_customize->add_setting('ross_topbar_marquee_enable', array(
        'default'              => false,
        'sanitize_callback'    => 'ross_sanitize_checkbox',
        'type'                 => 'theme_mod',
        'transport'            => 'postMessage',
    ));
    $wp_customize->add_control('ross_topbar_marquee_enable', array(
        'label'       => __('🎬 Enable Marquee Animation', 'ross-theme'),
        'section'     => 'ross_topbar_section',
        'type'        => 'checkbox',
        'priority'    => 70,
    ));

    // ============================================
    // PANEL: Design Options (Right Column)
    // ============================================

    // Background Color
    $wp_customize->add_setting('ross_topbar_bg_color', array(
        'default'              => '#001946',
        'sanitize_callback'    => 'sanitize_hex_color',
        'type'                 => 'theme_mod',
        'transport'            => 'postMessage',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'ross_topbar_bg_color', array(
        'label'       => __('🎨 Background Color', 'ross-theme'),
        'section'     => 'ross_topbar_section',
        'priority'    => 80,
    )));

    // Text Color
    $wp_customize->add_setting('ross_topbar_text_color', array(
        'default'              => '#ffffff',
        'sanitize_callback'    => 'sanitize_hex_color',
        'type'                 => 'theme_mod',
        'transport'            => 'postMessage',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'ross_topbar_text_color', array(
        'label'       => __('🎨 Text Color', 'ross-theme'),
        'section'     => 'ross_topbar_section',
        'priority'    => 90,
    )));

    // Icon Color
    $wp_customize->add_setting('ross_topbar_icon_color', array(
        'default'              => '#E5C902',
        'sanitize_callback'    => 'sanitize_hex_color',
        'type'                 => 'theme_mod',
        'transport'            => 'postMessage',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'ross_topbar_icon_color', array(
        'label'       => __('🎨 Icon Color', 'ross-theme'),
        'section'     => 'ross_topbar_section',
        'priority'    => 100,
    )));

    // Font Size Slider
    $wp_customize->add_setting('ross_topbar_font_size', array(
        'default'              => 14,
        'sanitize_callback'    => 'ross_sanitize_integer',
        'type'                 => 'theme_mod',
        'transport'            => 'postMessage',
    ));
    $wp_customize->add_control('ross_topbar_font_size', array(
        'label'       => __('🖋️ Font Size (10px–24px)', 'ross-theme'),
        'section'     => 'ross_topbar_section',
        'type'        => 'range',
        'input_attrs' => array(
            'min'  => 10,
            'max'  => 24,
            'step' => 1,
        ),
        'priority'    => 110,
    ));

    // Alignment Selector
    $wp_customize->add_setting('ross_topbar_alignment', array(
        'default'              => 'left',
        'sanitize_callback'    => 'ross_sanitize_alignment',
        'type'                 => 'theme_mod',
        'transport'            => 'postMessage',
    ));
    $wp_customize->add_control('ross_topbar_alignment', array(
        'label'       => __('🧍 Text Alignment', 'ross-theme'),
        'section'     => 'ross_topbar_section',
        'type'        => 'select',
        'choices'     => array(
            'left'   => __('Left', 'ross-theme'),
            'center' => __('Center', 'ross-theme'),
            'right'  => __('Right', 'ross-theme'),
        ),
        'priority'    => 120,
    ));

    // ============================================
    // SECTION: Social Links Settings
    // ============================================
    $wp_customize->add_section('ross_topbar_social_section', array(
        'title'       => __('🔗 Social Links', 'ross-theme'),
        'description' => __('Configure social media icons in top bar', 'ross-theme'),
        'priority'    => 35,
        'capability'  => 'edit_theme_options',
    ));

    // Enable Social Icons
    $wp_customize->add_setting('ross_topbar_social_enable', array(
        'default'              => false,
        'sanitize_callback'    => 'ross_sanitize_checkbox',
        'type'                 => 'theme_mod',
        'transport'            => 'postMessage',
    ));
    $wp_customize->add_control('ross_topbar_social_enable', array(
        'label'       => __('✅ Enable Social Icons', 'ross-theme'),
        'section'     => 'ross_topbar_social_section',
        'type'        => 'checkbox',
        'priority'    => 10,
    ));

    // Social Platforms Configuration
    $social_platforms = array(
        'facebook'  => array('label' => 'Facebook', 'icon' => 'fab fa-facebook'),
        'twitter'   => array('label' => 'Twitter/X', 'icon' => 'fab fa-twitter'),
        'linkedin'  => array('label' => 'LinkedIn', 'icon' => 'fab fa-linkedin'),
        'instagram' => array('label' => 'Instagram', 'icon' => 'fab fa-instagram'),
        'youtube'   => array('label' => 'YouTube', 'icon' => 'fab fa-youtube'),
    );

    $priority = 20;
    foreach ($social_platforms as $platform => $config) {
        // URL Setting
        $wp_customize->add_setting("ross_topbar_social_{$platform}_url", array(
            'default'              => '',
            'sanitize_callback'    => 'esc_url_raw',
            'type'                 => 'theme_mod',
            'transport'            => 'postMessage',
        ));
        $wp_customize->add_control("ross_topbar_social_{$platform}_url", array(
            'label'       => sprintf(__('%s URL', 'ross-theme'), $config['label']),
            'section'     => 'ross_topbar_social_section',
            'type'        => 'url',
            'priority'    => $priority,
        ));

        // Enable Toggle
        $wp_customize->add_setting("ross_topbar_social_{$platform}_enabled", array(
            'default'              => false,
            'sanitize_callback'    => 'ross_sanitize_checkbox',
            'type'                 => 'theme_mod',
            'transport'            => 'postMessage',
        ));
        $wp_customize->add_control("ross_topbar_social_{$platform}_enabled", array(
            'label'       => sprintf(__('Enable %s', 'ross-theme'), $config['label']),
            'section'     => 'ross_topbar_social_section',
            'type'        => 'checkbox',
            'priority'    => $priority + 1,
        ));

        // Icon Upload
        $wp_customize->add_setting("ross_topbar_social_{$platform}_icon", array(
            'default'              => $config['icon'],
            'sanitize_callback'    => 'sanitize_text_field',
            'type'                 => 'theme_mod',
            'transport'            => 'postMessage',
        ));
        $wp_customize->add_control("ross_topbar_social_{$platform}_icon", array(
            'label'       => sprintf(__('%s Icon Class (FontAwesome)', 'ross-theme'), $config['label']),
            'description' => __('e.g., fab fa-facebook or dashicons-facebook', 'ross-theme'),
            'section'     => 'ross_topbar_social_section',
            'type'        => 'text',
            'priority'    => $priority + 2,
        ));

        $priority += 3;
    }

    // ============================================
    // SECTION: Style Enhancements
    // ============================================
    $wp_customize->add_section('ross_topbar_style_section', array(
        'title'       => __('✨ Style Enhancements', 'ross-theme'),
        'description' => __('Advanced styling options for top bar', 'ross-theme'),
        'priority'    => 40,
        'capability'  => 'edit_theme_options',
    ));

    // Shadow Toggle
    $wp_customize->add_setting('ross_topbar_shadow_enable', array(
        'default'              => false,
        'sanitize_callback'    => 'ross_sanitize_checkbox',
        'type'                 => 'theme_mod',
        'transport'            => 'postMessage',
    ));
    $wp_customize->add_control('ross_topbar_shadow_enable', array(
        'label'       => __('💡 Enable Drop Shadow', 'ross-theme'),
        'section'     => 'ross_topbar_style_section',
        'type'        => 'checkbox',
        'priority'    => 10,
    ));

    // Gradient Background Toggle
    $wp_customize->add_setting('ross_topbar_gradient_enable', array(
        'default'              => false,
        'sanitize_callback'    => 'ross_sanitize_checkbox',
        'type'                 => 'theme_mod',
        'transport'            => 'postMessage',
    ));
    $wp_customize->add_control('ross_topbar_gradient_enable', array(
        'label'       => __('🌈 Enable Gradient Background', 'ross-theme'),
        'section'     => 'ross_topbar_style_section',
        'type'        => 'checkbox',
        'priority'    => 20,
    ));

    // Gradient Color 1
    $wp_customize->add_setting('ross_topbar_gradient_color1', array(
        'default'              => '#001946',
        'sanitize_callback'    => 'sanitize_hex_color',
        'type'                 => 'theme_mod',
        'transport'            => 'postMessage',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'ross_topbar_gradient_color1', array(
        'label'       => __('🎨 Gradient Color 1', 'ross-theme'),
        'section'     => 'ross_topbar_style_section',
        'priority'    => 30,
    )));

    // Gradient Color 2
    $wp_customize->add_setting('ross_topbar_gradient_color2', array(
        'default'              => '#003d7a',
        'sanitize_callback'    => 'sanitize_hex_color',
        'type'                 => 'theme_mod',
        'transport'            => 'postMessage',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'ross_topbar_gradient_color2', array(
        'label'       => __('🎨 Gradient Color 2', 'ross-theme'),
        'section'     => 'ross_topbar_style_section',
        'priority'    => 40,
    )));

    // Border Bottom Color
    $wp_customize->add_setting('ross_topbar_border_color', array(
        'default'              => '#E5C902',
        'sanitize_callback'    => 'sanitize_hex_color',
        'type'                 => 'theme_mod',
        'transport'            => 'postMessage',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'ross_topbar_border_color', array(
        'label'       => __('💠 Border Bottom Color', 'ross-theme'),
        'section'     => 'ross_topbar_style_section',
        'priority'    => 50,
    )));

    // Border Bottom Width
    $wp_customize->add_setting('ross_topbar_border_width', array(
        'default'              => 0,
        'sanitize_callback'    => 'ross_sanitize_integer',
        'type'                 => 'theme_mod',
        'transport'            => 'postMessage',
    ));
    $wp_customize->add_control('ross_topbar_border_width', array(
        'label'       => __('📏 Border Width (0-5px)', 'ross-theme'),
        'section'     => 'ross_topbar_style_section',
        'type'        => 'range',
        'input_attrs' => array(
            'min'  => 0,
            'max'  => 5,
            'step' => 1,
        ),
        'priority'    => 60,
    ));
}
// Customizer registration for Top Bar deprecated.
// These settings have been migrated to the admin Header Options -> Top Bar.
// add_action('customize_register', 'ross_theme_customize_register');

/**
 * Sanitization Functions
 */
function ross_sanitize_checkbox($input) {
    return (isset($input) ? true : false);
}

function ross_sanitize_integer($input) {
    return absint($input);
}

function ross_sanitize_phone($input) {
    return sanitize_text_field($input);
}

function ross_sanitize_alignment($input) {
    $valid = array('left', 'center', 'right');
    return (in_array($input, $valid) ? $input : 'left');
}

/**
 * Display Top Bar from Customizer Settings
 * Hooked to wp_body_open to display before header
 */
// Customizer topbar display is deprecated (migrated to admin settings).
// function ross_theme_display_customizer_topbar() {
//     $enable = get_theme_mod('ross_topbar_enable', false);
//     if (!$enable) {
//         return;
//     }
//     get_template_part('template-parts/topbar');
// }
// add_action('wp_body_open', 'ross_theme_display_customizer_topbar', 5);

/**
 * Output Dynamic CSS from Customizer Settings
 */
function ross_topbar_dynamic_css() {
    if (!is_customize_preview() && !is_admin()) {
        $enable_topbar = get_theme_mod('ross_topbar_enable', false);
        if (!$enable_topbar) {
            return;
        }
    }

    $bg_color = get_theme_mod('ross_topbar_bg_color', '#001946');
    $text_color = get_theme_mod('ross_topbar_text_color', '#ffffff');
    $icon_color = get_theme_mod('ross_topbar_icon_color', '#E5C902');
    $font_size = get_theme_mod('ross_topbar_font_size', 14);
    $alignment = get_theme_mod('ross_topbar_alignment', 'left');
    $shadow_enable = get_theme_mod('ross_topbar_shadow_enable', false);
    $gradient_enable = get_theme_mod('ross_topbar_gradient_enable', false);
    $gradient_color1 = get_theme_mod('ross_topbar_gradient_color1', '#001946');
    $gradient_color2 = get_theme_mod('ross_topbar_gradient_color2', '#003d7a');
    $border_color = get_theme_mod('ross_topbar_border_color', '#E5C902');
    $border_width = get_theme_mod('ross_topbar_border_width', 0);

    echo '<style id="ross-topbar-dynamic-css">';
    
    echo '.site-topbar {';
    if ($gradient_enable) {
        echo 'background: linear-gradient(90deg, ' . sanitize_hex_color($gradient_color1) . ', ' . sanitize_hex_color($gradient_color2) . ') !important;';
    } else {
        echo 'background-color: ' . sanitize_hex_color($bg_color) . ' !important;';
    }
    echo 'color: ' . sanitize_hex_color($text_color) . ' !important;';
    echo 'font-size: ' . absint($font_size) . 'px !important;';
    echo 'text-align: ' . esc_attr($alignment) . ' !important;';
    if ($shadow_enable) {
        echo 'box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15) !important;';
    }
    if ($border_width > 0) {
        echo 'border-bottom: ' . absint($border_width) . 'px solid ' . sanitize_hex_color($border_color) . ' !important;';
    }
    echo '}';

    echo '.site-topbar a, .topbar-left, .topbar-center, .topbar-right {';
    echo 'color: ' . sanitize_hex_color($text_color) . ' !important;';
    echo '}';

    echo '.site-topbar .social-link, .site-topbar .topbar-phone {';
    echo 'color: ' . sanitize_hex_color($icon_color) . ' !important;';
    echo '}';

    echo '.site-topbar a:hover {';
    echo 'color: ' . sanitize_hex_color($icon_color) . ' !important;';
    echo '}';

    echo '</style>';
}
// Dynamic CSS from Customizer for Top Bar is deprecated.
// Styles are now generated from admin header options (see ross_theme_topbar_dynamic_css()).
// add_action('wp_head', 'ross_topbar_dynamic_css', 999);
