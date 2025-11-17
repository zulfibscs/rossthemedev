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
    // Render topbar (if enabled) then the header using our modular system
    if (function_exists('ross_theme_render_topbar')) {
        ross_theme_render_topbar();
    }
    if (function_exists('ross_theme_display_header')) {
        ross_theme_display_header();
    }
    ?>

    <main id="primary" class="site-main">