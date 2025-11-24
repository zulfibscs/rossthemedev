<?php
/**
 * Advanced Top Bar Settings
 * Modern, organized top bar configuration with enhanced functionality
 */

if (!defined('ABSPATH')) exit;

class RossAdvancedTopBarSettings {
    
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('wp_head', array($this, 'output_dynamic_css'));
        add_action('wp_body_open', array($this, 'render_topbar'), 5);
    }
    
    /**
     * Add admin menu page
     */
    public function add_admin_menu() {
        add_theme_page(
            __('Advanced Top Bar', 'ross-theme'),
            __('Top Bar Pro', 'ross-theme'),
            'edit_theme_options',
            'ross-advanced-topbar',
            array($this, 'render_admin_page')
        );
    }
    
    /**
     * Register settings
     */
    public function register_settings() {
        register_setting('ross_advanced_topbar', 'ross_advanced_topbar_options', array($this, 'sanitize_options'));
        
        // Main Settings Section
        add_settings_section(
            'ross_topbar_main',
            __('Main Settings', 'ross-theme'),
            array($this, 'main_section_callback'),
            'ross-advanced-topbar'
        );
        
        // Content Settings Section
        add_settings_section(
            'ross_topbar_content',
            __('Content Configuration', 'ross-theme'),
            array($this, 'content_section_callback'),
            'ross-advanced-topbar'
        );
        
        // Advanced Settings Section
        add_settings_section(
            'ross_topbar_advanced',
            __('Advanced Features', 'ross-theme'),
            array($this, 'advanced_section_callback'),
            'ross-advanced-topbar'
        );
        
        // Style Settings Section
        add_settings_section(
            'ross_topbar_style',
            __('Design & Style', 'ross-theme'),
            array($this, 'style_section_callback'),
            'ross-advanced-topbar'
        );
        
        $this->add_main_fields();
        $this->add_content_fields();
        $this->add_advanced_fields();
        $this->add_style_fields();
    }
    
    /**
     * Add main settings fields
     */
    private function add_main_fields() {
        // Enable Top Bar
        add_settings_field(
            'enable_topbar',
            __('Enable Top Bar', 'ross-theme'),
            array($this, 'render_checkbox'),
            'ross-advanced-topbar',
            'ross_topbar_main',
            array('name' => 'enable_topbar', 'label' => 'Show top bar on website')
        );
        
        // Layout Style
        add_settings_field(
            'layout_style',
            __('Layout Style', 'ross-theme'),
            array($this, 'render_select'),
            'ross-advanced-topbar',
            'ross_topbar_main',
            array(
                'name' => 'layout_style',
                'options' => array(
                    'default' => __('Default (Left - Center - Right)', 'ross-theme'),
                    'centered' => __('Centered Content', 'ross-theme'),
                    'minimal' => __('Minimal (Left - Right)', 'ross-theme'),
                    'split' => __('Split Layout', 'ross-theme')
                )
            )
        );
        
        // Visibility Control
        add_settings_field(
            'visibility',
            __('Visibility Control', 'ross-theme'),
            array($this, 'render_select'),
            'ross-advanced-topbar',
            'ross_topbar_main',
            array(
                'name' => 'visibility',
                'options' => array(
                    'all' => __('Show on All Pages', 'ross-theme'),
                    'homepage_only' => __('Homepage Only', 'ross-theme'),
                    'exclude_homepage' => __('Exclude Homepage', 'ross-theme'),
                    'custom' => __('Custom Pages', 'ross-theme')
                )
            )
        );
        
        // User Role Visibility
        add_settings_field(
            'user_visibility',
            __('User Role Visibility', 'ross-theme'),
            array($this, 'render_checkbox_group'),
            'ross-advanced-topbar',
            'ross_topbar_main',
            array(
                'name' => 'user_visibility',
                'options' => array(
                    'guest' => __('Guest Users', 'ross-theme'),
                    'subscriber' => __('Subscribers', 'ross-theme'),
                    'customer' => __('Customers', 'ross-theme'),
                    'contributor' => __('Contributors', 'ross-theme'),
                    'author' => __('Authors', 'ross-theme'),
                    'editor' => __('Editors', 'ross-theme'),
                    'administrator' => __('Administrators', 'ross-theme')
                )
            )
        );
    }
    
    /**
     * Add content settings fields
     */
    private function add_content_fields() {
        // Left Section
        add_settings_field(
            'left_content',
            __('Left Section Content', 'ross-theme'),
            array($this, 'render_content_builder'),
            'ross-advanced-topbar',
            'ross_topbar_content',
            array('section' => 'left')
        );
        
        // Center Section
        add_settings_field(
            'center_content',
            __('Center Section Content', 'ross-theme'),
            array($this, 'render_content_builder'),
            'ross-advanced-topbar',
            'ross_topbar_content',
            array('section' => 'center')
        );
        
        // Right Section
        add_settings_field(
            'right_content',
            __('Right Section Content', 'ross-theme'),
            array($this, 'render_content_builder'),
            'ross-advanced-topbar',
            'ross_topbar_content',
            array('section' => 'right')
        );
        
        // Social Media Integration
        add_settings_field(
            'social_media',
            __('Social Media Integration', 'ross-theme'),
            array($this, 'render_social_media_builder'),
            'ross-advanced-topbar',
            'ross_topbar_content',
            array()
        );
    }
    
    /**
     * Add advanced settings fields
     */
    private function add_advanced_fields() {
        // Sticky Behavior
        add_settings_field(
            'sticky_behavior',
            __('Sticky Behavior', 'ross-theme'),
            array($this, 'render_select'),
            'ross-advanced-topbar',
            'ross_topbar_advanced',
            array(
                'name' => 'sticky_behavior',
                'options' => array(
                    'none' => __('No Sticky', 'ross-theme'),
                    'always' => __('Always Sticky', 'ross-theme'),
                    'scroll_up' => __('Sticky on Scroll Up', 'ross-theme'),
                    'scroll_down' => __('Hide on Scroll Down', 'ross-theme')
                )
            )
        );
        
        // Animation Effects
        add_settings_field(
            'animation_effects',
            __('Animation Effects', 'ross-theme'),
            array($this, 'render_animation_builder'),
            'ross-advanced-topbar',
            'ross_topbar_advanced',
            array()
        );
        
        // Mobile Settings
        add_settings_field(
            'mobile_settings',
            __('Mobile Settings', 'ross-theme'),
            array($this, 'render_mobile_settings'),
            'ross-advanced-topbar',
            'ross_topbar_advanced',
            array()
        );
        
        // Time-based Display
        add_settings_field(
            'time_display',
            __('Time-based Display', 'ross-theme'),
            array($this, 'render_time_settings'),
            'ross-advanced-topbar',
            'ross_topbar_advanced',
            array()
        );
    }
    
    /**
     * Add style settings fields
     */
    private function add_style_fields() {
        // Color Scheme
        add_settings_field(
            'color_scheme',
            __('Color Scheme', 'ross-theme'),
            array($this, 'render_color_scheme'),
            'ross-advanced-topbar',
            'ross_topbar_style',
            array()
        );
        
        // Typography
        add_settings_field(
            'typography',
            __('Typography', 'ross-theme'),
            array($this, 'render_typography_settings'),
            'ross-advanced-topbar',
            'ross_topbar_style',
            array()
        );
        
        // Spacing & Layout
        add_settings_field(
            'spacing_layout',
            __('Spacing & Layout', 'ross-theme'),
            array($this, 'render_spacing_settings'),
            'ross-advanced-topbar',
            'ross_topbar_style',
            array()
        );
        
        // Border & Effects
        add_settings_field(
            'border_effects',
            __('Border & Effects', 'ross-theme'),
            array($this, 'render_border_settings'),
            'ross-advanced-topbar',
            'ross_topbar_style',
            array()
        );
    }
    
    /**
     * Render field callbacks
     */
    public function render_checkbox($args) {
        $options = get_option('ross_advanced_topbar_options', array());
        $name = $args['name'];
        $value = isset($options[$name]) ? $options[$name] : false;
        $label = $args['label'];
        
        echo "<label>";
        echo "<input type='checkbox' name='ross_advanced_topbar_options[$name]' value='1' " . checked(1, $value, false) . " />";
        echo " $label</label>";
    }
    
    public function render_select($args) {
        $options = get_option('ross_advanced_topbar_options', array());
        $name = $args['name'];
        $value = isset($options[$name]) ? $options[$name] : '';
        $select_options = $args['options'];
        
        echo "<select name='ross_advanced_topbar_options[$name]'>";
        foreach ($select_options as $key => $label) {
            echo "<option value='" . esc_attr($key) . "' " . selected($key, $value, false) . ">" . esc_html($label) . "</option>";
        }
        echo "</select>";
    }
    
    public function render_checkbox_group($args) {
        $options = get_option('ross_advanced_topbar_options', array());
        $name = $args['name'];
        $checkbox_options = $args['options'];
        $selected = isset($options[$name]) ? (array) $options[$name] : array();
        
        echo "<div style='display: grid; grid-template-columns: repeat(2, 1fr); gap: 8px;'>";
        foreach ($checkbox_options as $key => $label) {
            echo "<label>";
            echo "<input type='checkbox' name='ross_advanced_topbar_options[$name][]' value='" . esc_attr($key) . "' " . checked(in_array($key, $selected), true, false) . " />";
            echo " " . esc_html($label) . "</label>";
        }
        echo "</div>";
    }
    
    public function render_content_builder($args) {
        $section = $args['section'];
        $options = get_option('ross_advanced_topbar_options', array());
        $content_options = isset($options[$section . '_content']) ? $options[$section . '_content'] : array();
        
        echo "<div class='content-builder' data-section='" . esc_attr($section) . "'>";
        echo "<div class='content-items'>";
        
        if (!empty($content_options)) {
            foreach ($content_options as $index => $item) {
                $this->render_content_item($section, $index, $item);
            }
        }
        
        echo "</div>";
        echo "<button type='button' class='button add-content-item' data-section='" . esc_attr($section) . "'>+ Add Item</button>";
        echo "</div>";
    }
    
    private function render_content_item($section, $index, $item) {
        $type = isset($item['type']) ? $item['type'] : 'text';
        $content = isset($item['content']) ? $item['content'] : '';
        $icon = isset($item['icon']) ? $item['icon'] : '';
        $link = isset($item['link']) ? $item['link'] : '';
        
        echo "<div class='content-item' data-index='" . esc_attr($index) . "'>";
        echo "<select name='ross_advanced_topbar_options[" . esc_attr($section) . "_content][" . esc_attr($index) . "][type]' class='content-type'>";
        echo "<option value='text' " . selected('text', $type, false) . ">Text</option>";
        echo "<option value='phone' " . selected('phone', $type, false) . ">Phone</option>";
        echo "<option value='email' " . selected('email', $type, false) . ">Email</option>";
        echo "<option value='link' " . selected('link', $type, false) . ">Link</option>";
        echo "<option value='social' " . selected('social', $type, false) . ">Social</option>";
        echo "<option value='custom' " . selected('custom', $type, false) . ">Custom HTML</option>";
        echo "</select>";
        
        echo "<input type='text' name='ross_advanced_topbar_options[" . esc_attr($section) . "_content][" . esc_attr($index) . "][content]' value='" . esc_attr($content) . "' placeholder='Content' class='content-input' />";
        echo "<input type='text' name='ross_advanced_topbar_options[" . esc_attr($section) . "_content][" . esc_attr($index) . "][icon]' value='" . esc_attr($icon) . "' placeholder='Icon (fa fa-phone)' class='icon-input' />";
        echo "<input type='text' name='ross_advanced_topbar_options[" . esc_attr($section) . "_content][" . esc_attr($index) . "][link]' value='" . esc_attr($link) . "' placeholder='Link URL' class='link-input' />";
        
        echo "<button type='button' class='button remove-content-item'>Remove</button>";
        echo "</div>";
    }
    
    /**
     * Render admin page
     */
    public function render_admin_page() {
        ?>
        <div class="wrap">
            <h1><?php _e('Advanced Top Bar Settings', 'ross-theme'); ?></h1>
            
            <form method="post" action="options.php">
                <?php
                settings_fields('ross_advanced_topbar');
                do_settings_sections('ross-advanced-topbar');
                submit_button();
                ?>
            </form>
        </div>
        
        <style>
        .content-builder {
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 4px;
            background: #f9f9f9;
        }
        
        .content-item {
            display: grid;
            grid-template-columns: 120px 1fr 150px 150px 80px;
            gap: 10px;
            align-items: center;
            margin-bottom: 10px;
            padding: 10px;
            background: white;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .content-item select,
        .content-item input {
            width: 100%;
        }
        
        .add-content-item {
            margin-top: 10px;
        }
        
        .remove-content-item {
            background: #dc3545;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .remove-content-item:hover {
            background: #c82333;
        }
        </style>
        
        <script>
        jQuery(document).ready(function($) {
            $('.add-content-item').on('click', function() {
                var section = $(this).data('section');
                var container = $(this).siblings('.content-items');
                var index = container.find('.content-item').length;
                
                var itemHtml = '<div class="content-item" data-index="' + index + '">' +
                    '<select name="ross_advanced_topbar_options[' + section + '_content][' + index + '][type]" class="content-type">' +
                    '<option value="text">Text</option>' +
                    '<option value="phone">Phone</option>' +
                    '<option value="email">Email</option>' +
                    '<option value="link">Link</option>' +
                    '<option value="social">Social</option>' +
                    '<option value="custom">Custom HTML</option>' +
                    '</select>' +
                    '<input type="text" name="ross_advanced_topbar_options[' + section + '_content][' + index + '][content]" placeholder="Content" class="content-input" />' +
                    '<input type="text" name="ross_advanced_topbar_options[' + section + '_content][' + index + '][icon]" placeholder="Icon (fa fa-phone)" class="icon-input" />' +
                    '<input type="text" name="ross_advanced_topbar_options[' + section + '_content][' + index + '][link]" placeholder="Link URL" class="link-input" />' +
                    '<button type="button" class="remove-content-item">Remove</button>' +
                    '</div>';
                
                container.append(itemHtml);
            });
            
            $(document).on('click', '.remove-content-item', function() {
                $(this).closest('.content-item').remove();
            });
        });
        </script>
        <?php
    }
    
    /**
     * Sanitize options
     */
    public function sanitize_options($input) {
        $sanitized = array();
        
        // Main settings
        $sanitized['enable_topbar'] = isset($input['enable_topbar']) ? 1 : 0;
        $sanitized['layout_style'] = sanitize_text_field($input['layout_style']);
        $sanitized['visibility'] = sanitize_text_field($input['visibility']);
        $sanitized['user_visibility'] = isset($input['user_visibility']) ? array_map('sanitize_text_field', $input['user_visibility']) : array();
        
        // Content sections
        $sections = array('left', 'center', 'right');
        foreach ($sections as $section) {
            $key = $section . '_content';
            if (isset($input[$key]) && is_array($input[$key])) {
                $sanitized[$key] = array();
                foreach ($input[$key] as $index => $item) {
                    if (is_array($item)) {
                        $sanitized_item = array();
                        $sanitized_item['type'] = sanitize_text_field($item['type']);
                        $sanitized_item['content'] = wp_kses_post($item['content']);
                        $sanitized_item['icon'] = sanitize_text_field($item['icon']);
                        $sanitized_item['link'] = esc_url_raw($item['link']);
                        $sanitized[$key][] = $sanitized_item;
                    }
                }
            }
        }
        
        return $sanitized;
    }
    
    /**
     * Render topbar
     */
    public function render_topbar() {
        $options = get_option('ross_advanced_topbar_options', array());
        
        if (!isset($options['enable_topbar']) || !$options['enable_topbar']) {
            return;
        }
        
        // Check visibility
        if (!$this->check_visibility($options)) {
            return;
        }
        
        $layout_style = isset($options['layout_style']) ? $options['layout_style'] : 'default';
        
        echo '<div id="ross-advanced-topbar" class="ross-topbar layout-' . esc_attr($layout_style) . '">';
        echo '<div class="ross-topbar-container">';
        
        $this->render_content_section('left', $options);
        $this->render_content_section('center', $options);
        $this->render_content_section('right', $options);
        
        echo '</div>';
        echo '</div>';
    }
    
    private function render_content_section($section, $options) {
        $key = $section . '_content';
        if (!isset($options[$key]) || empty($options[$key])) {
            return;
        }
        
        echo '<div class="ross-topbar-' . esc_attr($section) . '">';
        
        foreach ($options[$key] as $item) {
            $this->render_content_item_display($item);
        }
        
        echo '</div>';
    }
    
    private function render_content_item_display($item) {
        $type = isset($item['type']) ? $item['type'] : 'text';
        $content = isset($item['content']) ? $item['content'] : '';
        $icon = isset($item['icon']) ? $item['icon'] : '';
        $link = isset($item['link']) ? $item['link'] : '';
        
        $output = '';
        
        if (!empty($icon)) {
            $output .= '<i class="' . esc_attr($icon) . '"></i> ';
        }
        
        switch ($type) {
            case 'phone':
                $output .= '<a href="tel:' . esc_attr(preg_replace('/[^0-9+]/', '', $content)) . '">' . esc_html($content) . '</a>';
                break;
            case 'email':
                $output .= '<a href="mailto:' . esc_attr($content) . '">' . esc_html($content) . '</a>';
                break;
            case 'link':
                $output .= '<a href="' . esc_url($link) . '">' . esc_html($content) . '</a>';
                break;
            case 'social':
                $output .= '<a href="' . esc_url($link) . '" target="_blank" rel="noopener">' . esc_html($content) . '</a>';
                break;
            case 'custom':
                $output .= $content; // Already sanitized
                break;
            default:
                $output .= esc_html($content);
        }
        
        echo '<span class="topbar-item topbar-item-' . esc_attr($type) . '">' . $output . '</span>';
    }
    
    /**
     * Check visibility conditions
     */
    private function check_visibility($options) {
        // Check user role visibility
        if (isset($options['user_visibility']) && !empty($options['user_visibility'])) {
            $current_user = wp_get_current_user();
            $user_roles = $current_user->roles;
            
            // Add 'guest' role for non-logged-in users
            if (empty($user_roles)) {
                $user_roles = array('guest');
            }
            
            $allowed_roles = $options['user_visibility'];
            $has_permission = false;
            
            foreach ($user_roles as $role) {
                if (in_array($role, $allowed_roles)) {
                    $has_permission = true;
                    break;
                }
            }
            
            if (!$has_permission) {
                return false;
            }
        }
        
        // Check page visibility
        if (isset($options['visibility'])) {
            switch ($options['visibility']) {
                case 'homepage_only':
                    return is_front_page();
                case 'exclude_homepage':
                    return !is_front_page();
                case 'custom':
                    // TODO: Add custom page selection logic
                    return true;
                default:
                    return true;
            }
        }
        
        return true;
    }
    
    /**
     * Output dynamic CSS
     */
    public function output_dynamic_css() {
        $options = get_option('ross_advanced_topbar_options', array());
        
        if (!isset($options['enable_topbar']) || !$options['enable_topbar']) {
            return;
        }
        
        echo '<style id="ross-advanced-topbar-css">';
        echo '.ross-topbar {';
        echo 'background: #001946;';
        echo 'color: #ffffff;';
        echo 'font-size: 14px;';
        echo 'line-height: 1.5;';
        echo 'border-bottom: 1px solid rgba(255, 255, 255, 0.1);';
        echo 'position: relative;';
        echo 'z-index: 1000;';
        echo '}';
        
        echo '.ross-topbar-container {';
        echo 'max-width: 1200px;';
        echo 'margin: 0 auto;';
        echo 'padding: 0 20px;';
        echo 'display: flex;';
        echo 'align-items: center;';
        echo 'justify-content: space-between;';
        echo 'min-height: 40px;';
        echo '}';
        
        echo '.ross-topbar-left, .ross-topbar-center, .ross-topbar-right {';
        echo 'flex: 1;';
        echo '}';
        
        echo '.ross-topbar-center {';
        echo 'text-align: center;';
        echo '}';
        
        echo '.ross-topbar-right {';
        echo 'text-align: right;';
        echo '}';
        
        echo '.topbar-item {';
        echo 'display: inline-flex;';
        echo 'align-items: center;';
        echo 'gap: 6px;';
        echo 'margin-right: 15px;';
        echo 'font-size: 13px;';
        echo '}';
        
        echo '.topbar-item:last-child {';
        echo 'margin-right: 0;';
        echo '}';
        
        echo '.topbar-item a {';
        echo 'color: #ffffff;';
        echo 'text-decoration: none;';
        echo 'transition: color 0.3s ease;';
        echo '}';
        
        echo '.topbar-item a:hover {';
        echo 'color: #E5C902;';
        echo '}';
        
        echo '.topbar-item i {';
        echo 'color: #E5C902;';
        echo 'font-size: 12px;';
        echo '}';
        
        // Layout specific styles
        if (isset($options['layout_style'])) {
            switch ($options['layout_style']) {
                case 'centered':
                    echo '.ross-topbar-container { justify-content: center; }';
                    echo '.ross-topbar-left, .ross-topbar-right { display: none; }';
                    break;
                case 'minimal':
                    echo '.ross-topbar-center { display: none; }';
                    echo '.ross-topbar-left { flex: 2; }';
                    echo '.ross-topbar-right { flex: 1; }';
                    break;
                case 'split':
                    echo '.ross-topbar-left { text-align: left; }';
                    echo '.ross-topbar-right { text-align: right; }';
                    break;
            }
        }
        
        // Responsive styles
        echo '@media (max-width: 768px) {';
        echo '.ross-topbar-container { flex-direction: column; gap: 10px; padding: 10px 0; }';
        echo '.ross-topbar-left, .ross-topbar-center, .ross-topbar-right { flex: none; width: 100%; text-align: center; }';
        echo '.topbar-item { margin-right: 10px; font-size: 12px; }';
        echo '}';
        
        echo '</style>';
    }
    
    /**
     * Section callbacks
     */
    public function main_section_callback() {
        echo '<p>' . __('Configure the basic top bar settings and visibility options.', 'ross-theme') . '</p>';
    }
    
    public function content_section_callback() {
        echo '<p>' . __('Build your top bar content using the content builder. Add text, contact info, links, and custom elements.', 'ross-theme') . '</p>';
    }
    
    public function advanced_section_callback() {
        echo '<p>' . __('Configure advanced features like sticky behavior, animations, and mobile-specific settings.', 'ross-theme') . '</p>';
    }
    
    public function style_section_callback() {
        echo '<p>' . __('Customize the appearance of your top bar with colors, typography, and spacing options.', 'ross-theme') . '</p>';
    }
}

// Initialize the advanced top bar settings
new RossAdvancedTopBarSettings();
