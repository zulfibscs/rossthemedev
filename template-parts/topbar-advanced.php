<?php
/**
 * Advanced Top Bar Template
 * Modern, flexible top bar with enhanced functionality
 */

if (!defined('ABSPATH')) exit;

// Debug removed - template is working properly

// Define helper function first
function render_content_item_display($item) {
    $type = isset($item['type']) ? $item['type'] : 'text';
    $content = isset($item['content']) ? $item['content'] : '';
    $icon = isset($item['icon']) ? $item['icon'] : '';
    $link = isset($item['link']) ? $item['link'] : '';
    
    if (empty($content)) {
        return;
    }
    
    $output = '';
    $item_class = 'ross-topbar-item ross-topbar-' . esc_attr($type);
    
    // Add icon if specified
    if (!empty($icon)) {
        $output .= '<i class="' . esc_attr($icon) . '"></i> ';
    }
    
    // Build content based on type
    switch ($type) {
        case 'phone':
            $phone_number = preg_replace('/[^0-9+]/', '', $content);
            $output .= '<a href="tel:' . esc_attr($phone_number) . '">' . esc_html($content) . '</a>';
            break;
            
        case 'email':
            $output .= '<a href="mailto:' . esc_attr($content) . '">' . esc_html($content) . '</a>';
            break;
            
        case 'link':
            $href = !empty($link) ? $link : '#';
            $output .= '<a href="' . esc_url($href) . '">' . esc_html($content) . '</a>';
            break;
            
        case 'social':
            if (!empty($link)) {
                $output = '<a href="' . esc_url($link) . '" target="_blank" rel="noopener noreferrer" class="ross-social-icon" title="' . esc_attr($content) . '">';
                if (!empty($icon)) {
                    $output .= '<i class="' . esc_attr($icon) . '"></i>';
                } else {
                    $output .= esc_html(substr($content, 0, 1));
                }
                $output .= '</a>';
            } else {
                $output .= esc_html($content);
            }
            break;
            
        case 'custom':
            // Custom HTML - already sanitized
            $output .= $content;
            break;
            
        default:
            $output .= esc_html($content);
    }
    
    echo '<span class="' . esc_attr($item_class) . '">' . $output . '</span>';
}

$options = get_option('ross_advanced_topbar_options', array());

// Check if top bar is enabled - always show for testing
if (!isset($options['enable_topbar'])) {
    // Set default enabled state
    $options['enable_topbar'] = 1;
    $options['layout_style'] = 'default';
    // Add default content
    $options['left_content'] = array(
        array('type' => 'phone', 'content' => '+1-234-567-8900', 'icon' => 'fa fa-phone')
    );
    $options['right_content'] = array(
        array('type' => 'email', 'content' => 'info@example.com', 'icon' => 'fa fa-envelope')
    );
}
if (!$options['enable_topbar']) {
    return;
}

// Get layout style
$layout_style = isset($options['layout_style']) ? $options['layout_style'] : 'default';

// Get visibility settings
$visibility_class = '';
if (isset($options['sticky_behavior']) && $options['sticky_behavior'] !== 'none') {
    $visibility_class .= ' sticky-' . esc_attr($options['sticky_behavior']);
}

// Get animation settings
$animation_class = '';
if (isset($options['animation_type']) && !empty($options['animation_type'])) {
    $animation_class .= ' animated animation-' . esc_attr($options['animation_type']);
}

// Get mobile settings
$mobile_class = '';
if (isset($options['mobile_hide']) && $options['mobile_hide']) {
    $mobile_class .= ' mobile-hidden';
}

// Build CSS classes
$css_classes = array(
    'ross-advanced-topbar',
    'layout-' . esc_attr($layout_style),
    $visibility_class,
    $animation_class,
    $mobile_class
);

$css_classes = array_filter($css_classes);
$css_class = implode(' ', $css_classes);

