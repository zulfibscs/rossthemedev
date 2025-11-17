/**
 * Top Bar Customizer - Example Implementation & Testing
 * This file shows how to use the top bar customizer in real scenarios
 */

// ============================================
// EXAMPLE 1: Display top bar with custom wrapper
// ============================================

// In template file (e.g., header.php)
?>
<header class="site-header">
    <!-- Top bar with custom classes -->
    <div class="header-wrapper">
        <?php 
        // Get top bar visibility
        if (get_theme_mod('ross_topbar_enable', false)) {
            get_template_part('template-parts/topbar');
        }
        ?>
        
        <!-- Rest of header content -->
    </div>
</header>
<?php

// ============================================
// EXAMPLE 2: Conditionally display elements
// ============================================

// Only show email if configured
if (!empty(get_theme_mod('ross_topbar_email'))) {
    echo '<a href="mailto:' . esc_attr(get_theme_mod('ross_topbar_email')) . '">';
    echo esc_html(get_theme_mod('ross_topbar_email'));
    echo '</a>';
}

// Only show phone if configured
$phone = get_theme_mod('ross_topbar_phone', '');
if (!empty($phone)) {
    echo '<a href="tel:' . esc_attr($phone) . '">';
    echo esc_html($phone);
    echo '</a>';
}

// ============================================
// EXAMPLE 3: Build dynamic social links
// ============================================

function get_topbar_social_links() {
    $social_enable = get_theme_mod('ross_topbar_social_enable', false);
    if (!$social_enable) {
        return array();
    }

    $platforms = array('facebook', 'twitter', 'linkedin', 'instagram', 'youtube');
    $links = array();

    foreach ($platforms as $platform) {
        $enabled = get_theme_mod("ross_topbar_social_{$platform}_enabled", false);
        $url = get_theme_mod("ross_topbar_social_{$platform}_url", '');
        $icon = get_theme_mod("ross_topbar_social_{$platform}_icon", '');

        if ($enabled && !empty($url)) {
            $links[] = array(
                'platform' => $platform,
                'url'      => esc_url($url),
                'icon'     => esc_attr($icon),
                'label'    => ucfirst($platform),
            );
        }
    }

    return $links;
}

// Usage:
$social_links = get_topbar_social_links();
foreach ($social_links as $link) {
    echo '<a href="' . $link['url'] . '" class="social-link" title="' . $link['label'] . '">';
    echo '<i class="' . $link['icon'] . '"></i>';
    echo '</a>';
}

// ============================================
// EXAMPLE 4: Programmatically set defaults
// ============================================

// Set default top bar settings on theme activation
function setup_topbar_defaults() {
    $defaults = array(
        'ross_topbar_enable' => true,
        'ross_topbar_bg_color' => '#001946',
        'ross_topbar_text_color' => '#ffffff',
        'ross_topbar_icon_color' => '#E5C902',
        'ross_topbar_font_size' => 14,
        'ross_topbar_alignment' => 'left',
        'ross_topbar_left_content' => 'Welcome to our site!',
        'ross_topbar_phone' => '',
        'ross_topbar_email' => '',
        'ross_topbar_announcement' => 'Special offer: 20% off!',
        'ross_topbar_marquee_enable' => false,
        'ross_topbar_social_enable' => true,
        'ross_topbar_shadow_enable' => false,
        'ross_topbar_gradient_enable' => false,
        'ross_topbar_border_width' => 0,
    );

    foreach ($defaults as $option => $value) {
        if (empty(get_theme_mod($option))) {
            set_theme_mod($option, $value);
        }
    }
}
// Call on theme activation: add_action('after_switch_theme', 'setup_topbar_defaults');

// ============================================
// EXAMPLE 5: Export/Import settings
// ============================================

// Export top bar settings as JSON
function export_topbar_settings() {
    $settings = array(
        'enable' => get_theme_mod('ross_topbar_enable', false),
        'left_content' => get_theme_mod('ross_topbar_left_content', ''),
        'phone' => get_theme_mod('ross_topbar_phone', ''),
        'email' => get_theme_mod('ross_topbar_email', ''),
        'announcement' => get_theme_mod('ross_topbar_announcement', ''),
        'bg_color' => get_theme_mod('ross_topbar_bg_color', '#001946'),
        'text_color' => get_theme_mod('ross_topbar_text_color', '#ffffff'),
        'icon_color' => get_theme_mod('ross_topbar_icon_color', '#E5C902'),
        'font_size' => get_theme_mod('ross_topbar_font_size', 14),
        'alignment' => get_theme_mod('ross_topbar_alignment', 'left'),
        'social_enable' => get_theme_mod('ross_topbar_social_enable', false),
        'shadow_enable' => get_theme_mod('ross_topbar_shadow_enable', false),
        'gradient_enable' => get_theme_mod('ross_topbar_gradient_enable', false),
        'gradient_color1' => get_theme_mod('ross_topbar_gradient_color1', '#001946'),
        'gradient_color2' => get_theme_mod('ross_topbar_gradient_color2', '#003d7a'),
        'border_width' => get_theme_mod('ross_topbar_border_width', 0),
        'border_color' => get_theme_mod('ross_topbar_border_color', '#E5C902'),
    );
    
    return json_encode($settings, JSON_PRETTY_PRINT);
}

