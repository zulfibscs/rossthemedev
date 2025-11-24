<?php
/**
 * Centered Header - Logo Centered, Navigation Below
 */

$options = ross_theme_get_header_options();
$logo_url = $options['logo_upload'] ?: get_template_directory_uri() . '/assets/img/logo.png';
$site_title = get_bloginfo('name');
$inline_style = ross_theme_get_header_inline_style();
?>

<header class="<?php echo esc_attr(ross_theme_header_classes()); ?>" style="<?php echo $inline_style; ?>">
    <?php if ( isset($options['header_width']) && $options['header_width'] === 'contained' ) : ?>
    <div class="container">
        
    <?php else : ?>
        
    <?php endif; ?>
        <!-- Logo Centered -->
        <div class="header-logo-centered">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="site-logo" style="display:flex;align-items:center;gap:0.6rem;">
                <?php if ($options['logo_upload'] || file_exists(get_template_directory() . '/assets/img/logo.png')): ?>
                    <img src="<?php echo esc_url($logo_url); ?>" alt="<?php echo esc_attr($site_title); ?>" style="max-width: <?php echo esc_attr($options['logo_width']); ?>px; height: auto; display:block;">
                <?php endif; ?>

                <?php if ( ! empty( $options['show_site_title'] ) ) : ?>
                    <div class="logo-text"><?php echo esc_html($site_title); ?></div>
                <?php elseif ( empty( $options['logo_upload'] ) && ! file_exists( get_template_directory() . '/assets/img/logo.png' ) ) : ?>
                    <div class="logo-text"><?php echo esc_html($site_title); ?></div>
                <?php endif; ?>
            </a>
        </div>

        <!-- Navigation Centered -->
        <button class="menu-toggle" aria-expanded="false" aria-controls="primary-menu">☰ Menu</button>
        <nav class="header-navigation-centered" id="primary-menu">
            <?php
            if (has_nav_menu('primary')) {
                wp_nav_menu(array(
                    'theme_location' => 'primary',
                    'menu_class' => 'primary-menu centered-menu',
                    'container' => false,
                ));
            } else {
                echo '<ul class="primary-menu centered-menu">';
                echo '<li><a href="' . home_url() . '">Home</a></li>';
                echo '<li><a href="' . home_url('/about') . '">About</a></li>';
                echo '<li><a href="' . home_url('/services') . '">Services</a></li>';
                echo '<li><a href="' . home_url('/contact') . '">Contact</a></li>';
                echo '</ul>';
            }
            ?>
        </nav>

        <!-- CTA Button Centered -->
        <?php if (ross_theme_header_feature_enabled('cta_button')):
            $cta_url = ! empty($options['cta_button_url']) ? $options['cta_button_url'] : home_url('/contact');
        ?>
        <div class="header-actions-centered">
            <a href="<?php echo esc_url($cta_url); ?>" class="cta-button" style="background: <?php echo esc_attr($options['cta_button_color']); ?>;">
                <?php echo esc_html($options['cta_button_text']); ?>
            </a>
        </div>
        <?php endif; ?>

    <?php if ( isset($options['header_width']) && $options['header_width'] === 'contained' ) : ?>
    </div>
    <?php endif; ?>
</header>