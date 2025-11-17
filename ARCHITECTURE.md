# Architecture Diagram: Top Bar Customizer

## System Flow

```
┌─────────────────────────────────────────────────────────────────────┐
│                     WORDPRESS THEME SYSTEM                         │
└─────────────────────────────────────────────────────────────────────┘

                                  ↓

┌─────────────────────────────────────────────────────────────────────┐
│                          functions.php                             │
│  (Main loader - requires all modules)                              │
└─────────────────────────────────────────────────────────────────────┘

                                  ↓
                    
    ┌─────────────────────────────────────────────────────────────┐
    │             ADMIN CUSTOMIZER MODULES                         │
    └─────────────────────────────────────────────────────────────┘
    
    ┌──────────────────────┐    ┌─────────────────────────────────┐
    │ customizer-topbar.   │    │   customizer-enqueuer.php       │
    │ php                  │    │   (Asset Management)            │
    │ ────────────────     │    │   ────────────────────────────  │
    │ • Settings Reg.      │    │   • Enqueue CSS                 │
    │ • 27 Controls        │    │   • Enqueue Preview JS          │
    │ • Sanitization       │    │   • Load FontAwesome            │
    │ • Dynamic CSS        │    │                                 │
    │ • Display Function   │    │                                 │
    └──────────────────────┘    └─────────────────────────────────┘
                ↓                           ↓
                └──────────────┬────────────┘
                               ↓
                    ┌──────────────────────┐
                    │  CUSTOMIZER PANEL    │
                    │  wp-admin/customize  │
                    └──────────────────────┘
                               ↓
        ┌──────────────────────┴────────────────────────┐
        │                                               │
        ↓                                               ↓
    
┌──────────────────────────────┐    ┌──────────────────────────────┐
│  ADMIN PANEL STYLING         │    │  LIVE PREVIEW JAVASCRIPT     │
│  customizer-topbar.css       │    │  customizer-topbar-preview.js│
│  ───────────────────────     │    │  ──────────────────────────  │
│  • 2-Column Grid Layout      │    │  • Listen for Setting Changes│
│  • Control Styling           │    │  • Update Preview DOM        │
│  • Range Slider Custom       │    │  • Apply CSS Changes         │
│  • Focus States              │    │  • Smooth Transitions        │
│  • Responsive Design         │    │                              │
└──────────────────────────────┘    └──────────────────────────────┘
        ↑                                   ↑
        │                                   │
        └───────────────┬───────────────────┘
                        │
                  (postMessage)
                        │
                ┌───────┴──────────┐
                │                  │
                ↓                  ↓
    
    ┌─────────────────────────┐    
    │   CUSTOMIZER PREVIEW    │    
    │   (Right Side Panel)    │    
    │                         │    
    │   • Live Updates ✨     │    
    │   • No Reload Needed    │    
    │   • Real-time Preview   │    
    └─────────────────────────┘

                        ↓

    ┌─────────────────────────────────────┐
    │  DATABASE (wp_options table)        │
    │  ─────────────────────────────────  │
    │  theme_mod_ross_topbar_*            │
    │                                     │
    │  Stores:                            │
    │  • Colors, sizes, alignment         │
    │  • Text content                     │
    │  • Social links & toggles           │
    │  • Style effects (shadow, gradient) │
    └─────────────────────────────────────┘

                        ↓

    When "Publish" is clicked:
    Settings saved to database

                        ↓

┌────────────────────────────────────────────────────────────────────┐
│                     FRONTEND RENDERING                             │
└────────────────────────────────────────────────────────────────────┘
                        ↓
    
    ┌──────────────────────────────────────┐
    │  header.php                          │
    │  ──────────────────────────────────  │
    │  • Calls wp_body_open()              │
    │  • Top bar displays via hook         │
    └──────────────────────────────────────┘
                        ↓
    
    ┌──────────────────────────────────────┐
    │  wp_body_open hook                   │
    │  ──────────────────────────────────  │
    │  ross_theme_display_customizer_topbar│
    │  (priority: 5)                       │
    └──────────────────────────────────────┘
                        ↓
    
    ┌──────────────────────────────────────┐
    │  template-parts/topbar.php           │
    │  ──────────────────────────────────  │
    │  • Fetches settings from DB          │
    │  • Checks enable flag                │
    │  • Renders 3-column layout:          │
    │    - Left: left_content              │
    │    - Center: announcement            │
    │    - Right: phone, email, social     │
    │  • Applies inline styles             │
    │  • Responsive CSS included           │
    └──────────────────────────────────────┘
                        ↓
    
    ┌──────────────────────────────────────┐
    │  FRONTEND TOP BAR                    │
    │  ──────────────────────────────────  │
    │  <div class="site-topbar">           │
    │    <div class="topbar-left">...</div>│
    │    <div class="topbar-center">...</div>
    │    <div class="topbar-right">...</div>
    │  </div>                              │
    │                                      │
    │  • Desktop: 3-column grid            │
    │  • Tablet: 2-column grid             │
    │  • Mobile: 1-column stack            │
    └──────────────────────────────────────┘
                        ↓
    
    ┌──────────────────────────────────────┐
    │  RENDERED ON WEBSITE                 │
    │  ✨ Live & Responsive ✨             │
    └──────────────────────────────────────┘
```

