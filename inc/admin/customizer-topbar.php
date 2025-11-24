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
    // Announcement section (mapped to ross_theme_header_options)
    $wp_customize->add_section('ross_announcement_section', array(
        'title'       => __('📣 Announcement Bar', 'ross-theme'),
        'description' => __('Configure the global announcement bar.', 'ross-theme'),
        'priority'    => 20,
        'capability'  => 'edit_theme_options',
    ));

    $wp_customize->add_setting('ross_theme_header_options[enable_announcement]', array(
        'default'           => 0,
        'type'              => 'option',
        'sanitize_callback' => 'ross_sanitize_checkbox',
    ));
    $wp_customize->add_control('ross_theme_header_options[enable_announcement]', array(
        'label'    => __('✅ Enable Announcement Bar', 'ross-theme'),
        'section'  => 'ross_announcement_section',
        'type'     => 'checkbox',
        'priority' => 10,
    ));

    $wp_customize->add_setting('ross_theme_header_options[announcement_text]', array(
        'default'           => '',
        'type'              => 'option',
        'sanitize_callback' => 'wp_kses_post',
    ));
    $wp_customize->add_control('ross_theme_header_options[announcement_text]', array(
        'label'       => __('📰 Announcement Text', 'ross-theme'),
        'description' => __('HTML allowed; keep it concise.', 'ross-theme'),
        'section'     => 'ross_announcement_section',
        'type'        => 'textarea',
        'priority'    => 20,
    ));

    $wp_customize->add_setting('ross_theme_header_options[announcement_animation]', array(
        'default'           => 'marquee',
        'type'              => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('ross_theme_header_options[announcement_animation]', array(
        'label'    => __('Animation', 'ross-theme'),
        'section'  => 'ross_announcement_section',
        'type'     => 'select',
        'choices'  => array(
            'none'    => __('None', 'ross-theme'),
            'marquee' => __('Marquee (smooth scroll)', 'ross-theme'),
        ),
        'priority' => 30,
    ));

    $wp_customize->add_setting('ross_theme_header_options[announcement_bg_color]', array(
        'default'           => '#E5C902',
        'type'              => 'option',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'ross_theme_header_options[announcement_bg_color]', array(
        'label'    => __('Background Color', 'ross-theme'),
        'section'  => 'ross_announcement_section',
        'priority' => 40,
    )));

    $wp_customize->add_setting('ross_theme_header_options[announcement_text_color]', array(
        'default'           => '#001946',
        'type'              => 'option',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'ross_theme_header_options[announcement_text_color]', array(
        'label'    => __('Text Color', 'ross-theme'),
        'section'  => 'ross_announcement_section',
        'priority' => 50,
    )));

    $wp_customize->add_setting('ross_theme_header_options[announcement_font_size]', array(
        'default'           => '14px',
        'type'              => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('ross_theme_header_options[announcement_font_size]', array(
        'label'    => __('Font Size', 'ross-theme'),
        'section'  => 'ross_announcement_section',
        'type'     => 'select',
        'choices'  => array(
            '12px' => __('Small (12px)', 'ross-theme'),
            '14px' => __('Medium (14px)', 'ross-theme'),
            '16px' => __('Large (16px)', 'ross-theme'),
            '18px' => __('Extra Large (18px)', 'ross-theme'),
        ),
        'priority' => 60,
    ));

    $wp_customize->add_setting('ross_theme_header_options[announcement_position]', array(
        'default'           => 'top_of_topbar',
        'type'              => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('ross_theme_header_options[announcement_position]', array(
        'label'    => __('Position', 'ross-theme'),
        'section'  => 'ross_announcement_section',
        'type'     => 'select',
        'choices'  => array(
            'top_of_topbar' => __('Top of Topbar (topmost)', 'ross-theme'),
            'below_topbar' => __('Below Topbar (top of header)', 'ross-theme'),
            'below_header' => __('Below Header (after header)', 'ross-theme'),
        ),
        'priority' => 70,
    ));

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
    $wp_customize->add_setting('ross_theme_header_options[enable_topbar]', array(
        'default'           => 0,
        'type'              => 'option',
        'sanitize_callback' => 'ross_sanitize_checkbox',
    ));
    $wp_customize->add_control('ross_theme_header_options[enable_topbar]', array(
        'label'       => __('✅ Enable Top Bar', 'ross-theme'),
        'section'     => 'ross_topbar_section',
        'type'        => 'checkbox',
        'priority'    => 10,
    ));

    // Left Section Content
    $wp_customize->add_setting('ross_theme_header_options[topbar_left_content]', array(
        'default'           => '',
        'type'              => 'option',
        'sanitize_callback' => 'wp_kses_post',
    ));
    $wp_customize->add_control('ross_theme_header_options[topbar_left_content]', array(
        'label'       => __('📝 Left Section Content', 'ross-theme'),
        'description' => __('Accepts text, phone, email, or custom HTML', 'ross-theme'),
        'section'     => 'ross_topbar_section',
        'type'        => 'textarea',
        'priority'    => 20,
    ));

    // Show Left Section
    $wp_customize->add_setting('ross_theme_header_options[enable_topbar_left]', array(
        'default'           => 1,
        'type'              => 'option',
        'sanitize_callback' => 'ross_sanitize_checkbox',
    ));
    $wp_customize->add_control('ross_theme_header_options[enable_topbar_left]', array(
        'label'       => __('✅ Show Left Section', 'ross-theme'),
        'section'     => 'ross_topbar_section',
        'type'        => 'checkbox',
        'priority'    => 30,
    ));

    // Phone Number
    $wp_customize->add_setting('ross_theme_header_options[phone_number]', array(
        'default'           => '',
        'type'              => 'option',
        'sanitize_callback' => 'ross_sanitize_phone',
    ));
    $wp_customize->add_control('ross_theme_header_options[phone_number]', array(
        'label'       => __('📞 Phone Number', 'ross-theme'),
        'section'     => 'ross_topbar_section',
        'type'        => 'text',
        'priority'    => 40,
    ));

    // Email Address
    $wp_customize->add_setting('ross_theme_header_options[topbar_email]', array(
        'default'           => '',
        'type'              => 'option',
        'sanitize_callback' => 'sanitize_email',
    ));
    $wp_customize->add_control('ross_theme_header_options[topbar_email]', array(
        'label'       => __('✉️ Email Address', 'ross-theme'),
        'section'     => 'ross_topbar_section',
        'type'        => 'email',
        'priority'    => 50,
    ));

    // Enable Social Icons (uses Header Options social/link repeaters)
    $wp_customize->add_setting('ross_theme_header_options[enable_social]', array(
        'default'           => 0,
        'type'              => 'option',
        'sanitize_callback' => 'ross_sanitize_checkbox',
    ));
    $wp_customize->add_control('ross_theme_header_options[enable_social]', array(
        'label'       => __('✅ Enable Social Icons', 'ross-theme'),
        'description' => __('Actual icons & links are configured in Header Options → Top Bar.', 'ross-theme'),
        'section'     => 'ross_topbar_section',
        'type'        => 'checkbox',
        'priority'    => 60,
    ));

    // Sticky Top Bar
    $wp_customize->add_setting('ross_theme_header_options[sticky_topbar]', array(
        'default'           => 0,
        'type'              => 'option',
        'sanitize_callback' => 'ross_sanitize_checkbox',
    ));
    $wp_customize->add_control('ross_theme_header_options[sticky_topbar]', array(
        'label'       => __('📌 Make Top Bar Sticky', 'ross-theme'),
        'section'     => 'ross_topbar_section',
        'type'        => 'checkbox',
        'priority'    => 70,
    ));

    $wp_customize->add_setting('ross_theme_header_options[topbar_sticky_offset]', array(
        'default'           => 0,
        'type'              => 'option',
        'sanitize_callback' => 'ross_sanitize_integer',
    ));
    $wp_customize->add_control('ross_theme_header_options[topbar_sticky_offset]', array(
        'label'       => __('Sticky Offset (px)', 'ross-theme'),
        'section'     => 'ross_topbar_section',
        'type'        => 'number',
        'input_attrs' => array(
            'min'  => 0,
            'max'  => 200,
            'step' => 1,
        ),
        'priority'    => 80,
    ));

    // ============================================
    // PANEL: Design Options (Right Column)
    // ============================================

    // Background Color
    $wp_customize->add_setting('ross_theme_header_options[topbar_bg_color]', array(
        'default'           => '#001946',
        'type'              => 'option',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'ross_theme_header_options[topbar_bg_color]', array(
        'label'       => __('🎨 Background Color', 'ross-theme'),
        'section'     => 'ross_topbar_section',
        'priority'    => 90,
    )));

    // Text Color
    $wp_customize->add_setting('ross_theme_header_options[topbar_text_color]', array(
        'default'           => '#ffffff',
        'type'              => 'option',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'ross_theme_header_options[topbar_text_color]', array(
        'label'       => __('🎨 Text Color', 'ross-theme'),
        'section'     => 'ross_topbar_section',
        'priority'    => 100,
    )));

    // Icon Color
    $wp_customize->add_setting('ross_theme_header_options[topbar_icon_color]', array(
        'default'           => '#E5C902',
        'type'              => 'option',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'ross_theme_header_options[topbar_icon_color]', array(
        'label'       => __('🎨 Icon Color', 'ross-theme'),
        'section'     => 'ross_topbar_section',
        'priority'    => 110,
    )));

    // Icon Hover Color
    $wp_customize->add_setting('ross_theme_header_options[topbar_icon_hover_color]', array(
        'default'           => '#E5C902',
        'type'              => 'option',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'ross_theme_header_options[topbar_icon_hover_color]', array(
        'label'       => __('🎨 Icon Hover Color', 'ross-theme'),
        'section'     => 'ross_topbar_section',
        'priority'    => 120,
    )));

    // Font Size Slider
    $wp_customize->add_setting('ross_theme_header_options[topbar_font_size]', array(
        'default'           => 14,
        'type'              => 'option',
        'sanitize_callback' => 'ross_sanitize_integer',
    ));
    $wp_customize->add_control('ross_theme_header_options[topbar_font_size]', array(
        'label'       => __('🖋️ Font Size (10px–24px)', 'ross-theme'),
        'section'     => 'ross_topbar_section',
        'type'        => 'range',
        'input_attrs' => array(
            'min'  => 10,
            'max'  => 24,
            'step' => 1,
        ),
        'priority'    => 130,
    ));

    // Alignment Selector
    $wp_customize->add_setting('ross_theme_header_options[topbar_alignment]', array(
        'default'           => 'left',
        'type'              => 'option',
        'sanitize_callback' => 'ross_sanitize_alignment',
    ));
    $wp_customize->add_control('ross_theme_header_options[topbar_alignment]', array(
        'label'       => __('🧍 Text Alignment', 'ross-theme'),
        'section'     => 'ross_topbar_section',
        'type'        => 'select',
        'choices'     => array(
            'left'   => __('Left', 'ross-theme'),
            'center' => __('Center', 'ross-theme'),
            'right'  => __('Right', 'ross-theme'),
        ),
        'priority'    => 140,
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
    $wp_customize->add_setting('ross_topbar_social_dummy', array(
        'default'           => false,
        'type'              => 'theme_mod',
        'sanitize_callback' => 'ross_sanitize_checkbox',
    ));
    $wp_customize->add_control('ross_topbar_social_dummy', array(
        'label'       => __('Social icons are managed from Header Options → Top Bar.', 'ross-theme'),
        'section'     => 'ross_topbar_social_section',
        'type'        => 'hidden',
        'priority'    => 10,
    ));

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
    $wp_customize->add_setting('ross_theme_header_options[topbar_shadow_enable]', array(
        'default'           => 0,
        'type'              => 'option',
        'sanitize_callback' => 'ross_sanitize_checkbox',
    ));
    $wp_customize->add_control('ross_theme_header_options[topbar_shadow_enable]', array(
        'label'       => __('💡 Enable Drop Shadow', 'ross-theme'),
        'section'     => 'ross_topbar_style_section',
        'type'        => 'checkbox',
        'priority'    => 10,
    ));

    // Gradient Background Toggle
    $wp_customize->add_setting('ross_theme_header_options[topbar_gradient_enable]', array(
        'default'           => 0,
        'type'              => 'option',
        'sanitize_callback' => 'ross_sanitize_checkbox',
    ));
    $wp_customize->add_control('ross_theme_header_options[topbar_gradient_enable]', array(
        'label'       => __('🌈 Enable Gradient Background', 'ross-theme'),
        'section'     => 'ross_topbar_style_section',
        'type'        => 'checkbox',
        'priority'    => 20,
    ));

    // Gradient Color 1
    $wp_customize->add_setting('ross_theme_header_options[topbar_gradient_color1]', array(
        'default'           => '#001946',
        'type'              => 'option',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'ross_theme_header_options[topbar_gradient_color1]', array(
        'label'       => __('🎨 Gradient Color 1', 'ross-theme'),
        'section'     => 'ross_topbar_style_section',
        'priority'    => 30,
    )));

    // Gradient Color 2
    $wp_customize->add_setting('ross_theme_header_options[topbar_gradient_color2]', array(
        'default'           => '#003d7a',
        'type'              => 'option',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'ross_theme_header_options[topbar_gradient_color2]', array(
        'label'       => __('🎨 Gradient Color 2', 'ross-theme'),
        'section'     => 'ross_topbar_style_section',
        'priority'    => 40,
    )));

    // Border Bottom Color
    $wp_customize->add_setting('ross_theme_header_options[topbar_border_color]', array(
        'default'           => '#E5C902',
        'type'              => 'option',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'ross_theme_header_options[topbar_border_color]', array(
        'label'       => __('💠 Border Bottom Color', 'ross-theme'),
        'section'     => 'ross_topbar_style_section',
        'priority'    => 50,
    )));

    // Border Bottom Width
    $wp_customize->add_setting('ross_theme_header_options[topbar_border_width]', array(
        'default'           => 0,
        'type'              => 'option',
        'sanitize_callback' => 'ross_sanitize_integer',
    ));
    $wp_customize->add_control('ross_theme_header_options[topbar_border_width]', array(
        'label'       => __('📏 Border Width (0-5px)', 'ross-theme'),
        'section'     => 'ross_topbar_style_section',
        'type'        => 'number',
        'input_attrs' => array(
            'min'  => 0,
            'max'  => 5,
            'step' => 1,
        ),
        'priority'    => 60,
    ));
}

function ross_sanitize_integer($input) {
    return sanitize_text_field($input);
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
 * Output Dynamic CSS from Customizer Settings (deprecated)
 * Kept for backward compatibility but hook is commented out.
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
