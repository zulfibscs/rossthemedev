# Logo Upload Troubleshooting Guide

## Recent Fixes Applied (November 12, 2025)

The following improvements have been made to fix logo upload functionality:

### 1. **Added Media Script Dependency**
- **Files Modified**: 
  - `inc/features/header/header-options.php`
  - `inc/features/general/general-options.php`
- **Change**: Added `'media'` to the script dependencies array
- **Before**: `array('jquery', 'wp-color-picker')`
- **After**: `array('jquery', 'wp-color-picker', 'media')`

### 2. **Enhanced JavaScript with Comprehensive Debugging**
- **Files Modified**:
  - `assets/js/admin/header-options.js`
  - `assets/js/admin/general-options.js`
- **Improvements**:
  - Better event binding using `$(document).on('click', ...)`
  - Added `e.stopPropagation()` to prevent event bubbling
  - Added comprehensive error checking and validation
  - Added detailed console logging for debugging
  - Improved error messages and user alerts
  - Added frame caching to prevent multiple frame instances
  - Better try-catch error handling

### 3. **Added Global Media Enqueue Hook**
- **File Modified**: `inc/admin/admin-pages.php`
- **Change**: Added a priority-5 admin_enqueue_scripts hook to ensure `wp_enqueue_media()` is called early

---

## How to Troubleshoot Logo Upload

### Step 1: Clear Cache and Refresh
1. **Browser Cache**: Press `Ctrl+Shift+Delete` (or `Cmd+Shift+Delete` on Mac)
2. **WordPress Cache**: If using a caching plugin, clear it
3. **Hard Refresh**: Press `Ctrl+F5` (or `Cmd+Shift+R` on Mac)

### Step 2: Check Browser Console for Errors
1. Open **Header Options** or **General Settings** in WordPress Admin
2. Press **F12** to open Developer Tools
3. Go to the **Console** tab
4. Click the "Upload Logo" button
5. Look for these log messages:

```
=== Header Options Script Loaded ===
wp object: object
wp.media available: function
Color pickers initialized
Upload buttons found: 2
Button 0 target: logo_upload
Button 1 target: logo_dark
```

### Step 3: Common Issues and Solutions

#### Issue: "wp.media is not available"
**Possible Causes:**
- Media library scripts not loaded
- User doesn't have upload permissions
- Plugin conflict

**Solutions:**
- Verify user role has `upload_files` capability
- Disable all plugins except Ross Theme to test
- Check that `wp_enqueue_media()` is being called

#### Issue: Button click has no effect
**Possible Causes:**
- Script not loading
- jQuery conflicts
- DOM elements not ready

**Solutions:**
- Verify in console that the script loaded (look for "Header Options Script Loaded")
- Check for JavaScript errors in the console
- Ensure jQuery is loaded

#### Issue: Media library opens but selection doesn't work
**Possible Causes:**
- Attachment data format issue
- Target input field ID mismatch

**Solutions:**
- Check console for "Attachment data:" log to see what's being retrieved
- Verify input field IDs match (should be `logo_upload`, `logo_dark`, `default_logo`, `dark_mode_logo`, `favicon`)

#### Issue: URL is set but not saved
**Possible Causes:**
- Form not submitted
- Sanitization issue
- PHP validation error

**Solutions:**
- Click the "Save Header Settings" or "Save General Settings" button
- Check for admin notices at the top of the page
- Review server error logs

---

## Debugging Tips

### Enable Console Logging
The scripts now include extensive console logging. Open the Developer Console (F12) to see:

1. **Script Load Messages**: Confirms the admin script is loading
2. **Button Click Messages**: Shows when upload button is clicked
3. **wp.media Status**: Shows if WordPress media library is available
4. **Image Selection**: Shows when an image is selected
5. **Error Messages**: Any errors encountered

### Example Console Output:
```
=== Header Options Script Loaded ===
wp object: object
wp.media available: function
Color pickers initialized
Upload buttons found: 2
Button 0 target: logo_upload
Button 1 target: logo_dark
=== Upload button clicked ===
Target field: logo_upload
jQuery version: 3.6.0
wp object: object
wp.media: function
All validation checks passed, opening media library...
Media library opened successfully
Image selected
Attachment data: {id: 123, url: "http://example.com/wp-content/uploads/2025/11/logo.png", ...}
Setting URL to: http://example.com/wp-content/uploads/2025/11/logo.png
SUCCESS: Image URL set and change event triggered
```

---

## File Locations

### Admin Pages
- **Header Options**: `/wp-admin/admin.php?page=ross-theme-header`
- **General Settings**: `/wp-admin/admin.php?page=ross-theme-general`

### Configuration Files
- **Header Options Class**: `inc/features/header/header-options.php`
- **General Options Class**: `inc/features/general/general-options.php`
- **Admin Pages**: `inc/admin/admin-pages.php`

### JavaScript Files
- **Header Options JS**: `assets/js/admin/header-options.js`
- **General Options JS**: `assets/js/admin/general-options.js`

---

## Logo Upload Fields

### Header Options
- `logo_upload` - Main logo
- `logo_dark` - Dark mode logo variant

### General Settings
- `favicon` - Website favicon
- `default_logo` - Default logo (fallback)
- `dark_mode_logo` - Dark mode logo (General settings)

---

## Manual Testing

To manually test the upload functionality:

1. Go to **Ross Theme > Header Options**
2. Click "Upload Logo" button
3. Select an image from your media library
4. Verify the image URL appears in the text field
5. Verify the image preview displays below the field
6. Click "Save Header Settings"
7. Visit the front-end to verify the logo displays

---

## If Issues Persist

If you're still experiencing issues after trying these steps:

1. **Note the exact error message** shown in browser console
2. **Check PHP error logs** (usually in `/var/log/php.log` or your hosting control panel)
3. **Verify file permissions** on the uploads directory
4. **Disable conflicting plugins** one by one to identify conflicts
5. **Clear all caches** (browser, WordPress, hosting provider)
6. **Test in a fresh browser** window (incognito/private mode)

---

## Code Changes Summary

### header-options.php (Line 18)
```php
wp_enqueue_script('ross-header-admin', get_template_directory_uri() . '/assets/js/admin/header-options.js', array('jquery', 'wp-color-picker', 'media'), '1.0.0', true);
```

### general-options.php (Line 22)
```php
wp_enqueue_script('ross-general-admin', get_template_directory_uri() . '/assets/js/admin/general-options.js', array('jquery', 'wp-color-picker', 'media'), '1.0.0', true);
```

### admin-pages.php (Lines 6-12)
```php
function ross_theme_enqueue_admin_scripts($hook) {
    if (strpos($hook, 'ross-theme') !== false) {
        wp_enqueue_media();
        wp_enqueue_script('jquery');
    }
}
add_action('admin_enqueue_scripts', 'ross_theme_enqueue_admin_scripts', 5);
```

---

Last Updated: November 12, 2025