?>
<div id="ross-advanced-topbar" class="<?php echo esc_attr($css_class); ?>" role="banner" aria-label="<?php esc_attr_e('Top Bar Information', 'ross-theme'); ?>">
    <div class="ross-topbar-container">
        <?php
        // Render content sections based on layout
        $sections = array('left', 'center', 'right');
        
        foreach ($sections as $section) {
            $key = $section . '_content';
            // Debug: check if content exists
            if (!isset($options[$key]) || empty($options[$key])) {
                // For demo, show placeholder content
                if ($section === 'left') {
                    echo '<div class="ross-topbar-' . esc_attr($section) . '">';
                    echo '<span class="ross-topbar-item"><i class="fa fa-phone"></i> +1-234-567-8900</span>';
                    echo '</div>';
                } elseif ($section === 'right') {
                    echo '<div class="ross-topbar-' . esc_attr($section) . '">';
                    echo '<span class="ross-topbar-item"><i class="fa fa-envelope"></i> info@example.com</span>';
                    echo '</div>';
                }
                continue;
            }
            
            echo '<div class="ross-topbar-' . esc_attr($section) . '">';
            
            foreach ($options[$key] as $item) {
                render_content_item_display($item);
            }
            
            echo '</div>';
        }
        ?>
    </div>
</div>

<?php
/**
 * Output JavaScript for advanced features
 */
function output_topbar_scripts($options) {
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const topbar = document.getElementById('ross-advanced-topbar');
        if (!topbar) return;
        
        // Sticky behavior handlers
        <?php if (isset($options['sticky_behavior']) && $options['sticky_behavior'] === 'scroll-up'): ?>
        let lastScrollTop = 0;
        window.addEventListener('scroll', function() {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            
            if (scrollTop > lastScrollTop && scrollTop > 100) {
                topbar.classList.remove('visible');
            } else {
                topbar.classList.add('visible');
            }
            
            lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
        });
        <?php endif; ?>
        
        <?php if (isset($options['sticky_behavior']) && $options['sticky_behavior'] === 'scroll-down'): ?>
        let lastScrollTop = 0;
        window.addEventListener('scroll', function() {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            
            if (scrollTop > lastScrollTop && scrollTop > 100) {
                topbar.classList.add('hidden');
            } else {
                topbar.classList.remove('hidden');
            }
            
            lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
        });
        <?php endif; ?>
        
        // Time-based display
        <?php if (isset($options['time_enabled']) && $options['time_enabled']): ?>
        function checkTimeDisplay() {
            const now = new Date();
            const currentHour = now.getHours();
            const startHour = <?php echo isset($options['time_start']) ? (int)$options['time_start'] : 0; ?>;
            const endHour = <?php echo isset($options['time_end']) ? (int)$options['time_end'] : 23; ?>;
            
            if (currentHour >= startHour && currentHour <= endHour) {
                topbar.style.display = 'block';
            } else {
                topbar.style.display = 'none';
            }
        }
        
        checkTimeDisplay();
        setInterval(checkTimeDisplay, 60000); // Check every minute
        <?php endif; ?>
        
        // Add smooth reveal animation on page load
        if (topbar.classList.contains('animated')) {
            setTimeout(function() {
                topbar.style.opacity = '1';
                topbar.style.transform = 'translateY(0)';
            }, 100);
        }
    });
    </script>
    <?php
}

output_topbar_scripts($options);
?>

