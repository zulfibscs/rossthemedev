<?php
/**
 * Default Footer Template
 */
?>
<footer class="site-footer">
    <?php if (ross_theme_should_show_footer_cta()): ?>
        <div class="footer-cta">
            <div class="container">
                <h3><?php echo esc_html(get_option('ross_theme_footer_options')['cta_title']); ?></h3>
                <a href="/contact" class="btn btn-primary">
                    <?php echo esc_html(get_option('ross_theme_footer_options')['cta_button_text']); ?>
                </a>
            </div>
        </div>
    <?php endif; ?>

    <?php if (ross_theme_should_show_footer_widgets()): ?>
        <div class="footer-widgets">
            <div class="container">
                <div class="footer-columns">
                    <!-- Footer widgets will be displayed here -->
                    <?php if (is_active_sidebar('footer-1')): ?>
                        <div class="footer-column"><?php dynamic_sidebar('footer-1'); ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="footer-copyright">
        <div class="container">
            <div class="copyright-inner">
                <div class="copyright-text">
                    <?php echo wp_kses_post(ross_theme_get_copyright_text()); ?>
                </div>
                
                <?php if (ross_theme_should_show_social_icons()): ?>
                    <div class="footer-social">
                        <?php
                        $footer_options = get_option('ross_theme_footer_options');
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
</footer>