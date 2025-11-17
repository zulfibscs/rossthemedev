<?php
/**
 * General Options Module
 * Controls global theme settings and appearance
 */

class RossGeneralOptions {
    
    private $options;
    
    public function __construct() {
        $this->options = get_option('ross_theme_general_options');
        add_action('admin_init', array($this, 'register_general_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_general_scripts'), 0);
    }
    
    public function enqueue_general_scripts($hook) {
        if ('toplevel_page_ross-theme-general' === $hook) {
            // CRITICAL: Enqueue media library FIRST
            wp_enqueue_media();
            
            // Then jQuery
            wp_enqueue_script('jquery');
            
            // Then color picker
            wp_enqueue_style('wp-color-picker');
            wp_enqueue_script('wp-color-picker');
            
            // Finally our uploader script
            wp_enqueue_script('ross-uploader-standalone', get_template_directory_uri() . '/assets/js/admin/uploader-standalone.js', array(), '1.0.0', false);
        }
    }
    
    public function register_general_settings() {
        register_setting(
            'ross_theme_general_group',
            'ross_theme_general_options',
            array($this, 'sanitize_general_options')
        );
        
        $this->add_layout_section();
        $this->add_colors_section();
        $this->add_typography_section();
        $this->add_buttons_section();
        $this->add_media_section();
        $this->add_blog_section();
        $this->add_performance_section();
        $this->add_custom_section();
    }
    
    private function add_layout_section() {
        add_settings_section(
            'ross_general_layout_section',
            '🧱 Site Layout',
            array($this, 'layout_section_callback'),
            'ross-theme-general'
        );
        
        add_settings_field(
            'layout_style',
            'Layout Style',
            array($this, 'layout_style_callback'),
            'ross-theme-general',
            'ross_general_layout_section'
        );
        
        add_settings_field(
            'container_width',
            'Container Width (px)',
            array($this, 'container_width_callback'),
            'ross-theme-general',
            'ross_general_layout_section'
        );
        
        add_settings_field(
            'content_spacing',
            'Content Spacing (px)',
            array($this, 'content_spacing_callback'),
            'ross-theme-general',
            'ross_general_layout_section'
        );
        
        add_settings_field(
            'global_border_radius',
            'Global Border Radius (px)',
            array($this, 'global_border_radius_callback'),
            'ross-theme-general',
            'ross_general_layout_section'
        );
        
        add_settings_field(
            'enable_preloader',
            'Enable Page Preloader',
            array($this, 'enable_preloader_callback'),
            'ross-theme-general',
            'ross_general_layout_section'
        );
    }
    
    private function add_colors_section() {
        add_settings_section(
            'ross_general_colors_section',
            '🎨 Colors',
            array($this, 'colors_section_callback'),
            'ross-theme-general'
        );
        
        add_settings_field(
            'primary_color',
            'Primary Color',
            array($this, 'primary_color_callback'),
            'ross-theme-general',
            'ross_general_colors_section'
        );
        
        add_settings_field(
            'secondary_color',
            'Secondary Color',
            array($this, 'secondary_color_callback'),
            'ross-theme-general',
            'ross_general_colors_section'
        );
        
        add_settings_field(
            'accent_color',
            'Accent Color',
            array($this, 'accent_color_callback'),
            'ross-theme-general',
            'ross_general_colors_section'
        );
        
        add_settings_field(
            'background_color',
            'Background Color',
            array($this, 'background_color_callback'),
            'ross-theme-general',
            'ross_general_colors_section'
        );
        
        add_settings_field(
            'heading_color',
            'Heading Color',
            array($this, 'heading_color_callback'),
            'ross-theme-general',
            'ross_general_colors_section'
        );
        
        add_settings_field(
            'text_color',
            'Text Color',
            array($this, 'text_color_callback'),
            'ross-theme-general',
            'ross_general_colors_section'
        );
    }
    
