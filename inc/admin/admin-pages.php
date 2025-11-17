<?php
/**
 * Admin Pages Module
 */

// Hook to enqueue scripts for Ross Theme pages - PRIORITY 1 (FIRST)
function ross_theme_enqueue_admin_scripts($hook) {
    // Check if we're on a Ross theme admin page
    if (strpos($hook, 'ross-theme') !== false) {
        wp_enqueue_media();
        wp_enqueue_script('jquery');
    }
}
add_action('admin_enqueue_scripts', 'ross_theme_enqueue_admin_scripts', 1);

function ross_theme_admin_menu() {
    add_menu_page(
        'Ross Theme Settings',
        'Ross Theme', 
        'manage_options',
        'ross-theme',
        'ross_theme_main_page',
        'dashicons-admin-customizer',
        60
    );
    
    add_submenu_page(
        'ross-theme',
        'Header Options',
        'Header Options',
        'manage_options', 
        'ross-theme-header',
        'ross_theme_header_page'
    );
    
    add_submenu_page(
        'ross-theme', 
        'Footer Options',
        'Footer Options',
        'manage_options',
        'ross-theme-footer',
        'ross_theme_footer_page'
    );
    
    add_submenu_page(
        'ross-theme',
        'General Settings', 
        'General Settings',
        'manage_options',
        'ross-theme-general',
        'ross_theme_general_page'
    );
    
    // Note: Top Bar Settings are handled via WordPress Customizer (customize.php)
    // Reset Settings submenu is handled by RossThemeResetUtility
}
add_action('admin_menu', 'ross_theme_admin_menu');

function ross_theme_main_page() {
    ?>
    <div class="wrap">
        <h1>Ross Theme Settings</h1>
        <div class="card">
            <h2>Welcome to Ross Theme</h2>
            <p>Use the submenus to configure different aspects of your theme:</p>
            <ul>
                <li><strong><a href="<?php echo admin_url('admin.php?page=ross-theme-header'); ?>">Header Options</a>:</strong> Configure logo, navigation, and header layout</li>
                <li><strong><a href="<?php echo admin_url('admin.php?page=ross-theme-footer'); ?>">Footer Options</a>:</strong> Setup footer layout, widgets, and copyright</li>
                <li><strong><a href="<?php echo admin_url('admin.php?page=ross-theme-general'); ?>">General Settings</a>:</strong> Customize colors, typography, and global settings</li>
                <li><strong><a href="<?php echo admin_url('customize.php'); ?>">🎯 Top Bar Settings</a>:</strong> Configure top bar in WordPress Customizer</li>
                <li><strong><a href="<?php echo admin_url('admin.php?page=ross-theme-reset'); ?>">Reset Settings</a>:</strong> Reset all settings to defaults</li>
            </ul>
        </div>
    </div>
    <?php
}

