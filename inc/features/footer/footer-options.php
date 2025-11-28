<?php
/**
 * Footer Options Module
 * Controls everything visible in the site footer
 */

class RossFooterOptions {
    
    private $options;
    
    public function __construct() {
        $this->options = get_option('ross_theme_footer_options');
        // Run a quick migration for legacy template keys before registering settings
        add_action('admin_init', array($this, 'migrate_legacy_template_keys'), 5);
        add_action('admin_init', array($this, 'ensure_default_template_options'), 6);
        add_action('admin_init', array($this, 'register_footer_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_footer_scripts'));
        add_action('wp_ajax_ross_apply_footer_template', array($this, 'ajax_apply_footer_template'));
        add_action('wp_ajax_ross_restore_footer_backup', array($this, 'ajax_restore_footer_backup'));
        add_action('wp_ajax_ross_delete_footer_backup', array($this, 'ajax_delete_footer_backup'));
        add_action('wp_ajax_ross_list_footer_backups', array($this, 'ajax_list_footer_backups'));
        add_action('wp_ajax_ross_get_footer_template_preview', array($this, 'ajax_get_footer_template_preview'));
    }

    /**
     * Migrate legacy per-template option keys saved under names like
     * 'template_template1_bg' into the new keys 'template1_bg', etc.
     * This runs once on admin_init and updates the stored option if needed.
     */
    public function migrate_legacy_template_keys() {
        if (!is_admin()) return;

        $opts = get_option('ross_theme_footer_options', array());
        if (empty($opts) || !is_array($opts)) return;

        $changed = false;
        for ($i = 1; $i <= 4; $i++) {
            $legacy_prefix = 'template_template' . $i . '_';
            $new_prefix = 'template' . $i . '_';
            $keys = array('bg', 'text', 'accent', 'social');
            foreach ($keys as $k) {
                $legacy = $legacy_prefix . $k;
                $new = $new_prefix . $k;
                if (isset($opts[$legacy])) {
                    // If new key empty, copy value; otherwise drop legacy key to avoid confusion
                    if (empty($opts[$new]) && $opts[$new] !== '0') {
                        $opts[$new] = $opts[$legacy];
                    }
                    unset($opts[$legacy]);
                    $changed = true;
                }
            }
        }

        if ($changed) {
            update_option('ross_theme_footer_options', $opts);
            // Refresh local copy for current request
            $this->options = $opts;
        }
    }

    /** Ensure that default footer templates exist in stored options (for customization or persistence) */
    public function ensure_default_template_options() {
        if (!is_admin()) return;
        $templates = get_option('ross_theme_footer_templates', array());
        if (!is_array($templates) || empty($templates)) {
            $default = array(
                'template1' => array('title' => 'Business Professional', 'cols' => array('About Us|We provide finance and accounting services to small businesses.', 'Services|Auditing, Tax, Advisory', 'Resources|Blog, Guides, FAQs', 'Contact|1 Example Street, City — info@example.com'), 'bg' => '#f8f9fb'),
                'template2' => array('title' => 'E-commerce', 'cols' => array('Shop|All Products, New Arrivals, Sale', 'Customer Service|Shipping, Returns, FAQ', 'Company|About, Careers, Press', 'Subscribe|Join our newsletter'), 'bg' => '#fff'),
                'template3' => array('title' => 'Creative Agency', 'cols' => array('Who We Are|Design-led agency crafting beautiful experiences.', 'Work|Case Studies, Clients', 'Services|Branding, UX/UI', 'Contact|hello@agency.example'), 'bg' => '#111'),
                'template4' => array('title' => 'Minimal Modern', 'cols' => array('Company|About, Contact', 'Explore|Features, Pricing', 'Resources|Docs, API', 'Follow|Social links'), 'bg' => '#fafafa'),
            );
            update_option('ross_theme_footer_templates', $default);
            $templates = $default;
        }
        return $templates;
    }

    /** Return templates array - stored override falls back to default hardcoded list */
    private function get_templates() {
        $stored = get_option('ross_theme_footer_templates', array());
        if (is_array($stored) && !empty($stored)) return $stored;

        // default templates (same as earlier sample list)
        return array(
            'template1' => array('title' => 'Business Professional', 'cols' => array('About Us|We provide finance and accounting services to small businesses.', 'Services|Auditing, Tax, Advisory', 'Resources|Blog, Guides, FAQs', 'Contact|1 Example Street, City — info@example.com'), 'bg' => '#f8f9fb'),
            'template2' => array('title' => 'E-commerce', 'cols' => array('Shop|All Products, New Arrivals, Sale', 'Customer Service|Shipping, Returns, FAQ', 'Company|About, Careers, Press', 'Subscribe|Join our newsletter'), 'bg' => '#fff'),
            'template3' => array('title' => 'Creative Agency', 'cols' => array('Who We Are|Design-led agency crafting beautiful experiences.', 'Work|Case Studies, Clients', 'Services|Branding, UX/UI', 'Contact|hello@agency.example'), 'bg' => '#111'),
            'template4' => array('title' => 'Minimal Modern', 'cols' => array('Company|About, Contact', 'Explore|Features, Pricing', 'Resources|Docs, API', 'Follow|Social links'), 'bg' => '#fafafa'),
        );
    }
    
    public function enqueue_footer_scripts($hook) {
        // Enqueue footer admin scripts when on the Footer Options page.
        // The $hook value differs for top-level vs submenu pages, so check by presence
        // of the page slug as well as the GET param for robustness.
        $is_footer_page = false;
        if (is_string($hook) && strpos($hook, 'ross-theme-footer') !== false) {
            $is_footer_page = true;
        }
        if (!$is_footer_page && isset($_GET['page']) && $_GET['page'] === 'ross-theme-footer') {
            $is_footer_page = true;
        }

        if ($is_footer_page) {
            wp_enqueue_style('wp-color-picker');
            wp_enqueue_script('wp-color-picker');
            // Media uploader for background image field
            wp_enqueue_media();
            wp_enqueue_script('ross-footer-admin', get_template_directory_uri() . '/assets/js/admin/footer-options.js', array('jquery', 'wp-color-picker'), '1.0.1', true);
            // Admin UI styling for footer options
            wp_enqueue_style('ross-footer-admin-css', get_template_directory_uri() . '/assets/css/admin/footer-styling-admin.css', array(), '1.0.0');
            wp_localize_script('ross-footer-admin', 'rossFooterAdmin', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('ross_apply_footer_template'),
                'site_url' => home_url('/'),
                'site_name' => get_bloginfo('name'),
                'widgets_url' => admin_url('widgets.php')
            ));
            // enqueue preview CSS
            wp_enqueue_style('ross-footer-preview-css', get_template_directory_uri() . '/assets/css/admin/footer-preview.css', array(), '1.0.0');
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
        $this->add_styling_section();
        $this->add_cta_section();
        $this->add_social_section();
        $this->add_copyright_section();
    }

    private function add_styling_section() {
        add_settings_section(
            'ross_footer_styling_section',
            '🎨 Footer Styling',
            array($this, 'styling_section_callback'),
            'ross-theme-footer-styling'
        );

        // SECTION 1: Background
        add_settings_field(
            'styling_bg_color',
            'Background Color',
            array($this, 'styling_bg_color_callback'),
            'ross-theme-footer-styling',
            'ross_footer_styling_section'
        );

        add_settings_field(
            'styling_bg_gradient',
            'Background Gradient (enable)',
            array($this, 'styling_bg_gradient_callback'),
            'ross-theme-footer-styling',
            'ross_footer_styling_section'
        );

        add_settings_field(
            'styling_bg_image',
            'Background Image (URL)',
            array($this, 'styling_bg_image_callback'),
            'ross-theme-footer-styling',
            'ross_footer_styling_section'
        );

        // Background type selector (color | image | gradient)
        add_settings_field(
            'styling_bg_type',
            'Background Type',
            array($this, 'styling_bg_type_callback'),
            'ross-theme-footer-styling',
            'ross_footer_styling_section'
        );

        // Overlay controls
        add_settings_field(
            'styling_overlay_enabled',
            'Enable Background Overlay',
            array($this, 'styling_overlay_enabled_callback'),
            'ross-theme-footer-styling',
            'ross_footer_styling_section'
        );

        add_settings_field(
            'styling_overlay_type',
            'Overlay Type',
            array($this, 'styling_overlay_type_callback'),
            'ross-theme-footer-styling',
            'ross_footer_styling_section'
        );

        // Overlay color, image, gradient
        add_settings_field(
            'styling_overlay_color',
            'Overlay Color',
            array($this, 'styling_overlay_color_callback'),
            'ross-theme-footer-styling',
            'ross_footer_styling_section'
        );
        add_settings_field(
            'styling_overlay_image',
            'Overlay Image (URL)',
            array($this, 'styling_overlay_image_callback'),
            'ross-theme-footer-styling',
            'ross_footer_styling_section'
        );
        add_settings_field(
            'styling_overlay_gradient_from',
            'Overlay Gradient - From',
            array($this, 'styling_overlay_gradient_from_callback'),
            'ross-theme-footer-styling',
            'ross_footer_styling_section'
        );
        add_settings_field(
            'styling_overlay_gradient_to',
            'Overlay Gradient - To',
            array($this, 'styling_overlay_gradient_to_callback'),
            'ross-theme-footer-styling',
            'ross_footer_styling_section'
        );
        add_settings_field(
            'styling_overlay_opacity',
            'Overlay Opacity',
            array($this, 'styling_overlay_opacity_callback'),
            'ross-theme-footer-styling',
            'ross_footer_styling_section'
        );

        add_settings_field(
            'styling_bg_gradient_from',
            'Background Gradient - From',
            array($this, 'styling_bg_gradient_from_callback'),
            'ross-theme-footer-styling',
            'ross_footer_styling_section'
        );

        add_settings_field(
            'styling_bg_gradient_to',
            'Background Gradient - To',
            array($this, 'styling_bg_gradient_to_callback'),
            'ross-theme-footer-styling',
            'ross_footer_styling_section'
        );

        add_settings_field(
            'styling_bg_opacity',
            'Background Opacity (0-1)',
            array($this, 'styling_bg_opacity_callback'),
            'ross-theme-footer-styling',
            'ross_footer_styling_section'
        );

        // SECTION 2: Text & Links
        add_settings_field('styling_text_color','Text Color',array($this,'styling_text_color_callback'),'ross-theme-footer-styling','ross_footer_styling_section');
        add_settings_field('styling_link_color','Link Color',array($this,'styling_link_color_callback'),'ross-theme-footer-styling','ross_footer_styling_section');
        add_settings_field('styling_link_hover','Link Hover Color',array($this,'styling_link_hover_callback'),'ross-theme-footer-styling','ross_footer_styling_section');

        // SECTION 3: Typography
        add_settings_field('styling_font_size','Font Size (px)',array($this,'styling_font_size_callback'),'ross-theme-footer-styling','ross_footer_styling_section');
        add_settings_field('styling_line_height','Line Height',array($this,'styling_line_height_callback'),'ross-theme-footer-styling','ross_footer_styling_section');

        // SECTION 4: Spacing
        add_settings_field('styling_col_gap','Column Gap (px)',array($this,'styling_col_gap_callback'),'ross-theme-footer-styling','ross_footer_styling_section');
        add_settings_field('styling_row_gap','Row Gap (px)',array($this,'styling_row_gap_callback'),'ross-theme-footer-styling','ross_footer_styling_section');
        add_settings_field('styling_padding_lr','Padding Left / Right (px)',array($this,'styling_padding_lr_callback'),'ross-theme-footer-styling','ross_footer_styling_section');
        add_settings_field('styling_padding_top','Padding Top (px)',array($this,'styling_padding_top_callback'),'ross-theme-footer-styling','ross_footer_styling_section');
        add_settings_field('styling_padding_bottom','Padding Bottom (px)',array($this,'styling_padding_bottom_callback'),'ross-theme-footer-styling','ross_footer_styling_section');
        add_settings_field('styling_padding_left','Padding Left (px)',array($this,'styling_padding_left_callback'),'ross-theme-footer-styling','ross_footer_styling_section');
        add_settings_field('styling_padding_right','Padding Right (px)',array($this,'styling_padding_right_callback'),'ross-theme-footer-styling','ross_footer_styling_section');

        // SECTION 5: Border
        add_settings_field('styling_border_top','Border Top (enable)',array($this,'styling_border_top_callback'),'ross-theme-footer-styling','ross_footer_styling_section');
        add_settings_field('styling_border_color','Border Color',array($this,'styling_border_color_callback'),'ross-theme-footer-styling','ross_footer_styling_section');
        add_settings_field('styling_border_thickness','Border Thickness (px)',array($this,'styling_border_thickness_callback'),'ross-theme-footer-styling','ross_footer_styling_section');

        // SECTION 6: Widget Styling
        add_settings_field('styling_widget_title_color','Widget Title Color',array($this,'styling_widget_title_color_callback'),'ross-theme-footer-styling','ross_footer_styling_section');
        add_settings_field('styling_widget_title_size','Widget Title Font Size (px)',array($this,'styling_widget_title_size_callback'),'ross-theme-footer-styling','ross_footer_styling_section');
    }