    private function add_typography_section() {
        add_settings_section(
            'ross_general_typography_section',
            '✍️ Typography',
            array($this, 'typography_section_callback'),
            'ross-theme-general'
        );
        
        add_settings_field(
            'base_font_family',
            'Base Font Family',
            array($this, 'base_font_family_callback'),
            'ross-theme-general',
            'ross_general_typography_section'
        );
        
        add_settings_field(
            'base_font_size',
            'Base Font Size (px)',
            array($this, 'base_font_size_callback'),
            'ross-theme-general',
            'ross_general_typography_section'
        );
        
        add_settings_field(
            'base_line_height',
            'Base Line Height',
            array($this, 'base_line_height_callback'),
            'ross-theme-general',
            'ross_general_typography_section'
        );
        
        add_settings_field(
            'heading_font_family',
            'Heading Font Family',
            array($this, 'heading_font_family_callback'),
            'ross-theme-general',
            'ross_general_typography_section'
        );
        
        add_settings_field(
            'heading_font_weight',
            'Heading Font Weight',
            array($this, 'heading_font_weight_callback'),
            'ross-theme-general',
            'ross_general_typography_section'
        );
    }
    
    private function add_buttons_section() {
        add_settings_section(
            'ross_general_buttons_section',
            '🔘 Buttons',
            array($this, 'buttons_section_callback'),
            'ross-theme-general'
        );
        
        add_settings_field(
            'button_shape',
            'Button Shape',
            array($this, 'button_shape_callback'),
            'ross-theme-general',
            'ross_general_buttons_section'
        );
        
        add_settings_field(
            'button_padding',
            'Button Padding (px)',
            array($this, 'button_padding_callback'),
            'ross-theme-general',
            'ross_general_buttons_section'
        );
        
        add_settings_field(
            'button_bg_color',
            'Button Background Color',
            array($this, 'button_bg_color_callback'),
            'ross-theme-general',
            'ross_general_buttons_section'
        );
        
        add_settings_field(
            'button_text_color',
            'Button Text Color',
            array($this, 'button_text_color_callback'),
            'ross-theme-general',
            'ross_general_buttons_section'
        );
        
        add_settings_field(
            'button_border_radius',
            'Button Border Radius (px)',
            array($this, 'button_border_radius_callback'),
            'ross-theme-general',
            'ross_general_buttons_section'
        );
    }
    
    private function add_media_section() {
        add_settings_section(
            'ross_general_media_section',
            '📸 Logo & Media',
            array($this, 'media_section_callback'),
            'ross-theme-general'
        );
        
        add_settings_field(
            'favicon',
            'Upload Favicon',
            array($this, 'favicon_callback'),
            'ross-theme-general',
            'ross_general_media_section'
        );
        
        add_settings_field(
            'default_logo',
            'Upload Default Logo',
            array($this, 'default_logo_callback'),
            'ross-theme-general',
            'ross_general_media_section'
        );
        
        add_settings_field(
            'dark_mode_logo',
            'Upload Dark Mode Logo',
            array($this, 'dark_mode_logo_callback'),
            'ross-theme-general',
            'ross_general_media_section'
        );
        
        add_settings_field(
            'lazy_load_images',
            'Lazy Load Images',
            array($this, 'lazy_load_images_callback'),
            'ross-theme-general',
            'ross_general_media_section'
        );
    }
    
    private function add_blog_section() {
        add_settings_section(
            'ross_general_blog_section',
            '📰 Blog Settings',
            array($this, 'blog_section_callback'),
            'ross-theme-general'
        );
        
        add_settings_field(
            'blog_layout',
            'Blog Layout',
            array($this, 'blog_layout_callback'),
            'ross-theme-general',
            'ross_general_blog_section'
        );
        
        add_settings_field(
            'excerpt_length',
            'Excerpt Length (words)',
            array($this, 'excerpt_length_callback'),
            'ross-theme-general',
            'ross_general_blog_section'
        );
        
        add_settings_field(
            'show_meta',
            'Show Post Meta',
            array($this, 'show_meta_callback'),
            'ross-theme-general',
            'ross_general_blog_section'
        );
        
        add_settings_field(
            'read_more_text',
            'Read More Text',
            array($this, 'read_more_text_callback'),
            'ross-theme-general',
            'ross_general_blog_section'
        );
    }
    
