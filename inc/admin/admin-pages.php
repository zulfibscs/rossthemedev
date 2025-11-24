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
            <button class="ross-tab-btn" data-tab="announcement">📣 Announcement</button>
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
                // Include the improved topbar admin interface
                require_once dirname(__FILE__) . '/topbar-admin-improved.php';
                ross_theme_render_topbar_admin_improved();
                ?>
                
                <!-- Enqueue improved admin assets -->
                <style>
                    <?php include_once dirname(__FILE__) . '/../../assets/css/admin/topbar-admin-improved.css'; ?>
                </style>
                
                <script>
                    <?php include_once dirname(__FILE__) . '/../../assets/js/admin/topbar-admin-improved.js'; ?>
                </script>
            </div>
            
            <!-- Announcement Tab -->
            <div class="ross-tab-content" id="tab-announcement">
                <?php
                // Include announcement admin interface (moved out of Top Bar)
                require_once dirname(__FILE__) . '/announcement-admin.php';
                ?>
            </div>

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
    <div class="wrap ross-theme-admin">
        <h1>Footer Options</h1>
        <?php settings_errors(); ?>

        <div class="ross-tabs-nav">
            <button class="ross-tab-btn active" data-tab="layout">🧱 Layout</button>
            <button class="ross-tab-btn" data-tab="widgets">🧰 Widgets</button>
            <button class="ross-tab-btn" data-tab="styling">🎨 Styling</button>
            <button class="ross-tab-btn" data-tab="cta">📢 CTA</button>
            <button class="ross-tab-btn" data-tab="social">🌍 Social</button>
            <button class="ross-tab-btn" data-tab="copyright">🧾 Copyright</button>
        </div>

        <form method="post" action="options.php" class="ross-form-tabbed">
            <?php settings_fields('ross_theme_footer_group'); ?>

            <div class="ross-tab-content active" id="tab-layout">
                <?php do_settings_sections('ross-theme-footer-layout'); ?>
            </div>

            <div class="ross-tab-content" id="tab-widgets">
                <?php do_settings_sections('ross-theme-footer-widgets'); ?>
            </div>

            <div class="ross-tab-content" id="tab-styling">
                <?php do_settings_sections('ross-theme-footer-styling'); ?>
            </div>

            <div class="ross-tab-content" id="tab-cta">
                <?php do_settings_sections('ross-theme-footer-cta'); ?>
            </div>

            <div class="ross-tab-content" id="tab-social">
                <?php do_settings_sections('ross-theme-footer-social'); ?>
            </div>

            <div class="ross-tab-content" id="tab-copyright">
                <?php do_settings_sections('ross-theme-footer-copyright'); ?>
            </div>

            <?php submit_button('Save Footer Settings', 'primary', 'submit', true, array('class' => 'button-large ross-submit')); ?>
        </form>
    </div>

    <style>
        /* reuse admin tab styles from header */
        .ross-theme-admin .ross-tabs-nav { display:flex; gap:0.5rem; margin:1.25rem 0; border-bottom:2px solid #ccc; flex-wrap:wrap; }
        .ross-theme-admin .ross-tab-btn { padding:0.6rem 1rem; background:#f1f1f1; border:none; border-bottom:3px solid transparent; cursor:pointer; font-weight:600; }
        .ross-theme-admin .ross-tab-btn.active { background:white; border-bottom-color:#0073aa; color:#0073aa; }
        .ross-theme-admin .ross-tab-content { display:none; background:white; padding:1.5rem; border:1px solid #ccc; margin-bottom:1.5rem; }
        .ross-theme-admin .ross-tab-content.active { display:block; }
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function(){
        var tabBtns = document.querySelectorAll('.ross-tab-btn');
        var tabContents = document.querySelectorAll('.ross-tab-content');
        tabBtns.forEach(function(btn){
            btn.addEventListener('click', function(e){
                e.preventDefault();
                var tab = this.getAttribute('data-tab');
                tabBtns.forEach(function(b){ b.classList.remove('active'); });
                tabContents.forEach(function(c){ c.classList.remove('active'); });
                this.classList.add('active');
                document.getElementById('tab-' + tab).classList.add('active');
            });
        });
    });
    </script>

    <?php
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