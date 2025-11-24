<?php
/**
 * Top Bar Template
 * Displays the top bar with improved admin settings
 */

if (!defined('ABSPATH')) exit;

// Get header options from the improved admin interface
$header_options = get_option('ross_theme_header_options', array());

$enable = isset($header_options['enable_topbar']) ? $header_options['enable_topbar'] : false;
if (!$enable) {
    return;
}

$show_left = isset($header_options['enable_topbar_left']) ? $header_options['enable_topbar_left'] : true;
$left_content = isset($header_options['topbar_left_content']) ? $header_options['topbar_left_content'] : '';
$phone = isset($header_options['phone_number']) ? $header_options['phone_number'] : '';
$announcement_enabled = false; // announcements handled centrally
$social_enable = isset($header_options['enable_social']) ? $header_options['enable_social'] : false;

// Color and style options
$bg_color = isset($header_options['topbar_bg_color']) ? $header_options['topbar_bg_color'] : '#001946';
$text_color = isset($header_options['topbar_text_color']) ? $header_options['topbar_text_color'] : '#ffffff';
$icon_color = isset($header_options['topbar_icon_color']) ? $header_options['topbar_icon_color'] : $text_color;
$gradient_enable = isset($header_options['topbar_gradient_enable']) ? $header_options['topbar_gradient_enable'] : false;
$gradient_color1 = isset($header_options['topbar_gradient_color1']) ? $header_options['topbar_gradient_color1'] : '#001946';
$gradient_color2 = isset($header_options['topbar_gradient_color2']) ? $header_options['topbar_gradient_color2'] : '#003d7a';
$shadow_enable = isset($header_options['topbar_shadow_enable']) ? $header_options['topbar_shadow_enable'] : false;
$border_width = isset($header_options['topbar_border_width']) ? $header_options['topbar_border_width'] : 0;
$border_color = isset($header_options['topbar_border_color']) ? $header_options['topbar_border_color'] : '#E5C902';

// Social media URLs
$social_urls = array(
    'facebook' => isset($header_options['social_facebook']) ? $header_options['social_facebook'] : '',
    'twitter' => isset($header_options['social_twitter']) ? $header_options['social_twitter'] : '',
    'linkedin' => isset($header_options['social_linkedin']) ? $header_options['social_linkedin'] : '',
    'instagram' => isset($header_options['social_instagram']) ? $header_options['social_instagram'] : '',
    'youtube' => isset($header_options['social_youtube']) ? $header_options['social_youtube'] : '',
);

// Custom social icons
$custom_icons = isset($header_options['social_custom_icons']) ? $header_options['social_custom_icons'] : array();

// Build inline style
$style = '';
if ($gradient_enable) {
    $style .= 'background: linear-gradient(90deg, ' . esc_attr($gradient_color1) . ', ' . esc_attr($gradient_color2) . ');';
} else {
    $style .= 'background-color: ' . esc_attr($bg_color) . ';';
}
$style .= ' color: ' . esc_attr($text_color) . ';';
if ($shadow_enable) {
    $style .= ' box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);';
}
if ($border_width > 0) {
    $style .= ' border-bottom: ' . absint($border_width) . 'px solid ' . esc_attr($border_color) . ';';
}
?>

