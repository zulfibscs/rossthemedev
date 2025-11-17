<?php
/**
 * Footer Options Module
 * Controls everything visible in the site footer
 */

class RossFooterOptions {
    
    private $options;
    
    public function __construct() {
        $this->options = get_option('ross_theme_footer_options');
        add_action('admin_init', array($this, 'register_footer_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_footer_scripts'));
    }
    
    public function enqueue_footer_scripts($hook) {
        if ('toplevel_page_ross-theme-footer' === $hook) {
            wp_enqueue_style('wp-color-picker');
            wp_enqueue_script('wp-color-picker');
            wp_enqueue_script('ross-footer-admin', get_template_directory_uri() . '/assets/js/admin/footer-options.js', array('jquery', 'wp-color-picker'), '1.0.0', true);
        }
    }
    
    public function register_footer_settings() {
        register_setting(
            'ross_theme_footer_group',
            'ross_theme_footer_options',
            array($this, 'sanitize_footer_options')
        );
        
        $this->add_layout_section();
        $this->add_widgets_section();
        $this->add_cta_section();
        $this->add_social_section();
        $this->add_copyright_section();
    }
    
    private function add_layout_section() {
        add_settings_section(
            'ross_footer_layout_section',
            '🧱 Footer Layout',
            array($this, 'layout_section_callback'),
            'ross-theme-footer'
        );
        
        add_settings_field(
            'footer_style',
            'Footer Style',
            array($this, 'footer_style_callback'),
            'ross-theme-footer',
            'ross_footer_layout_section'
        );
        
        add_settings_field(
            'footer_columns',
            'Footer Columns',
            array($this, 'footer_columns_callback'),
            'ross-theme-footer',
            'ross_footer_layout_section'
        );
        
        add_settings_field(
            'footer_width',
            'Footer Width',
            array($this, 'footer_width_callback'),
            'ross-theme-footer',
            'ross_footer_layout_section'
        );
        
        add_settings_field(
            'footer_padding',
            'Footer Padding (px)',
            array($this, 'footer_padding_callback'),
            'ross-theme-footer',
            'ross_footer_layout_section'
        );
    }
    
    private function add_widgets_section() {
        add_settings_section(
            'ross_footer_widgets_section',
            '🧰 Footer Widgets',
            array($this, 'widgets_section_callback'),
            'ross-theme-footer'
        );
        
        add_settings_field(
            'enable_widgets',
            'Enable Widgets Area',
            array($this, 'enable_widgets_callback'),
            'ross-theme-footer',
            'ross_footer_widgets_section'
        );
        
        add_settings_field(
            'widgets_bg_color',
            'Background Color',
            array($this, 'widgets_bg_color_callback'),
            'ross-theme-footer',
            'ross_footer_widgets_section'
        );
        
        add_settings_field(
            'widgets_text_color',
            'Text Color',
            array($this, 'widgets_text_color_callback'),
            'ross-theme-footer',
            'ross_footer_widgets_section'
        );
        
        add_settings_field(
            'widget_title_color',
            'Widget Title Color',
            array($this, 'widget_title_color_callback'),
            'ross-theme-footer',
            'ross_footer_widgets_section'
        );
    }
    
    private function add_cta_section() {
        add_settings_section(
            'ross_footer_cta_section',
            '📢 Footer CTA (Optional)',
            array($this, 'cta_section_callback'),
            'ross-theme-footer'
        );
        
        add_settings_field(
            'enable_footer_cta',
            'Enable CTA Section',
            array($this, 'enable_footer_cta_callback'),
            'ross-theme-footer',
            'ross_footer_cta_section'
        );
        
        add_settings_field(
            'cta_title',
            'CTA Title',
            array($this, 'cta_title_callback'),
            'ross-theme-footer',
            'ross_footer_cta_section'
        );
        
        add_settings_field(
            'cta_button_text',
            'CTA Button Text',
            array($this, 'cta_button_text_callback'),
            'ross-theme-footer',
            'ross_footer_cta_section'
        );
        
        add_settings_field(
            'cta_bg_color',
            'CTA Background Color',
            array($this, 'cta_bg_color_callback'),
            'ross-theme-footer',
            'ross_footer_cta_section'
        );
    }
    
