<?php
/**
 * Top Bar Template
 * Displays the top bar with customizer settings
 */

if (!defined('ABSPATH')) exit;

$enable = get_theme_mod('ross_topbar_enable', false);
if (!$enable) {
    return;
}

$left_content = get_theme_mod('ross_topbar_left_content', '');
$show_left = get_theme_mod('ross_topbar_show_left', true);
$phone = get_theme_mod('ross_topbar_phone', '');
$email = get_theme_mod('ross_topbar_email', '');
$announcement = get_theme_mod('ross_topbar_announcement', '');
$marquee = get_theme_mod('ross_topbar_marquee_enable', false);
$social_enable = get_theme_mod('ross_topbar_social_enable', false);
$bg_color = get_theme_mod('ross_topbar_bg_color', '#001946');
$text_color = get_theme_mod('ross_topbar_text_color', '#ffffff');
$alignment = get_theme_mod('ross_topbar_alignment', 'left');
$gradient_enable = get_theme_mod('ross_topbar_gradient_enable', false);
$gradient_color1 = get_theme_mod('ross_topbar_gradient_color1', '#001946');
$gradient_color2 = get_theme_mod('ross_topbar_gradient_color2', '#003d7a');
$shadow_enable = get_theme_mod('ross_topbar_shadow_enable', false);
$border_width = get_theme_mod('ross_topbar_border_width', 0);
$border_color = get_theme_mod('ross_topbar_border_color', '#E5C902');

// Build inline style
$style = 'text-align: ' . esc_attr($alignment) . '; color: ' . esc_attr($text_color) . ';';
if ($gradient_enable) {
    $style .= ' background: linear-gradient(90deg, ' . esc_attr($gradient_color1) . ', ' . esc_attr($gradient_color2) . ');';
} else {
    $style .= ' background-color: ' . esc_attr($bg_color) . ';';
}
if ($shadow_enable) {
    $style .= ' box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);';
}
if ($border_width > 0) {
    $style .= ' border-bottom: ' . absint($border_width) . 'px solid ' . esc_attr($border_color) . ';';
}
?>

<div class="site-topbar" style="<?php echo $style; ?>">
    <div class="container topbar-inner">
        <!-- Left Section -->
        <div class="topbar-left" <?php echo !$show_left ? 'style="display: none;"' : ''; ?>>
            <?php echo wp_kses_post($left_content); ?>
        </div>

        <!-- Center Announcement -->
        <div class="topbar-center">
            <?php if ($announcement): ?>
                <div class="topbar-announcement <?php echo $marquee ? 'announce-marquee' : ''; ?>">
                    <?php echo wp_kses_post($announcement); ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Right Section: Phone, Email, Social -->
        <div class="topbar-right">
            <?php if ($phone): ?>
                <a class="topbar-phone" href="tel:<?php echo esc_attr($phone); ?>">
                    <?php echo esc_html($phone); ?>
                </a>
            <?php endif; ?>

            <?php if ($email): ?>
                <a class="topbar-email" href="mailto:<?php echo esc_attr($email); ?>">
                    <?php echo esc_html($email); ?>
                </a>
            <?php endif; ?>

            <?php if ($social_enable): ?>
                <div class="topbar-social">
                    <?php
                    $platforms = array('facebook', 'twitter', 'linkedin', 'instagram', 'youtube');
                    foreach ($platforms as $platform) {
                        $enabled = get_theme_mod("ross_topbar_social_{$platform}_enabled", false);
                        $url = get_theme_mod("ross_topbar_social_{$platform}_url", '');
                        $icon = get_theme_mod("ross_topbar_social_{$platform}_icon", '');

                        if ($enabled && !empty($url) && !empty($icon)) {
                            echo '<a class="social-link" data-social="' . esc_attr($platform) . '" href="' . esc_url($url) . '" target="_blank" rel="noopener noreferrer" title="' . esc_attr($platform) . '">';
                            echo '<i class="' . esc_attr($icon) . '"></i>';
                            echo '</a>';
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
    }

    .topbar-inner {
        display: grid;
        grid-template-columns: 1fr auto 1fr;
        gap: 20px;
        align-items: center;
        padding: 0 20px;
    }

    .topbar-left {
        text-align: left;
    }

    .topbar-center {
        text-align: center;
        flex-grow: 1;
    }

    .topbar-announcement {
        font-weight: 500;
        padding: 5px 10px;
        border-radius: 3px;
    }

    .topbar-announcement.announce-marquee {
        animation: marquee 20s linear infinite;
        overflow: hidden;
        white-space: nowrap;
        display: inline-block;
        width: 100%;
    }

    @keyframes marquee {
        0% { transform: translateX(100%); }
        100% { transform: translateX(-100%); }
    }

    .topbar-right {
        text-align: right;
        display: flex;
        gap: 15px;
        justify-content: flex-end;
        align-items: center;
    }

    .topbar-phone,
    .topbar-email {
        text-decoration: none;
        display: inline-block;
        transition: opacity 0.3s;
    }

    .topbar-phone:hover,
    .topbar-email:hover {
        opacity: 0.8;
    }

    .topbar-social {
        display: flex;
        gap: 12px;
        align-items: center;
    }

    .social-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
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

    /* Responsive */
    @media (max-width: 768px) {
        .topbar-inner {
            grid-template-columns: 1fr;
            gap: 10px;
        }

        .topbar-left,
        .topbar-center,
        .topbar-right {
            text-align: center;
        }

        .topbar-right {
            justify-content: center;
        }
    }
</style>