    private function add_performance_section() {
        add_settings_section(
            'ross_general_performance_section',
            '⚡ Performance',
            array($this, 'performance_section_callback'),
            'ross-theme-general'
        );
        
        add_settings_field(
            'minify_css_js',
            'Minify CSS/JS',
            array($this, 'minify_css_js_callback'),
            'ross-theme-general',
            'ross_general_performance_section'
        );
        
        add_settings_field(
            'remove_emojis',
            'Remove Emojis',
            array($this, 'remove_emojis_callback'),
            'ross-theme-general',
            'ross_general_performance_section'
        );
        
        add_settings_field(
            'defer_js_loading',
            'Defer JS Loading',
            array($this, 'defer_js_loading_callback'),
            'ross-theme-general',
            'ross_general_performance_section'
        );
    }
    
    private function add_custom_section() {
        add_settings_section(
            'ross_general_custom_section',
            '🧰 Custom Uploads / Scripts',
            array($this, 'custom_section_callback'),
            'ross-theme-general'
        );
        
        add_settings_field(
            'custom_css',
            'Custom CSS',
            array($this, 'custom_css_callback'),
            'ross-theme-general',
            'ross_general_custom_section'
        );
        
        add_settings_field(
            'custom_js',
            'Custom JavaScript',
            array($this, 'custom_js_callback'),
            'ross-theme-general',
            'ross_general_custom_section'
        );
        
        add_settings_field(
            'google_analytics',
            'Google Analytics Code',
            array($this, 'google_analytics_callback'),
            'ross-theme-general',
            'ross_general_custom_section'
        );
    }
    
    // Section Callbacks
    public function layout_section_callback() {
        echo '<p>Control the overall site layout and structure.</p>';
    }
    
    public function colors_section_callback() {
        echo '<p>Define your color scheme and branding colors.</p>';
    }
    
    public function typography_section_callback() {
        echo '<p>Configure fonts and typography across your site.</p>';
    }
    
    public function buttons_section_callback() {
        echo '<p>Customize button styles and appearance.</p>';
    }
    
    public function media_section_callback() {
        echo '<p>Manage logos, favicons, and media settings.</p>';
    }
    
    public function blog_section_callback() {
        echo '<p>Configure blog and post display settings.</p>';
    }
    
    public function performance_section_callback() {
        echo '<p>Optimize your site performance and speed.</p>';
    }
    
    public function custom_section_callback() {
        echo '<p>Add custom code and tracking scripts.</p>';
    }
    
    // Field Callbacks - Layout Section
    public function layout_style_callback() {
        $value = isset($this->options['layout_style']) ? $this->options['layout_style'] : 'full-width';
        ?>
        <select name="ross_theme_general_options[layout_style]" id="layout_style">
            <option value="full-width" <?php selected($value, 'full-width'); ?>>Full Width</option>
            <option value="boxed" <?php selected($value, 'boxed'); ?>>Boxed</option>
        </select>
        <?php
    }
    
    public function container_width_callback() {
        $value = isset($this->options['container_width']) ? $this->options['container_width'] : '1200';
        ?>
        <input type="number" name="ross_theme_general_options[container_width]" value="<?php echo esc_attr($value); ?>" class="small-text" /> px
        <?php
    }
    
    public function content_spacing_callback() {
        $value = isset($this->options['content_spacing']) ? $this->options['content_spacing'] : '40';
        ?>
        <input type="number" name="ross_theme_general_options[content_spacing]" value="<?php echo esc_attr($value); ?>" class="small-text" /> px
        <?php
    }
    
    public function global_border_radius_callback() {
        $value = isset($this->options['global_border_radius']) ? $this->options['global_border_radius'] : '4';
        ?>
        <input type="number" name="ross_theme_general_options[global_border_radius]" value="<?php echo esc_attr($value); ?>" class="small-text" /> px
        <?php
    }
    
    public function enable_preloader_callback() {
        $value = isset($this->options['enable_preloader']) ? $this->options['enable_preloader'] : 0;
        ?>
        <input type="checkbox" name="ross_theme_general_options[enable_preloader]" value="1" <?php checked(1, $value); ?> />
        <label for="enable_preloader">Enable page preloader animation</label>
        <?php
    }
    