    private function add_social_section() {
        add_settings_section(
            'ross_footer_social_section',
            '🌍 Social Icons',
            array($this, 'social_section_callback'),
            'ross-theme-footer'
        );
        
        add_settings_field(
            'enable_social_icons',
            'Enable Social Icons',
            array($this, 'enable_social_icons_callback'),
            'ross-theme-footer',
            'ross_footer_social_section'
        );
        
        add_settings_field(
            'facebook_url',
            'Facebook URL',
            array($this, 'facebook_url_callback'),
            'ross-theme-footer',
            'ross_footer_social_section'
        );
        
        add_settings_field(
            'linkedin_url',
            'LinkedIn URL',
            array($this, 'linkedin_url_callback'),
            'ross-theme-footer',
            'ross_footer_social_section'
        );
        
        add_settings_field(
            'instagram_url',
            'Instagram URL',
            array($this, 'instagram_url_callback'),
            'ross-theme-footer',
            'ross_footer_social_section'
        );
        
        add_settings_field(
            'social_icon_color',
            'Icon Color',
            array($this, 'social_icon_color_callback'),
            'ross-theme-footer',
            'ross_footer_social_section'
        );
    }
    
    private function add_copyright_section() {
        add_settings_section(
            'ross_footer_copyright_section',
            '🧾 Copyright Bar',
            array($this, 'copyright_section_callback'),
            'ross-theme-footer'
        );
        
        add_settings_field(
            'enable_copyright',
            'Enable Copyright',
            array($this, 'enable_copyright_callback'),
            'ross-theme-footer',
            'ross_footer_copyright_section'
        );
        
        add_settings_field(
            'copyright_text',
            'Copyright Text',
            array($this, 'copyright_text_callback'),
            'ross-theme-footer',
            'ross_footer_copyright_section'
        );
        
        add_settings_field(
            'copyright_bg_color',
            'Background Color',
            array($this, 'copyright_bg_color_callback'),
            'ross-theme-footer',
            'ross_footer_copyright_section'
        );
        
        add_settings_field(
            'copyright_text_color',
            'Text Color',
            array($this, 'copyright_text_color_callback'),
            'ross-theme-footer',
            'ross_footer_copyright_section'
        );
        
        add_settings_field(
            'copyright_alignment',
            'Alignment',
            array($this, 'copyright_alignment_callback'),
            'ross-theme-footer',
            'ross_footer_copyright_section'
        );
    }
    
    // Section Callbacks
    public function layout_section_callback() {
        echo '<p>Control the overall structure and layout of your footer.</p>';
    }
    
    public function widgets_section_callback() {
        echo '<p>Configure the footer widgets area appearance.</p>';
    }
    
    public function cta_section_callback() {
        echo '<p>Add a call-to-action section above the main footer.</p>';
    }
    
    public function social_section_callback() {
        echo '<p>Configure social media icons and links.</p>';
    }
    
    public function copyright_section_callback() {
        echo '<p>Customize the copyright bar at the bottom.</p>';
    }
    
    // Field Callbacks - Layout Section
    public function footer_style_callback() {
        $value = isset($this->options['footer_style']) ? $this->options['footer_style'] : 'default';
        ?>
        <select name="ross_theme_footer_options[footer_style]" id="footer_style">
            <option value="default" <?php selected($value, 'default'); ?>>Default (Widgets + Bottom Bar)</option>
            <option value="minimal" <?php selected($value, 'minimal'); ?>>Minimal (Single Row)</option>
            <option value="cta" <?php selected($value, 'cta'); ?>>CTA Footer</option>
        </select>
        <?php
    }
    
    public function footer_columns_callback() {
        $value = isset($this->options['footer_columns']) ? $this->options['footer_columns'] : '4';
        ?>
        <select name="ross_theme_footer_options[footer_columns]" id="footer_columns">
            <option value="1" <?php selected($value, '1'); ?>>1 Column</option>
            <option value="2" <?php selected($value, '2'); ?>>2 Columns</option>
            <option value="3" <?php selected($value, '3'); ?>>3 Columns</option>
            <option value="4" <?php selected($value, '4'); ?>>4 Columns</option>
        </select>
        <?php
    }
    
    public function footer_width_callback() {
        $value = isset($this->options['footer_width']) ? $this->options['footer_width'] : 'contained';
        ?>
        <select name="ross_theme_footer_options[footer_width]" id="footer_width">
            <option value="full" <?php selected($value, 'full'); ?>>Full Width</option>
            <option value="contained" <?php selected($value, 'contained'); ?>>Contained</option>
        </select>
        <?php
    }
    
    public function footer_padding_callback() {
        $value = isset($this->options['footer_padding']) ? $this->options['footer_padding'] : '60';
        ?>
        <input type="number" name="ross_theme_footer_options[footer_padding]" value="<?php echo esc_attr($value); ?>" class="small-text" /> px
        <?php
    }
    