    public function styling_section_callback() {
        echo '<p>Fine-grained visual controls for the footer. These settings affect frontend appearance.</p>';
        // open the grid container for two-column layout
        echo '<div class="ross-footer-styling-grid">';
    }

    // Styling field callbacks
    public function styling_bg_color_callback() {
        $v = isset($this->options['styling_bg_color']) ? $this->options['styling_bg_color'] : '';
        echo '<input type="text" name="ross_theme_footer_options[styling_bg_color]" value="' . esc_attr($v) . '" class="color-picker" />';
    }

    public function styling_bg_gradient_from_callback() {
        $v = isset($this->options['styling_bg_gradient_from']) ? $this->options['styling_bg_gradient_from'] : '';
        echo '<input type="text" name="ross_theme_footer_options[styling_bg_gradient_from]" value="' . esc_attr($v) . '" class="color-picker" />';
    }

    public function styling_bg_gradient_to_callback() {
        $v = isset($this->options['styling_bg_gradient_to']) ? $this->options['styling_bg_gradient_to'] : '';
        echo '<input type="text" name="ross_theme_footer_options[styling_bg_gradient_to]" value="' . esc_attr($v) . '" class="color-picker" />';
    }

    public function styling_bg_gradient_callback() {
        $v = isset($this->options['styling_bg_gradient']) ? $this->options['styling_bg_gradient'] : 0;
        // Modern toggle switch markup
        $checked = checked(1, $v, false);
        echo '<label class="ross-toggle">';
        echo '<input type="checkbox" name="ross_theme_footer_options[styling_bg_gradient]" value="1" ' . $checked . ' />';
        echo '<span class="ross-toggle-slider"></span> Enable gradient (uses two template colors)';
        echo '</label>';
    }

    public function styling_bg_image_callback() {
        $v = isset($this->options['styling_bg_image']) ? $this->options['styling_bg_image'] : '';
        echo '<div class="field">';
        echo '<input type="text" id="ross-styling-bg-image" name="ross_theme_footer_options[styling_bg_image]" value="' . esc_attr($v) . '" class="regular-text" placeholder="https://..." />';
        echo ' <button type="button" class="button ross-upload-button" data-target="ross-styling-bg-image" data-input-name="ross_theme_footer_options[styling_bg_image]">Upload</button>';
        echo ' <button type="button" class="button ross-remove-upload" data-target="ross-styling-bg-image">Remove</button>';
        echo '<input type="hidden" id="ross-styling-bg-image-id" name="ross_theme_footer_options[styling_bg_image_id]" value="' . esc_attr(isset($this->options['styling_bg_image_id']) ? $this->options['styling_bg_image_id'] : '') . '" />';
        echo '&nbsp;<span id="ross-styling-bg-image-preview">';
        if (!empty($v)) {
            echo '<img src="' . esc_url($v) . '" style="max-height:40px;vertical-align:middle;border:1px solid #ddd;padding:2px;" />';
        }
        echo '</span>';
        echo '</div>';
    }

    public function styling_bg_type_callback() {
        $v = isset($this->options['styling_bg_type']) ? $this->options['styling_bg_type'] : 'color';
        ?>
        <select name="ross_theme_footer_options[styling_bg_type]" id="styling_bg_type">
            <option value="color" <?php selected($v,'color'); ?>>Color</option>
            <option value="image" <?php selected($v,'image'); ?>>Image</option>
            <option value="gradient" <?php selected($v,'gradient'); ?>>Gradient</option>
        </select>
        <?php
    }

    public function styling_overlay_enabled_callback() {
        $v = isset($this->options['styling_overlay_enabled']) ? $this->options['styling_overlay_enabled'] : 0;
        echo '<label class="ross-toggle">';
        echo '<input type="checkbox" name="ross_theme_footer_options[styling_overlay_enabled]" value="1" ' . checked(1, $v, false) . ' />';
        echo '<span class="ross-toggle-slider"></span> Enable Overlay';
        echo '</label>';
    }

    public function styling_overlay_type_callback() {
        $v = isset($this->options['styling_overlay_type']) ? $this->options['styling_overlay_type'] : 'color';
        ?>
        <select name="ross_theme_footer_options[styling_overlay_type]" id="styling_overlay_type">
            <option value="color" <?php selected($v,'color'); ?>>Color</option>
            <option value="image" <?php selected($v,'image'); ?>>Image</option>
            <option value="gradient" <?php selected($v,'gradient'); ?>>Gradient</option>
        </select>
        <?php
    }

    public function styling_overlay_color_callback() {
        $v = isset($this->options['styling_overlay_color']) ? $this->options['styling_overlay_color'] : '';
        echo '<input type="text" name="ross_theme_footer_options[styling_overlay_color]" value="' . esc_attr($v) . '" class="color-picker" />';
    }

    public function styling_overlay_image_callback() {
        $v = isset($this->options['styling_overlay_image']) ? $this->options['styling_overlay_image'] : '';
        echo '<div class="field">';
        echo '<input type="text" id="ross-styling-overlay-image" name="ross_theme_footer_options[styling_overlay_image]" value="' . esc_attr($v) . '" class="regular-text" placeholder="https://..." />';
        echo ' <button type="button" class="button ross-upload-button" data-target="ross-styling-overlay-image" data-input-name="ross_theme_footer_options[styling_overlay_image]">Upload</button>';
        echo ' <button type="button" class="button ross-remove-upload" data-target="ross-styling-overlay-image">Remove</button>';
        echo '<input type="hidden" id="ross-styling-overlay-image-id" name="ross_theme_footer_options[styling_overlay_image_id]" value="' . esc_attr(isset($this->options['styling_overlay_image_id']) ? $this->options['styling_overlay_image_id'] : '') . '" />';
        echo '&nbsp;<span id="ross-styling-overlay-image-preview">';
        if (!empty($v)) {
            echo '<img src="' . esc_url($v) . '" style="max-height:40px;vertical-align:middle;border:1px solid #ddd;padding:2px;" />';
        }
        echo '</span>';
        echo '</div>';
    }

    public function styling_overlay_gradient_from_callback() {
        $v = isset($this->options['styling_overlay_gradient_from']) ? $this->options['styling_overlay_gradient_from'] : '';
        echo '<input type="text" name="ross_theme_footer_options[styling_overlay_gradient_from]" value="' . esc_attr($v) . '" class="color-picker" />';
    }

    public function styling_overlay_gradient_to_callback() {
        $v = isset($this->options['styling_overlay_gradient_to']) ? $this->options['styling_overlay_gradient_to'] : '';
        echo '<input type="text" name="ross_theme_footer_options[styling_overlay_gradient_to]" value="' . esc_attr($v) . '" class="color-picker" />';
    }

    public function styling_overlay_opacity_callback() {
        $v = isset($this->options['styling_overlay_opacity']) ? $this->options['styling_overlay_opacity'] : '0.5';
        echo '<input type="number" step="0.1" min="0" max="1" name="ross_theme_footer_options[styling_overlay_opacity]" value="' . esc_attr($v) . '" class="small-text" />';
    }

    public function styling_bg_opacity_callback() {
        $v = isset($this->options['styling_bg_opacity']) ? $this->options['styling_bg_opacity'] : '1';
        echo '<input type="number" step="0.1" min="0" max="1" name="ross_theme_footer_options[styling_bg_opacity]" value="' . esc_attr($v) . '" class="small-text" />';
    }

    // Padding callbacks
    public function styling_padding_top_callback() {
        $v = isset($this->options['styling_padding_top']) ? $this->options['styling_padding_top'] : '';
        echo '<input type="number" name="ross_theme_footer_options[styling_padding_top]" value="' . esc_attr($v) . '" class="small-text" /> px';
    }

    public function styling_padding_bottom_callback() {
        $v = isset($this->options['styling_padding_bottom']) ? $this->options['styling_padding_bottom'] : '';
        echo '<input type="number" name="ross_theme_footer_options[styling_padding_bottom]" value="' . esc_attr($v) . '" class="small-text" /> px';
    }

    public function styling_padding_left_callback() {
        $v = isset($this->options['styling_padding_left']) ? $this->options['styling_padding_left'] : '';
        echo '<input type="number" name="ross_theme_footer_options[styling_padding_left]" value="' . esc_attr($v) . '" class="small-text" /> px';
    }

    public function styling_padding_right_callback() {
        $v = isset($this->options['styling_padding_right']) ? $this->options['styling_padding_right'] : '';
        echo '<input type="number" name="ross_theme_footer_options[styling_padding_right]" value="' . esc_attr($v) . '" class="small-text" /> px';
    }

    public function styling_text_color_callback() {
        $v = isset($this->options['styling_text_color']) ? $this->options['styling_text_color'] : '';
        echo '<input type="text" name="ross_theme_footer_options[styling_text_color]" value="' . esc_attr($v) . '" class="color-picker" />';
    }

    public function styling_link_color_callback() {
        $v = isset($this->options['styling_link_color']) ? $this->options['styling_link_color'] : '';
        echo '<input type="text" name="ross_theme_footer_options[styling_link_color]" value="' . esc_attr($v) . '" class="color-picker" />';
    }

    public function styling_link_hover_callback() {
        $v = isset($this->options['styling_link_hover']) ? $this->options['styling_link_hover'] : '';
        echo '<input type="text" name="ross_theme_footer_options[styling_link_hover]" value="' . esc_attr($v) . '" class="color-picker" />';
    }

    public function styling_font_size_callback() {
        $v = isset($this->options['styling_font_size']) ? $this->options['styling_font_size'] : '14';
        echo '<input type="number" name="ross_theme_footer_options[styling_font_size]" value="' . esc_attr($v) . '" class="small-text" /> px';
    }

    public function styling_line_height_callback() {
        $v = isset($this->options['styling_line_height']) ? $this->options['styling_line_height'] : '1.6';
        echo '<input type="number" step="0.1" name="ross_theme_footer_options[styling_line_height]" value="' . esc_attr($v) . '" class="small-text" />';
    }

    public function styling_col_gap_callback() {
        $v = isset($this->options['styling_col_gap']) ? $this->options['styling_col_gap'] : '24';
        echo '<input type="number" name="ross_theme_footer_options[styling_col_gap]" value="' . esc_attr($v) . '" class="small-text" /> px';
    }