    // Field Callbacks - Colors Section
    public function primary_color_callback() {
        $value = isset($this->options['primary_color']) ? $this->options['primary_color'] : '#001946';
        ?>
        <input type="text" name="ross_theme_general_options[primary_color]" value="<?php echo esc_attr($value); ?>" class="color-picker" data-default-color="#001946" />
        <?php
    }
    
    public function secondary_color_callback() {
        $value = isset($this->options['secondary_color']) ? $this->options['secondary_color'] : '#E5C902';
        ?>
        <input type="text" name="ross_theme_general_options[secondary_color]" value="<?php echo esc_attr($value); ?>" class="color-picker" data-default-color="#E5C902" />
        <?php
    }
    
    public function accent_color_callback() {
        $value = isset($this->options['accent_color']) ? $this->options['accent_color'] : '#0073aa';
        ?>
        <input type="text" name="ross_theme_general_options[accent_color]" value="<?php echo esc_attr($value); ?>" class="color-picker" data-default-color="#0073aa" />
        <?php
    }
    
    public function background_color_callback() {
        $value = isset($this->options['background_color']) ? $this->options['background_color'] : '#ffffff';
        ?>
        <input type="text" name="ross_theme_general_options[background_color]" value="<?php echo esc_attr($value); ?>" class="color-picker" data-default-color="#ffffff" />
        <?php
    }
    
    public function heading_color_callback() {
        $value = isset($this->options['heading_color']) ? $this->options['heading_color'] : '#001946';
        ?>
        <input type="text" name="ross_theme_general_options[heading_color]" value="<?php echo esc_attr($value); ?>" class="color-picker" data-default-color="#001946" />
        <?php
    }
    
    public function text_color_callback() {
        $value = isset($this->options['text_color']) ? $this->options['text_color'] : '#333333';
        ?>
        <input type="text" name="ross_theme_general_options[text_color]" value="<?php echo esc_attr($value); ?>" class="color-picker" data-default-color="#333333" />
        <?php
    }
    
    // Field Callbacks - Typography Section
    public function base_font_family_callback() {
        $value = isset($this->options['base_font_family']) ? $this->options['base_font_family'] : 'Raleway';
        ?>
        <select name="ross_theme_general_options[base_font_family]" id="base_font_family">
            <option value="Raleway" <?php selected($value, 'Raleway'); ?>>Raleway</option>
            <option value="Poppins" <?php selected($value, 'Poppins'); ?>>Poppins</option>
            <option value="Open Sans" <?php selected($value, 'Open Sans'); ?>>Open Sans</option>
            <option value="Montserrat" <?php selected($value, 'Montserrat'); ?>>Montserrat</option>
            <option value="Lato" <?php selected($value, 'Lato'); ?>>Lato</option>
            <option value="Roboto" <?php selected($value, 'Roboto'); ?>>Roboto</option>
        </select>
        <?php
    }
    
    public function base_font_size_callback() {
        $value = isset($this->options['base_font_size']) ? $this->options['base_font_size'] : '16';
        ?>
        <input type="number" name="ross_theme_general_options[base_font_size]" value="<?php echo esc_attr($value); ?>" class="small-text" /> px
        <?php
    }
    
    public function base_line_height_callback() {
        $value = isset($this->options['base_line_height']) ? $this->options['base_line_height'] : '1.6';
        ?>
        <input type="number" name="ross_theme_general_options[base_line_height]" value="<?php echo esc_attr($value); ?>" step="0.1" min="1" max="2" class="small-text" />
        <?php
    }
    
    public function heading_font_family_callback() {
        $value = isset($this->options['heading_font_family']) ? $this->options['heading_font_family'] : 'Raleway';
        ?>
        <select name="ross_theme_general_options[heading_font_family]" id="heading_font_family">
            <option value="Raleway" <?php selected($value, 'Raleway'); ?>>Raleway</option>
            <option value="Poppins" <?php selected($value, 'Poppins'); ?>>Poppins</option>
            <option value="Montserrat" <?php selected($value, 'Montserrat'); ?>>Montserrat</option>
            <option value="Open Sans" <?php selected($value, 'Open Sans'); ?>>Open Sans</option>
        </select>
        <?php
    }
    