<style>
/* Base Top Bar Styles */
.ross-advanced-topbar {
    background: linear-gradient(135deg, #001946 0%, #003d7a 100%);
    color: #ffffff;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    font-size: 14px;
    line-height: 1.5;
    border-bottom: 1px solid rgba(229, 201, 2, 0.3);
    position: relative;
    z-index: 1000;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.ross-topbar-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    min-height: 48px;
    position: relative;
}

/* Layout Styles */
.ross-advanced-topbar.layout-default .ross-topbar-left,
.ross-advanced-topbar.layout-default .ross-topbar-center,
.ross-advanced-topbar.layout-default .ross-topbar-right {
    flex: 1;
}

.ross-advanced-topbar.layout-default .ross-topbar-center {
    text-align: center;
}

.ross-advanced-topbar.layout-default .ross-topbar-right {
    text-align: right;
}

.ross-advanced-topbar.layout-centered .ross-topbar-container {
    justify-content: center;
    text-align: center;
}

.ross-advanced-topbar.layout-centered .ross-topbar-left,
.ross-advanced-topbar.layout-centered .ross-topbar-right {
    display: none;
}

.ross-advanced-topbar.layout-minimal .ross-topbar-center {
    display: none;
}

.ross-advanced-topbar.layout-minimal .ross-topbar-left {
    flex: 2;
    text-align: left;
}

.ross-advanced-topbar.layout-minimal .ross-topbar-right {
    flex: 1;
    text-align: right;
}

.ross-advanced-topbar.layout-split .ross-topbar-container {
    justify-content: space-between;
}

.ross-advanced-topbar.layout-split .ross-topbar-left {
    flex: 1;
    text-align: left;
}

.ross-advanced-topbar.layout-split .ross-topbar-center {
    flex: 0;
    display: none;
}

.ross-advanced-topbar.layout-split .ross-topbar-right {
    flex: 1;
    text-align: right;
}

/* Content Items */
.ross-topbar-item {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    margin-right: 20px;
    font-size: 13px;
    font-weight: 400;
    transition: all 0.3s ease;
    position: relative;
}

.ross-topbar-item:last-child {
    margin-right: 0;
}

.ross-topbar-item:hover {
    transform: translateY(-1px);
}

.ross-topbar-item i {
    color: #E5C902;
    font-size: 14px;
    transition: all 0.3s ease;
}

.ross-topbar-item:hover i {
    color: #ffffff;
    transform: scale(1.1);
}

.ross-topbar-item a {
    color: #ffffff;
    text-decoration: none;
    transition: all 0.3s ease;
    position: relative;
}

.ross-topbar-item a::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 0;
    width: 0;
    height: 1px;
    background: #E5C902;
    transition: width 0.3s ease;
}

.ross-topbar-item a:hover {
    color: #E5C902;
}

.ross-topbar-item a:hover::after {
    width: 100%;
}

/* Social Media Icons */
.ross-topbar-social {
    display: inline-flex;
    align-items: center;
    gap: 12px;
}

.ross-social-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    background: rgba(255, 255, 255, 0.1);
    color: #ffffff;
    text-decoration: none;
    border-radius: 50%;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    font-size: 14px;
    position: relative;
    overflow: hidden;
}

.ross-social-icon::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    background: #E5C902;
    border-radius: 50%;
    transition: all 0.3s ease;
    transform: translate(-50%, -50%);
    z-index: 0;
}

.ross-social-icon:hover::before {
    width: 100%;
    height: 100%;
}

.ross-social-icon i {
    position: relative;
    z-index: 1;
    transition: color 0.3s ease;
}

.ross-social-icon:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 12px rgba(229, 201, 2, 0.3);
}

.ross-social-icon:hover i {
    color: #001946;
}

/* Sticky Behaviors */
.ross-advanced-topbar.sticky-always {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1000;
}

.ross-advanced-topbar.sticky-scroll-up {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1000;
    transform: translateY(-100%);
    transition: transform 0.3s ease;
}

.ross-advanced-topbar.sticky-scroll-up.visible {
    transform: translateY(0);
}

.ross-advanced-topbar.sticky-scroll-down {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1000;
    transform: translateY(0);
    transition: transform 0.3s ease;
}

.ross-advanced-topbar.sticky-scroll-down.hidden {
    transform: translateY(-100%);
}

/* Animation Effects */
.ross-advanced-topbar.animated.animation-slide {
    animation: slideDown 0.6s ease-out;
}