    public function styling_row_gap_callback() {
        $v = isset($this->options['styling_row_gap']) ? $this->options['styling_row_gap'] : '18';
        echo '<input type="number" name="ross_theme_footer_options[styling_row_gap]" value="' . esc_attr($v) . '" class="small-text" /> px';
    }

    public function styling_padding_lr_callback() {
        $v = isset($this->options['styling_padding_lr']) ? $this->options['styling_padding_lr'] : '20';
        echo '<input type="number" name="ross_theme_footer_options[styling_padding_lr]" value="' . esc_attr($v) . '" class="small-text" /> px';
    }

    public function styling_border_top_callback() {
        $v = isset($this->options['styling_border_top']) ? $this->options['styling_border_top'] : 0;
        $checked = checked(1, $v, false);
        echo '<label class="ross-toggle">';
        echo '<input type="checkbox" name="ross_theme_footer_options[styling_border_top]" value="1" ' . $checked . ' />';
        echo '<span class="ross-toggle-slider"></span> Enable top border';
        echo '</label>';
    }

    public function styling_border_color_callback() {
        $v = isset($this->options['styling_border_color']) ? $this->options['styling_border_color'] : '';
        echo '<input type="text" name="ross_theme_footer_options[styling_border_color]" value="' . esc_attr($v) . '" class="color-picker" />';
    }

    public function styling_border_thickness_callback() {
        $v = isset($this->options['styling_border_thickness']) ? $this->options['styling_border_thickness'] : '1';
        echo '<input type="number" name="ross_theme_footer_options[styling_border_thickness]" value="' . esc_attr($v) . '" class="small-text" /> px';
    }

    public function styling_widget_title_color_callback() {
        $v = isset($this->options['styling_widget_title_color']) ? $this->options['styling_widget_title_color'] : '';
        echo '<input type="text" name="ross_theme_footer_options[styling_widget_title_color]" value="' . esc_attr($v) . '" class="color-picker" />';
    }

    public function styling_widget_title_size_callback() {
        $v = isset($this->options['styling_widget_title_size']) ? $this->options['styling_widget_title_size'] : '16';
        echo '<input type="number" name="ross_theme_footer_options[styling_widget_title_size]" value="' . esc_attr($v) . '" class="small-text" /> px';
        // close the grid container
        echo '</div>';
    }
    
    private function add_layout_section() {
        add_settings_section(
            'ross_footer_layout_section',
            '🧱 Footer Layout',
            array($this, 'layout_section_callback'),
            'ross-theme-footer-layout'
        );
        
        // Footer Style option removed: layout selection handled by theme templates.

        add_settings_field(
            'footer_template',
            'Footer Template',
            array($this, 'footer_template_callback'),
            'ross-theme-footer-layout',
            'ross_footer_layout_section'
        );
        
        add_settings_field(
            'footer_columns',
            'Footer Columns',
            array($this, 'footer_columns_callback'),
            'ross-theme-footer-layout',
            'ross_footer_layout_section'
        );
        
        add_settings_field(
            'footer_width',
            'Footer Width',
            array($this, 'footer_width_callback'),
            'ross-theme-footer-layout',
            'ross_footer_layout_section'
        );
        
        
    }
    
    private function add_widgets_section() {
        // Moved Widgets section into Layout page (was on a separate Widgets tab). This consolidates layout-related controls.
        add_settings_section(
            'ross_footer_widgets_section',
            '🧰 Footer Widgets',
            array($this, 'widgets_section_callback'),
            'ross-theme-footer-layout'
        );
        
        add_settings_field(
            'enable_widgets',
            'Enable Widgets Area',
            array($this, 'enable_widgets_callback'),
            'ross-theme-footer-layout',
            'ross_footer_widgets_section'
        );
        
        add_settings_field(
            'widgets_bg_color',
            'Background Color',
            array($this, 'widgets_bg_color_callback'),
            'ross-theme-footer-layout',
            'ross_footer_widgets_section'
        );
        
        add_settings_field(
            'widgets_text_color',
            'Text Color',
            array($this, 'widgets_text_color_callback'),
            'ross-theme-footer-layout',
            'ross_footer_widgets_section'
        );
        
        add_settings_field(
            'widget_title_color',
            'Widget Title Color',
            array($this, 'widget_title_color_callback'),
            'ross-theme-footer-layout',
            'ross_footer_widgets_section'
        );

        // Template-specific color overrides
        add_settings_field(
            'use_template_colors',
            'Use Template Default Colors',
            array($this, 'use_template_colors_callback'),
            'ross-theme-footer-layout',
            'ross_footer_widgets_section'
        );

        add_settings_field(
            'template_custom_colors',
            'Template Custom Colors',
            array($this, 'template_custom_colors_callback'),
            'ross-theme-footer-layout',
            'ross_footer_widgets_section'
        );
    }
    
