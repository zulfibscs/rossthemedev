<?php
/**
 * Theme Reset Utility Module
 * Handles resetting theme options to defaults
 */

// Only load if in admin area
if (!is_admin()) {
    return;
}

class RossThemeResetUtility {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('admin_init', array($this, 'init'));
        add_action('admin_menu', array($this, 'add_reset_submenu'));
    }
    
    public function init() {
        // Handle reset request
        add_action('admin_post_ross_theme_reset_settings', array($this, 'handle_reset_request'));
        
        // Show success message if reset was successful
        $this->show_reset_success();
    }
    
    /**
     * Add reset submenu page
     */
    public function add_reset_submenu() {
        add_submenu_page(
            'ross-theme',
            'Reset Settings',
            'Reset Settings',
            'manage_options',
            'ross-theme-reset',
            array($this, 'display_reset_page')
        );
    }
    
    /**
     * Display the reset settings page
     */
    public function display_reset_page() {
        // Security nonce
        $nonce = wp_create_nonce('ross_theme_reset_nonce');
        ?>
        <div class="wrap">
            <h1>🔁 Reset Theme Settings</h1>
            
            <?php
            // Show success message if reset was done
            if (isset($_GET['reset']) && $_GET['reset'] === 'success') {
                echo '<div class="notice notice-success is-dismissible"><p>✅ <strong>Success!</strong> All theme settings have been reset to default values.</p></div>';
            }
            ?>
            
            <div class="card" style="max-width: 600px; margin-top: 20px;">
                <h2>Reset to Default Settings</h2>
                
                <div style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 15px 0;">
                    <strong>⚠️ Warning:</strong> This will reset ALL theme settings to their default values. This action cannot be undone.
                </div>
                
                <p>This will reset:</p>
                <ul style="list-style: disc; margin-left: 20px;">
                    <li>📄 Header Options (layout, logo, navigation, colors)</li>
                    <li>📄 Footer Options (layout, widgets, copyright, social)</li>
                    <li>📄 General Settings (colors, typography, buttons, blog)</li>
                </ul>
                
                <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" id="ross-reset-form">
                    <input type="hidden" name="action" value="ross_theme_reset_settings">
                    <input type="hidden" name="ross_reset_nonce" value="<?php echo esc_attr($nonce); ?>">
                    
                    <p>
                        <label>
                            <input type="checkbox" name="confirm_reset" value="1" required>
                            I understand this will reset all settings and cannot be undone
                        </label>
                    </p>
                    
                    <p>
                        <button type="button" id="ross-reset-button" class="button button-danger" 
                                style="background: #dc3545; color: white; border-color: #dc3545;">
                            🗑️ Reset All Settings to Default
                        </button>
                    </p>
                </form>
            </div>
            
            <!-- Backup Current Settings -->
            <div class="card" style="max-width: 600px; margin-top: 20px;">
                <h2>Backup Current Settings</h2>
                <p>Before resetting, you might want to note down your current settings:</p>
                
                <?php
                $header_options = get_option('ross_theme_header_options');
                $footer_options = get_option('ross_theme_footer_options');
                $general_options = get_option('ross_theme_general_options');
                ?>
                
                <div style="background: #f8f9fa; padding: 15px; border-radius: 4px; margin: 15px 0;">
                    <h3>Current Header Options:</h3>
                    <div style="background: white; padding: 10px; border-radius: 4px; font-family: monospace; font-size: 12px; max-height: 200px; overflow-y: auto;">
                        <?php 
                        if ($header_options) {
                            foreach ($header_options as $key => $value) {
                                echo '<strong>' . esc_html($key) . ':</strong> ' . esc_html($value) . '<br>';
                            }
                        } else {
                            echo 'No header options set yet.';
                        }
                        ?>
                    </div>
                </div>
                
                <div style="background: #f8f9fa; padding: 15px; border-radius: 4px; margin: 15px 0;">
                    <h3>Current Footer Options:</h3>
                    <div style="background: white; padding: 10px; border-radius: 4px; font-family: monospace; font-size: 12px; max-height: 200px; overflow-y: auto;">
                        <?php 
                        if ($footer_options) {
                            foreach ($footer_options as $key => $value) {
                                echo '<strong>' . esc_html($key) . ':</strong> ' . esc_html($value) . '<br>';
                            }
                        } else {
                            echo 'No footer options set yet.';
                        }
                        ?>
                    </div>
                </div>
                
                <div style="background: #f8f9fa; padding: 15px; border-radius: 4px; margin: 15px 0;">
                    <h3>Current General Options:</h3>
                    <div style="background: white; padding: 10px; border-radius: 4px; font-family: monospace; font-size: 12px; max-height: 200px; overflow-y: auto;">
                        <?php 
                        if ($general_options) {
                            foreach ($general_options as $key => $value) {
                                echo '<strong>' . esc_html($key) . ':</strong> ' . esc_html($value) . '<br>';
                            }
                        } else {
                            echo 'No general options set yet.';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        
        <script>
        jQuery(document).ready(function($) {
            $('#ross-reset-button').on('click', function(e) {
                e.preventDefault();
                
                if (!$('input[name="confirm_reset"]').is(':checked')) {
                    alert('Please confirm that you understand this action cannot be undone.');
                    return;
                }
                
                if (confirm('🚨 ARE YOU SURE?\n\nThis will reset ALL theme settings to default values.\nAll your current settings will be lost!\n\nThis action cannot be undone.')) {
                    $('#ross-reset-form').submit();
                }
            });
        });
        </script>
        
        <style>
        .button-danger:hover {
            background: #c82333 !important;
            border-color: #c82333 !important;
        }
        </style>
        <?php
    }
    
    /**
     * Handle reset request
     */
    public function handle_reset_request() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['ross_reset_nonce'], 'ross_theme_reset_nonce')) {
            wp_die('Security verification failed.');
        }
        
        // Verify user capabilities
        if (!current_user_can('manage_options')) {
            wp_die('You do not have permission to perform this action.');
        }
        
        // Verify confirmation
        if (!isset($_POST['confirm_reset']) || $_POST['confirm_reset'] != '1') {
            wp_die('Please confirm the reset action.');
        }
        
        // Reset all theme options to defaults
        $this->reset_all_settings();
        
        // Redirect back with success message
        wp_redirect(admin_url('admin.php?page=ross-theme-reset&reset=success'));
        exit;
    }
    
    /**
     * Reset all theme settings to defaults
     */
    private function reset_all_settings() {
        // Default Header Options
        $default_header_options = array(
            'header_style' => 'default',
            'header_width' => 'contained',
            'sticky_header' => 0,
            'header_height' => '80',
            'logo_upload' => '',
            'logo_dark' => '',
            'logo_width' => '200',
            'show_site_title' => 1,
            'enable_topbar' => 0,
            'topbar_left_content' => '',
            'topbar_bg_color' => '#001946',
            'topbar_text_color' => '#ffffff',
            'menu_alignment' => 'left',
            'menu_font_size' => '16',
            'active_item_color' => '#E5C902',
            'mobile_breakpoint' => '768',
            'enable_search' => 1,
            'enable_cta_button' => 1,
            'cta_button_text' => 'Get Free Consultation',
            'cta_button_color' => '#E5C902',
            'header_bg_color' => '#ffffff',
            'header_text_color' => '#333333',
            'header_link_hover_color' => '#E5C902',
            'transparent_homepage' => 0,
        );
        
        // Default Footer Options
        $default_footer_options = array(
            'footer_style' => 'default',
            'footer_columns' => '4',
            'footer_width' => 'contained',
            'footer_padding' => '60',
            'enable_widgets' => 1,
            'widgets_bg_color' => '#001946',
            'widgets_text_color' => '#ffffff',
            'widget_title_color' => '#E5C902',
            'enable_footer_cta' => 0,
            'cta_title' => 'Ready to Get Started?',
            'cta_text' => 'Join us today and start growing your business.',
            'cta_button_text' => 'Contact Us Today',
            'cta_button_url' => '#',
            'cta_icon' => '',
            'cta_bg_type' => 'color',
            'cta_bg_color' => '#E5C902',
            'cta_text_color' => '#001946',
            'cta_button_bg_color' => '#001946',
            'cta_button_text_color' => '#ffffff',
            'cta_padding_top' => 24,
            'cta_padding_right' => 0,
            'cta_padding_bottom' => 24,
            'cta_padding_left' => 0,
            'cta_margin_top' => 0,
            'cta_margin_right' => 0,
            'cta_margin_bottom' => 0,
            'cta_margin_left' => 0,
            'cta_gap' => 12,
            'cta_alignment' => 'center',
            'cta_layout_direction' => 'row',
            'cta_animation' => 'none',
            'cta_anim_delay' => 150,
            'cta_anim_duration' => 400,
            'cta_overlay_enabled' => 0,
            'cta_overlay_type' => 'color',
            'cta_overlay_color' => '',
            'cta_overlay_image' => '',
            'cta_overlay_opacity' => 0.5,
            'enable_social_icons' => 1,
            'facebook_url' => '',
            'linkedin_url' => '',
            'instagram_url' => '',
            'social_icon_color' => '#ffffff',
            'enable_copyright' => 1,
            'copyright_text' => '© ' . date('Y') . ' ROSS MCKINLEY ACCOUNTANTS LTD. All Rights Reserved.',
            'copyright_bg_color' => '#001946',
            'copyright_text_color' => '#ffffff',
            'copyright_alignment' => 'center',
            'copyright_font_size' => 14,
            'copyright_font_weight' => 'normal',
            'copyright_letter_spacing' => 0,
            'custom_footer_css' => '',
            'custom_footer_js' => '',
        );
        
        // Default General Options
        $default_general_options = array(
            'layout_style' => 'full-width',
            'container_width' => '1200',
            'content_spacing' => '40',
            'global_border_radius' => '4',
            'enable_preloader' => 0,
            'primary_color' => '#001946',
            'secondary_color' => '#E5C902',
            'accent_color' => '#0073aa',
            'background_color' => '#ffffff',
            'heading_color' => '#001946',
            'text_color' => '#333333',
            'base_font_family' => 'Raleway',
            'base_font_size' => '16',
            'base_line_height' => '1.6',
            'heading_font_family' => 'Raleway',
            'heading_font_weight' => '600',
            'button_shape' => 'rounded',
            'button_padding' => '12',
            'button_bg_color' => '#E5C902',
            'button_text_color' => '#001946',
            'button_border_radius' => '4',
            'favicon' => '',
            'default_logo' => '',
            'dark_mode_logo' => '',
            'lazy_load_images' => 1,
            'blog_layout' => 'grid',
            'excerpt_length' => '55',
            'show_meta' => 1,
            'read_more_text' => 'Read More',
            'minify_css_js' => 0,
            'remove_emojis' => 0,
            'defer_js_loading' => 0,
            'custom_css' => '',
            'custom_js' => '',
            'google_analytics' => '',
        );
        
        // Update options with defaults
        update_option('ross_theme_header_options', $default_header_options);
        update_option('ross_theme_footer_options', $default_footer_options);
        update_option('ross_theme_general_options', $default_general_options);
        
        // Log the reset action
        $this->log_reset_action();
    }
    
    /**
     * Log reset action for debugging
     */
    private function log_reset_action() {
        $user = wp_get_current_user();
        $log_message = sprintf(
            'Ross Theme settings reset by user: %s (ID: %d) on %s',
            $user->user_login,
            $user->ID,
            current_time('mysql')
        );
        
        error_log('ROSS THEME RESET: ' . $log_message);
    }
    
    /**
     * Show reset success message
     */
    private function show_reset_success() {
        if (isset($_GET['page']) && $_GET['page'] === 'ross-theme-reset' && isset($_GET['reset']) && $_GET['reset'] === 'success') {
            add_action('admin_notices', function() {
                ?>
                <div class="notice notice-success is-dismissible">
                    <p>✅ <strong>Success!</strong> All theme settings have been reset to default values.</p>
                </div>
                <?php
            });
        }
    }
}

// Initialize the reset utility
RossThemeResetUtility::get_instance();