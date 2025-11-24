<?php
/**
 * Test script for Top Bar settings
 * Run this to verify the improved top bar admin interface is working
 */

if (!defined('ABSPATH')) {
    // Load WordPress if not already loaded
    $wp_load = dirname(__FILE__) . '/../../../wp-load.php';
    if (file_exists($wp_load)) {
        require_once $wp_load;
    } else {
        die('WordPress not found');
    }
}

// Get header options
$header_options = get_option('ross_theme_header_options', array());

echo "<h1>Top Bar Settings Test</h1>";
echo "<h2>Current Settings:</h2>";
echo "<pre>";
var_dump($header_options);
echo "</pre>";

// Check if the improved admin file exists
$admin_file = dirname(__FILE__) . '/inc/admin/topbar-admin-improved.php';
echo "<h2>File Check:</h2>";
echo "<p>Improved admin file exists: " . (file_exists($admin_file) ? "YES" : "NO") . "</p>";

// Check CSS and JS files
$css_file = dirname(__FILE__) . '/assets/css/admin/topbar-admin-improved.css';
$js_file = dirname(__FILE__) . '/assets/js/admin/topbar-admin-improved.js';
echo "<p>CSS file exists: " . (file_exists($css_file) ? "YES" : "NO") . "</p>";
echo "<p>JS file exists: " . (file_exists($js_file) ? "YES" : "NO") . "</p>";

// Test the rendering function
if (function_exists('ross_theme_render_topbar_admin_improved')) {
    echo "<h2>Rendering Function Test:</h2>";
    echo "<p>Function exists: YES</p>";
} else {
    echo "<p>Function exists: NO - Need to include the admin file</p>";
    if (file_exists($admin_file)) {
        include_once $admin_file;
        if (function_exists('ross_theme_render_topbar_admin_improved')) {
            echo "<p>Function loaded successfully: YES</p>";
        }
    }
}

// Check frontend template
$template_file = dirname(__FILE__) . '/template-parts/topbar.php';
echo "<h2>Frontend Template:</h2>";
echo "<p>Template file exists: " . (file_exists($template_file) ? "YES" : "NO") . "</p>";

echo "<h2>Sample Frontend Output:</h2>";
// Simulate frontend rendering
$enable = isset($header_options['enable_topbar']) ? $header_options['enable_topbar'] : false;
if ($enable) {
    echo "<p>Top bar is enabled</p>";
    $show_left = isset($header_options['enable_topbar_left']) ? $header_options['enable_topbar_left'] : true;
    $phone = isset($header_options['phone_number']) ? $header_options['phone_number'] : '';
    $social_enable = isset($header_options['enable_social']) ? $header_options['enable_social'] : false;
    
    echo "<ul>";
    echo "<li>Left section enabled: " . ($show_left ? "YES" : "NO") . "</li>";
    echo "<li>Phone number: " . ($phone ? $phone : "Not set") . "</li>";
    echo "<li>Social icons enabled: " . ($social_enable ? "YES" : "NO") . "</li>";
    
    if ($social_enable) {
        echo "<li>Social URLs:<br>";
        $platforms = array('facebook', 'twitter', 'linkedin', 'instagram', 'youtube');
        foreach ($platforms as $platform) {
            $url = isset($header_options["social_{$platform}"]) ? $header_options["social_{$platform}"] : '';
            echo "- $platform: " . ($url ? $url : "Not set") . "<br>";
        }
        echo "</li>";
    }
    
    echo "</ul>";
} else {
    echo "<p>Top bar is disabled</p>";
}

echo "<h2>Next Steps:</h2>";
echo "<ol>";
echo "<li>Visit the admin page: <a href='/wp-admin/admin.php?page=ross-theme-header'>Header Options</a></li>";
echo "<li>Click on the 'Top Bar' tab</li>";
echo "<li>Configure your settings</li>";
echo "<li>Check the frontend to see the results</li>";
echo "</ol>";
?>
