<?php
/*
 * Front Page Template - Accountant Landing
 * This template hides the theme header and renders a custom front page.
 */
get_header();
?>

<!-- Front page uses the normal theme header (no hiding) -->

<main id="primary" class="site-main front-page">
    <section class="front-page-hero">
        <div class="hero-inner container">
            <div class="hero-copy">
                <h1>Accounting & Advisory for Growing Businesses</h1>
                <p class="lead">Trusted accountants delivering proactive advice, bookkeeping, tax strategies and cloud accounting.</p>
                <p class="hero-cta"><a class="button" href="#contact">Get a Free Consultation</a></p>
            </div>
            <div class="hero-visual">
                <img src="https://images.unsplash.com/photo-1559526324-593bc073d938?w=800&q=80&auto=format&fit=crop&ixlib=rb-4.0.3&s=0f3c7f8f23d3b3c7c9f8c1d6c2eef3b7" alt="Accounting" style="max-width:420px;width:100%;border-radius:12px;box-shadow:0 12px 40px rgba(2,6,23,0.14);" />
            </div>
        </div>
    </section>

    <section class="front-services container">
        <h2>Our Services</h2>
        <div class="services-grid">
            <div class="service-card">
                <h3>Bookkeeping</h3>
                <p>Accurate bookkeeping and month-end reporting so you can focus on growth.</p>
            </div>
            <div class="service-card">
                <h3>Tax Planning</h3>
                <p>Reduce liabilities and stay compliant with proactive tax strategies.</p>
            </div>
            <div class="service-card">
                <h3>Payroll</h3>
                <p>Full payroll service, auto-enrolment and PAYE management.</p>
            </div>
            <div class="service-card">
                <h3>Advisory</h3>
                <p>Cashflow forecasting, KPI dashboards and financial modelling.</p>
            </div>
        </div>
    </section>

    <section class="front-testimonials container">
        <h2>Testimonials</h2>
        <div class="testimonials-grid">
            <div class="testimonial">
                <p>"They transformed our accounting process and saved us time every month."</p>
                <div class="author">— Sarah J., CEO</div>
            </div>
            <div class="testimonial">
                <p>"Reliable, insightful and great communication. Highly recommended."</p>
                <div class="author">— Mark P., Founder</div>
            </div>
        </div>
    </section>

    <section class="front-reviews container">
        <h2>Reviews</h2>
        <div class="review-list">
            <div class="review">⭐⭐⭐⭐⭐ — Excellent support and fast turnaround.</div>
            <div class="review">⭐⭐⭐⭐ — Helpful advice and friendly team.</div>
            <div class="review">⭐⭐⭐⭐⭐ — Saved us significant tax last year.</div>
        </div>
    </section>

    <section class="front-comments container">
        <h2>Recent Comments</h2>
        <div class="comment">Great article on small business tax — very practical.</div>
        <div class="comment">Helpful onboarding process, thanks.</div>
    </section>

    <section class="front-gallery container">
        <h2>Gallery</h2>
        <div class="gallery-grid">
            <img src="https://images.unsplash.com/photo-1585386959984-a415522e3f9b?w=800&q=80&auto=format&fit=crop&ixlib=rb-4.0.3&s=5b4b1b2d8f5b9ce6d6b5b4c9f986b3c1" alt="gallery-1"/>
            <img src="https://images.unsplash.com/photo-1521737604893-d14cc237f11d?w=800&q=80&auto=format&fit=crop&ixlib=rb-4.0.3&s=7c3a5abc55b1f6c3b5f2b2ff1b1f3e7a" alt="gallery-2"/>
            <img src="https://images.unsplash.com/photo-1551836022-1ba2f3c0cde4?w=800&q=80&auto=format&fit=crop&ixlib=rb-4.0.3&s=9a1d4a8c1d3e4b5a6c7d8e9f0a1b2c3d" alt="gallery-3"/>
            <img src="https://images.unsplash.com/photo-1508385082359-fc2f3d8f8f48?w=800&q=80&auto=format&fit=crop&ixlib=rb-4.0.3&s=2e3b4c5d6f7a8b9c0d1e2f3a4b5c6d7e" alt="gallery-4"/>
        </div>
    </section>

</main>

<?php get_footer();
