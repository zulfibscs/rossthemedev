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

        // Compose background based on the chosen background type
        $bg_type = isset($footer_options['styling_bg_type']) ? $footer_options['styling_bg_type'] : 'color';
        if ($bg_type === 'gradient' && !empty($footer_options['styling_bg_gradient_from']) && !empty($footer_options['styling_bg_gradient_to'])) {
            $from = $footer_options['styling_bg_gradient_from'];
            $to = $footer_options['styling_bg_gradient_to'];
            $bg_layers[] = 'linear-gradient(to bottom, ' . esc_attr($from) . ', ' . esc_attr($to) . ')';
        } elseif ($bg_type === 'image' && !empty($footer_options['styling_bg_image'])) {
            $img = esc_url($footer_options['styling_bg_image']);
            $bg_layers[] = 'url("' . $img . '")';
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

        // Overlay layer handling (topmost layer)
        $overlay_layers = array();
        if (!empty($footer_options['styling_overlay_enabled']) && intval($footer_options['styling_overlay_enabled'])) {
            $overlay_type = isset($footer_options['styling_overlay_type']) ? $footer_options['styling_overlay_type'] : 'color';
            $overlay_opacity = isset($footer_options['styling_overlay_opacity']) ? floatval($footer_options['styling_overlay_opacity']) : 0.5;
            if ($overlay_type === 'color' && !empty($footer_options['styling_overlay_color'])) {
                $hex = ltrim($footer_options['styling_overlay_color'], '#');
                if (strlen($hex) === 3) {
                    $r = hexdec(str_repeat(substr($hex,0,1),2));
                    $g = hexdec(str_repeat(substr($hex,1,1),2));
                    $b = hexdec(str_repeat(substr($hex,2,1),2));
                } else {
                    $r = hexdec(substr($hex,0,2));
                    $g = hexdec(substr($hex,2,2));
                    $b = hexdec(substr($hex,4,2));
                }
                $rgba = 'rgba(' . $r . ',' . $g . ',' . $b . ',' . $overlay_opacity . ')';
                $overlay_layers[] = 'linear-gradient(' . $rgba . ',' . $rgba . ')';
            }
            if ($overlay_type === 'gradient' && !empty($footer_options['styling_overlay_gradient_from']) && !empty($footer_options['styling_overlay_gradient_to'])) {
                $from = $footer_options['styling_overlay_gradient_from'];
                $to = $footer_options['styling_overlay_gradient_to'];
                // apply overlay opacity by converting from/to to rgba with opacity
                $from_hex = ltrim($from, '#'); $to_hex = ltrim($to, '#');
                if (strlen($from_hex) === 3) { $fr = hexdec(str_repeat(substr($from_hex,0,1),2)); $fg = hexdec(str_repeat(substr($from_hex,1,1),2)); $fb = hexdec(str_repeat(substr($from_hex,2,1),2)); } else { $fr = hexdec(substr($from_hex,0,2)); $fg = hexdec(substr($from_hex,2,2)); $fb = hexdec(substr($from_hex,4,2)); }
                if (strlen($to_hex) === 3) { $tr = hexdec(str_repeat(substr($to_hex,0,1),2)); $tg = hexdec(str_repeat(substr($to_hex,1,1),2)); $tb = hexdec(str_repeat(substr($to_hex,2,1),2)); } else { $tr = hexdec(substr($to_hex,0,2)); $tg = hexdec(substr($to_hex,2,2)); $tb = hexdec(substr($to_hex,4,2)); }
                $from_rgba = 'rgba(' . $fr . ',' . $fg . ',' . $fb . ',' . $overlay_opacity . ')';
                $to_rgba = 'rgba(' . $tr . ',' . $tg . ',' . $tb . ',' . $overlay_opacity . ')';
                $overlay_layers[] = 'linear-gradient(to bottom, ' . $from_rgba . ', ' . $to_rgba . ')';
            }
            if ($overlay_type === 'image' && !empty($footer_options['styling_overlay_image'])) {
                $oi = esc_url($footer_options['styling_overlay_image']);
                // If opacity < 1, add an overlay tint above the image to simulate shading
                if ($overlay_opacity < 1 && !empty($footer_options['styling_overlay_color'])) {
                    // tint overlay for better control
                    $hex = ltrim($footer_options['styling_overlay_color'], '#');
                    if (strlen($hex) === 3) { $r = hexdec(str_repeat(substr($hex,0,1),2)); $g = hexdec(str_repeat(substr($hex,1,1),2)); $b = hexdec(str_repeat(substr($hex,2,1),2)); } else { $r = hexdec(substr($hex,0,2)); $g = hexdec(substr($hex,2,2)); $b = hexdec(substr($hex,4,2)); }
                    $rgba = 'rgba(' . $r . ',' . $g . ',' . $b . ',' . (1-$overlay_opacity) . ')';
                    $overlay_layers[] = 'linear-gradient(' . $rgba . ',' . $rgba . ')';
                }
                $overlay_layers[] = 'url("' . $oi . '")';
            }
        }

        // Prepend overlay layers so they sit on top
        if (!empty($overlay_layers)) {
            $bg_layers = array_merge($overlay_layers, $bg_layers);
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

        // Column and Row gaps
        $col_gap = isset($footer_options['styling_col_gap']) ? intval($footer_options['styling_col_gap']) : 24;
        $row_gap = isset($footer_options['styling_row_gap']) ? intval($footer_options['styling_row_gap']) : 18;
        echo '.footer-columns { gap: ' . $row_gap . 'px ' . $col_gap . 'px !important; grid-row-gap: ' . $row_gap . 'px !important; grid-column-gap: ' . $col_gap . 'px !important; }';

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

        // CTA Styling — generate if CTA will be visible (either enabled or always visible)
        $should_show_cta = function_exists('ross_theme_should_show_footer_cta') ? ross_theme_should_show_footer_cta() : (isset($footer_options['enable_footer_cta']) && $footer_options['enable_footer_cta']);
        if ($should_show_cta) {
        $cta_bg = isset($footer_options['cta_bg_color']) ? sanitize_hex_color($footer_options['cta_bg_color']) : '';
        $cta_bg_type = isset($footer_options['cta_bg_type']) ? $footer_options['cta_bg_type'] : 'color';
        $cta_bg_img = isset($footer_options['cta_bg_image']) ? esc_url($footer_options['cta_bg_image']) : '';
        $cta_grad_from = isset($footer_options['cta_bg_gradient_from']) ? sanitize_hex_color($footer_options['cta_bg_gradient_from']) : '';
        $cta_grad_to = isset($footer_options['cta_bg_gradient_to']) ? sanitize_hex_color($footer_options['cta_bg_gradient_to']) : '';
        $cta_text_color = isset($footer_options['cta_text_color']) ? sanitize_hex_color($footer_options['cta_text_color']) : '';
        $cta_button_bg = isset($footer_options['cta_button_bg_color']) ? sanitize_hex_color($footer_options['cta_button_bg_color']) : '';
        $cta_button_text_color = isset($footer_options['cta_button_text_color']) ? sanitize_hex_color($footer_options['cta_button_text_color']) : '';
        $cta_alignment = isset($footer_options['cta_alignment']) ? sanitize_text_field($footer_options['cta_alignment']) : 'center';
        $cta_gap = isset($footer_options['cta_gap']) ? intval($footer_options['cta_gap']) : 12;
        $cta_padding_top = isset($footer_options['cta_padding_top']) ? intval($footer_options['cta_padding_top']) : 24;
        $cta_padding_right = isset($footer_options['cta_padding_right']) ? intval($footer_options['cta_padding_right']) : 0;
        $cta_padding_bottom = isset($footer_options['cta_padding_bottom']) ? intval($footer_options['cta_padding_bottom']) : 24;
        $cta_padding_left = isset($footer_options['cta_padding_left']) ? intval($footer_options['cta_padding_left']) : 0;

        if ($cta_bg_type === 'color' && !empty($cta_bg)) {
            echo '.footer-cta { background-color: ' . esc_attr($cta_bg) . ' !important; }';
        }
        if ($cta_bg_type === 'gradient' && !empty($cta_grad_from) && !empty($cta_grad_to)) {
            echo '.footer-cta { background-image: linear-gradient(to right, ' . esc_attr($cta_grad_from) . ', ' . esc_attr($cta_grad_to) . ') !important; }';
        }
        if ($cta_bg_type === 'image' && !empty($cta_bg_img)) {
            echo '.footer-cta { background-image: url("' . esc_url($cta_bg_img) . '") !important; background-size: cover !important; background-position: center center !important; }';
        }
        if (!empty($cta_text_color)) {
            echo '.footer-cta, .footer-cta .footer-cta-text, .footer-cta h2, .footer-cta h3 { color: ' . esc_attr($cta_text_color) . ' !important; }';
        }
        if (!empty($cta_button_bg)) {
            echo '.footer-cta .btn { background: ' . esc_attr($cta_button_bg) . ' !important; }';
        }
        if (!empty($cta_button_text_color)) {
            echo '.footer-cta .btn { color: ' . esc_attr($cta_button_text_color) . ' !important; }';
        }
        echo '.footer-cta { padding: ' . intval($cta_padding_top) . 'px ' . intval($cta_padding_right) . 'px ' . intval($cta_padding_bottom) . 'px ' . intval($cta_padding_left) . 'px !important; }';
        // alignment
        if (!empty($cta_alignment)) {
            $justify = 'center';
            if ($cta_alignment === 'left') $justify = 'flex-start';
            if ($cta_alignment === 'right') $justify = 'flex-end';
            echo '.footer-cta .footer-cta-inner { display: flex; align-items: center; justify-content: ' . $justify . ' !important; gap: ' . intval($cta_gap) . 'px !important; }';
        }
        // Icon color
        if (!empty($footer_options['cta_icon_color'])) {
            echo '.footer-cta .cta-icon { color: ' . esc_attr($footer_options['cta_icon_color']) . ' !important; }';
        }

        // Layout specifics (direction, wrap, justify, align)
        $dir = isset($footer_options['cta_layout_direction']) ? $footer_options['cta_layout_direction'] : 'row';
        $wrap = isset($footer_options['cta_layout_wrap']) && $footer_options['cta_layout_wrap'] ? 'wrap' : 'nowrap';
        $justify = isset($footer_options['cta_layout_justify']) ? $footer_options['cta_layout_justify'] : 'center';
        $align = isset($footer_options['cta_layout_align']) ? $footer_options['cta_layout_align'] : 'center';
        echo '.footer-cta .footer-cta-inner { flex-direction: ' . esc_attr($dir) . ' !important; flex-wrap: ' . esc_attr($wrap) . ' !important; justify-content:' . esc_attr($justify) . ' !important; align-items:' . esc_attr($align) . ' !important; }';

            // small animations
            if (isset($footer_options['cta_animation']) && $footer_options['cta_animation'] !== 'none') {
            $anim = $footer_options['cta_animation'];
            $anim_delay = isset($footer_options['cta_anim_delay']) ? intval($footer_options['cta_anim_delay']) : 150;
            $anim_delay_s = max(0.0, floatval($anim_delay / 1000.0));
            if ($anim === 'fade') {
                echo '.footer-cta--anim-fade { opacity: 0; transform: translateY(6px); transition: opacity 400ms ease, transform 400ms ease; }';
                echo '.footer-cta--anim-fade.visible { opacity: 1; transform: none; }';
                echo '.footer-cta--anim-fade { transition-delay:' . $anim_delay_s . 's !important; }';
            }
            if ($anim === 'slide') {
                echo '.footer-cta--anim-slide { opacity: 0; transform: translateY(12px); transition: opacity 500ms ease, transform 500ms ease; }';
                echo '.footer-cta--anim-slide.visible { opacity: 1; transform: none; }';
                echo '.footer-cta--anim-slide { transition-delay:' . $anim_delay_s . 's !important; }';
            }
            if ($anim === 'pop') {
                echo '.footer-cta--anim-pop { opacity: 0; transform: scale(.96); transition: opacity 350ms ease, transform 350ms ease; }';
                echo '.footer-cta--anim-pop.visible { opacity: 1; transform: scale(1); }';
                echo '.footer-cta--anim-pop { transition-delay:' . $anim_delay_s . 's !important; }';
            }
            if ($anim === 'zoom') {
                echo '.footer-cta--anim-zoom { opacity: 0; transform: scale(.95); transition: opacity 400ms ease, transform 400ms ease; }';
                echo '.footer-cta--anim-zoom.visible { opacity: 1; transform: scale(1.02); }';
                echo '.footer-cta--anim-zoom { transition-delay:' . $anim_delay_s . 's !important; }';
            }
            }
        }
        // Custom CTA CSS (advanced)
        if (!empty($footer_options['enable_custom_cta']) && !empty($footer_options['custom_cta_css'])) {
            echo $footer_options['custom_cta_css'];
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