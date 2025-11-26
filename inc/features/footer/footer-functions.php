<?php
/**
 * Footer Functions Module
 * Handles footer display logic based on options
 */

function ross_theme_get_footer_layout() {
    // Footer style option removed; always use the default layout.
    return 'default';
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
        case 'four-column':
            // Use the default footer template which supports dynamic columns
            get_template_part('template-parts/footer/footer-default');
            break;
        default:
            get_template_part('template-parts/footer/footer-default');
            break;
    }
}


/**
 * Render custom footer HTML when enabled
 */
function ross_theme_render_custom_footer() {
    $footer_options = get_option('ross_theme_footer_options');
    if (empty($footer_options) || empty($footer_options['enable_custom_footer'])) {
        return;
    }

    if (!empty($footer_options['custom_footer_html'])) {
        echo '<div class="site-footer-custom">' . $footer_options['custom_footer_html'] . '</div>';
    }
}

function ross_theme_should_show_footer_widgets() {
    $footer_options = get_option('ross_theme_footer_options');
    return isset($footer_options['enable_widgets']) ? $footer_options['enable_widgets'] : true;
}

function ross_theme_should_show_footer_cta() {
    $footer_options = get_option('ross_theme_footer_options');
    // Developer override: allow defining ROSS_DISABLE_CTA to globally disable CTA
    if (defined('ROSS_DISABLE_CTA') && ROSS_DISABLE_CTA) {
        return false;
    }
    // Always-visible override
    if (!empty($footer_options['cta_always_visible'])) {
        return true;
    }

    // Respect the enable toggle; fall back to false
    $enabled = isset($footer_options['enable_footer_cta']) ? (bool) $footer_options['enable_footer_cta'] : false;
    if (!$enabled) return false;

    // If enabled, optionally check 'cta_display_on' for template-specific visibility
    if (!empty($footer_options['cta_display_on']) && is_array($footer_options['cta_display_on'])) {
        $visible_on = $footer_options['cta_display_on'];
        // If 'all' selected, show everywhere
        if (in_array('all', $visible_on, true)) return true;
        // Front page
        if (in_array('front', $visible_on, true) && is_front_page()) return true;
        if (in_array('home', $visible_on, true) && is_home()) return true;
        if (in_array('single', $visible_on, true) && is_single()) return true;
        if (in_array('page', $visible_on, true) && is_page()) return true;
        if (in_array('archive', $visible_on, true) && is_archive()) return true;

        // No matching visibility: do not show.
        return false;
    }

    return $enabled;
}

function ross_theme_should_show_social_icons() {
    $footer_options = get_option('ross_theme_footer_options');
    return isset($footer_options['enable_social_icons']) ? $footer_options['enable_social_icons'] : true;
}

function ross_theme_should_show_copyright() {
    $footer_options = get_option('ross_theme_footer_options');
    return isset($footer_options['enable_copyright']) ? (bool) $footer_options['enable_copyright'] : true;
}

function ross_theme_get_copyright_text() {
    $footer_options = get_option('ross_theme_footer_options');
    return isset($footer_options['copyright_text']) ? $footer_options['copyright_text'] : '© ' . date('Y') . ' ' . get_bloginfo('name') . '. All rights reserved.';
}


/**
 * Register footer widget areas (footer-1 .. footer-4)
 */
function ross_theme_register_footer_sidebars() {
    for ($i = 1; $i <= 4; $i++) {
        register_sidebar(array(
            'name' => sprintf(__('Footer Column %d', 'rosstheme'), $i),
            'id' => 'footer-' . $i,
            'description' => sprintf(__('Widgets for footer column %d', 'rosstheme'), $i),
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget' => '</div>',
            'before_title' => '<h4 class="widget-title">',
            'after_title' => '</h4>',
        ));
    }
}
add_action('widgets_init', 'ross_theme_register_footer_sidebars');

/**
 * Display the optional footer CTA — placed OUTSIDE the <footer> element.
 * This allows CTA to be shown above the footer rather than inside it.
 */
function ross_theme_display_footer_cta() {
    if (!function_exists('ross_theme_should_show_footer_cta') || !ross_theme_should_show_footer_cta()) {
        return;
    }
    // Use get_template_part so developers can override markup in child themes
    get_template_part('template-parts/footer/footer-cta');
}