<?php
/**
 * Default Footer Template
 */
?>
<footer class="site-footer" <?php
    $footer_options = get_option('ross_theme_footer_options');
    // per-side padding support: styling_padding_top/bottom/left/right fall back to footer_padding
    $pad_top = isset($footer_options['styling_padding_top']) && $footer_options['styling_padding_top'] !== '' ? intval($footer_options['styling_padding_top']) : (isset($footer_options['footer_padding']) ? intval($footer_options['footer_padding']) : 60);
    $pad_bottom = isset($footer_options['styling_padding_bottom']) && $footer_options['styling_padding_bottom'] !== '' ? intval($footer_options['styling_padding_bottom']) : (isset($footer_options['footer_padding']) ? intval($footer_options['footer_padding']) : 60);
    $pad_left = isset($footer_options['styling_padding_left']) && $footer_options['styling_padding_left'] !== '' ? intval($footer_options['styling_padding_left']) : (isset($footer_options['styling_padding_lr']) ? intval($footer_options['styling_padding_lr']) : 20);
    $pad_right = isset($footer_options['styling_padding_right']) && $footer_options['styling_padding_right'] !== '' ? intval($footer_options['styling_padding_right']) : (isset($footer_options['styling_padding_lr']) ? intval($footer_options['styling_padding_lr']) : 20);
    echo 'style="padding-top:' . esc_attr($pad_top) . 'px; padding-bottom:' . esc_attr($pad_bottom) . 'px; padding-left:' . esc_attr($pad_left) . 'px; padding-right:' . esc_attr($pad_right) . 'px;"';
?>>
    <?php // CTA is rendered outside the footer by ross_theme_display_footer_cta() ?>

    <?php if (ross_theme_should_show_footer_widgets()):
        // Allow selected template to influence default columns/social position
        $template = isset($footer_options['footer_template']) ? $footer_options['footer_template'] : 'template1';
        $template_map = array(
            'template1' => array('columns' => 4, 'social_align' => 'center'),
            'template2' => array('columns' => 4, 'social_align' => 'center'),
            'template3' => array('columns' => 4, 'social_align' => 'center'),
            'template4' => array('columns' => 1, 'social_align' => 'left'),
        );
        $default_cols = isset($footer_options['footer_columns']) ? intval($footer_options['footer_columns']) : 4;
        $mapped = $template_map[$template] ?? array('columns' => 4, 'social_align' => 'center');
        $columns = max(1, min(4, $default_cols ?: $mapped['columns']));
        $container_class = (isset($footer_options['footer_width']) && $footer_options['footer_width'] === 'full') ? 'container-fluid' : 'container';
    ?>
        <div class="footer-widgets">
            <div class="<?php echo esc_attr($container_class); ?>">
                <div class="footer-columns footer-columns--<?php echo esc_attr($columns); ?>">
                    <?php
                    // Render widget areas footer-1 .. footer-N if active; fall back to placeholder when empty
                    for ($i = 1; $i <= $columns; $i++):
                        $id = 'footer-' . $i;
                        echo '<div class="footer-column footer-column-' . $i . '">';
                        if (is_active_sidebar($id)) {
                            dynamic_sidebar($id);
                        } else {
                            // placeholder for maintain structure
                            echo '<div class="footer-placeholder">';
                            echo '<!-- No widgets in ' . esc_html($id) . ' -->';
                            echo '</div>';
                        }
                        echo '</div>';
                    endfor;
                    ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php
    // Custom footer HTML (site-wide) if enabled
    if (function_exists('ross_theme_render_custom_footer')) {
        ross_theme_render_custom_footer();
    }
    ?>

    <?php if (function_exists('ross_theme_should_show_copyright') && ross_theme_should_show_copyright()):
        $copyright_bg = isset($footer_options['copyright_bg_color']) ? sanitize_hex_color($footer_options['copyright_bg_color']) : '';
        $copyright_text_color = isset($footer_options['copyright_text_color']) ? sanitize_hex_color($footer_options['copyright_text_color']) : '';
        $copyright_alignment = isset($footer_options['copyright_alignment']) ? esc_attr($footer_options['copyright_alignment']) : 'center';

        $copyright_styles = array();
        if ($copyright_bg) $copyright_styles[] = 'background:' . esc_attr($copyright_bg);
        if ($copyright_text_color) $copyright_styles[] = 'color:' . esc_attr($copyright_text_color);

        $copyright_style_attr = !empty($copyright_styles) ? 'style="' . esc_attr(implode(';', $copyright_styles)) . '"' : '';
    ?>
    <div class="footer-copyright" <?php echo $copyright_style_attr; ?>>
        <div class="container">
            <div class="copyright-inner" style="text-align:<?php echo esc_attr($copyright_alignment); ?>;">
                <div class="copyright-text">
                    <?php echo wp_kses_post(ross_theme_get_copyright_text()); ?>
                </div>

                <?php if (ross_theme_should_show_social_icons()): ?>
                    <div class="footer-social">
                        <?php
                        if (!empty($footer_options['facebook_url'])): ?>
                            <a href="<?php echo esc_url($footer_options['facebook_url']); ?>" target="_blank" class="social-icon">Facebook</a>
                        <?php endif; ?>
                        <?php if (!empty($footer_options['linkedin_url'])): ?>
                            <a href="<?php echo esc_url($footer_options['linkedin_url']); ?>" target="_blank" class="social-icon">LinkedIn</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <?php if (!empty($footer_options['custom_footer_js'])): ?>
        <script><?php echo esc_html($footer_options['custom_footer_js']); ?></script>
    <?php endif; ?>
</footer>