    // Field Callbacks - Widgets Section
    public function enable_widgets_callback() {
        $value = isset($this->options['enable_widgets']) ? $this->options['enable_widgets'] : 1;
        ?>
        <input type="checkbox" name="ross_theme_footer_options[enable_widgets]" value="1" <?php checked(1, $value); ?> />
        <label for="enable_widgets">Enable footer widgets area</label>
        <?php
    }
    
    public function widgets_bg_color_callback() {
        $value = isset($this->options['widgets_bg_color']) ? $this->options['widgets_bg_color'] : '#001946';
        ?>
        <input type="text" name="ross_theme_footer_options[widgets_bg_color]" value="<?php echo esc_attr($value); ?>" class="color-picker" data-default-color="#001946" />
        <?php
    }
    
    public function widgets_text_color_callback() {
        $value = isset($this->options['widgets_text_color']) ? $this->options['widgets_text_color'] : '#ffffff';
        ?>
        <input type="text" name="ross_theme_footer_options[widgets_text_color]" value="<?php echo esc_attr($value); ?>" class="color-picker" data-default-color="#ffffff" />
        <?php
    }
    
    public function widget_title_color_callback() {
        $value = isset($this->options['widget_title_color']) ? $this->options['widget_title_color'] : '#E5C902';
        ?>
        <input type="text" name="ross_theme_footer_options[widget_title_color]" value="<?php echo esc_attr($value); ?>" class="color-picker" data-default-color="#E5C902" />
        <?php
    }
    
    // Field Callbacks - CTA Section
    public function enable_footer_cta_callback() {
        $value = isset($this->options['enable_footer_cta']) ? $this->options['enable_footer_cta'] : 0;
        ?>
        <input type="checkbox" name="ross_theme_footer_options[enable_footer_cta]" value="1" <?php checked(1, $value); ?> />
        <label for="enable_footer_cta">Enable footer CTA section</label>
        <?php
    }
    
    public function cta_title_callback() {
        $value = isset($this->options['cta_title']) ? $this->options['cta_title'] : 'Ready to Get Started?';
        ?>
        <input type="text" name="ross_theme_footer_options[cta_title]" value="<?php echo esc_attr($value); ?>" class="regular-text" />
        <?php
    }
    
    public function cta_button_text_callback() {
        $value = isset($this->options['cta_button_text']) ? $this->options['cta_button_text'] : 'Contact Us Today';
        ?>
        <input type="text" name="ross_theme_footer_options[cta_button_text]" value="<?php echo esc_attr($value); ?>" class="regular-text" />
        <?php
    }
    
    public function cta_bg_color_callback() {
        $value = isset($this->options['cta_bg_color']) ? $this->options['cta_bg_color'] : '#E5C902';
        ?>
        <input type="text" name="ross_theme_footer_options[cta_bg_color]" value="<?php echo esc_attr($value); ?>" class="color-picker" data-default-color="#E5C902" />
        <?php
    }
    
    // Field Callbacks - Social Section
    public function enable_social_icons_callback() {
        $value = isset($this->options['enable_social_icons']) ? $this->options['enable_social_icons'] : 1;
        ?>
        <input type="checkbox" name="ross_theme_footer_options[enable_social_icons]" value="1" <?php checked(1, $value); ?> />
        <label for="enable_social_icons">Show social media icons</label>
        <?php
    }
    
    public function facebook_url_callback() {
        $value = isset($this->options['facebook_url']) ? $this->options['facebook_url'] : '';
        ?>
        <input type="url" name="ross_theme_footer_options[facebook_url]" value="<?php echo esc_url($value); ?>" class="regular-text" placeholder="https://facebook.com/yourpage" />
        <?php
    }
    
    public function linkedin_url_callback() {
        $value = isset($this->options['linkedin_url']) ? $this->options['linkedin_url'] : '';
        ?>
        <input type="url" name="ross_theme_footer_options[linkedin_url]" value="<?php echo esc_url($value); ?>" class="regular-text" placeholder="https://linkedin.com/company/yourcompany" />
        <?php
    }
    
    public function instagram_url_callback() {
        $value = isset($this->options['instagram_url']) ? $this->options['instagram_url'] : '';
        ?>
        <input type="url" name="ross_theme_footer_options[instagram_url]" value="<?php echo esc_url($value); ?>" class="regular-text" placeholder="https://instagram.com/yourprofile" />
        <?php
    }
    
    public function social_icon_color_callback() {
        $value = isset($this->options['social_icon_color']) ? $this->options['social_icon_color'] : '#ffffff';
        ?>
        <input type="text" name="ross_theme_footer_options[social_icon_color]" value="<?php echo esc_attr($value); ?>" class="color-picker" data-default-color="#ffffff" />
        <?php
    }
    