function ross_theme_header_page() {
    ?>
    <div class="wrap ross-theme-admin">
        <h1>Header Options</h1>
        <?php settings_errors(); ?>
        
        <!-- Tab Navigation -->
        <div class="ross-tabs-nav">
            <button class="ross-tab-btn active" data-tab="layout">🧱 Layout & Structure</button>
            <button class="ross-tab-btn" data-tab="logo">🧭 Logo & Branding</button>
            <button class="ross-tab-btn" data-tab="topbar">☎️ Top Bar</button>
            <button class="ross-tab-btn" data-tab="navigation">🔗 Navigation</button>
            <button class="ross-tab-btn" data-tab="cta">🔍 CTA & Search</button>
            <button class="ross-tab-btn" data-tab="appearance">🌗 Appearance</button>
        </div>
        
        <form method="post" action="options.php" class="ross-form-tabbed">
            <?php
            settings_fields('ross_theme_header_group');
            ?>
            
            <!-- Layout & Structure Tab -->
            <div class="ross-tab-content active" id="tab-layout">
                <?php do_settings_sections('ross-theme-header-layout'); ?>
            </div>
            
            <!-- Logo & Branding Tab -->
            <div class="ross-tab-content" id="tab-logo">
                <?php do_settings_sections('ross-theme-header-logo'); ?>
            </div>
            
            <!-- Top Bar Tab -->
            <div class="ross-tab-content" id="tab-topbar">
                <?php
                // Render Top Bar Settings manually for a clean two-column layout
                $opts = get_option('ross_theme_header_options', array());
                $get = function($key, $default = '') use ($opts) {
                    return isset($opts[$key]) ? $opts[$key] : $default;
                };
                ?>

                <div class="ross-topbar-grid" style="display:flex; gap:24px; align-items:flex-start;">
                    <div class="ross-topbar-left" style="flex:1; min-width:320px; background:#fff; padding:16px; border-radius:8px; border:1px solid #eee; box-shadow:0 1px 2px rgba(0,0,0,0.03);">
                        <p style="margin-bottom:10px;"><label><input type="checkbox" name="ross_theme_header_options[enable_topbar]" value="1" <?php checked(1, $get('enable_topbar', 0)); ?> /> <strong>Enable Top Bar</strong> <span class="ross-help" title="Toggle the top bar visibility on the front-end">?</span></label></p>

                        <h3 style="font-size:13px; margin-top:12px; margin-bottom:8px;">— Layout & Content —</h3>
                        <p style="margin:6px 0;"><label><input type="checkbox" name="ross_theme_header_options[enable_topbar_left]" value="1" <?php checked(1, $get('enable_topbar_left', 1)); ?> /> Enable Left Section <span class="ross-help" title="Show or hide the left area (phone/text/custom HTML)">?</span></label></p>
                        <p style="margin-top:8px; margin-bottom:6px;"><strong>Left Section Content</strong> <span class="ross-help" title="Use short content: icons, short text or a link. Keep it concise for a clean top bar.">?</span></p>
                        <?php
                        $left_val = $get('topbar_left_content', '');
                        $editor_id = 'ross_topbar_left_editor';
                        wp_editor($left_val, $editor_id, array('textarea_name' => 'ross_theme_header_options[topbar_left_content]', 'textarea_rows' => 3, 'teeny' => true, 'media_buttons' => false));
                        ?>
                        <p style="margin-top:8px;"><label>Phone Number<br/><input type="text" name="ross_theme_header_options[phone_number]" value="<?php echo esc_attr($get('phone_number')); ?>" class="regular-text" placeholder="e.g., +44 20 7123 4567" /></label></p>

                        <h3 style="font-size:13px; margin-top:12px;">— Social Icons —</h3>
                        <p><label><input type="checkbox" name="ross_theme_header_options[enable_social]" value="1" <?php checked(1, $get('enable_social', 0)); ?> /> Enable Social Icons</label></p>
                        <p style="margin-top:6px;"><label>🔵 Facebook URL<br/><input type="url" name="ross_theme_header_options[social_facebook]" value="<?php echo esc_attr($get('social_facebook')); ?>" class="regular-text" placeholder="https://facebook.com/yourpage" /></label></p>
                        <p style="margin-top:6px;"><label>𝕏 Twitter URL<br/><input type="url" name="ross_theme_header_options[social_twitter]" value="<?php echo esc_attr($get('social_twitter')); ?>" class="regular-text" placeholder="https://twitter.com/yourprofile" /></label></p>
                        <p style="margin-top:6px;"><label>🔗 LinkedIn URL<br/><input type="url" name="ross_theme_header_options[social_linkedin]" value="<?php echo esc_attr($get('social_linkedin')); ?>" class="regular-text" placeholder="https://linkedin.com/company/yours" /></label></p>

                        <h3 style="font-size:13px; margin-top:12px;">— Announcement Bar —</h3>
                        <p style="margin:6px 0;"><label><input type="checkbox" name="ross_theme_header_options[enable_announcement]" value="1" <?php checked(1, $get('enable_announcement', 0)); ?> /> Enable Announcement <span class="ross-help" title="Display a short announcement in the center of the top bar.">?</span></label></p>
                        <p style="margin-top:8px;"><strong>Announcement Text</strong> <span class="ross-help" title="Supports simple formatting. Preview will show how animation looks.">?</span></p>
                        <?php
                        $ann = $get('announcement_text', '');
                        wp_editor($ann, 'ross_announcement_editor', array('textarea_name' => 'ross_theme_header_options[announcement_text]', 'textarea_rows' => 4, 'teeny' => true, 'media_buttons' => false));
                        ?>
                        <p style="margin-top:8px;"><label>Announcement Animation<br/>
                            <select name="ross_theme_header_options[announcement_animation]">
                                <option value="marquee" <?php selected($get('announcement_animation', 'marquee'), 'marquee'); ?>>Marquee (scroll)</option>
                                <option value="slide" <?php selected($get('announcement_animation', 'marquee'), 'slide'); ?>>Slide</option>
                            </select>
                        </label></p>

                        <h3 style="font-size:13px; margin-top:12px;">— Advanced Style Options —</h3>
                        <p><label><input type="checkbox" name="ross_theme_header_options[topbar_gradient_enable]" value="1" <?php checked(1, $get('topbar_gradient_enable', 0)); ?> /> Enable Gradient Background</label></p>
                        <p style="margin-top:6px;"><label>Border Bottom Width (px)<br/><input type="number" min="0" max="5" name="ross_theme_header_options[topbar_border_width]" value="<?php echo esc_attr($get('topbar_border_width', 0)); ?>" class="small-text" /> px</label></p>
                        <p style="margin-top:6px;"><label><input type="checkbox" name="ross_theme_header_options[topbar_shadow_enable]" value="1" <?php checked(1, $get('topbar_shadow_enable', 0)); ?> /> Enable Drop Shadow</label></p>
                    </div>

                    <div class="ross-topbar-right" style="width:360px; background:#fff; padding:16px; border-radius:8px; border:1px solid #eee;">
                        <h3 style="font-size:13px;">— Colors & Style —</h3>
                        <p style="margin-top:8px;"><label><strong>Background Color</strong> <span class="ross-help" title="Sets the background of the top bar. If gradient is enabled, this is overridden by the gradient.">?</span><br/><small style="color:#666;">Background of the entire top bar</small><br/><input type="text" class="color-picker" id="ross_topbar_bg" name="ross_theme_header_options[topbar_bg_color]" value="<?php echo esc_attr($get('topbar_bg_color', '#001946')); ?>" data-default-color="#001946" /></label></p>
                        <p style="margin-top:6px;"><label><strong>Text Color</strong> <span class="ross-help" title="Color of regular text (phone, left section text) in the top bar.">?</span><br/><small style="color:#666;">Text in left section & phone number</small><br/><input type="text" class="color-picker" id="ross_topbar_text" name="ross_theme_header_options[topbar_text_color]" value="<?php echo esc_attr($get('topbar_text_color', '#ffffff')); ?>" data-default-color="#ffffff" /></label></p>
                        <p style="margin-top:6px;"><label><strong>Icon Color</strong> <span class="ross-help" title="Color of social & custom icons. If not set, defaults to text color.">?</span><br/><small style="color:#666;">Social & custom icon links color</small><br/><input type="text" class="color-picker" id="ross_topbar_icon" name="ross_theme_header_options[topbar_icon_color]" value="<?php echo esc_attr($get('topbar_icon_color', '#ffffff')); ?>" data-default-color="#ffffff" /></label></p>
                        <p style="margin-top:6px;"><label><strong>Border Bottom Color</strong> <span class="ross-help" title="Color of the thin border line at the bottom of the top bar.">?</span><br/><small style="color:#666;">Bottom border line color</small><br/><input type="text" class="color-picker" id="ross_topbar_border" name="ross_theme_header_options[topbar_border_color]" value="<?php echo esc_attr($get('topbar_border_color', '#E5C902')); ?>" data-default-color="#E5C902" /></label></p>
                        <p style="margin-top:6px;"><label><strong>Gradient Color 1</strong> <span class="ross-help" title="First gradient color (start). Enable gradient background to use.">?</span><br/><small style="color:#666;">Gradient start color</small><br/><input type="text" class="color-picker" id="ross_topbar_grad1" name="ross_theme_header_options[topbar_gradient_color1]" value="<?php echo esc_attr($get('topbar_gradient_color1', '#001946')); ?>" data-default-color="#001946" /></label></p>
                        <p style="margin-top:6px;"><label><strong>Gradient Color 2</strong> <span class="ross-help" title="Second gradient color (end). Enable gradient background to use.">?</span><br/><small style="color:#666;">Gradient end color</small><br/><input type="text" class="color-picker" id="ross_topbar_grad2" name="ross_theme_header_options[topbar_gradient_color2]" value="<?php echo esc_attr($get('topbar_gradient_color2', '#003d7a')); ?>" data-default-color="#003d7a" /></label></p>

                        <div id="ross-topbar-preview" style="margin-top:14px; border-radius:6px; padding:10px; border:1px dashed #e6e6e6; background:#f9f9f9;">
                            <div style="font-size:13px; font-weight:700; margin-bottom:8px;">Live Preview</div>
                            <div id="ross-topbar-preview-bar" style="padding:8px; border-radius:4px; display:flex; align-items:center; gap:12px; justify-content:space-between;">
                                <div id="ross-preview-left" style="min-width:100px;">Left</div>
                                <div id="ross-preview-center" style="flex:1; text-align:center; overflow:hidden;"></div>
                                <div id="ross-preview-right" style="min-width:100px; text-align:right;">Icons</div>
                            </div>
                            <div style="margin-top:8px; font-size:12px; color:#666;">Preview updates live as you change colors and announcement text.</div>
                        </div>
                    </div>
                </div>
            </div>

            <script>
            (function(){
                // Initialize color pickers and live preview
                function initTopbarPreview() {
                    var $ = window.jQuery || null;
                    function q(sel){ return document.querySelector(sel); }

                    // Elements
                    var previewBar = q('#ross-topbar-preview-bar');
                    var previewCenter = q('#ross-preview-center');
                    var previewLeft = q('#ross-preview-left');
                    var previewRight = q('#ross-preview-right');
                    var inputBg = q('#ross_topbar_bg');
                    var inputText = q('#ross_topbar_text');
                    var inputIcon = q('#ross_topbar_icon');
                    var inputBorder = q('#ross_topbar_border');
                    var inputG1 = q('#ross_topbar_grad1');
                    var inputG2 = q('#ross_topbar_grad2');
                    var announcementTextarea = document.querySelector('textarea[name="ross_theme_header_options[announcement_text]"]');
                    var animSelect = document.querySelector('select[name="ross_theme_header_options[announcement_animation]"]');
                    var gradientEnabled = document.querySelector('input[name="ross_theme_header_options[topbar_gradient_enable]"]');

                    function applyColors() {
                        var bg = inputBg ? inputBg.value : '#001946';
                        var txt = inputText ? inputText.value : '#ffffff';
                        var icon = inputIcon ? inputIcon.value : txt;
                        var brc = inputBorder ? inputBorder.value : '#E5C902';
                        var g1 = inputG1 ? inputG1.value : '';
                        var g2 = inputG2 ? inputG2.value : '';

                        if (gradientEnabled && gradientEnabled.checked && g1 && g2) {
                            previewBar.style.background = 'linear-gradient(90deg,'+g1+','+g2+')';
                        } else {
                            previewBar.style.background = bg;
                        }
                        previewBar.style.color = txt;
                        previewLeft.style.color = icon;
                        previewRight.style.color = icon;
                        previewBar.style.borderBottom = '3px solid ' + brc;
                    }

                    function applyAnnouncement() {
                        var text = announcementTextarea ? announcementTextarea.value : '';
                        var anim = animSelect ? animSelect.value : 'marquee';
                        previewCenter.innerHTML = text || '<em>No announcement</em>';
                        // Reset classes
                        previewCenter.className = '';
                        if (anim === 'marquee') {
                            previewCenter.classList.add('ross-preview-marquee');
                        } else if (anim === 'slide') {
                            previewCenter.classList.add('ross-preview-slide');
                        }
                    }

                    // Bind events
                    [inputBg,inputText,inputIcon,inputBorder,inputG1,inputG2].forEach(function(el){
                        if (!el) return;
                        el.addEventListener('change', applyColors);
                        el.addEventListener('input', applyColors);
                    });
                    if (announcementTextarea) announcementTextarea.addEventListener('input', applyAnnouncement);
                    if (animSelect) animSelect.addEventListener('change', applyAnnouncement);
                    if (gradientEnabled) gradientEnabled.addEventListener('change', applyColors);

                    // Init color picker if jQuery + wpColorPicker present
                    if ($ && $.fn && $.fn.wpColorPicker) {
                        $('#ross_topbar_bg, #ross_topbar_text, #ross_topbar_icon, #ross_topbar_border, #ross_topbar_grad1, #ross_topbar_grad2').wpColorPicker({
                            change: function(event, ui) { applyColors(); }
                        });
                    }

                    // initial apply
                    applyColors();
                    applyAnnouncement();
                }

                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', initTopbarPreview);
                } else {
                    initTopbarPreview();
                }
            })();
            </script>

            <style>
                .ross-help { display:inline-block; width:18px; height:18px; line-height:18px; text-align:center; border-radius:50%; background:#f1f1f1; color:#333; margin-left:6px; font-size:12px; cursor:help; }
                #ross-topbar-preview-bar { transition: all 0.25s ease; }
                .ross-preview-marquee { white-space:nowrap; animation:ross-marquee 6s linear infinite; }
                .ross-preview-slide { animation:ross-slide 1.2s ease-in-out; }
                @keyframes ross-marquee { 0% { transform:translateX(100%); } 100% { transform:translateX(-100%); } }
                @keyframes ross-slide { 0% { opacity:0; transform:translateY(-8px);} 100% { opacity:1; transform:translateY(0);} }
                
                /* Responsive layout for Top Bar settings */
                @media (max-width: 1200px) {
                    .ross-topbar-grid { flex-direction: column !important; gap: 16px !important; }
                    .ross-topbar-left, .ross-topbar-right { width: 100% !important; min-width: 0 !important; }
                }
                @media (max-width: 768px) {
                    .ross-topbar-grid { padding: 0 8px; }
                    .ross-topbar-left, .ross-topbar-right { padding: 12px !important; }
                    #ross-topbar-preview-bar { flex-direction: column !important; gap: 8px !important; }
                    #ross-preview-left, #ross-preview-right, #ross-preview-center { width: 100% !important; text-align: center !important; }
                }
            </style>

            <!-- Navigation Tab -->
            <div class="ross-tab-content" id="tab-navigation">
                <?php do_settings_sections('ross-theme-header-nav'); ?>
            </div>
            
            <!-- CTA & Search Tab -->
            <div class="ross-tab-content" id="tab-cta">
                <?php do_settings_sections('ross-theme-header-cta'); ?>
            </div>
            
            <!-- Appearance Tab -->
            <div class="ross-tab-content" id="tab-appearance">
                <?php do_settings_sections('ross-theme-header-appearance'); ?>
            </div>
            
            <?php submit_button('Save Header Settings', 'primary', 'submit', true, array('class' => 'button-large ross-submit')); ?>
        </form>
    </div>
    
    <style>
        .ross-theme-admin .ross-tabs-nav {
            display: flex;
            gap: 0.5rem;
            margin: 1.5rem 0;
            border-bottom: 2px solid #ccc;
            flex-wrap: wrap;
        }
        .ross-theme-admin .ross-tab-btn {
            padding: 0.75rem 1.2rem;
            background: #f1f1f1;
            border: none;
            border-bottom: 3px solid transparent;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }
        .ross-theme-admin .ross-tab-btn:hover {
            background: #e9e9e9;
        }
        .ross-theme-admin .ross-tab-btn.active {
            background: white;
            border-bottom-color: #0073aa;
            color: #0073aa;
        }
        .ross-theme-admin .ross-tab-content {
            display: none;
            background: white;
            padding: 2rem;
            border: 1px solid #ccc;
            margin-bottom: 2rem;
        }
        .ross-theme-admin .ross-tab-content.active {
            display: block;
        }
        .ross-theme-admin .ross-form-fields {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }
        @media (max-width: 1000px) {
            .ross-theme-admin .ross-form-fields {
                grid-template-columns: 1fr;
            }
        }
        
    </style>
    
    <script>
    document.addEventListener('DOMContentLoaded', function(){
        var tabBtns = document.querySelectorAll('.ross-tab-btn');
        var tabContents = document.querySelectorAll('.ross-tab-content');
        
        tabBtns.forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                var tabId = this.getAttribute('data-tab');
                
                // Deactivate all tabs
                tabBtns.forEach(function(b) { b.classList.remove('active'); });
                tabContents.forEach(function(c) { c.classList.remove('active'); });
                
                // Activate clicked tab
                this.classList.add('active');
                document.getElementById('tab-' + tabId).classList.add('active');
            });
        });
    });
    </script>
    
    <script type="text/javascript">
    (function() {
        function initMediaUploaders() {
            if (typeof wp === 'undefined' || typeof wp.media === 'undefined') {
                return;
            }
            
            var buttons = document.querySelectorAll('.ross-upload-button');
            buttons.forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    var targetId = this.getAttribute('data-target');
                    if (!targetId) return;
                    
                    var frame = wp.media({
                        title: 'Select Image',
                        button: {text: 'Select'},
                        multiple: false
                    });
                    
                    frame.on('select', function() {
                        var attachment = frame.state().get('selection').first().toJSON();
                        var input = document.getElementById(targetId);
                        if (input) {
                            input.value = attachment.url;
                        }
                    });
                    
                    frame.open();
                    return false;
                });
            });
        }
        
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initMediaUploaders);
        } else {
            initMediaUploaders();
        }
    })();
    </script>
    <?php
}

