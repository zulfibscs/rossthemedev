# Quick Start: Top Bar Customizer

## ⚡ Access the Top Bar Settings
1. **WordPress Admin Dashboard**
2. **Appearance → Customize**
3. **Section: Top Bar Settings** (or Social Links / Style Enhancements)

## 🎯 What You Can Configure

### General Options
- ✅ Enable/disable top bar
- 📝 Left section content (HTML allowed)
- 📞 Phone number with tel: link
- ✉️ Email address with mailto: link
- 📰 Announcement text with marquee option

### Design Options
- 🎨 Background color (color picker)
- 🎨 Text color (color picker)
- 🎨 Icon/accent color (color picker)
- 🖋️ Font size (10-24px slider)
- 🧍 Text alignment (left/center/right)

### Social Links
- Enable/disable per platform
- Add URL for each: Facebook, Twitter, LinkedIn, Instagram, YouTube
- Customize FontAwesome icon classes
- Each platform can be toggled independently

### Style Effects
- 💡 Drop shadow (toggle)
- 🌈 Gradient background (with 2 color pickers)
- 💠 Bottom border (color + width 0-5px)

## 🔧 Code Examples

### Display top bar in template
```php
<?php get_template_part('template-parts/topbar'); ?>
```

### Get settings in PHP
```php
$phone = get_theme_mod('ross_topbar_phone', '');
$social_enable = get_theme_mod('ross_topbar_social_enable', false);
$bg_color = get_theme_mod('ross_topbar_bg_color', '#001946');
```

### Check if feature enabled
```php
if (get_theme_mod('ross_topbar_enable', false)) {
    // Top bar is enabled
}
```

### Get social link
```php
$facebook_url = get_theme_mod('ross_topbar_social_facebook_url', '');
$facebook_enabled = get_theme_mod('ross_topbar_social_facebook_enabled', false);
$facebook_icon = get_theme_mod('ross_topbar_social_facebook_icon', 'fab fa-facebook');
```

## 📁 Key Files
- `inc/admin/customizer-topbar.php` - Settings registration
- `template-parts/topbar.php` - Frontend display
- `assets/js/admin/customizer-topbar-preview.js` - Live preview
- `assets/css/admin/customizer-topbar.css` - Admin styling

## 🚀 Live Preview
- Changes update in real-time in the customizer preview pane
- No page refresh needed
- Works for all color, text, and layout changes

## ✨ Default Values
- Background: #001946 (dark blue)
- Text Color: #ffffff (white)
- Icon Color: #E5C902 (gold)
- Font Size: 14px
- Alignment: Left
- All toggles: OFF by default

## 🎨 Common Customizations

### Change default colors
Edit `inc/admin/customizer-topbar.php` line 86-90:
```php
'default' => '#001946', // Change this
```

### Add new social platform
Add to `$social_platforms` array around line 320

### Modify layout spacing
Edit CSS in `template-parts/topbar.php` styles section

## 🔗 FontAwesome Icon Classes
- Facebook: `fab fa-facebook`
- Twitter: `fab fa-twitter`
- LinkedIn: `fab fa-linkedin`
- Instagram: `fab fa-instagram`
- YouTube: `fab fa-youtube`
- Phone: `fas fa-phone`
- Email: `fas fa-envelope`

## 💾 Data Storage
All settings stored as WordPress theme mods:
- Can be exported/imported with theme settings
- Persists across theme updates (if properly coded)
- Accessible via `get_theme_mod()` function

## 🐛 Debug
Enable debug comments in header (if admin):
```php
<!-- Ross Theme Header: default -->
<!-- Logo URL: ... -->
```

---
**For detailed documentation**, see `TOPBAR_SETTINGS_GUIDE.md`