    // Field Callbacks - Copyright Section
    public function enable_copyright_callback() {
        $value = isset($this->options['enable_copyright']) ? $this->options['enable_copyright'] : 1;
        ?>
        <input type="checkbox" name="ross_theme_footer_options[enable_copyright]" value="1" <?php checked(1, $value); ?> />
        <label for="enable_copyright">Show copyright bar</label>
        <?php
    }
    
    public function copyright_text_callback() {
        $value = isset($this->options['copyright_text']) ? $this->options['copyright_text'] : '© ' . date('Y') . ' ROSS MCKINLEY ACCOUNTANTS LTD. All Rights Reserved.';
        ?>
        <textarea name="ross_theme_footer_options[copyright_text]" rows="3" class="large-text"><?php echo esc_textarea($value); ?></textarea>
        <?php
    }
    
    public function copyright_bg_color_callback() {
        $value = isset($this->options['copyright_bg_color']) ? $this->options['copyright_bg_color'] : '#001946';
        ?>
        <input type="text" name="ross_theme_footer_options[copyright_bg_color]" value="<?php echo esc_attr($value); ?>" class="color-picker" data-default-color="#001946" />
        <?php
    }
    
    public function copyright_text_color_callback() {
        $value = isset($this->options['copyright_text_color']) ? $this->options['copyright_text_color'] : '#ffffff';
        ?>
        <input type="text" name="ross_theme_footer_options[copyright_text_color]" value="<?php echo esc_attr($value); ?>" class="color-picker" data-default-color="#ffffff" />
        <?php
    }
    
    public function copyright_alignment_callback() {
        $value = isset($this->options['copyright_alignment']) ? $this->options['copyright_alignment'] : 'center';
        ?>
        <select name="ross_theme_footer_options[copyright_alignment]" id="copyright_alignment">
            <option value="left" <?php selected($value, 'left'); ?>>Left</option>
            <option value="center" <?php selected($value, 'center'); ?>>Center</option>
            <option value="right" <?php selected($value, 'right'); ?>>Right</option>
        </select>
        <?php
    }
    
    // Sanitization
    public function sanitize_footer_options($input) {
        $sanitized = array();
        
        // Layout
        $sanitized['footer_style'] = sanitize_text_field($input['footer_style']);
        $sanitized['footer_columns'] = sanitize_text_field($input['footer_columns']);
        $sanitized['footer_width'] = sanitize_text_field($input['footer_width']);
        $sanitized['footer_padding'] = absint($input['footer_padding']);
        
        // Widgets
        $sanitized['enable_widgets'] = isset($input['enable_widgets']) ? 1 : 0;
        $sanitized['widgets_bg_color'] = sanitize_hex_color($input['widgets_bg_color']);
        $sanitized['widgets_text_color'] = sanitize_hex_color($input['widgets_text_color']);
        $sanitized['widget_title_color'] = sanitize_hex_color($input['widget_title_color']);
        
        // CTA
        $sanitized['enable_footer_cta'] = isset($input['enable_footer_cta']) ? 1 : 0;
        $sanitized['cta_title'] = sanitize_text_field($input['cta_title']);
        $sanitized['cta_button_text'] = sanitize_text_field($input['cta_button_text']);
        $sanitized['cta_bg_color'] = sanitize_hex_color($input['cta_bg_color']);
        
        // Social
        $sanitized['enable_social_icons'] = isset($input['enable_social_icons']) ? 1 : 0;
        $sanitized['facebook_url'] = esc_url_raw($input['facebook_url']);
        $sanitized['linkedin_url'] = esc_url_raw($input['linkedin_url']);
        $sanitized['instagram_url'] = esc_url_raw($input['instagram_url']);
        $sanitized['social_icon_color'] = sanitize_hex_color($input['social_icon_color']);
        
        // Copyright
        $sanitized['enable_copyright'] = isset($input['enable_copyright']) ? 1 : 0;
        $sanitized['copyright_text'] = wp_kses_post($input['copyright_text']);
        $sanitized['copyright_bg_color'] = sanitize_hex_color($input['copyright_bg_color']);
        $sanitized['copyright_text_color'] = sanitize_hex_color($input['copyright_text_color']);
        $sanitized['copyright_alignment'] = sanitize_text_field($input['copyright_alignment']);
        
        return $sanitized;
    }
}

// Initialize
if (is_admin()) {
    new RossFooterOptions();
}