function ross_theme_footer_page() {
    ?>
    <div class="wrap">
        <h1>Footer Options</h1>
        <?php settings_errors(); ?>
        <form method="post" action="options.php">
            <?php
            settings_fields('ross_theme_footer_group');
            do_settings_sections('ross-theme-footer');
            submit_button('Save Footer Settings');
            ?>
            
}

function ross_theme_general_page() {
    ?>
    <div class="wrap">
        <h1>General Settings</h1>
        <?php settings_errors(); ?>
        <form method="post" action="options.php">
            <?php
            settings_fields('ross_theme_general_group');
            do_settings_sections('ross-theme-general');
            submit_button('Save General Settings');
            ?>
        </form>
    </div>
    
    <script type="text/javascript">
    (function() {
        function initMediaUploaders() {
            if (typeof wp === 'undefined' || typeof wp.media === 'undefined') {
                return;
            }
            
            var buttons = document.querySelectorAll('.ross-upload-button');
            buttons.forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    var targetId = this.getAttribute('data-target');
                    if (!targetId) return;
                    
                    var frame = wp.media({
                        title: 'Select Image',
                        button: {text: 'Select'},
                        multiple: false
                    });
                    
                    frame.on('select', function() {
                        var attachment = frame.state().get('selection').first().toJSON();
                        var input = document.getElementById(targetId);
                        if (input) {
                            input.value = attachment.url;
                        }
                    });
                    
                    frame.open();
                    return false;
                });
            });
        }
        
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initMediaUploaders);
        } else {
            initMediaUploaders();
        }
    })();
    </script>
    <?php
}