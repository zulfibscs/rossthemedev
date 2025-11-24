<?php
/**
 * Announcement Admin Tab
 * Renders the announcement settings section (moved out of Top Bar tab)
 */

if (!defined('ABSPATH')) exit;

function ross_theme_render_announcement_admin() {
    ?>
    <div class="ross-announcement-admin">
        <div class="ross-admin-section">
            <h2>📣 Announcement Settings</h2>
            <p>Configure the announcement strip: enable/disable, edit content, and choose animation and styling.</p>
        </div>

        <div class="ross-announcement-grid">
            <?php
            // Use WordPress settings API sections registered to the announcement page
            do_settings_sections('ross-theme-header-announcement');
            ?>
        </div>
    </div>
    <?php
}

// When included in the header options page, call the render function
ross_theme_render_announcement_admin();