## Component Hierarchy

```
Top Bar Customizer
│
├── Admin Interface (WordPress Customizer)
│   ├── Customizer Sections
│   │   ├── 🎯 Top Bar Settings
│   │   │   ├── General Options (Column 1)
│   │   │   │   ├── Enable Top Bar
│   │   │   │   ├── Left Content
│   │   │   │   ├── Show Left Section
│   │   │   │   ├── Phone Number
│   │   │   │   ├── Email Address
│   │   │   │   ├── Announcement Text
│   │   │   │   └── Marquee Toggle
│   │   │   │
│   │   │   └── Design Options (Column 2)
│   │   │       ├── Background Color
│   │   │       ├── Text Color
│   │   │       ├── Icon Color
│   │   │       ├── Font Size Slider
│   │   │       └── Alignment Selector
│   │   │
│   │   ├── 🔗 Social Links
│   │   │   ├── Enable Social Icons
│   │   │   ├── Facebook
│   │   │   │   ├── URL
│   │   │   │   ├── Enable/Disable
│   │   │   │   └── Icon Class
│   │   │   ├── Twitter
│   │   │   │   ├── URL
│   │   │   │   ├── Enable/Disable
│   │   │   │   └── Icon Class
│   │   │   ├── LinkedIn
│   │   │   ├── Instagram
│   │   │   └── YouTube
│   │   │
│   │   └── ✨ Style Enhancements
│   │       ├── Shadow Toggle
│   │       ├── Gradient Toggle
│   │       ├── Gradient Color 1
│   │       ├── Gradient Color 2
│   │       ├── Border Color
│   │       └── Border Width
│   │
│   └── Live Preview
│       └── Customizer Preview Pane
│           └── Real-time Updates
│
├── Backend Processing
│   ├── Settings Registration
│   │   └── 27 Customizer Controls
│   ├── Sanitization
│   │   └── Text, Email, Color, URL, Integer
│   ├── Database Storage
│   │   └── wp_options table (theme_mods)
│   └── Dynamic CSS Generation
│       └── Inline styles in wp_head
│
└── Frontend Display
    ├── Header Integration
    │   └── wp_body_open hook
    ├── Top Bar Template
    │   ├── Left Section
    │   ├── Announcement (Center)
    │   └── Right Section
    │       ├── Phone Link
    │       ├── Email Link
    │       └── Social Icons
    └── Responsive Styling
        ├── Desktop (3-column)
        ├── Tablet (2-column)
        └── Mobile (1-column stack)
```

## Data Flow

