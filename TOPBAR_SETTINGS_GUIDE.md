# 🎯 Top Bar Settings - WordPress Theme Customizer

## Overview
A comprehensive WordPress Theme Customizer section for configuring a top bar with general options, design controls, social links, and style enhancements. All settings are saved as WordPress theme mods and provide live preview updates.

## Features

### ✅ General Options (Left Column)
- **Enable Top Bar**: Toggle top bar visibility
- **Left Section Content**: Text, phone, email, or custom HTML
- **Show Left Section**: Show/hide left content area
- **Phone Number**: Clickable phone link
- **Email Address**: Clickable email link
- **Announcement Text**: Promotional or notification text
- **Marquee Animation**: Enable scrolling animation for announcement

### 🎨 Design Options (Right Column)
- **Background Color**: Color picker with live preview
- **Text Color**: Text/link color control
- **Icon Color**: Social icon and accent color
- **Font Size Slider**: 10px–24px range
- **Text Alignment**: Left / Center / Right options

### 🔗 Social Links Section
- **Enable Social Icons**: Master toggle for social icons
- **Repeater Fields** for each platform:
  - **Facebook**, **Twitter/X**, **LinkedIn**, **Instagram**, **YouTube**
  - URL field for social profile link
  - Enable/disable toggle per platform
  - FontAwesome icon class customization
  - Custom icon image upload support

### ✨ Style Enhancements
- **Drop Shadow Toggle**: Subtle box-shadow effect
- **Gradient Background**: Enable/disable gradient
- **Gradient Colors**: Two color pickers for gradient
- **Border Bottom**: Color and width controls (0-5px)

## Installation

### 1. Module Files
The top bar customizer is split across several files:

```
inc/admin/
├── customizer-topbar.php          # Main customizer registration
└── customizer-enqueuer.php        # Script/style enqueuing

assets/
├── js/admin/
│   └── customizer-topbar-preview.js   # Live preview bindings
└── css/admin/
    └── customizer-topbar.css          # Admin panel styling

template-parts/
└── topbar.php                     # Frontend top bar display
```

### 2. Automatic Loading
The modules are automatically loaded in `functions.php`:

```php
require_once get_template_directory() . '/inc/admin/customizer-topbar.php';
require_once get_template_directory() . '/inc/admin/customizer-enqueuer.php';
```

### 3. Header Integration
The top bar is automatically displayed in `header.php` via:

```php
<?php get_template_part('template-parts/topbar'); ?>
```

## Configuration

### Theme Customizer Settings
Access all settings in **WordPress Admin → Customize → Top Bar Settings**

#### Theme Mods (Database Storage)
All settings are stored as theme mods and can be retrieved:

```php
// General Options
$enable = get_theme_mod('ross_topbar_enable', false);
$left_content = get_theme_mod('ross_topbar_left_content', '');
$phone = get_theme_mod('ross_topbar_phone', '');
$email = get_theme_mod('ross_topbar_email', '');
$announcement = get_theme_mod('ross_topbar_announcement', '');
$marquee = get_theme_mod('ross_topbar_marquee_enable', false);

// Design Options
$bg_color = get_theme_mod('ross_topbar_bg_color', '#001946');
$text_color = get_theme_mod('ross_topbar_text_color', '#ffffff');
$icon_color = get_theme_mod('ross_topbar_icon_color', '#E5C902');
$font_size = get_theme_mod('ross_topbar_font_size', 14);
$alignment = get_theme_mod('ross_topbar_alignment', 'left');

// Style Options
$gradient_enable = get_theme_mod('ross_topbar_gradient_enable', false);
$gradient_color1 = get_theme_mod('ross_topbar_gradient_color1', '#001946');
$gradient_color2 = get_theme_mod('ross_topbar_gradient_color2', '#003d7a');
$shadow_enable = get_theme_mod('ross_topbar_shadow_enable', false);
$border_width = get_theme_mod('ross_topbar_border_width', 0);
$border_color = get_theme_mod('ross_topbar_border_color', '#E5C902');

// Social Links
$social_enable = get_theme_mod('ross_topbar_social_enable', false);
$fb_url = get_theme_mod('ross_topbar_social_facebook_url', '');
$fb_enabled = get_theme_mod('ross_topbar_social_facebook_enabled', false);
$fb_icon = get_theme_mod('ross_topbar_social_facebook_icon', 'fab fa-facebook');
```

