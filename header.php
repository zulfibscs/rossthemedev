<?php
/**
 * The header for our theme
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div id="page" class="site">
    
    <!-- DEBUG: Check if header is loading -->
    <?php if (current_user_can('manage_options')): ?>
    <!-- 🎯 ROSS THEME DEBUG: Loading header... -->
    <?php endif; ?>

    <?php
    // Render announcement / topbar / header using centralized helpers
    if (function_exists('ross_theme_render_announcement_at')) {
        ross_theme_render_announcement_at('before_topbar');
    }

    if (function_exists('ross_theme_render_topbar')) {
        ross_theme_render_topbar();
    }

    if (function_exists('ross_theme_render_announcement_at')) {
        ross_theme_render_announcement_at('between_topbar_header');
    }

    if (function_exists('ross_theme_display_header')) {
        ross_theme_display_header();
    }

    // Include global header search overlay (if enabled)
    get_template_part('template-parts/header/header', 'search');

    if (function_exists('ross_theme_render_announcement_at')) {
        ross_theme_render_announcement_at('below_header');
    }
    ?>

    <main id="primary" class="site-main">