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

    // Menu font size and alignment
    if (!empty($header_options['menu_font_size'])) {
        $size = absint($header_options['menu_font_size']);
        echo '.primary-menu a { font-size: ' . $size . 'px !important; }';
    }

    if (!empty($header_options['menu_alignment'])) {
        $align = esc_attr($header_options['menu_alignment']);
        $justify = 'flex-start';
        if ($align === 'center') $justify = 'center';
        if ($align === 'right') $justify = 'flex-end';
        echo '.primary-menu { justify-content: ' . $justify . ' !important; }';
    }

    // Menu hover, background and border colors
    if (!empty($header_options['menu_hover_color'])) {
        echo '.primary-menu a:hover { color: ' . esc_attr($header_options['menu_hover_color']) . ' !important; }';
        echo '.primary-menu a:hover::after { background: ' . esc_attr($header_options['menu_hover_color']) . ' !important; }';
    }

    if (isset($header_options['menu_bg_color']) && $header_options['menu_bg_color'] !== '') {
        echo '.header-navigation, .header-navigation-centered { background-color: ' . esc_attr($header_options['menu_bg_color']) . ' !important; }';
    }

    if (!empty($header_options['menu_border_color'])) {
        echo '.primary-menu a::after { background: ' . esc_attr($header_options['menu_border_color']) . ' !important; }';
        echo '.primary-menu .current-menu-item a::after { background: ' . esc_attr($header_options['menu_border_color']) . ' !important; }';
    }

    // Footer template colors
    $footer_options = get_option('ross_theme_footer_options', array());
    $template = isset($footer_options['footer_template']) ? $footer_options['footer_template'] : 'template1';
    $use_template = isset($footer_options['use_template_colors']) ? intval($footer_options['use_template_colors']) : 1;

    // Define sane defaults for templates
    $tpl_defaults = array(
        'template1' => array('bg' => '#1a1a1a', 'text' => '#ffffff', 'accent' => '#007cba', 'social' => '#ffffff', 'columns' => 4, 'social_align' => 'center'),
        'template2' => array('bg' => '#2d3748', 'text' => '#e2e8f0', 'accent' => '#38b2ac', 'social' => '#ffffff', 'columns' => 4, 'social_align' => 'center'),
        'template3' => array('bg' => '#000000', 'text' => '#ffffff', 'accent' => '#e53e3e', 'social' => '#ffffff', 'columns' => 4, 'social_align' => 'center'),
        'template4' => array('bg' => '#f7fafc', 'text' => '#4a5568', 'accent' => '#000000', 'social' => '#4a5568', 'columns' => 1, 'social_align' => 'left')
    );

    if (isset($tpl_defaults[$template])) {
        $defaults = $tpl_defaults[$template];
        // allow overrides from settings when use_template_colors disabled
        $bg = ($use_template ? $defaults['bg'] : ($footer_options[$template . '_bg'] ?? $defaults['bg']));
        $text = ($use_template ? $defaults['text'] : ($footer_options[$template . '_text'] ?? $defaults['text']));
        $accent = ($use_template ? $defaults['accent'] : ($footer_options[$template . '_accent'] ?? $defaults['accent']));
        $social = ($use_template ? $defaults['social'] : ($footer_options[$template . '_social'] ?? $defaults['social']));

        // Admin 'Styling' overrides (higher priority)
        if (!empty($footer_options['styling_bg_color'])) {
            $bg = $footer_options['styling_bg_color'];
        }
        if (!empty($footer_options['styling_text_color'])) {
            $text = $footer_options['styling_text_color'];
        }
        if (!empty($footer_options['styling_link_color'])) {
            $accent = $footer_options['styling_link_color'];
        }

        // link hover
        $link_hover = isset($footer_options['styling_link_hover']) && $footer_options['styling_link_hover'] !== '' ? $footer_options['styling_link_hover'] : '';

        // typography
        $font_size = isset($footer_options['styling_font_size']) && intval($footer_options['styling_font_size']) ? intval($footer_options['styling_font_size']) : 0;
        $line_height = isset($footer_options['styling_line_height']) && floatval($footer_options['styling_line_height']) ? floatval($footer_options['styling_line_height']) : 0;

        // spacing & padding
        $padding_lr = isset($footer_options['styling_padding_lr']) ? absint($footer_options['styling_padding_lr']) : 0;

        // border
        $border_top = isset($footer_options['styling_border_top']) && $footer_options['styling_border_top'] ? true : false;
        $border_color = isset($footer_options['styling_border_color']) ? $footer_options['styling_border_color'] : '';
        $border_thickness = isset($footer_options['styling_border_thickness']) ? absint($footer_options['styling_border_thickness']) : 0;

        // widget title style
        $widget_title_color = isset($footer_options['styling_widget_title_color']) ? $footer_options['styling_widget_title_color'] : '';
        $widget_title_size = isset($footer_options['styling_widget_title_size']) ? absint($footer_options['styling_widget_title_size']) : 0;

        // Build background layers: gradient/top-overlay/image
        $bg_layers = array();

        // If gradient enabled and both colors present, add gradient as top layer
        if (!empty($footer_options['styling_bg_gradient']) && !empty($footer_options['styling_bg_gradient_from']) && !empty($footer_options['styling_bg_gradient_to'])) {
            $from = $footer_options['styling_bg_gradient_from'];
            $to = $footer_options['styling_bg_gradient_to'];
            $bg_layers[] = 'linear-gradient(to bottom, ' . esc_attr($from) . ', ' . esc_attr($to) . ')';
        } else {
            // If an overlay color with opacity is requested, create a semi-transparent overlay layer
            if (isset($footer_options['styling_bg_opacity']) && $footer_options['styling_bg_opacity'] !== '' && $footer_options['styling_bg_opacity'] < 1 && !empty($footer_options['styling_bg_color'])) {
                $op = floatval($footer_options['styling_bg_opacity']);
                $hex = ltrim($footer_options['styling_bg_color'], '#');
                if (strlen($hex) === 3) {
                    $r = hexdec(str_repeat(substr($hex,0,1),2));
                    $g = hexdec(str_repeat(substr($hex,1,1),2));
                    $b = hexdec(str_repeat(substr($hex,2,1),2));
                } else {
                    $r = hexdec(substr($hex,0,2));
                    $g = hexdec(substr($hex,2,2));
                    $b = hexdec(substr($hex,4,2));
                }
                $rgba = 'rgba(' . $r . ',' . $g . ',' . $b . ',' . $op . ')';
                $bg_layers[] = 'linear-gradient(' . $rgba . ', ' . $rgba . ')';
            }
        }

        // If an image provided, add it as the bottom layer
        if (!empty($footer_options['styling_bg_image'])) {
            $img = esc_url($footer_options['styling_bg_image']);
            $bg_layers[] = 'url("' . $img . '")';
        }

        // Always output a fallback solid background color (so color works even when image/gradient present)
        if (!empty($bg)) {
            echo '.site-footer { background-color: ' . esc_attr($bg) . ' !important; color: ' . esc_attr($text) . ' !important; }';
        }

        if (!empty($bg_layers)) {
            $bg_css = implode(', ', $bg_layers);
            echo '.site-footer { background-image: ' . $bg_css . ' !important; background-size: cover !important; background-position: center center !important; }';
            echo '.site-footer a { color: ' . esc_attr($accent) . ' !important; }';
        }

        if (!empty($social)) {
            echo '.site-footer .social-icon { color: ' . esc_attr($social) . ' !important; }';
        }

        // link hover
        if (!empty($link_hover)) {
            echo '.site-footer a:hover { color: ' . esc_attr($link_hover) . ' !important; }';
        }

        // font size and line-height
        if (!empty($font_size)) {
            echo '.site-footer, .site-footer p, .site-footer li, .site-footer a { font-size: ' . intval($font_size) . 'px !important; }';
        }
        if (!empty($line_height)) {
            echo '.site-footer { line-height: ' . floatval($line_height) . ' !important; }';
        }

        // padding: per-side support with fallbacks
        $pad_top = isset($footer_options['styling_padding_top']) && $footer_options['styling_padding_top'] !== '' ? intval($footer_options['styling_padding_top']) : (isset($footer_options['footer_padding']) ? intval($footer_options['footer_padding']) : 60);
        $pad_bottom = isset($footer_options['styling_padding_bottom']) && $footer_options['styling_padding_bottom'] !== '' ? intval($footer_options['styling_padding_bottom']) : (isset($footer_options['footer_padding']) ? intval($footer_options['footer_padding']) : 60);
        $pad_left = isset($footer_options['styling_padding_left']) && $footer_options['styling_padding_left'] !== '' ? intval($footer_options['styling_padding_left']) : (isset($footer_options['styling_padding_lr']) ? intval($footer_options['styling_padding_lr']) : 20);
        $pad_right = isset($footer_options['styling_padding_right']) && $footer_options['styling_padding_right'] !== '' ? intval($footer_options['styling_padding_right']) : (isset($footer_options['styling_padding_lr']) ? intval($footer_options['styling_padding_lr']) : 20);
        echo '.site-footer { padding-top: ' . $pad_top . 'px !important; padding-bottom: ' . $pad_bottom . 'px !important; padding-left: ' . $pad_left . 'px !important; padding-right: ' . $pad_right . 'px !important; }';

        // horizontal padding for containers (if user set padding_lr separately)
        if (!empty($padding_lr)) {
            echo '.footer-widgets .container, .footer-copyright .container { padding-left: ' . intval($padding_lr) . 'px !important; padding-right: ' . intval($padding_lr) . 'px !important; }';
        }

        // border top
        if ($border_top && $border_thickness > 0 && !empty($border_color)) {
            echo '.footer-widgets { border-top: ' . intval($border_thickness) . 'px solid ' . esc_attr($border_color) . ' !important; }';
        }

        // widget title styles
        if (!empty($widget_title_color)) {
            echo '.footer-widgets .widget-title, .footer-widgets h4 { color: ' . esc_attr($widget_title_color) . ' !important; }';
        }
        if (!empty($widget_title_size)) {
            echo '.footer-widgets .widget-title, .footer-widgets h4 { font-size: ' . intval($widget_title_size) . 'px !important; }';
        }
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