<div class="site-topbar" style="<?php echo $style; ?>">
    <!-- Announcement is rendered centrally via ross_theme_render_announcement_strip() -->
    <div class="container topbar-inner">
        <!-- Left Section -->
        <div class="topbar-left" <?php echo !$show_left ? 'style="display: none;"' : ''; ?>>
            <?php if ($phone): ?>
                <a class="topbar-phone" href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $phone)); ?>" style="color: inherit; text-decoration: none;">
                    📞 <?php echo esc_html($phone); ?>
                </a>
            <?php endif; ?>
            <?php if (!empty($left_content)): ?>
                <span class="topbar-custom-content"><?php echo wp_kses_post($left_content); ?></span>
            <?php endif; ?>
        </div>

        <!-- Center (reserved for nav/branding). Announcement is shown above as a single-line strip. -->
        <div class="topbar-center">
            <!-- reserved for center content -->
        </div>

        <!-- Right Section: Social Icons -->
        <div class="topbar-right">
            <?php if ($social_enable): ?>
                <div class="topbar-social">
                    <?php
                    // Default social icons with Font Awesome classes
                    $social_icons = array(
                        'facebook' => 'fab fa-facebook-f',
                        'twitter' => 'fab fa-twitter',
                        'linkedin' => 'fab fa-linkedin-in',
                        'instagram' => 'fab fa-instagram',
                        'youtube' => 'fab fa-youtube'
                    );
                    
                    foreach ($social_urls as $platform => $url) {
                        if (!empty($url)) {
                            echo '<a class="social-link" data-social="' . esc_attr($platform) . '" href="' . esc_url($url) . '" target="_blank" rel="noopener noreferrer" title="' . esc_attr(ucfirst($platform)) . '" style="color: ' . esc_attr($icon_color) . ';">';
                            echo '<i class="' . esc_attr($social_icons[$platform]) . '"></i>';
                            echo '</a>';
                        }
                    }
                    
                    // Custom social icons
                    if (!empty($custom_icons) && is_array($custom_icons)) {
                        foreach ($custom_icons as $icon) {
                            if (isset($icon['enabled']) && $icon['enabled'] && !empty($icon['url']) && !empty($icon['icon'])) {
                                echo '<a class="social-link social-custom" href="' . esc_url($icon['url']) . '" target="_blank" rel="noopener noreferrer" title="' . esc_attr($icon['name'] ?? 'Custom') . '" style="color: ' . esc_attr($icon_color) . ';">';
                                if (!empty($icon['icon_url'])) {
                                    echo '<img src="' . esc_url($icon['icon_url']) . '" alt="' . esc_attr($icon['name'] ?? '') . '" style="width: 16px; height: 16px; filter: brightness(0) invert(1);">';
                                } else {
                                    echo '<i class="' . esc_attr($icon['icon']) . '"></i>';
                                }
                                echo '</a>';
                            }
                        }
                    }
                    ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
    .site-topbar {
        width: 100%;
        padding: 12px 0;
        transition: all 0.3s ease;
        font-size: 14px;
    }

    .topbar-inner {
        display: grid;
        grid-template-columns: 1fr auto 1fr;
        gap: 20px;
        align-items: center;
        padding: 0 20px;
        max-width: 1200px;
        margin: 0 auto;
    }

    .topbar-left {
        text-align: left;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .topbar-center {
        text-align: center;
        flex-grow: 1;
        min-width: 0;
    }

    /* Announcement markup/styles are handled centrally in assets/css/frontend/header.css */

    .topbar-right {
        text-align: right;
        display: flex;
        gap: 15px;
        justify-content: flex-end;
        align-items: center;
    }

    .topbar-phone {
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: opacity 0.3s;
        font-weight: 500;
    }

    .topbar-phone:hover {
        opacity: 0.8;
    }

    .topbar-custom-content {
        display: inline-block;
    }

    .topbar-custom-content a {
        color: inherit;
        text-decoration: none;
        transition: opacity 0.3s;
    }

    .topbar-custom-content a:hover {
        opacity: 0.8;
    }

    .topbar-social {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .social-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.1);
        text-decoration: none;
        transition: all 0.3s ease;
        font-size: 14px;
    }

    .social-link:hover {
        background: rgba(255, 255, 255, 0.2);
        transform: scale(1.1);
    }

    .social-link.social-custom img {
        width: 18px;
        height: 18px;
        object-fit: contain;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .topbar-inner {
            grid-template-columns: 1fr;
            gap: 10px;
            padding: 0 15px;
        }

        .topbar-left,
        .topbar-center,
        .topbar-right {
            text-align: center;
            justify-content: center;
        }

        .topbar-right {
            justify-content: center;
        }

        .topbar-left {
            flex-direction: column;
            gap: 8px;
        }

        .site-topbar {
            padding: 8px 0;
            font-size: 13px;
        }
    }

    @media (max-width: 480px) {
        .topbar-social {
            gap: 8px;
        }

        .social-link {
            width: 28px;
            height: 28px;
            font-size: 12px;
        }
    }
</style>
