// Standalone uploader - works without dependencies
(function() {
    'use strict';
    
    function initUploader() {
        console.log('=== STANDALONE UPLOADER INITIALIZING ===');
        console.log('Document ready');
        console.log('wp:', typeof window.wp);
        console.log('wp.media:', typeof (window.wp && window.wp.media));
        
        var buttons = document.querySelectorAll('.ross-upload-button');
        console.log('Upload buttons found:', buttons.length);
        
        if (buttons.length === 0) {
            console.warn('No upload buttons found on page');
            return;
        }
        
        // Add click handler to each button
        Array.prototype.forEach.call(buttons, function(button, index) {
            console.log('Setting up button ' + index);
            
            button.addEventListener('click', handleUploadClick);
        });
        
        console.log('=== STANDALONE UPLOADER READY ===');
    }
    
    function handleUploadClick(e) {
        e.preventDefault();
        e.stopPropagation();
        
        var button = e.currentTarget;
        var targetId = button.getAttribute('data-target');
        
        console.log('=== UPLOAD CLICK HANDLER ===');
        console.log('Button clicked');
        console.log('Target ID:', targetId);
        console.log('wp:', typeof window.wp);
        console.log('wp.media:', typeof (window.wp && window.wp.media));
        
        // Check if wp.media is available
        if (!window.wp || !window.wp.media) {
            console.error('wp.media is not available');
            alert('Error: Media library not available. Please refresh the page.');
            return false;
        }
        
        // Check if target field exists
        var targetInput = document.getElementById(targetId);
        if (!targetInput) {
            console.error('Target input not found:', targetId);
            alert('Error: Target input field not found: ' + targetId);
            return false;
        }
        
        console.log('Opening media library...');
        
        try {
            var frame = wp.media({
                title: 'Select Image',
                button: {
                    text: 'Select'
                },
                multiple: false,
                library: {
                    type: 'image'
                }
            });
            
            frame.on('select', function() {
                console.log('Image selected');
                var selection = frame.state().get('selection');
                
                if (!selection) {
                    console.error('No selection object');
                    return;
                }
                
                var attachment = selection.first();
                
                if (!attachment) {
                    console.error('No attachment selected');
                    return;
                }
                
                var data = attachment.toJSON();
                console.log('Attachment data:', data);
                
                if (data.url) {
                    console.log('Setting URL to:', data.url);
                    targetInput.value = data.url;
                    
                    // Trigger change event
                    var event = new Event('change', { bubbles: true });
                    targetInput.dispatchEvent(event);
                    
                    console.log('SUCCESS: URL set and change event triggered');
                } else {
                    console.error('No URL in attachment data');
                    alert('Error: Could not get image URL');
                }
            });
            
            frame.open();
            console.log('Media library opened');
            
        } catch (err) {
            console.error('Error:', err.message);
            alert('Error: ' + err.message);
        }
        
        return false;
    }
    
    // Initialize when document is ready
    if (document.readyState === 'loading') {
        console.log('Document still loading, waiting...');
        document.addEventListener('DOMContentLoaded', initUploader);
    } else {
        console.log('Document already loaded');
        initUploader();
    }
    
    // Also initialize after a short delay to catch any late-loading buttons
    setTimeout(initUploader, 1000);
})();