## Frontend Display

### Manual Inclusion
Display top bar anywhere with:

```php
<?php get_template_part('template-parts/topbar'); ?>
```

### Automatic Display
The top bar displays automatically in the header if enabled in customizer.

### CSS Classes & Structure

```html
<div class="site-topbar">
    <div class="container topbar-inner">
        <div class="topbar-left">...</div>
        <div class="topbar-center">
            <div class="topbar-announcement">...</div>
        </div>
        <div class="topbar-right">
            <a class="topbar-phone">...</a>
            <a class="topbar-email">...</a>
            <div class="topbar-social">
                <a class="social-link" data-social="facebook">...</a>
            </div>
        </div>
    </div>
</div>
```

## Customization

### Add Custom Social Platform
Edit `customizer-topbar.php` and extend the `$social_platforms` array:

```php
$social_platforms = array(
    'facebook'  => array('label' => 'Facebook', 'icon' => 'fab fa-facebook'),
    'twitter'   => array('label' => 'Twitter/X', 'icon' => 'fab fa-twitter'),
    'tiktok'    => array('label' => 'TikTok', 'icon' => 'fab fa-tiktok'), // NEW
    // ... more platforms
);
```

### Modify Frontend Styling
Edit `template-parts/topbar.php` CSS section or create custom CSS:

```css
.site-topbar {
    /* Your custom styles */
}

.topbar-social .social-link {
    /* Custom social icon styling */
}
```

### Extend Customizer Controls
Add new controls in `customizer-topbar.php` within `ross_theme_customize_register()`:

```php
$wp_customize->add_setting('ross_topbar_custom_option', array(
    'default'           => '',
    'sanitize_callback' => 'sanitize_text_field',
    'transport'         => 'postMessage',
));
$wp_customize->add_control('ross_topbar_custom_option', array(
    'label'    => 'Custom Option',
    'section'  => 'ross_topbar_section',
    'type'     => 'text',
));
```

Then add preview binding in `customizer-topbar-preview.js`:

```javascript
wp.customize('ross_topbar_custom_option', function(value) {
    value.bind(function(to) {
        $('.site-topbar').data('custom', to);
    });
});
```

## Live Preview

The customizer includes live preview functionality. Changes update in real-time via `customizer-topbar-preview.js` which:

1. Listens for `postMessage` transport changes
2. Updates DOM elements immediately
3. Applies CSS changes without page reload
4. Handles color, gradient, font, and content updates

## FontAwesome Icons

Default FontAwesome 6.4.0 icons are used. Customize icon classes:

1. Go to **Customize → Social Links**
2. Each platform has an "Icon Class" field
3. Enter FontAwesome class: `fab fa-facebook`, `fas fa-phone`, etc.
4. Or use Dashicons: `dashicons dashicons-facebook`

## Sanitization & Security

All user inputs are sanitized:

- **Text/HTML**: `wp_kses_post()` (allows safe HTML)
- **Emails**: `sanitize_email()`
- **URLs**: `esc_url_raw()`
- **Colors**: `sanitize_hex_color()`
- **Integers**: `absint()`
- **Text**: `sanitize_text_field()`
- **Checkboxes**: Custom boolean sanitization

## Browser Support

- Modern browsers (Chrome, Firefox, Safari, Edge)
- CSS Grid layout with fallbacks
- Marquee animation via CSS keyframes
- Gradient support (all modern browsers)

## Troubleshooting

### Top bar not showing?
1. Enable in **Customize → Top Bar Settings → Enable Top Bar**
2. Check WordPress debug log for errors
3. Verify `wp_body_open()` is called in header.php

### Live preview not updating?
1. Ensure `wp_customize_preview.js` is enqueued
2. Check browser console for JavaScript errors
3. Clear browser cache and reload customizer

### Social icons not showing?
1. Enable **Enable Social Icons** toggle
2. Add social platform URLs
3. Verify icon classes are correct FontAwesome classes
4. Check that FontAwesome CSS is loaded

## Performance Notes

- All CSS is output inline (no separate stylesheet enqueue needed)
- Icons use FontAwesome CDN link for external hosting option
- Marquee animation uses CSS-only (no JavaScript)
- Live preview only activates in customizer (not frontend)

## Changelog

### Version 1.0.0
- Initial release with full feature set
- Support for 5 social platforms
- Gradient and shadow effects
- Live preview functionality
