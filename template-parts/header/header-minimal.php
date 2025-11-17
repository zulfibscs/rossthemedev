<?php
/**
 * Minimal Header - Clean & Simple
 */

$options = ross_theme_get_header_options();
$logo_url = $options['logo_upload'] ?: get_template_directory_uri() . '/assets/img/logo.png';
$site_title = get_bloginfo('name');
$inline_style = ross_theme_get_header_inline_style();
?>

<header class="<?php echo esc_attr(ross_theme_header_classes()); ?>" style="<?php echo $inline_style; ?>">
    <?php if ( isset($options['header_width']) && $options['header_width'] === 'contained' ) : ?>
    <div class="container">
        <div class="header-inner">
    <?php else : ?>
        <div class="header-inner">
    <?php endif; ?>
            
            <!-- Logo -->
            <div class="header-logo">
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

            <!-- Simple Navigation -->
            <nav class="header-navigation">
                <ul class="primary-menu minimal-menu">
                    <li><a href="<?php echo esc_url(home_url('/services')); ?>">Services</a></li>
                    <li><a href="<?php echo esc_url(home_url('/contact')); ?>">Contact</a></li>
                </ul>
            </nav>

    <?php if ( isset($options['header_width']) && $options['header_width'] === 'contained' ) : ?>
        </div>
    </div>
    <?php else : ?>
        </div>
    <?php endif; ?>
</header>