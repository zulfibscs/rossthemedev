<?php
/**
 * Header Search Overlay
 */

if (! defined('ABSPATH')) exit;

$options = ross_theme_get_header_options();
if ( ! ross_theme_header_feature_enabled('search') ) {
    return;
}

?>
<div class="search-overlay" id="site-search" aria-hidden="true">
    <div class="search-overlay-inner" role="dialog" aria-modal="true" aria-labelledby="search-overlay-title">
        <button class="search-overlay-close" aria-label="Close search">&times;</button>
        <h2 id="search-overlay-title" class="screen-reader-text"><?php esc_html_e('Search the site', 'rosstheme'); ?></h2>
        <div class="search-overlay-form">
            <?php get_search_form(); ?>
        </div>
    </div>
</div>