    public function heading_font_weight_callback() {
        $value = isset($this->options['heading_font_weight']) ? $this->options['heading_font_weight'] : '600';
        ?>
        <select name="ross_theme_general_options[heading_font_weight]" id="heading_font_weight">
            <option value="300" <?php selected($value, '300'); ?>>300 (Light)</option>
            <option value="400" <?php selected($value, '400'); ?>>400 (Regular)</option>
            <option value="500" <?php selected($value, '500'); ?>>500 (Medium)</option>
            <option value="600" <?php selected($value, '600'); ?>>600 (Semi Bold)</option>
            <option value="700" <?php selected($value, '700'); ?>>700 (Bold)</option>
        </select>
        <?php
    }
    
    // Field Callbacks - Buttons Section
    public function button_shape_callback() {
        $value = isset($this->options['button_shape']) ? $this->options['button_shape'] : 'rounded';
        ?>
        <select name="ross_theme_general_options[button_shape]" id="button_shape">
            <option value="square" <?php selected($value, 'square'); ?>>Square</option>
            <option value="rounded" <?php selected($value, 'rounded'); ?>>Rounded</option>
            <option value="pill" <?php selected($value, 'pill'); ?>>Pill</option>
        </select>
        <?php
    }
    
    public function button_padding_callback() {
        $value = isset($this->options['button_padding']) ? $this->options['button_padding'] : '12';
        ?>
        <input type="number" name="ross_theme_general_options[button_padding]" value="<?php echo esc_attr($value); ?>" class="small-text" /> px
        <?php
    }
    
    public function button_bg_color_callback() {
        $value = isset($this->options['button_bg_color']) ? $this->options['button_bg_color'] : '#E5C902';
        ?>
        <input type="text" name="ross_theme_general_options[button_bg_color]" value="<?php echo esc_attr($value); ?>" class="color-picker" data-default-color="#E5C902" />
        <?php
    }
    
    public function button_text_color_callback() {
        $value = isset($this->options['button_text_color']) ? $this->options['button_text_color'] : '#001946';
        ?>
        <input type="text" name="ross_theme_general_options[button_text_color]" value="<?php echo esc_attr($value); ?>" class="color-picker" data-default-color="#001946" />
        <?php
    }
    
    public function button_border_radius_callback() {
        $value = isset($this->options['button_border_radius']) ? $this->options['button_border_radius'] : '4';
        ?>
        <input type="number" name="ross_theme_general_options[button_border_radius]" value="<?php echo esc_attr($value); ?>" class="small-text" /> px
        <?php
    }
    
    // Field Callbacks - Media Section
    public function favicon_callback() {
        $value = isset($this->options['favicon']) ? $this->options['favicon'] : '';
        ?>
        <input type="text" name="ross_theme_general_options[favicon]" id="favicon" value="<?php echo esc_url($value); ?>" class="regular-text" />
        <input type="button" class="button ross-upload-button" data-target="favicon" value="Upload Favicon" />
        <?php if ($value): ?>
            <p><img src="<?php echo esc_url($value); ?>" style="max-width: 32px; height: auto; margin-top: 10px;" /></p>
        <?php endif; ?>
        <?php
    }
    
    public function default_logo_callback() {
        $value = isset($this->options['default_logo']) ? $this->options['default_logo'] : '';
        ?>
        <input type="text" name="ross_theme_general_options[default_logo]" id="default_logo" value="<?php echo esc_url($value); ?>" class="regular-text" />
        <input type="button" class="button ross-upload-button" data-target="default_logo" value="Upload Default Logo" />
        <?php if ($value): ?>
            <p><img src="<?php echo esc_url($value); ?>" style="max-width: 200px; height: auto; margin-top: 10px;" /></p>
        <?php endif; ?>
        <?php
    }
    
