<?php
/**
 * Asset loader - enqueue frontend and admin assets
 */

if (!defined('ABSPATH')) exit;

/**
 * Enqueue front-end styles and scripts
 */
function ross_theme_enqueue_assets() {
	// Main theme stylesheet (style.css)
	wp_enqueue_style('ross-theme-style', get_stylesheet_uri(), array(), filemtime(get_template_directory() . '/style.css'));

	// Additional frontend CSS (optional, keep if exists)
	$frontend_css = get_template_directory() . '/assets/css/frontend/base.css';
	if (file_exists($frontend_css)) {
		wp_enqueue_style('ross-theme-frontend-base', get_template_directory_uri() . '/assets/css/frontend/base.css', array('ross-theme-style'), filemtime($frontend_css));
	}

	// Header CSS
	$header_css = get_template_directory() . '/assets/css/frontend/header.css';
	if (file_exists($header_css)) {
		wp_enqueue_style('ross-theme-frontend-header', get_template_directory_uri() . '/assets/css/frontend/header.css', array('ross-theme-frontend-base'), filemtime($header_css));
	}

	// Front page styles
	$front_css = get_template_directory() . '/assets/css/frontend/front-page.css';
	if (file_exists($front_css)) {
		wp_enqueue_style('ross-theme-front-page', get_template_directory_uri() . '/assets/css/frontend/front-page.css', array('ross-theme-frontend-header'), filemtime($front_css));
	}

	// Navigation JS
	$nav_js = get_template_directory() . '/assets/js/frontend/navigation.js';
	if (file_exists($nav_js)) {
		wp_enqueue_script('ross-theme-navigation', get_template_directory_uri() . '/assets/js/frontend/navigation.js', array('jquery'), filemtime($nav_js), true);
	}
}
add_action('wp_enqueue_scripts', 'ross_theme_enqueue_assets');

/**
 * Enqueue admin assets if needed (placeholder)
 */
function ross_theme_enqueue_admin_assets($hook) {
	// Admin enqueues are handled in individual modules, but ensure stylesheet for WP-admin preview if needed.
}
add_action('admin_enqueue_scripts', 'ross_theme_enqueue_admin_assets');