    private function add_cta_section() {
        // Create multiple CTA sub-sections (visibility, content, layout, styling, spacing, animation, advanced)
        add_settings_section(
            'ross_footer_cta_visibility',
            'Visibility',
            array($this, 'cta_visibility_section_callback'),
            'ross-theme-footer-cta'
        );
        add_settings_section(
            'ross_footer_cta_content',
            'Content',
            array($this, 'cta_content_section_callback'),
            'ross-theme-footer-cta'
        );
        add_settings_section(
            'ross_footer_cta_layout',
            'Layout',
            array($this, 'cta_layout_section_callback'),
            'ross-theme-footer-cta'
        );
        add_settings_section(
            'ross_footer_cta_styling',
            'Styling',
            array($this, 'cta_styling_section_callback'),
            'ross-theme-footer-cta'
        );
        add_settings_section(
            'ross_footer_cta_spacing',
            'Spacing',
            array($this, 'cta_spacing_section_callback'),
            'ross-theme-footer-cta'
        );
        add_settings_section(
            'ross_footer_cta_animation',
            'Animation',
            array($this, 'cta_animation_section_callback'),
            'ross-theme-footer-cta'
        );
        add_settings_section(
            'ross_footer_cta_advanced',
            'Advanced',
            array($this, 'cta_advanced_section_callback'),
            'ross-theme-footer-cta'
        );
        
        // Visibility
        add_settings_field(
            'enable_footer_cta',
            'Enable CTA Section',
            array($this, 'enable_footer_cta_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_visibility'
        );
        add_settings_field(
            'cta_always_visible',
            'Always Show CTA',
            array($this, 'cta_always_visible_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_visibility'
        );
        add_settings_field(
            'cta_display_on',
            'Display On',
            array($this, 'cta_display_on_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_visibility'
        );

        // Content
        add_settings_field(
            'cta_title',
            'CTA Title',
            array($this, 'cta_title_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_content'
        );
        add_settings_field(
            'cta_text',
            'CTA Subtitle / Text',
            array($this, 'cta_text_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_content'
        );
        add_settings_field(
            'cta_button_text',
            'CTA Button Text',
            array($this, 'cta_button_text_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_content'
        );
        add_settings_field(
            'cta_button_url',
            'Button URL',
            array($this, 'cta_button_url_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_content'
        );
        add_settings_field(
            'cta_icon',
            'CTA Icon',
            array($this, 'cta_icon_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_content'
        );

        // Layout
        add_settings_field(
            'cta_alignment',
            'Alignment',
            array($this, 'cta_alignment_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_layout'
        );
        add_settings_field(
            'cta_layout_direction',
            'Layout Direction',
            array($this, 'cta_layout_direction_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_layout'
        );
        add_settings_field(
            'cta_layout_wrap',
            'Allow Wrap',
            array($this, 'cta_layout_wrap_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_layout'
        );
        add_settings_field(
            'cta_layout_justify',
            'Justify',
            array($this, 'cta_layout_justify_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_layout'
        );
        add_settings_field(
            'cta_layout_align',
            'Align Items',
            array($this, 'cta_layout_align_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_layout'
        );
        add_settings_field(
            'cta_gap',
            'Gap Between Items (px)',
            array($this, 'cta_gap_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_layout'
        );

        // Styling
        add_settings_field(
            'cta_bg_type',
            'Background Type',
            array($this, 'cta_bg_type_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_styling'
        );
        // CTA Overlay controls
        add_settings_field('cta_overlay_enabled','Enable CTA Overlay',array($this,'cta_overlay_enabled_callback'),'ross-theme-footer-cta','ross_footer_cta_styling');
        add_settings_field('cta_overlay_type','Overlay Type',array($this,'cta_overlay_type_callback'),'ross-theme-footer-cta','ross_footer_cta_styling');
        add_settings_field('cta_overlay_color','Overlay Color',array($this,'cta_overlay_color_callback'),'ross-theme-footer-cta','ross_footer_cta_styling');
        add_settings_field('cta_overlay_image','Overlay Image (URL)',array($this,'cta_overlay_image_callback'),'ross-theme-footer-cta','ross_footer_cta_styling');
        add_settings_field('cta_overlay_gradient_from','Overlay Gradient - From',array($this,'cta_overlay_gradient_from_callback'),'ross-theme-footer-cta','ross_footer_cta_styling');
        add_settings_field('cta_overlay_gradient_to','Overlay Gradient - To',array($this,'cta_overlay_gradient_to_callback'),'ross-theme-footer-cta','ross_footer_cta_styling');
        add_settings_field('cta_overlay_opacity','Overlay Opacity (0-1)',array($this,'cta_overlay_opacity_callback'),'ross-theme-footer-cta','ross_footer_cta_styling');
        add_settings_field(
            'cta_bg_color',
            'CTA Background Color',
            array($this, 'cta_bg_color_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_styling'
        );
        add_settings_field(
            'cta_bg_gradient_from',
            'CTA Gradient - From',
            array($this, 'cta_bg_gradient_from_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_styling'
        );
        add_settings_field(
            'cta_bg_gradient_to',
            'CTA Gradient - To',
            array($this, 'cta_bg_gradient_to_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_styling'
        );
        add_settings_field(
            'cta_bg_image',
            'CTA Background Image',
            array($this, 'cta_bg_image_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_styling'
        );
        add_settings_field(
            'cta_text_color',
            'CTA Text Color',
            array($this, 'cta_text_color_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_styling'
        );
        add_settings_field(
            'cta_button_bg_color',
            'CTA Button Background',
            array($this, 'cta_button_bg_color_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_styling'
        );
        add_settings_field(
            'cta_button_text_color',
            'CTA Button Text Color',
            array($this, 'cta_button_text_color_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_styling'
        );
        // Spacing (padding)
        add_settings_field(
            'cta_padding_top',
            'Padding Top (px)',
            array($this, 'cta_padding_top_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_spacing'
        );
        add_settings_field(
            'cta_padding_right',
            'Padding Right (px)',
            array($this, 'cta_padding_right_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_spacing'
        );
        add_settings_field(
            'cta_padding_bottom',
            'Padding Bottom (px)',
            array($this, 'cta_padding_bottom_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_spacing'
        );
        add_settings_field(
            'cta_padding_left',
            'Padding Left (px)',
            array($this, 'cta_padding_left_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_spacing'
        );

        add_settings_field(
            'cta_icon_color',
            'CTA Icon Color',
            array($this, 'cta_icon_color_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_styling'
        );

        // Animation
        add_settings_field(
            'cta_animation',
            'CTA Animation',
            array($this, 'cta_animation_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_animation'
        );
        add_settings_field(
            'cta_anim_delay',
            'Animation Delay (ms)',
            array($this, 'cta_anim_delay_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_animation'
        );
        add_settings_field(
            'cta_anim_duration',
            'Animation Duration (ms)',
            array($this, 'cta_anim_duration_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_animation'
        );

        // Advanced
        add_settings_field(
            'enable_custom_cta',
            'Enable Custom CTA Content',
            array($this, 'enable_custom_cta_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_advanced'
        );
        add_settings_field(
            'custom_cta_html',
            'Custom CTA HTML',
            array($this, 'custom_cta_html_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_advanced'
        );
        add_settings_field(
            'custom_cta_css',
            'Custom CTA CSS',
            array($this, 'custom_cta_css_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_advanced'
        );
        add_settings_field(
            'custom_cta_js',
            'Custom CTA JS',
            array($this, 'custom_cta_js_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_advanced'
        );

        // Extra: Spacing - add margin controls (new)
        add_settings_field('cta_margin_top','Margin Top (px)',array($this,'cta_margin_top_callback'),'ross-theme-footer-cta','ross_footer_cta_spacing');
        add_settings_field('cta_margin_right','Margin Right (px)',array($this,'cta_margin_right_callback'),'ross-theme-footer-cta','ross_footer_cta_spacing');
        add_settings_field('cta_margin_bottom','Margin Bottom (px)',array($this,'cta_margin_bottom_callback'),'ross-theme-footer-cta','ross_footer_cta_spacing');
        add_settings_field('cta_margin_left','Margin Left (px)',array($this,'cta_margin_left_callback'),'ross-theme-footer-cta','ross_footer_cta_spacing');
    }
    
    private function add_social_section() {
        add_settings_section(
            'ross_footer_social_section',
            '🌍 Social Icons',
            array($this, 'social_section_callback'),
            'ross-theme-footer-social'
        );
        
        add_settings_field(
            'enable_social_icons',
            'Enable Social Icons',
            array($this, 'enable_social_icons_callback'),
            'ross-theme-footer-social',
            'ross_footer_social_section'
        );
        
        add_settings_field(
            'facebook_url',
            'Facebook URL',
            array($this, 'facebook_url_callback'),
            'ross-theme-footer-social',
            'ross_footer_social_section'
        );
        
        add_settings_field(
            'linkedin_url',
            'LinkedIn URL',
            array($this, 'linkedin_url_callback'),
            'ross-theme-footer-social',
            'ross_footer_social_section'
        );
        
        add_settings_field(
            'instagram_url',
            'Instagram URL',
            array($this, 'instagram_url_callback'),
            'ross-theme-footer-social',
            'ross_footer_social_section'
        );
        
        add_settings_field(
            'social_icon_color',
            'Icon Color',
            array($this, 'social_icon_color_callback'),
            'ross-theme-footer-social',
            'ross_footer_social_section'
        );
    }
    
    private function add_copyright_section() {
        add_settings_section(
            'ross_footer_copyright_section',
            '🧾 Copyright Bar',
            array($this, 'copyright_section_callback'),
            'ross-theme-footer-copyright'
        );
        // Create sub-sections to organize fields
        add_settings_section(
            'ross_footer_copyright_visibility',
            'Visibility',
            array($this, 'copyright_visibility_section_callback'),
            'ross-theme-footer-copyright'
        );
        add_settings_section(
            'ross_footer_copyright_content',
            'Content',
            array($this, 'copyright_content_section_callback'),
            'ross-theme-footer-copyright'
        );
        add_settings_section(
            'ross_footer_copyright_styling',
            'Styling',
            array($this, 'copyright_styling_section_callback'),
            'ross-theme-footer-copyright'
        );
        add_settings_section(
            'ross_footer_copyright_advanced',
            'Advanced / Custom',
            array($this, 'copyright_advanced_section_callback'),
            'ross-theme-footer-copyright'
        );

        // Visibility
        add_settings_field(
            'enable_copyright',
            'Enable Copyright',
            array($this, 'enable_copyright_callback'),
            'ross-theme-footer-copyright',
            'ross_footer_copyright_visibility'
        );
        add_settings_field(
            'copyright_alignment',
            'Alignment',
            array($this, 'copyright_alignment_callback'),
            'ross-theme-footer-copyright',
            'ross_footer_copyright_visibility'
        );

        // Content
        add_settings_field(
            'copyright_text',
            'Copyright Text',
            array($this, 'copyright_text_callback'),
            'ross-theme-footer-copyright',
            'ross_footer_copyright_content'
        );

        // Styling
        add_settings_field(
            'copyright_bg_color',
            'Background Color',
            array($this, 'copyright_bg_color_callback'),
            'ross-theme-footer-copyright',
            'ross_footer_copyright_styling'
        );
        add_settings_field(
            'copyright_text_color',
            'Text Color',
            array($this, 'copyright_text_color_callback'),
            'ross-theme-footer-copyright',
            'ross_footer_copyright_styling'
        );
        add_settings_field(
            'copyright_font_size',
            'Font Size (px)',
            array($this, 'copyright_font_size_callback'),
            'ross-theme-footer-copyright',
            'ross_footer_copyright_styling'
        );
        add_settings_field(
            'copyright_font_weight',
            'Font Weight',
            array($this, 'copyright_font_weight_callback'),
            'ross-theme-footer-copyright',
            'ross_footer_copyright_styling'
        );
        add_settings_field(
            'copyright_letter_spacing',
            'Letter Spacing (px)',
            array($this, 'copyright_letter_spacing_callback'),
            'ross-theme-footer-copyright',
            'ross_footer_copyright_styling'
        );

        // Custom footer area inside copyright/tab so it's easy to find
        // Advanced / Custom
        add_settings_field(
            'enable_custom_footer',
            'Enable Custom Footer',
            array($this, 'enable_custom_footer_callback'),
            'ross-theme-footer-copyright',
            'ross_footer_copyright_advanced'
        );
        add_settings_field(
            'custom_footer_html',
            'Custom Footer HTML',
            array($this, 'custom_footer_html_callback'),
            'ross-theme-footer-copyright',
            'ross_footer_copyright_advanced'
        );
        add_settings_field(
            'custom_footer_css',
            'Custom Footer CSS',
            array($this, 'custom_footer_css_callback'),
            'ross-theme-footer-copyright',
            'ross_footer_copyright_advanced'
        );
        add_settings_field(
            'custom_footer_js',
            'Custom Footer JS',
            array($this, 'custom_footer_js_callback'),
            'ross-theme-footer-copyright',
            'ross_footer_copyright_advanced'
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

    public function cta_visibility_section_callback() {
        echo '<p>Control where and when the CTA appears on your site.</p>';
    }
    public function cta_content_section_callback() {
        echo '<p>Content for the CTA: title, subtitle, button and icon.</p>';
    }
    public function cta_layout_section_callback() {
        echo '<p>Layout and structure of CTA: alignment, direction, and spacing.</p>';
    }
    public function cta_styling_section_callback() {
        echo '<p>Visual styling for the CTA, including background and colors.</p>';
    }
    public function cta_spacing_section_callback() {
        echo '<p>Spacing and margins for the CTA block.</p>';
    }
    public function cta_animation_section_callback() {
        echo '<p>Small animation options for the CTA entrance.</p>';
    }
    public function cta_advanced_section_callback() {
        echo '<p>Advanced options: custom HTML/CSS/JS for the CTA area.</p>';
    }
    
    public function social_section_callback() {
        echo '<p>Configure social media icons and links.</p>';
    }
    
    public function copyright_section_callback() {
        echo '<p>Customize the copyright bar at the bottom.</p>';
    }
    
    public function footer_template_callback() {
        $value = isset($this->options['footer_template']) ? $this->options['footer_template'] : 'template1';
        ?>
        <div class="ross-footer-templates">
            <label><input type="radio" name="ross_theme_footer_options[footer_template]" value="template1" <?php checked($value, 'template1'); ?> /> Template 1: Business Professional</label><br/>
            <label><input type="radio" name="ross_theme_footer_options[footer_template]" value="template2" <?php checked($value, 'template2'); ?> /> Template 2: E-commerce</label><br/>
            <label><input type="radio" name="ross_theme_footer_options[footer_template]" value="template3" <?php checked($value, 'template3'); ?> /> Template 3: Creative Agency</label><br/>
            <label><input type="radio" name="ross_theme_footer_options[footer_template]" value="template4" <?php checked($value, 'template4'); ?> /> Template 4: Minimal Modern</label>
        </div>
        <p class="description">Choose a pre-designed footer layout. You can customize template colors below.</p>

        <div style="margin-top:1rem;">
            <button type="button" class="button" id="ross-preview-template">Preview Selected Template</button>
            <button type="button" class="button button-primary" id="ross-apply-template">Apply Template (Replace current footer widgets)</button>
            <span class="description" style="margin-left:0.75rem;">Preview first, then click Apply to populate footer widget areas with sample content.</span>
        </div>

        <div id="ross-template-preview" style="margin-top:1rem; border:1px solid #ddd; padding:1rem; display:none; background:#fff;"></div>

        <?php // include hidden previews for quick client-side show without extra ajax calls ?>
        <div id="ross-hidden-previews" style="display:none;">
            <?php echo $this->get_template_preview_html('template1'); ?>
            <?php echo $this->get_template_preview_html('template2'); ?>
            <?php echo $this->get_template_preview_html('template3'); ?>
            <?php echo $this->get_template_preview_html('template4'); ?>
        </div>

        <div id="ross-footer-backups" style="margin-top:1rem;">
            <h4>Recent Footer Backups</h4>
            <?php echo $this->render_backups_list_html(); ?>
        </div>

        <!-- Confirmation modal (reused for apply/restore/delete) -->
        <div id="ross-confirm-modal" style="display:none;">
            <div class="ross-confirm-overlay" style="position:fixed;left:0;top:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:9998;display:flex;align-items:center;justify-content:center;">
                <div class="ross-confirm-box" style="background:#fff;padding:20px;border-radius:6px;max-width:520px;width:100%;box-shadow:0 6px 18px rgba(0,0,0,0.2);">
                    <div class="ross-confirm-message" style="margin-bottom:16px;font-size:15px;color:#222;"></div>
                    <div style="text-align:right;">
                        <button type="button" class="button" id="ross-confirm-cancel">Cancel</button>
                        <button type="button" class="button button-primary" id="ross-confirm-ok" style="margin-left:8px;">Confirm</button>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Return array of backups (most recent first)
     */
    private function get_backups() {
        $backups = get_option('ross_footer_template_backups', array());
        if (!is_array($backups)) $backups = array();
        return $backups;
    }

    private function save_backups($backups) {
        update_option('ross_footer_template_backups', $backups);
    }

    private function render_backups_list_html() {
        $backups = $this->get_backups();
        if (empty($backups)) {
            return '<p><em>No backups yet. Applying a template will create a backup.</em></p>';
        }

        $html = '<table class="widefat" style="max-width:800px;"><thead><tr><th>When</th><th>User</th><th>Template</th><th>Actions</th></tr></thead><tbody>';
        foreach ($backups as $b) {
            $id = esc_attr($b['id']);
            $when = esc_html($b['timestamp']);
            $user = isset($b['user_display']) ? esc_html($b['user_display']) : esc_html($b['user_id']);
            $template = isset($b['template']) ? esc_html($b['template']) : '';
            $html .= '<tr data-backup-id="' . $id . '"><td>' . $when . '</td><td>' . $user . '</td><td>' . $template . '</td><td><button class="button ross-restore-backup" data-backup-id="' . $id . '">Restore</button> <button class="button ross-delete-backup" data-backup-id="' . $id . '">Delete</button></td></tr>';
        }
        $html .= '</tbody></table>';
        return $html;
    }

    /**
     * Return HTML for a preview of a template (safe for admin display)
     */
    private function get_template_preview_html($tpl) {
        $samples = $this->get_templates();

        if (!isset($samples[$tpl])) return '';
        $s = $samples[$tpl];

        // Wrap preview with id and data-template so admin JS can find it
        // Use semantic markup and classes so we can style it in admin CSS
        $html = '<footer id="ross-preview-' . esc_attr($tpl) . '" class="ross-footer-preview ross-footer-preview--' . esc_attr($tpl) . '" data-template="' . esc_attr($tpl) . '">';
        $html .= '<div class="ross-footer-preview-inner">';
        $html .= '<div class="ross-footer-preview-columns">';
        foreach ($s['cols'] as $col) {
            $html .= '<div class="ross-footer-preview-col">' . $col . '</div>';
        }
        $html .= '</div>'; // .ross-footer-preview-columns
        $html .= '<div class="ross-footer-preview-bottom">';
        $html .= '<div class="ross-footer-preview-copyright">&copy; ' . date('Y') . ' ' . esc_html(get_bloginfo('name')) . '</div>';
        $html .= '<div class="ross-footer-preview-social">';
        $html .= '<a href="#" class="social">Facebook</a> <a href="#" class="social">LinkedIn</a>';
        $html .= '</div>'; // .ross-footer-preview-social
        $html .= '</div>'; // .ross-footer-preview-bottom
        $html .= '</div>'; // .ross-footer-preview-inner
        $html .= '</footer>';

        return $html;
    }

    /**
     * AJAX handler: create sample widgets and assign to footer sidebars
     */
    public function ajax_apply_footer_template() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized', 403);
        }

        check_ajax_referer('ross_apply_footer_template', 'nonce');

        $tpl = isset($_POST['template']) ? sanitize_text_field($_POST['template']) : 'template1';

        // Sample column contents (same structure as previews)
        $samples = $this->get_templates();

        if (!isset($samples[$tpl])) {
            wp_send_json_error('Invalid template');
        }

        $cols = $samples[$tpl]['cols'];

        // Backup current footer widgets and relevant widget options so we can undo
        $backup = array(
            'id' => uniqid('rossbk_'),
            'timestamp' => current_time('mysql'),
            'user_id' => get_current_user_id(),
            'user_display' => wp_get_current_user() ? wp_get_current_user()->display_name : '',
            'template' => $tpl,
            'sidebars_widgets' => get_option('sidebars_widgets'),
            'widget_options' => array(),
        );

        // capture all widget_* options for safer restore (only keys starting with widget_)
        global $wpdb;
        $all_options = wp_load_alloptions();
        foreach ($all_options as $opt_key => $opt_val) {
            if (strpos($opt_key, 'widget_') === 0) {
                $backup['widget_options'][$opt_key] = get_option($opt_key);
            }
        }

        // Store into backups array (most recent first) and rotate to keep last 10
        $backups = get_option('ross_footer_template_backups', array());
        if (!is_array($backups)) $backups = array();
        array_unshift($backups, $backup);
        // keep last 10
        $backups = array_slice($backups, 0, 10);
        update_option('ross_footer_template_backups', $backups);

        // Pick a widget base - prefer custom_html, fallback to text
        $widget_base = null;
        // Prefer custom_html if it exists; otherwise fallback to text widget
        if (get_option('widget_custom_html') !== false) {
            $widget_base = 'custom_html';
            $widget_option_name = 'widget_custom_html';
        } else {
            $widget_base = 'text';
            $widget_option_name = 'widget_text';
        }

        $widgets = get_option($widget_option_name, array());
        if (!is_array($widgets)) $widgets = array();

        // Ensure _multiwidget flag exists
        if (!isset($widgets['_multiwidget'])) $widgets['_multiwidget'] = 1;

        // Find next numeric index
        $next_index = 1;
        foreach ($widgets as $k => $v) {
            if (is_numeric($k) && intval($k) >= $next_index) $next_index = intval($k) + 1;
        }

        $created_ids = array();
        foreach ($cols as $i => $col_content) {
            list($title, $content) = array_map('trim', explode('|', $col_content, 2));

            $widget_data = array();
            if ($widget_base === 'custom_html') {
                $widget_data = array('title' => $title, 'content' => '<p>' . esc_html($content) . '</p>');
            } else {
                $widget_data = array('title' => $title, 'text' => $content, 'filter' => 0);
            }

            $widgets[$next_index] = $widget_data;

            // Record created widget unique id like custom_html-5
            $created_ids[] = $widget_base . '-' . $next_index;

            $next_index++;
        }

        // Update widget option
        update_option($widget_option_name, $widgets);

        // Assign widgets to footer sidebars
        $sidebars = get_option('sidebars_widgets', array());
        if (!is_array($sidebars)) $sidebars = array();

        // Clear existing footer-* sidebars and assign new created widgets per column
        for ($i = 1; $i <= 4; $i++) {
            $key = 'footer-' . $i;
            if (isset($created_ids[$i - 1])) {
                $sidebars[$key] = array($created_ids[$i - 1]);
            } else {
                // empty
                $sidebars[$key] = array();
            }
        }

        update_option('sidebars_widgets', $sidebars);

        $new_backups_html = $this->render_backups_list_html();
        wp_send_json_success(array('message' => 'Template applied', 'created' => $created_ids, 'backup_created' => true, 'backup_id' => $backup['id'], 'backups_html' => $new_backups_html));
    }

    /**
     * AJAX handler: restore backup created before applying template
     */
    public function ajax_restore_footer_backup() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized', 403);
        }

        check_ajax_referer('ross_apply_footer_template', 'nonce');
        $id = isset($_POST['backup_id']) ? sanitize_text_field($_POST['backup_id']) : '';

        $backups = get_option('ross_footer_template_backups', array());
        if (!is_array($backups) || empty($backups)) {
            wp_send_json_error('No backups found');
        }

        // find backup by id (or use first if no id)
        $found = null;
        $found_index = null;
        if ($id) {
            foreach ($backups as $k => $b) {
                if (isset($b['id']) && $b['id'] === $id) {
                    $found = $b;
                    $found_index = $k;
                    break;
                }
            }
        } else {
            $found = $backups[0];
            $found_index = 0;
        }

        if (empty($found) || !is_array($found)) {
            wp_send_json_error('Backup not found');
        }

        // Restore sidebars_widgets
        if (isset($found['sidebars_widgets'])) {
            update_option('sidebars_widgets', $found['sidebars_widgets']);
        }

        // Restore captured widget options
        if (!empty($found['widget_options']) && is_array($found['widget_options'])) {
            foreach ($found['widget_options'] as $opt_name => $opt_value) {
                update_option($opt_name, $opt_value);
            }
        }

        // Remove the restored backup from history
        array_splice($backups, $found_index, 1);
        update_option('ross_footer_template_backups', $backups);

        $new_backups_html = $this->render_backups_list_html();
        wp_send_json_success(array('message' => 'Footer restored from backup', 'restored_id' => isset($found['id']) ? $found['id'] : '', 'backups_html' => $new_backups_html));
    }

    /**
     * Delete a backup by id
     */
    public function ajax_delete_footer_backup() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized', 403);
        }

        check_ajax_referer('ross_apply_footer_template', 'nonce');

        $id = isset($_POST['backup_id']) ? sanitize_text_field($_POST['backup_id']) : '';
        if (!$id) wp_send_json_error('Missing backup id');

        $backups = $this->get_backups();
        $found_index = null;
        foreach ($backups as $k => $b) {
            if (isset($b['id']) && $b['id'] === $id) {
                $found_index = $k;
                break;
            }
        }

        if ($found_index === null) wp_send_json_error('Backup not found');

        array_splice($backups, $found_index, 1);
        $this->save_backups($backups);

        $new_backups_html = $this->render_backups_list_html();
        wp_send_json_success(array('message' => 'Backup deleted', 'deleted_id' => $id, 'backups_html' => $new_backups_html));
    }

    /**
     * Return backups array as JSON (for AJAX listing if needed)
     */
    public function ajax_list_footer_backups() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized', 403);
        }

        check_ajax_referer('ross_apply_footer_template', 'nonce');

        $backups = $this->get_backups();
        $html = $this->render_backups_list_html();
        wp_send_json_success(array('backups' => $backups, 'html' => $html));
    }

    /**
     * AJAX: return preview HTML for a given template key.
     */
    public function ajax_get_footer_template_preview() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized', 403);
        }

        check_ajax_referer('ross_apply_footer_template', 'nonce');

        $tpl = isset($_POST['template']) ? sanitize_text_field($_POST['template']) : '';
        if (empty($tpl)) wp_send_json_error('Missing template');

        $html = $this->get_template_preview_html($tpl);
        if (empty($html)) wp_send_json_error('No preview available');

        wp_send_json_success(array('template' => $tpl, 'html' => $html));
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
    
    

    // New: custom footer controls
    public function enable_custom_footer_callback() {
        $value = isset($this->options['enable_custom_footer']) ? $this->options['enable_custom_footer'] : 0;
        ?>
        <input type="checkbox" name="ross_theme_footer_options[enable_custom_footer]" value="1" <?php checked(1, $value); ?> />
        <label for="enable_custom_footer">Enable custom site footer HTML</label>
        <?php
    }

    public function custom_footer_html_callback() {
        $value = isset($this->options['custom_footer_html']) ? $this->options['custom_footer_html'] : '';
        ?>
        <textarea name="ross_theme_footer_options[custom_footer_html]" rows="6" class="large-text" placeholder="Paste allowed HTML for the footer (links allowed)"><?php echo esc_textarea($value); ?></textarea>
        <p class="description">Use basic HTML. Allowed tags: a, br, strong, em, p, span, div, ul, li</p>
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

    public function use_template_colors_callback() {
        $value = isset($this->options['use_template_colors']) ? $this->options['use_template_colors'] : 1;
        ?>
        <label class="ross-switch-label">
            <input type="checkbox" name="ross_theme_footer_options[use_template_colors]" value="1" <?php checked(1, $value); ?> />
            <span class="ross-label-text">Use selected template default colors (uncheck to customize)</span>
        </label>
        <?php
    }

    public function template_custom_colors_callback() {
        $tpl = isset($this->options['footer_template']) ? $this->options['footer_template'] : 'template1';
        // Load stored template color overrides (keys like 'template1_bg')
        $bg = isset($this->options[$tpl . '_bg']) ? $this->options[$tpl . '_bg'] : '';
        $text = isset($this->options[$tpl . '_text']) ? $this->options[$tpl . '_text'] : '';
        $accent = isset($this->options[$tpl . '_accent']) ? $this->options[$tpl . '_accent'] : '';
        $social = isset($this->options[$tpl . '_social']) ? $this->options[$tpl . '_social'] : '';
        ?>
        <div>
            <label>Background Color: <input type="text" name="ross_theme_footer_options[<?php echo esc_attr($tpl); ?>_bg]" value="<?php echo esc_attr($bg); ?>" class="color-picker" /></label><br/>
            <label>Text Color: <input type="text" name="ross_theme_footer_options[<?php echo esc_attr($tpl); ?>_text]" value="<?php echo esc_attr($text); ?>" class="color-picker" /></label><br/>
            <label>Accent Color: <input type="text" name="ross_theme_footer_options[<?php echo esc_attr($tpl); ?>_accent]" value="<?php echo esc_attr($accent); ?>" class="color-picker" /></label><br/>
            <label>Social Icon Color: <input type="text" name="ross_theme_footer_options[<?php echo esc_attr($tpl); ?>_social]" value="<?php echo esc_attr($social); ?>" class="color-picker" /></label>
        </div>
        <p class="description">Customize colors for the selected template. Leave empty to use the template defaults.</p>
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

    public function cta_always_visible_callback() {
        $v = isset($this->options['cta_always_visible']) ? $this->options['cta_always_visible'] : 0;
        echo '<label class="ross-toggle">';
        echo '<input type="checkbox" name="ross_theme_footer_options[cta_always_visible]" value="1" ' . checked(1, $v, false) . ' />';
        echo '<span class="ross-toggle-slider"></span> Always show CTA (ignore conditional visibility)';
        echo '</label>';
    }

    public function cta_display_on_callback() {
        $v = isset($this->options['cta_display_on']) ? $this->options['cta_display_on'] : array();
        if (!is_array($v)) $v = array($v);
        $choices = array('all' => 'Everywhere', 'front' => 'Front Page', 'home' => 'Blog Index', 'single' => 'Single Posts', 'page' => 'Pages', 'archive' => 'Archives');
        foreach ($choices as $key => $label) {
            $checked = in_array($key, $v, true) ? 'checked="checked"' : '';
            echo '<label style="display:block;margin-right:12px;">';
            echo '<input type="checkbox" name="ross_theme_footer_options[cta_display_on][]" value="' . esc_attr($key) . '" ' . $checked . ' /> ' . esc_html($label);
            echo '</label>';
        }
        echo '<p class="description">Select where the CTA appears when enabled. Use "Everywhere" to show on all pages.</p>';
    }

    public function cta_text_callback() {
        $v = isset($this->options['cta_text']) ? $this->options['cta_text'] : '';
        echo '<textarea name="ross_theme_footer_options[cta_text]" rows="3" class="large-text">' . esc_textarea($v) . '</textarea>';
    }

    public function cta_button_url_callback() {
        $v = isset($this->options['cta_button_url']) ? $this->options['cta_button_url'] : '';
        echo '<input type="url" name="ross_theme_footer_options[cta_button_url]" value="' . esc_attr($v) . '" class="regular-text" placeholder="https://..." />';
    }

    public function cta_icon_callback() {
        $v = isset($this->options['cta_icon']) ? $this->options['cta_icon'] : '';
        echo '<input type="text" name="ross_theme_footer_options[cta_icon]" value="' . esc_attr($v) . '" class="regular-text" placeholder="fa fa-arrow-right (class name)" />';
        echo '<p class="description">Set an icon class name to show an icon next to the CTA button (font-awesome / theme icons).</p>';
    }

    public function cta_alignment_callback() {
        $v = isset($this->options['cta_alignment']) ? $this->options['cta_alignment'] : 'center';
        ?>
        <select name="ross_theme_footer_options[cta_alignment]">
            <option value="left" <?php selected($v, 'left'); ?>>Left</option>
            <option value="center" <?php selected($v, 'center'); ?>>Center</option>
            <option value="right" <?php selected($v, 'right'); ?>>Right</option>
        </select>
        <?php
    }

    public function cta_layout_direction_callback() {
        $v = isset($this->options['cta_layout_direction']) ? $this->options['cta_layout_direction'] : 'row';
        ?>
        <select name="ross_theme_footer_options[cta_layout_direction]">
            <option value="row" <?php selected($v, 'row'); ?>>Horizontal</option>
            <option value="column" <?php selected($v, 'column'); ?>>Vertical</option>
        </select>
        <?php
    }

    public function cta_layout_wrap_callback() {
        $v = isset($this->options['cta_layout_wrap']) ? $this->options['cta_layout_wrap'] : 0;
        echo '<input type="checkbox" name="ross_theme_footer_options[cta_layout_wrap]" value="1" ' . checked(1, $v, false) . ' /> Allow wrap to next line on small viewports';
    }

    public function cta_layout_justify_callback() {
        $v = isset($this->options['cta_layout_justify']) ? $this->options['cta_layout_justify'] : 'center';
        ?>
        <select name="ross_theme_footer_options[cta_layout_justify]">
            <option value="flex-start" <?php selected($v, 'flex-start'); ?>>Left (flex-start)</option>
            <option value="center" <?php selected($v, 'center'); ?>>Center</option>
            <option value="flex-end" <?php selected($v, 'flex-end'); ?>>Right (flex-end)</option>
            <option value="space-between" <?php selected($v, 'space-between'); ?>>Space Between</option>
            <option value="space-around" <?php selected($v, 'space-around'); ?>>Space Around</option>
        </select>
        <?php
    }

    public function cta_layout_align_callback() {
        $v = isset($this->options['cta_layout_align']) ? $this->options['cta_layout_align'] : 'center';
        ?>
        <select name="ross_theme_footer_options[cta_layout_align]">
            <option value="flex-start" <?php selected($v, 'flex-start'); ?>>Top</option>
            <option value="center" <?php selected($v, 'center'); ?>>Center</option>
            <option value="flex-end" <?php selected($v, 'flex-end'); ?>>Bottom</option>
        </select>
        <?php
    }

    public function cta_gap_callback() {
        $v = isset($this->options['cta_gap']) ? intval($this->options['cta_gap']) : 12;
        echo '<input type="number" name="ross_theme_footer_options[cta_gap]" value="' . esc_attr($v) . '" class="small-text" /> px';
    }

    public function cta_bg_type_callback() {
        $v = isset($this->options['cta_bg_type']) ? $this->options['cta_bg_type'] : 'color';
        ?>
        <select name="ross_theme_footer_options[cta_bg_type]">
            <option value="color" <?php selected($v,'color'); ?>>Color</option>
            <option value="image" <?php selected($v,'image'); ?>>Image</option>
            <option value="gradient" <?php selected($v,'gradient'); ?>>Gradient</option>
        </select>
        <?php
    }

    public function cta_bg_gradient_from_callback() {
        $v = isset($this->options['cta_bg_gradient_from']) ? $this->options['cta_bg_gradient_from'] : '';
        echo '<input type="text" name="ross_theme_footer_options[cta_bg_gradient_from]" value="' . esc_attr($v) . '" class="color-picker" />';
    }

    public function cta_bg_gradient_to_callback() {
        $v = isset($this->options['cta_bg_gradient_to']) ? $this->options['cta_bg_gradient_to'] : '';
        echo '<input type="text" name="ross_theme_footer_options[cta_bg_gradient_to]" value="' . esc_attr($v) . '" class="color-picker" />';
    }

    public function cta_bg_image_callback() {
        $v = isset($this->options['cta_bg_image']) ? $this->options['cta_bg_image'] : '';
        echo '<input type="text" id="ross-cta-bg-image" name="ross_theme_footer_options[cta_bg_image]" value="' . esc_attr($v) . '" class="regular-text" placeholder="https://..." />';
        echo ' <button type="button" class="button ross-upload-button" data-target="ross-cta-bg-image" data-input-name="ross_theme_footer_options[cta_bg_image]">Upload</button>';
        echo '<input type="hidden" id="ross-cta-bg-image-id" name="ross_theme_footer_options[cta_bg_image_id]" value="' . esc_attr(isset($this->options['cta_bg_image_id']) ? $this->options['cta_bg_image_id'] : '') . '" />';
        echo '&nbsp;<span id="ross-cta-bg-image-preview">';
        if (!empty($v)) {
            echo '<img src="' . esc_url($v) . '" style="max-height:40px;vertical-align:middle;border:1px solid #ddd;padding:2px;" />';
        }
        echo '</span>';
    }

    public function cta_text_color_callback() {
        $v = isset($this->options['cta_text_color']) ? $this->options['cta_text_color'] : '';
        echo '<input type="text" name="ross_theme_footer_options[cta_text_color]" value="' . esc_attr($v) . '" class="color-picker" />';
    }

    public function cta_button_bg_color_callback() {
        $v = isset($this->options['cta_button_bg_color']) ? $this->options['cta_button_bg_color'] : '';
        echo '<input type="text" name="ross_theme_footer_options[cta_button_bg_color]" value="' . esc_attr($v) . '" class="color-picker" />';
    }

    public function cta_button_text_color_callback() {
        $v = isset($this->options['cta_button_text_color']) ? $this->options['cta_button_text_color'] : '';
        echo '<input type="text" name="ross_theme_footer_options[cta_button_text_color]" value="' . esc_attr($v) . '" class="color-picker" />';
    }

    public function cta_padding_top_callback() {
        $v = isset($this->options['cta_padding_top']) ? intval($this->options['cta_padding_top']) : 24;
        echo '<input type="number" name="ross_theme_footer_options[cta_padding_top]" value="' . esc_attr($v) . '" class="small-text" /> px';
    }
    public function cta_padding_right_callback() {
        $v = isset($this->options['cta_padding_right']) ? intval($this->options['cta_padding_right']) : 0;
        echo '<input type="number" name="ross_theme_footer_options[cta_padding_right]" value="' . esc_attr($v) . '" class="small-text" /> px';
    }
    public function cta_padding_bottom_callback() {
        $v = isset($this->options['cta_padding_bottom']) ? intval($this->options['cta_padding_bottom']) : 24;
        echo '<input type="number" name="ross_theme_footer_options[cta_padding_bottom]" value="' . esc_attr($v) . '" class="small-text" /> px';
    }
    public function cta_padding_left_callback() {
        $v = isset($this->options['cta_padding_left']) ? intval($this->options['cta_padding_left']) : 0;
        echo '<input type="number" name="ross_theme_footer_options[cta_padding_left]" value="' . esc_attr($v) . '" class="small-text" /> px';
    }

    public function cta_overlay_enabled_callback() {
        $v = isset($this->options['cta_overlay_enabled']) ? $this->options['cta_overlay_enabled'] : 0;
        echo '<label class="ross-toggle">';
        echo '<input type="checkbox" name="ross_theme_footer_options[cta_overlay_enabled]" value="1" ' . checked(1, $v, false) . ' />';
        echo '<span class="ross-toggle-slider"></span> Enable CTA Overlay';
        echo '</label>';
    }

    public function cta_overlay_type_callback() {
        $v = isset($this->options['cta_overlay_type']) ? $this->options['cta_overlay_type'] : 'color';
        ?>
        <select name="ross_theme_footer_options[cta_overlay_type]">
            <option value="color" <?php selected($v,'color'); ?>>Color</option>
            <option value="image" <?php selected($v,'image'); ?>>Image</option>
            <option value="gradient" <?php selected($v,'gradient'); ?>>Gradient</option>
        </select>
        <?php
    }

    public function cta_overlay_color_callback() {
        $v = isset($this->options['cta_overlay_color']) ? $this->options['cta_overlay_color'] : '';
        echo '<input type="text" name="ross_theme_footer_options[cta_overlay_color]" value="' . esc_attr($v) . '" class="color-picker" />';
    }

    public function cta_overlay_image_callback() {
        $v = isset($this->options['cta_overlay_image']) ? $this->options['cta_overlay_image'] : '';
        echo '<input type="text" id="ross-cta-overlay-image" name="ross_theme_footer_options[cta_overlay_image]" value="' . esc_attr($v) . '" class="regular-text" placeholder="https://..." />';
        echo ' <button type="button" class="button ross-upload-button" data-target="ross-cta-overlay-image" data-input-name="ross_theme_footer_options[cta_overlay_image]">Upload</button>';
        echo '<input type="hidden" id="ross-cta-overlay-image-id" name="ross_theme_footer_options[cta_overlay_image_id]" value="' . esc_attr(isset($this->options['cta_overlay_image_id']) ? $this->options['cta_overlay_image_id'] : '') . '" />';
        echo '&nbsp;<span id="ross-cta-overlay-image-preview">';
        if (!empty($v)) {
            echo '<img src="' . esc_url($v) . '" style="max-height:40px;vertical-align:middle;border:1px solid #ddd;padding:2px;" />';
        }
        echo '</span>';
    }

    public function cta_overlay_gradient_from_callback() {
        $v = isset($this->options['cta_overlay_gradient_from']) ? $this->options['cta_overlay_gradient_from'] : '';
        echo '<input type="text" name="ross_theme_footer_options[cta_overlay_gradient_from]" value="' . esc_attr($v) . '" class="color-picker" />';
    }

    public function cta_overlay_gradient_to_callback() {
        $v = isset($this->options['cta_overlay_gradient_to']) ? $this->options['cta_overlay_gradient_to'] : '';
        echo '<input type="text" name="ross_theme_footer_options[cta_overlay_gradient_to]" value="' . esc_attr($v) . '" class="color-picker" />';
    }

    public function cta_overlay_opacity_callback() {
        $v = isset($this->options['cta_overlay_opacity']) ? $this->options['cta_overlay_opacity'] : '0.5';
        echo '<input type="number" step="0.1" min="0" max="1" name="ross_theme_footer_options[cta_overlay_opacity]" value="' . esc_attr($v) . '" class="small-text" />';
    }

    // New Margin callbacks
    public function cta_margin_top_callback() {
        $v = isset($this->options['cta_margin_top']) ? intval($this->options['cta_margin_top']) : 0;
        echo '<input type="number" name="ross_theme_footer_options[cta_margin_top]" value="' . esc_attr($v) . '" class="small-text" /> px';
    }
    public function cta_margin_right_callback() {
        $v = isset($this->options['cta_margin_right']) ? intval($this->options['cta_margin_right']) : 0;
        echo '<input type="number" name="ross_theme_footer_options[cta_margin_right]" value="' . esc_attr($v) . '" class="small-text" /> px';
    }
    public function cta_margin_bottom_callback() {
        $v = isset($this->options['cta_margin_bottom']) ? intval($this->options['cta_margin_bottom']) : 0;
        echo '<input type="number" name="ross_theme_footer_options[cta_margin_bottom]" value="' . esc_attr($v) . '" class="small-text" /> px';
    }
    public function cta_margin_left_callback() {
        $v = isset($this->options['cta_margin_left']) ? intval($this->options['cta_margin_left']) : 0;
        echo '<input type="number" name="ross_theme_footer_options[cta_margin_left]" value="' . esc_attr($v) . '" class="small-text" /> px';
    }

    // Animation duration callback
    public function cta_anim_duration_callback() {
        $v = isset($this->options['cta_anim_duration']) ? intval($this->options['cta_anim_duration']) : 400;
        echo '<input type="number" name="ross_theme_footer_options[cta_anim_duration]" value="' . esc_attr($v) . '" class="small-text" /> ms';
    }

    public function cta_icon_color_callback() {
        $v = isset($this->options['cta_icon_color']) ? $this->options['cta_icon_color'] : '';
        echo '<input type="text" name="ross_theme_footer_options[cta_icon_color]" value="' . esc_attr($v) . '" class="color-picker" />';
    }

    public function cta_animation_callback() {
        $v = isset($this->options['cta_animation']) ? $this->options['cta_animation'] : 'none';
        ?>
        <select name="ross_theme_footer_options[cta_animation]">
            <option value="none" <?php selected($v,'none'); ?>>None</option>
            <option value="fade" <?php selected($v,'fade'); ?>>Fade</option>
            <option value="slide" <?php selected($v,'slide'); ?>>Slide Up</option>
            <option value="pop" <?php selected($v,'pop'); ?>>Pop</option>
            <option value="zoom" <?php selected($v,'zoom'); ?>>Zoom</option>
        </select>
        <?php
    }

    public function cta_anim_delay_callback() {
        $v = isset($this->options['cta_anim_delay']) ? intval($this->options['cta_anim_delay']) : 150;
        echo '<input type="number" name="ross_theme_footer_options[cta_anim_delay]" value="' . esc_attr($v) . '" class="small-text" /> ms';
    }

    public function enable_custom_cta_callback() {
        $v = isset($this->options['enable_custom_cta']) ? $this->options['enable_custom_cta'] : 0;
        echo '<input type="checkbox" name="ross_theme_footer_options[enable_custom_cta]" value="1" ' . checked(1, $v, false) . ' />';
        echo ' <label for="enable_custom_cta">Use custom CTA HTML/CSS instead of the built-in CTA</label>';
    }

    public function custom_cta_html_callback() {
        $v = isset($this->options['custom_cta_html']) ? $this->options['custom_cta_html'] : '';
        echo '<textarea name="ross_theme_footer_options[custom_cta_html]" rows="6" class="large-text">' . esc_textarea($v) . '</textarea>';
    }

    public function custom_cta_css_callback() {
        $v = isset($this->options['custom_cta_css']) ? $this->options['custom_cta_css'] : '';
        echo '<textarea name="ross_theme_footer_options[custom_cta_css]" rows="6" class="large-text" placeholder="Any additional CSS to apply to the CTA">' . esc_textarea($v) . '</textarea>';
    }

    public function custom_cta_js_callback() {
        $v = isset($this->options['custom_cta_js']) ? $this->options['custom_cta_js'] : '';
        echo '<textarea name="ross_theme_footer_options[custom_cta_js]" rows="6" class="large-text" placeholder="Custom JS for the CTA (will be escaped)">' . esc_textarea($v) . '</textarea>';
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
        <p class="description">Use {year} and {site_name} placeholders to keep the text dynamic. Links are allowed (<a> tags).</p>
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

    // New: Font size callback
    public function copyright_font_size_callback() {
        $v = isset($this->options['copyright_font_size']) ? intval($this->options['copyright_font_size']) : 14;
        echo '<input type="number" name="ross_theme_footer_options[copyright_font_size]" value="' . esc_attr($v) . '" class="small-text" /> px';
    }

    // New: Font weight callback
    public function copyright_font_weight_callback() {
        $v = isset($this->options['copyright_font_weight']) ? $this->options['copyright_font_weight'] : 'normal';
        ?>
        <select name="ross_theme_footer_options[copyright_font_weight]">
            <option value="light" <?php selected($v, 'light'); ?>>Light</option>
            <option value="normal" <?php selected($v, 'normal'); ?>>Normal</option>
            <option value="bold" <?php selected($v, 'bold'); ?>>Bold</option>
        </select>
        <?php
    }

    // New: Letter spacing
    public function copyright_letter_spacing_callback() {
        $v = isset($this->options['copyright_letter_spacing']) ? floatval($this->options['copyright_letter_spacing']) : 0;
        echo '<input type="number" step="0.1" name="ross_theme_footer_options[copyright_letter_spacing]" value="' . esc_attr($v) . '" class="small-text" /> px';
    }

    // New: Custom Footer CSS
    public function custom_footer_css_callback() {
        $v = isset($this->options['custom_footer_css']) ? $this->options['custom_footer_css'] : '';
        echo '<textarea name="ross_theme_footer_options[custom_footer_css]" rows="4" class="large-text" placeholder="Add custom CSS specific to the footer (no <style> tag needed)">' . esc_textarea($v) . '</textarea>';
    }

    // New: Custom Footer JS
    public function custom_footer_js_callback() {
        $v = isset($this->options['custom_footer_js']) ? $this->options['custom_footer_js'] : '';
        echo '<textarea name="ross_theme_footer_options[custom_footer_js]" rows="4" class="large-text" placeholder="Add JS to run for the footer (will be sanitized)">' . esc_textarea($v) . '</textarea>';
    }

    // Section callbacks for the new sections
    public function copyright_visibility_section_callback() {
        echo '<p>Show or hide the copyright and control its alignment.</p>';
    }
    public function copyright_content_section_callback() {
        echo '<p>Customize the copyright text and use placeholders {year} and {site_name}.</p>';
    }
    public function copyright_styling_section_callback() {
        echo '<p>Styling options for the copyright bar: colors, font and spacing.</p>';
    }
    public function copyright_advanced_section_callback() {
        echo '<p>Advanced options for the copyright area including custom HTML/CSS/JS.</p>';
    }
    
    // Sanitization
    public function sanitize_footer_options($input) {
        $sanitized = array();
        
        // Layout
        // footer_style option removed; default layout is used by the theme.
        $sanitized['footer_columns'] = isset($input['footer_columns']) ? sanitize_text_field($input['footer_columns']) : '4';
        $sanitized['footer_width'] = isset($input['footer_width']) ? sanitize_text_field($input['footer_width']) : 'contained';
        $sanitized['footer_padding'] = isset($input['footer_padding']) ? absint($input['footer_padding']) : 60; // kept for backward compatibility, now controlled by Styling padding

        // Custom footer
        $sanitized['enable_custom_footer'] = isset($input['enable_custom_footer']) ? 1 : 0;
        if (!empty($input['custom_footer_html'])) {
            $allowed = array(
                'a' => array('href' => array(), 'title' => array(), 'target' => array()),
                'br' => array(),
                'strong' => array(),
                'em' => array(),
                'p' => array(),
                'span' => array('class' => array()),
                'div' => array('class' => array()),
                'ul' => array(),
                'li' => array(),
            );

            $sanitized['custom_footer_html'] = wp_kses($input['custom_footer_html'], $allowed);
        } else {
            $sanitized['custom_footer_html'] = '';
        }
        
        // Widgets
        $sanitized['enable_widgets'] = isset($input['enable_widgets']) ? 1 : 0;
        $sanitized['widgets_bg_color'] = sanitize_hex_color($input['widgets_bg_color']);
        $sanitized['widgets_text_color'] = sanitize_hex_color($input['widgets_text_color']);
        $sanitized['widget_title_color'] = sanitize_hex_color($input['widget_title_color']);
        // Styling options
        $sanitized['styling_bg_color'] = isset($input['styling_bg_color']) ? sanitize_hex_color($input['styling_bg_color']) : '';
        $sanitized['styling_bg_gradient'] = isset($input['styling_bg_gradient']) ? 1 : 0;
        $sanitized['styling_bg_image'] = isset($input['styling_bg_image']) ? esc_url_raw($input['styling_bg_image']) : '';
        $sanitized['styling_bg_image_id'] = isset($input['styling_bg_image_id']) ? absint($input['styling_bg_image_id']) : '';
        $sanitized['styling_bg_type'] = isset($input['styling_bg_type']) && in_array($input['styling_bg_type'], array('color','image','gradient')) ? sanitize_text_field($input['styling_bg_type']) : 'color';
        $sanitized['styling_bg_opacity'] = isset($input['styling_bg_opacity']) ? floatval($input['styling_bg_opacity']) : '1';
        $sanitized['styling_bg_gradient_from'] = isset($input['styling_bg_gradient_from']) ? sanitize_hex_color($input['styling_bg_gradient_from']) : '';
        $sanitized['styling_bg_gradient_to'] = isset($input['styling_bg_gradient_to']) ? sanitize_hex_color($input['styling_bg_gradient_to']) : '';

        $sanitized['styling_text_color'] = isset($input['styling_text_color']) ? sanitize_hex_color($input['styling_text_color']) : '';
        $sanitized['styling_link_color'] = isset($input['styling_link_color']) ? sanitize_hex_color($input['styling_link_color']) : '';
        $sanitized['styling_link_hover'] = isset($input['styling_link_hover']) ? sanitize_hex_color($input['styling_link_hover']) : '';

        $sanitized['styling_font_size'] = isset($input['styling_font_size']) ? absint($input['styling_font_size']) : 14;
        $sanitized['styling_line_height'] = isset($input['styling_line_height']) ? floatval($input['styling_line_height']) : 1.6;

        $sanitized['styling_col_gap'] = isset($input['styling_col_gap']) ? absint($input['styling_col_gap']) : 24;
        $sanitized['styling_row_gap'] = isset($input['styling_row_gap']) ? absint($input['styling_row_gap']) : 18;
        $sanitized['styling_padding_lr'] = isset($input['styling_padding_lr']) ? absint($input['styling_padding_lr']) : 20;

        $sanitized['styling_padding_top'] = isset($input['styling_padding_top']) ? absint($input['styling_padding_top']) : '';
        $sanitized['styling_padding_bottom'] = isset($input['styling_padding_bottom']) ? absint($input['styling_padding_bottom']) : '';
        $sanitized['styling_padding_left'] = isset($input['styling_padding_left']) ? absint($input['styling_padding_left']) : '';
        $sanitized['styling_padding_right'] = isset($input['styling_padding_right']) ? absint($input['styling_padding_right']) : '';

        $sanitized['styling_border_top'] = isset($input['styling_border_top']) ? 1 : 0;
        $sanitized['styling_border_color'] = isset($input['styling_border_color']) ? sanitize_hex_color($input['styling_border_color']) : '';
        $sanitized['styling_border_thickness'] = isset($input['styling_border_thickness']) ? absint($input['styling_border_thickness']) : 1;

        $sanitized['styling_widget_title_color'] = isset($input['styling_widget_title_color']) ? sanitize_hex_color($input['styling_widget_title_color']) : '';
        $sanitized['styling_widget_title_size'] = isset($input['styling_widget_title_size']) ? absint($input['styling_widget_title_size']) : 16;
        // Overlay sanitization
        $sanitized['styling_overlay_enabled'] = isset($input['styling_overlay_enabled']) ? 1 : 0;
        $sanitized['styling_overlay_type'] = isset($input['styling_overlay_type']) && in_array($input['styling_overlay_type'], array('color','image','gradient')) ? sanitize_text_field($input['styling_overlay_type']) : 'color';
        $sanitized['styling_overlay_color'] = isset($input['styling_overlay_color']) ? sanitize_hex_color($input['styling_overlay_color']) : '';
        $sanitized['styling_overlay_image'] = isset($input['styling_overlay_image']) ? esc_url_raw($input['styling_overlay_image']) : '';
        $sanitized['styling_overlay_image_id'] = isset($input['styling_overlay_image_id']) ? absint($input['styling_overlay_image_id']) : '';
        $sanitized['styling_overlay_gradient_from'] = isset($input['styling_overlay_gradient_from']) ? sanitize_hex_color($input['styling_overlay_gradient_from']) : '';
        $sanitized['styling_overlay_gradient_to'] = isset($input['styling_overlay_gradient_to']) ? sanitize_hex_color($input['styling_overlay_gradient_to']) : '';
        $sanitized['styling_overlay_opacity'] = isset($input['styling_overlay_opacity']) ? floatval($input['styling_overlay_opacity']) : 0.5;
        // Template selection & custom colors
        $sanitized['footer_template'] = isset($input['footer_template']) ? sanitize_text_field($input['footer_template']) : 'template1';
        $sanitized['use_template_colors'] = isset($input['use_template_colors']) ? 1 : 0;
        // Allow per-template color overrides (template1_bg, template1_text, template1_accent, template1_social)
        for ($i = 1; $i <= 4; $i++) {
            $tpl = 'template' . $i;
            $key = $tpl . '_bg';
            $sanitized[$key] = isset($input[$key]) && !empty($input[$key]) ? sanitize_hex_color($input[$key]) : '';
            $key = $tpl . '_text';
            $sanitized[$key] = isset($input[$key]) && !empty($input[$key]) ? sanitize_hex_color($input[$key]) : '';
            $key = $tpl . '_accent';
            $sanitized[$key] = isset($input[$key]) && !empty($input[$key]) ? sanitize_hex_color($input[$key]) : '';
            $key = $tpl . '_social';
            $sanitized[$key] = isset($input[$key]) && !empty($input[$key]) ? sanitize_hex_color($input[$key]) : '';
        }
        
        // CTA
        $sanitized['enable_footer_cta'] = isset($input['enable_footer_cta']) ? 1 : 0;
        $sanitized['cta_title'] = isset($input['cta_title']) ? sanitize_text_field($input['cta_title']) : '';
        $sanitized['cta_text'] = isset($input['cta_text']) ? wp_kses_post($input['cta_text']) : '';
        $sanitized['cta_button_text'] = isset($input['cta_button_text']) ? sanitize_text_field($input['cta_button_text']) : '';
        $sanitized['cta_button_url'] = isset($input['cta_button_url']) ? esc_url_raw($input['cta_button_url']) : '';
        $sanitized['cta_icon'] = isset($input['cta_icon']) ? sanitize_text_field($input['cta_icon']) : '';
        $sanitized['cta_bg_color'] = isset($input['cta_bg_color']) ? sanitize_hex_color($input['cta_bg_color']) : '';
        $sanitized['cta_bg_type'] = isset($input['cta_bg_type']) && in_array($input['cta_bg_type'], array('color','image','gradient')) ? sanitize_text_field($input['cta_bg_type']) : 'color';
        $sanitized['cta_bg_image'] = isset($input['cta_bg_image']) ? esc_url_raw($input['cta_bg_image']) : '';
        $sanitized['cta_bg_image_id'] = isset($input['cta_bg_image_id']) ? absint($input['cta_bg_image_id']) : '';
        $sanitized['cta_bg_gradient_from'] = isset($input['cta_bg_gradient_from']) ? sanitize_hex_color($input['cta_bg_gradient_from']) : '';
        $sanitized['cta_bg_gradient_to'] = isset($input['cta_bg_gradient_to']) ? sanitize_hex_color($input['cta_bg_gradient_to']) : '';
        $sanitized['cta_text_color'] = isset($input['cta_text_color']) ? sanitize_hex_color($input['cta_text_color']) : '';
        $sanitized['cta_button_bg_color'] = isset($input['cta_button_bg_color']) ? sanitize_hex_color($input['cta_button_bg_color']) : '';
        $sanitized['cta_button_text_color'] = isset($input['cta_button_text_color']) ? sanitize_hex_color($input['cta_button_text_color']) : '';
        $sanitized['cta_padding_top'] = isset($input['cta_padding_top']) ? absint($input['cta_padding_top']) : 24;
        $sanitized['cta_padding_right'] = isset($input['cta_padding_right']) ? absint($input['cta_padding_right']) : 0;
        $sanitized['cta_padding_bottom'] = isset($input['cta_padding_bottom']) ? absint($input['cta_padding_bottom']) : 24;
        $sanitized['cta_padding_left'] = isset($input['cta_padding_left']) ? absint($input['cta_padding_left']) : 0;
        // CTA margins (new)
        $sanitized['cta_margin_top'] = isset($input['cta_margin_top']) ? absint($input['cta_margin_top']) : 0;
        $sanitized['cta_margin_right'] = isset($input['cta_margin_right']) ? absint($input['cta_margin_right']) : 0;
        $sanitized['cta_margin_bottom'] = isset($input['cta_margin_bottom']) ? absint($input['cta_margin_bottom']) : 0;
        $sanitized['cta_margin_left'] = isset($input['cta_margin_left']) ? absint($input['cta_margin_left']) : 0;
        $sanitized['cta_icon_color'] = isset($input['cta_icon_color']) ? sanitize_hex_color($input['cta_icon_color']) : '';
        // CTA overlay sanitization
        $sanitized['cta_overlay_enabled'] = isset($input['cta_overlay_enabled']) ? 1 : 0;
        $sanitized['cta_overlay_type'] = isset($input['cta_overlay_type']) && in_array($input['cta_overlay_type'], array('color','image','gradient')) ? sanitize_text_field($input['cta_overlay_type']) : 'color';
        $sanitized['cta_overlay_color'] = isset($input['cta_overlay_color']) ? sanitize_hex_color($input['cta_overlay_color']) : '';
        $sanitized['cta_overlay_image'] = isset($input['cta_overlay_image']) ? esc_url_raw($input['cta_overlay_image']) : '';
        $sanitized['cta_overlay_image_id'] = isset($input['cta_overlay_image_id']) ? absint($input['cta_overlay_image_id']) : '';
        $sanitized['cta_overlay_gradient_from'] = isset($input['cta_overlay_gradient_from']) ? sanitize_hex_color($input['cta_overlay_gradient_from']) : '';
        $sanitized['cta_overlay_gradient_to'] = isset($input['cta_overlay_gradient_to']) ? sanitize_hex_color($input['cta_overlay_gradient_to']) : '';
        $sanitized['cta_overlay_opacity'] = isset($input['cta_overlay_opacity']) ? floatval($input['cta_overlay_opacity']) : 0.5;
        // Layout & alignment
        $sanitized['cta_alignment'] = isset($input['cta_alignment']) && in_array($input['cta_alignment'], array('left','center','right')) ? sanitize_text_field($input['cta_alignment']) : 'center';
        $sanitized['cta_layout_direction'] = isset($input['cta_layout_direction']) && in_array($input['cta_layout_direction'], array('row','column')) ? sanitize_text_field($input['cta_layout_direction']) : 'row';
        $sanitized['cta_layout_wrap'] = isset($input['cta_layout_wrap']) ? 1 : 0;
        $allowed_justify = array('flex-start','center','flex-end','space-between','space-around');
        $sanitized['cta_layout_justify'] = isset($input['cta_layout_justify']) && in_array($input['cta_layout_justify'], $allowed_justify) ? sanitize_text_field($input['cta_layout_justify']) : 'center';
        $allowed_align = array('flex-start','center','flex-end');
        $sanitized['cta_layout_align'] = isset($input['cta_layout_align']) && in_array($input['cta_layout_align'], $allowed_align) ? sanitize_text_field($input['cta_layout_align']) : 'center';
        $sanitized['cta_gap'] = isset($input['cta_gap']) ? absint($input['cta_gap']) : 12;
        $sanitized['cta_animation'] = isset($input['cta_animation']) && in_array($input['cta_animation'], array('none','fade','slide','pop','zoom')) ? sanitize_text_field($input['cta_animation']) : 'none';
        $sanitized['cta_anim_delay'] = isset($input['cta_anim_delay']) ? absint($input['cta_anim_delay']) : 150;
        $sanitized['cta_anim_duration'] = isset($input['cta_anim_duration']) ? absint($input['cta_anim_duration']) : 400;
        // Visibility
        $sanitized['cta_always_visible'] = isset($input['cta_always_visible']) ? 1 : 0;
        if (!empty($input['cta_display_on']) && is_array($input['cta_display_on'])) {
            $allowed = array('all','front','home','single','page','archive');
            $san = array();
            foreach ($input['cta_display_on'] as $v) {
                if (in_array($v, $allowed, true)) $san[] = sanitize_text_field($v);
            }
            $sanitized['cta_display_on'] = $san;
        } else {
            $sanitized['cta_display_on'] = array();
        }
        // Advanced custom CTA
        $sanitized['enable_custom_cta'] = isset($input['enable_custom_cta']) ? 1 : 0;
        $sanitized['custom_cta_html'] = isset($input['custom_cta_html']) ? wp_kses($input['custom_cta_html'], array('a'=>array('href'=>array(),'title'=>array(),'target'=>array()),'br'=>array(),'strong'=>array(),'em'=>array(),'p'=>array(),'span'=>array(),'div'=>array(),'ul'=>array(),'li'=>array())) : '';
        $sanitized['custom_cta_css'] = isset($input['custom_cta_css']) ? wp_strip_all_tags($input['custom_cta_css']) : '';
        $sanitized['custom_cta_js'] = isset($input['custom_cta_js']) ? wp_strip_all_tags($input['custom_cta_js']) : '';
        
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
        // New styling fields
        $sanitized['copyright_font_size'] = isset($input['copyright_font_size']) ? absint($input['copyright_font_size']) : 14;
        $allowed_weights = array('light','normal','bold');
        $sanitized['copyright_font_weight'] = isset($input['copyright_font_weight']) && in_array($input['copyright_font_weight'], $allowed_weights) ? sanitize_text_field($input['copyright_font_weight']) : 'normal';
        $sanitized['copyright_letter_spacing'] = isset($input['copyright_letter_spacing']) ? floatval($input['copyright_letter_spacing']) : 0;
        // Advanced: custom CSS / JS
        $sanitized['custom_footer_css'] = isset($input['custom_footer_css']) ? wp_strip_all_tags($input['custom_footer_css']) : '';
        $sanitized['custom_footer_js'] = isset($input['custom_footer_js']) ? wp_strip_all_tags($input['custom_footer_js']) : '';
        
        return $sanitized;
    }
}

// Initialize
if (is_admin()) {
    new RossFooterOptions();
}