    public function dark_mode_logo_callback() {
        $value = isset($this->options['dark_mode_logo']) ? $this->options['dark_mode_logo'] : '';
        ?>
        <input type="text" name="ross_theme_general_options[dark_mode_logo]" id="dark_mode_logo" value="<?php echo esc_url($value); ?>" class="regular-text" />
        <input type="button" class="button ross-upload-button" data-target="dark_mode_logo" value="Upload Dark Mode Logo" />
        <?php if ($value): ?>
            <p><img src="<?php echo esc_url($value); ?>" style="max-width: 200px; height: auto; margin-top: 10px;" /></p>
        <?php endif; ?>
        <?php
    }
    
    public function lazy_load_images_callback() {
        $value = isset($this->options['lazy_load_images']) ? $this->options['lazy_load_images'] : 1;
        ?>
        <input type="checkbox" name="ross_theme_general_options[lazy_load_images]" value="1" <?php checked(1, $value); ?> />
        <label for="lazy_load_images">Enable lazy loading for images</label>
        <?php
    }
    
    // Field Callbacks - Blog Section
    public function blog_layout_callback() {
        $value = isset($this->options['blog_layout']) ? $this->options['blog_layout'] : 'grid';
        ?>
        <select name="ross_theme_general_options[blog_layout]" id="blog_layout">
            <option value="grid" <?php selected($value, 'grid'); ?>>Grid</option>
            <option value="list" <?php selected($value, 'list'); ?>>List</option>
            <option value="masonry" <?php selected($value, 'masonry'); ?>>Masonry</option>
        </select>
        <?php
    }
    
    public function excerpt_length_callback() {
        $value = isset($this->options['excerpt_length']) ? $this->options['excerpt_length'] : '55';
        ?>
        <input type="number" name="ross_theme_general_options[excerpt_length]" value="<?php echo esc_attr($value); ?>" class="small-text" /> words
        <?php
    }
    
    public function show_meta_callback() {
        $value = isset($this->options['show_meta']) ? $this->options['show_meta'] : 1;
        ?>
        <input type="checkbox" name="ross_theme_general_options[show_meta]" value="1" <?php checked(1, $value); ?> />
        <label for="show_meta">Show author, date, and tags on posts</label>
        <?php
    }
    
    public function read_more_text_callback() {
        $value = isset($this->options['read_more_text']) ? $this->options['read_more_text'] : 'Read More';
        ?>
        <input type="text" name="ross_theme_general_options[read_more_text]" value="<?php echo esc_attr($value); ?>" class="regular-text" />
        <?php
    }
    
    // Field Callbacks - Performance Section
    public function minify_css_js_callback() {
        $value = isset($this->options['minify_css_js']) ? $this->options['minify_css_js'] : 0;
        ?>
        <input type="checkbox" name="ross_theme_general_options[minify_css_js]" value="1" <?php checked(1, $value); ?> />
        <label for="minify_css_js">Minify CSS and JavaScript files</label>
        <?php
    }
    
    public function remove_emojis_callback() {
        $value = isset($this->options['remove_emojis']) ? $this->options['remove_emojis'] : 0;
        ?>
        <input type="checkbox" name="ross_theme_general_options[remove_emojis]" value="1" <?php checked(1, $value); ?> />
        <label for="remove_emojis">Remove WordPress emoji scripts</label>
        <?php
    }
    
    public function defer_js_loading_callback() {
        $value = isset($this->options['defer_js_loading']) ? $this->options['defer_js_loading'] : 0;
        ?>
        <input type="checkbox" name="ross_theme_general_options[defer_js_loading]" value="1" <?php checked(1, $value); ?> />
        <label for="defer_js_loading">Defer JavaScript loading</label>
        <?php
    }
    
    // Field Callbacks - Custom Section
    public function custom_css_callback() {
        $value = isset($this->options['custom_css']) ? $this->options['custom_css'] : '';
        ?>
        <textarea name="ross_theme_general_options[custom_css]" rows="10" class="large-text code"><?php echo esc_textarea($value); ?></textarea>
        <p class="description">Add custom CSS code here</p>
        <?php
    }
    
