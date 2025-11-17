# Logo Upload - Complete Testing Guide

## ✅ Latest Fix Applied (November 12, 2025 - Final)

### What Was Done:

1. **Created Standalone Uploader** (`uploader-standalone.js`)
   - No jQuery dependencies
   - No wp-color-picker dependencies
   - Pure vanilla JavaScript
   - Self-initializing with multiple fallbacks

2. **Added Inline Debug Script** to admin pages
   - Tests button detection immediately
   - Tests after DOM ready
   - Tests with 500ms delay
   - Provides fallback click handler

3. **Simplified Enqueue Process**
   - Only requires: `jquery`, `wp_enqueue_media()`, and our script
   - No complex dependency chains
   - Loads in page header (not footer)

---

## 🧪 Step-by-Step Testing

### Step 1: Clear Everything
```
1. Hard refresh browser: Ctrl+F5 (or Cmd+Shift+R on Mac)
2. Close browser completely
3. Open browser fresh
4. Go to WordPress Admin
```

### Step 2: Navigate to Header Options
```
WordPress Admin → Ross Theme → Header Options
```

### Step 3: Open Browser Console
```
Press: F12
Click: Console tab
```

### Step 4: Check for Initial Logs
You should see IMMEDIATELY:
```
INLINE SCRIPT: Header page loaded
wp available: object
wp.media available: function
DOMContentLoaded fired
Upload buttons found: 2
=== DELAYED CHECK ===
Upload buttons in page: 2
Button 0: {text: "Upload Logo", target: "logo_upload", class: "button ross-upload-button"}
Button 1: {text: "Upload Dark Logo", target: "logo_dark", class: "button ross-upload-button"}
```

If you DON'T see these messages:
- Check if page actually loaded
- Try a different browser
- Try incognito/private mode
- Check for JavaScript errors

### Step 5: Click Upload Button
When you click "Upload Logo", you should see:

**First set of messages** (from inline script fallback):
```
BUTTON CLICKED - Handler attached inline
Button clicked! Target: logo_upload
```

**OR second set of messages** (from standalone script):
```
=== UPLOAD CLICK HANDLER ===
Button clicked
Target ID: logo_upload
wp: object
wp.media: function
Opening media library...
Media library opened
```

### Step 6: Media Library Should Open
- WordPress media library should appear
- You should be able to select an image
- When selected, you should see:
```
Image selected
Attachment data: {id: 123, url: "http://...", ...}
Setting URL to: http://example.com/wp-content/uploads/...
SUCCESS: URL set and change event triggered
```

### Step 7: Verify URL Appears
- Check if the image URL appears in the text field
- Check if image preview appears below the field
- Click "Save Header Settings"

---

## 🐛 Troubleshooting If Still Not Working

### Issue 1: No Console Messages at All

**Cause**: Script not loading
**Solution**:
1. Check Network tab in DevTools (F12)
2. Look for `uploader-standalone.js`
3. Should show status 200 (loaded successfully)
4. Check for 404 errors

**If you see 404**:
- File may not exist or path is wrong
- Verify file exists: `assets/js/admin/uploader-standalone.js`

### Issue 2: Script Loads but No Output

**Cause**: wp.media not available
**Solution**:
1. Check if `wp_enqueue_media()` is being called
2. Verify you're on a Ross Theme admin page
3. Check for JavaScript conflicts/errors
4. Try disabling all plugins except Ross Theme

### Issue 3: Button Click Shows Alert but No Media Library

**Cause**: wp.media framework not initialized
**Solution**:
1. Make sure you're logged in as admin
2. Check user capabilities (should have upload_files)
3. Try in a fresh incognito window
4. Check browser console for specific errors

### Issue 4: Media Library Opens but Selection Doesn't Work

**Cause**: Attachment data format issue
**Solution**:
1. Check console for "Attachment data:" log
2. Verify URL exists in the data
3. Check that target input field exists
4. Look for errors after selection

---

## 🔍 Advanced Debugging

### Enable Full Logging

Open console and paste this to test manually:

```javascript
// Check if wp.media is available
console.log('wp:', window.wp);
console.log('wp.media:', window.wp.media);

// Find buttons
var buttons = document.querySelectorAll('.ross-upload-button');
console.log('Buttons:', buttons.length);

// Find input fields
var inputs = document.querySelectorAll('[id*="logo"]');
console.log('Logo inputs:', inputs.length);
inputs.forEach(function(input) {
    console.log('Input:', input.id, input.value);
});

// Test media library open
if (wp.media) {
    var frame = wp.media({title: 'Test', multiple: false});
    frame.open();
    console.log('Test frame opened');
}
```

### Check Network Requests

1. Open F12 → Network tab
2. Filter for `admin-ajax.php`
3. Click Upload button
4. Look for any failed requests
5. Check for 403 Forbidden errors

### Check Server Error Log

If all else fails, check your server logs:
```
/var/log/php.log
or check hosting control panel
```

---

## 📋 Files Involved

### PHP Files:
- `inc/features/header/header-options.php` - Enqueue script
- `inc/features/general/general-options.php` - Enqueue script
- `inc/admin/admin-pages.php` - Admin page rendering + inline debug

### JavaScript Files:
- `assets/js/admin/uploader-standalone.js` - Main uploader (NEW)
- `assets/js/admin/header-options-test.js` - Test version (backup)
- `assets/js/admin/general-options-test.js` - Test version (backup)
- `assets/js/admin/header-options.js` - Original (not used currently)
- `assets/js/admin/general-options.js` - Original (not used currently)

---

## ✅ Expected Behavior

1. Page loads → Console shows inline debug messages
2. Click button → Media library opens immediately
3. Select image → URL appears in text field
4. Save → Settings are saved with image URL

---

## 📞 If Still Not Working

Provide these details:
1. Browser and version (e.g., Chrome 120)
2. WordPress version
3. Full console output (copy/paste)
4. Network tab errors (screenshot or list)
5. Any JavaScript errors shown in console
6. Whether inline alert appeared when button clicked
7. Whether media library window appeared at all

---

Last Updated: November 12, 2025 - FINAL VERSION