.ross-advanced-topbar.animated.animation-fade {
    animation: fadeIn 0.8s ease-out;
}

.ross-advanced-topbar.animated.animation-bounce {
    animation: bounceIn 0.8s ease-out;
}

.ross-advanced-topbar.animated.animation-elastic {
    animation: elasticIn 1s ease-out;
}

/* Keyframes */
@keyframes slideDown {
    from {
        transform: translateY(-100%);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

@keyframes bounceIn {
    0% {
        transform: translateY(-100%);
        opacity: 0;
    }
    60% {
        transform: translateY(10px);
        opacity: 1;
    }
    100% {
        transform: translateY(0);
        opacity: 1;
    }
}

@keyframes elasticIn {
    0% {
        transform: translateY(-100%);
        opacity: 0;
    }
    70% {
        transform: translateY(20px);
        opacity: 1;
    }
    85% {
        transform: translateY(-10px);
    }
    100% {
        transform: translateY(0);
        opacity: 1;
    }
}

/* Responsive Design */
@media (max-width: 1024px) {
    .ross-topbar-container {
        padding: 0 15px;
    }
    
    .ross-topbar-item {
        margin-right: 15px;
        font-size: 12px;
    }
    
    .ross-social-icon {
        width: 28px;
        height: 28px;
        font-size: 12px;
    }
}

@media (max-width: 768px) {
    .ross-advanced-topbar .ross-topbar-container {
        flex-direction: column;
        gap: 12px;
        padding: 12px 15px;
        min-height: auto;
    }
    
    .ross-advanced-topbar.layout-default .ross-topbar-left,
    .ross-advanced-topbar.layout-default .ross-topbar-center,
    .ross-advanced-topbar.layout-default .ross-topbar-right {
        flex: none;
        width: 100%;
        text-align: center;
    }
    
    .ross-advanced-topbar.layout-minimal .ross-topbar-left,
    .ross-advanced-topbar.layout-minimal .ross-topbar-right {
        flex: none;
        width: 100%;
        text-align: center;
    }
    
    .ross-topbar-item {
        margin-right: 12px;
        font-size: 11px;
    }
    
    .ross-topbar-item i {
        font-size: 12px;
    }
    
    .ross-social-icon {
        width: 26px;
        height: 26px;
        font-size: 11px;
    }
    
    .ross-advanced-topbar.mobile-hidden {
        display: none;
    }
}

@media (max-width: 480px) {
    .ross-advanced-topbar .ross-topbar-container {
        gap: 8px;
        padding: 8px 10px;
    }
    
    .ross-topbar-item {
        margin-right: 8px;
        font-size: 10px;
    }
    
    .ross-topbar-item i {
        font-size: 10px;
    }
    
    .ross-social-icon {
        width: 24px;
        height: 24px;
        font-size: 10px;
    }
    
    .ross-topbar-social {
        gap: 8px;
    }
}

/* Dark Mode Support */
@media (prefers-color-scheme: dark) {
    .ross-advanced-topbar {
        background: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 100%);
        border-bottom-color: rgba(229, 201, 2, 0.2);
    }
}

/* High Contrast Mode */
@media (prefers-contrast: high) {
    .ross-advanced-topbar {
        border-bottom-width: 2px;
        border-bottom-color: #ffffff;
    }
    
    .ross-topbar-item a {
        text-decoration: underline;
    }
}

/* Reduced Motion */
@media (prefers-reduced-motion: reduce) {
    .ross-advanced-topbar,
    .ross-topbar-item,
    .ross-topbar-item a,
    .ross-topbar-item i,
    .ross-social-icon {
        transition: none;
        animation: none;
    }
    
    .ross-topbar-item:hover {
        transform: none;
    }
    
    .ross-social-icon:hover {
        transform: none;
    }
}

/* Print Styles */
@media print {
    .ross-advanced-topbar {
        display: none;
    }
}
</style>

<?php
?>