    public function custom_js_callback() {
        $value = isset($this->options['custom_js']) ? $this->options['custom_js'] : '';
        ?>
        <textarea name="ross_theme_general_options[custom_js]" rows="10" class="large-text code"><?php echo esc_textarea($value); ?></textarea>
        <p class="description">Add custom JavaScript code here</p>
        <?php
    }
    
    public function google_analytics_callback() {
        $value = isset($this->options['google_analytics']) ? $this->options['google_analytics'] : '';
        ?>
        <textarea name="ross_theme_general_options[google_analytics]" rows="5" class="large-text code" placeholder="Paste your Google Analytics tracking code here"><?php echo esc_textarea($value); ?></textarea>
        <p class="description">Add Google Analytics or other tracking codes</p>
        <?php
    }
    
    // Sanitization
    public function sanitize_general_options($input) {
        $sanitized = array();
        
        // Layout
        $sanitized['layout_style'] = sanitize_text_field($input['layout_style']);
        $sanitized['container_width'] = absint($input['container_width']);
        $sanitized['content_spacing'] = absint($input['content_spacing']);
        $sanitized['global_border_radius'] = absint($input['global_border_radius']);
        $sanitized['enable_preloader'] = isset($input['enable_preloader']) ? 1 : 0;
        
        // Colors
        $sanitized['primary_color'] = sanitize_hex_color($input['primary_color']);
        $sanitized['secondary_color'] = sanitize_hex_color($input['secondary_color']);
        $sanitized['accent_color'] = sanitize_hex_color($input['accent_color']);
        $sanitized['background_color'] = sanitize_hex_color($input['background_color']);
        $sanitized['heading_color'] = sanitize_hex_color($input['heading_color']);
        $sanitized['text_color'] = sanitize_hex_color($input['text_color']);
        
        // Typography
        $sanitized['base_font_family'] = sanitize_text_field($input['base_font_family']);
        $sanitized['base_font_size'] = absint($input['base_font_size']);
        $sanitized['base_line_height'] = floatval($input['base_line_height']);
        $sanitized['heading_font_family'] = sanitize_text_field($input['heading_font_family']);
        $sanitized['heading_font_weight'] = sanitize_text_field($input['heading_font_weight']);
        
        // Buttons
        $sanitized['button_shape'] = sanitize_text_field($input['button_shape']);
        $sanitized['button_padding'] = absint($input['button_padding']);
        $sanitized['button_bg_color'] = sanitize_hex_color($input['button_bg_color']);
        $sanitized['button_text_color'] = sanitize_hex_color($input['button_text_color']);
        $sanitized['button_border_radius'] = absint($input['button_border_radius']);
        
        // Media
        $sanitized['favicon'] = esc_url_raw($input['favicon']);
        $sanitized['default_logo'] = esc_url_raw($input['default_logo']);
        $sanitized['dark_mode_logo'] = esc_url_raw($input['dark_mode_logo']);
        $sanitized['lazy_load_images'] = isset($input['lazy_load_images']) ? 1 : 0;
        
        // Blog
        $sanitized['blog_layout'] = sanitize_text_field($input['blog_layout']);
        $sanitized['excerpt_length'] = absint($input['excerpt_length']);
        $sanitized['show_meta'] = isset($input['show_meta']) ? 1 : 0;
        $sanitized['read_more_text'] = sanitize_text_field($input['read_more_text']);
        
        // Performance
        $sanitized['minify_css_js'] = isset($input['minify_css_js']) ? 1 : 0;
        $sanitized['remove_emojis'] = isset($input['remove_emojis']) ? 1 : 0;
        $sanitized['defer_js_loading'] = isset($input['defer_js_loading']) ? 1 : 0;
        
        // Custom
        $sanitized['custom_css'] = wp_kses_post($input['custom_css']);
        $sanitized['custom_js'] = wp_kses_post($input['custom_js']);
        $sanitized['google_analytics'] = wp_kses_post($input['google_analytics']);
        
        return $sanitized;
    }
}

// Initialize
if (is_admin()) {
    new RossGeneralOptions();
}