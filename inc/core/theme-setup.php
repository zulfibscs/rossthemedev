<?php
/**
 * Theme setup - register menus and add theme supports
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function ross_theme_setup() {
	// Register navigation locations
	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'ross-theme' ),
		'footer'  => __( 'Footer Menu', 'ross-theme' ),
	) );

	// Basic theme supports
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
}
add_action( 'after_setup_theme', 'ross_theme_setup' );

?>
