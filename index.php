<?php
/**
 * The main template file
 */
get_header();
?>

<main id="primary" class="site-main">
    <div class="container">
        <!-- Public front-page hero -->
        <section class="front-hero">
            <div class="hero-inner container">
                <div class="hero-copy">
                    <h1>Welcome to Ross — Modern, Fast & Flexible</h1>
                    <p class="lead">A clean, modular WordPress theme crafted for conversion-focused sites. Try the header controls in the admin to customize layout, width and behavior.</p>
                    <p class="hero-cta"><a class="button" href="#">Get Started</a></p>
                </div>
                <div class="hero-visual">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/img/hero-placeholder.png" alt="Hero visual" style="max-width:360px;width:100%;border-radius:12px;box-shadow:0 10px 30px rgba(2,6,23,0.12);"/>
                </div>
            </div>
        </section>
        
        <!-- Header Status Check -->
        <section style="background: #e9f7ef; padding: 2rem; border-radius: 8px; margin: 2rem 0; text-align: center;">
            <h2 style="color: #001946;">Header Status Check</h2>
            
            <?php
            $header_options = get_option('ross_theme_header_options');
            $header_style = isset($header_options['header_style']) ? $header_options['header_style'] : 'default';
            $logo_url = isset($header_options['logo_upload']) ? $header_options['logo_upload'] : '';
            ?>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin: 1rem 0;">
                <div style="background: white; padding: 1rem; border-radius: 4px;">
                    <strong>Header Loaded:</strong><br>
                    ✅ Yes
                </div>
                <div style="background: white; padding: 1rem; border-radius: 4px;">
                    <strong>Header Style:</strong><br>
                    <?php echo esc_html($header_style); ?>
                </div>
                <div style="background: white; padding: 1rem; border-radius: 4px;">
                    <strong>Logo:</strong><br>
                    <?php echo $logo_url ? '✅ Uploaded' : '❌ Not Uploaded'; ?>
                </div>
            </div>
            
            <p style="margin-top: 1rem; font-style: italic;">
                If you can see a colored header above this message, it's working!
            </p>
        </section>

        <!-- Main Content -->
        <section style="padding: 4rem 0; text-align: center;">
            <h1 style="color: #001946; margin-bottom: 1rem;">
                🎉 Ross Theme is Working!
            </h1>
            <p style="font-size: 1.2rem; color: #666; margin-bottom: 2rem;">
                Your modular theme structure is functioning correctly.
            </p>
        </section>

        <?php if ( current_user_can('manage_options') ) : ?>
        <!-- Admin-only creative sticky header test -->
        <section id="ross-sticky-test" class="ross-admin-test">
            <div class="ross-hero">
                <div class="ross-hero-inner container">
                    <div class="ross-hero-content">
                        <h2>Sticky Header Playground</h2>
                        <p class="lead">A visual playground for admins to test header width, sticky behavior and responsiveness. Scroll to see the header in action.</p>
                        <p class="meta">Header style: <strong><?php echo esc_html($header_style); ?></strong></p>
                    </div>
                    <div class="ross-hero-cta">
                        <a class="button ross-test-btn" href="#ross-sticky-test">Test Now</a>
                    </div>
                </div>
            </div>

            <div class="ross-features container">
                <div class="ross-features-grid">
                    <article class="feature-card">
                        <h3>Responsive</h3>
                        <p>Resize the browser to validate header responsiveness across breakpoints.</p>
                    </article>
                    <article class="feature-card">
                        <h3>Full / Contained</h3>
                        <p>Switch header width to Full or Contained in settings to see immediate layout changes.</p>
                    </article>
                    <article class="feature-card">
                        <h3>Sticky Toggle</h3>
                        <p>Enable sticky header in settings and scroll to confirm it stays at the top.</p>
                    </article>
                    <article class="feature-card">
                        <h3>Logo & CTA</h3>
                        <p>Upload logos and toggle CTA to observe spacing and alignment in real-time.</p>
                    </article>
                </div>
            </div>

            <div class="ross-long container">
                <?php for ($i = 0; $i < 18; $i++) : ?>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse varius enim in eros elementum tristique. Duis cursus, mi quis viverra ornare, eros dolor interdum nulla.</p>
                <?php endfor; ?>
            </div>
        </section>
        <?php endif; ?>

    </div><!-- .container -->
</main><!-- #main -->

<?php
get_footer();