// Import top bar settings from JSON
function import_topbar_settings($json_data) {
    $settings = json_decode($json_data, true);
    
    if (!is_array($settings)) {
        return false;
    }
    
    foreach ($settings as $key => $value) {
        $theme_mod_key = 'ross_topbar_' . $key;
        set_theme_mod($theme_mod_key, $value);
    }
    
    return true;
}

// ============================================
// EXAMPLE 6: Get formatted topbar HTML
// ============================================

function get_topbar_html() {
    if (!get_theme_mod('ross_topbar_enable', false)) {
        return '';
    }
    
    ob_start();
    get_template_part('template-parts/topbar');
    return ob_get_clean();
}

// Usage in controller/hook:
$topbar_html = get_topbar_html();

// ============================================
// EXAMPLE 7: Customize top bar styling
// ============================================

// Add custom CSS based on settings
function custom_topbar_css() {
    $enable = get_theme_mod('ross_topbar_enable', false);
    if (!$enable) {
        return;
    }
    
    $padding = get_theme_mod('ross_topbar_padding', 12);
    $z_index = get_theme_mod('ross_topbar_z_index', 999);
    
    echo '<style id="custom-topbar-css">';
    echo '.site-topbar { padding: ' . absint($padding) . 'px 0; z-index: ' . absint($z_index) . '; }';
    echo '</style>';
}
// Uncomment to use: add_action('wp_head', 'custom_topbar_css');

// ============================================
// EXAMPLE 8: Mobile-specific adjustments
// ============================================

function responsive_topbar_css() {
    $enable = get_theme_mod('ross_topbar_enable', false);
    if (!$enable) {
        return;
    }
    
    echo '<style>';
    echo '@media (max-width: 768px) {';
    echo '  .topbar-inner { grid-template-columns: 1fr; gap: 8px; }';
    echo '  .topbar-left, .topbar-right { text-align: center; }';
    echo '  .topbar-right { justify-content: center; }';
    echo '}';
    echo '</style>';
}

// ============================================
// EXAMPLE 9: Add custom sanitization
// ============================================

// Add phone validation
function ross_sanitize_and_validate_phone($phone) {
    $phone = sanitize_text_field($phone);
    
    // Remove common formatting characters
    $phone = preg_replace('/[^0-9+\-\s\(\)]/', '', $phone);
    
    return $phone;
}

// ============================================
// EXAMPLE 10: Check if announcement has content
// ============================================

function has_announcement() {
    $announcement = get_theme_mod('ross_topbar_announcement', '');
    $enable = get_theme_mod('ross_topbar_enable', false);
    
    return $enable && !empty(trim($announcement));
}

// Usage:
if (has_announcement()) {
    // Show announcement-specific styling
    echo '<div class="announcement-active">';
    echo get_theme_mod('ross_topbar_announcement');
    echo '</div>';
}

// ============================================
// TESTING SCENARIOS
// ============================================

/*
TEST 1: Basic Setup
- Enable top bar in customizer
- Add phone number
- Verify it displays on frontend
- Check live preview updates

TEST 2: Social Links
- Enable social icons
- Add Facebook URL and enable
- Verify icon displays with correct href
- Test other platforms

TEST 3: Colors & Styling
- Change background color in customizer
- Enable gradient with two colors
- Enable shadow
- Verify all apply without page reload

TEST 4: Responsive
- Open site on mobile (< 768px)
- Verify layout stacks vertically
- Check text alignment is centered
- Test social links display correctly

TEST 5: Marquee Animation
- Add announcement text
- Enable marquee toggle
- Verify text scrolls left to right
- Disable and verify animation stops

TEST 6: Conditional Display
- Disable top bar in customizer
- Verify topbar not visible on frontend
- Re-enable and verify it appears

TEST 7: Data Persistence
- Set various options
- Reload page
- Verify settings still applied
- Switch themes and back (if applicable)

TEST 8: Accessibility
- Tab through top bar elements
- Verify social links are keyboard navigable
- Check color contrast (WCAG AA/AAA)
- Test with screen reader
*/

?>