```
User Input (Customizer)
    ↓
    └─→ Setting Registered
         └─→ Control Rendered
              └─→ User Changes Value
                  ↓
                  ├─→ Sanitize Input
                  │   └─→ postMessage to Preview
                  │
                  └─→ Preview JavaScript Binds
                      └─→ Update Preview DOM
                          └─→ Apply CSS Changes
                              ├─→ Colors
                              ├─→ Font Size
                              ├─→ Text Content
                              └─→ Visibility
                                  ↓
                                  Display in Preview Pane
                                  ↓
                                  [User sees live update]
                                  ↓
                                  User Clicks "Publish"
                                  ↓
                                  Save to Database
                                  ↓
                                  ┌─────────────────────┐
                                  │  FRONTEND RENDERS   │
                                  │  wp_body_open hook  │
                                  │  ↓                  │
                                  │  topbar.php loads   │
                                  │  ↓                  │
                                  │  Gets DB values     │
                                  │  ↓                  │
                                  │  Display on site    │
                                  └─────────────────────┘
```

## File Relationship Diagram

```
functions.php (Main Loader)
    │
    ├─→ inc/admin/customizer-topbar.php
    │   ├─→ Define: ross_theme_customize_register()
    │   ├─→ Hook: customize_register (priority: none)
    │   ├─→ Define: Sanitization functions
    │   ├─→ Define: ross_theme_display_customizer_topbar()
    │   ├─→ Hook: wp_body_open (priority: 5)
    │   ├─→ Define: ross_topbar_dynamic_css()
    │   └─→ Hook: wp_head (priority: 999)
    │
    └─→ inc/admin/customizer-enqueuer.php
        ├─→ Define: ross_enqueue_customizer_assets()
        ├─→ Enqueue: customizer-topbar.css
        ├─→ Enqueue: customizer-topbar-preview.js
        ├─→ Enqueue: FontAwesome CDN
        └─→ Hook: customize_enqueue_scripts

header.php
    └─→ Calls: wp_body_open()
        ├─→ Triggers: wp_body_open hook
        │   └─→ Calls: ross_theme_display_customizer_topbar()
        │       └─→ get_template_part( 'template-parts/topbar' )
        │
        └─→ Calls: wp_head()
            └─→ Triggers: wp_head hook
                └─→ Calls: ross_topbar_dynamic_css()
                    └─→ Output: <style id="ross-topbar-dynamic-css">

Customizer Preview (JS)
    └─→ Load: customizer-topbar-preview.js
        ├─→ wp.customize('ross_topbar_enable').bind()
        ├─→ wp.customize('ross_topbar_bg_color').bind()
        ├─→ wp.customize('ross_topbar_*').bind()
        └─→ All 27 settings have preview bindings
            └─→ Update DOM: $('.site-topbar')
                ├─→ CSS: background, color, font-size
                ├─→ Text: .topbar-left, .topbar-announcement
                └─→ Visibility: .topbar-social, .topbar-phone
```

## Technology Stack

```
├── WordPress Core
│   ├── Customizer API
│   │   ├── wp_customize object
│   │   ├── WP_Customize_Control
│   │   └── WP_Customize_Color_Control
│   ├── Theme Mods (Options API)
│   │   └── theme_mod_* storage in wp_options
│   └── Hooks & Filters
│       ├── customize_register
│       ├── customize_enqueue_scripts
│       ├── wp_body_open
│       └── wp_head
│
├── PHP (Backend)
│   ├── Sanitization Functions
│   ├── Settings Registration
│   ├── Dynamic CSS Generation
│   └── Database Retrieval
│
├── JavaScript (Frontend & Preview)
│   ├── jQuery (DOM manipulation)
│   ├── wp.customize API (preview binding)
│   ├── postMessage (transport)
│   └── CSS Updates (live preview)
│
├── CSS
│   ├── Grid Layout (2-column)
│   ├── Responsive Design (< 768px)
│   ├── Marquee Animation
│   ├── Gradient Support
│   └── Shadow Effects
│
└── Third-Party
    ├── FontAwesome 6.4.0 (CDN)
    └── WordPress Database (wp_options)
```

---

**This architecture ensures:**
- ✅ Clean separation of concerns
- ✅ Modular, maintainable code
- ✅ Live preview functionality
- ✅ Responsive design
- ✅ Database persistence
- ✅ Security (sanitization)
- ✅ Performance (single CSS output)
- ✅ Extensibility (easy to add features)
