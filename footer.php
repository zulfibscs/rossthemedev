<?php
/**
 * The footer for our theme
 * Uses modular footer system
 */
?>

    </main><!-- #primary -->

    <?php
    // Display CTA above the footer (outside <footer>), if enabled
    if (function_exists('ross_theme_display_footer_cta')) {
        ross_theme_display_footer_cta();
    }

    // Load the appropriate footer template based on options
    if (function_exists('ross_theme_display_footer')) {
        ross_theme_display_footer();
    } else {
        // Fallback basic footer
        echo '<footer class="site-footer"><div class="container"><p>&copy; ' . date('Y') . ' ' . get_bloginfo('name') . '</p></div></footer>';
    }
    ?>

</div><!-- #page -->

<?php wp_footer(); ?>
</body>
</html>