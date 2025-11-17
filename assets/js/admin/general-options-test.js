// Test version - minimal logo uploader for general options
(function() {
    'use strict';
    
    console.log('=== GENERAL OPTIONS TEST SCRIPT LOADED ===');
    console.log('wp:', typeof wp);
    console.log('wp.media:', typeof wp.media);
    console.log('jQuery:', typeof jQuery);
    
    // Wait for DOM and jQuery to be ready
    if (typeof jQuery !== 'undefined') {
        jQuery(document).ready(function($) {
            console.log('DOM ready - jQuery available');
            console.log('wp.media available:', typeof wp.media);
            
            // Find all upload buttons
            var uploadButtons = document.querySelectorAll('.ross-upload-button');
            console.log('Upload buttons found:', uploadButtons.length);
            
            // Attach click handler to each button
            uploadButtons.forEach(function(button, index) {
                console.log('Button ' + index + ':', button);
                console.log('  - Class:', button.className);
                console.log('  - Data target:', button.getAttribute('data-target'));
                
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    var targetId = this.getAttribute('data-target');
                    console.log('=== UPLOAD BUTTON CLICKED ===');
                    console.log('Target ID:', targetId);
                    console.log('wp:', typeof wp);
                    console.log('wp.media:', typeof wp.media);
                    
                    if (typeof wp === 'undefined' || typeof wp.media === 'undefined') {
                        alert('Media library not available. Please refresh the page.');
                        console.error('ERROR: wp.media not available');
                        return false;
                    }
                    
                    try {
                        console.log('Opening media library...');
                        
                        var frame = wp.media({
                            title: 'Select Image',
                            button: {
                                text: 'Select'
                            },
                            multiple: false
                        });
                        
                        frame.on('select', function() {
                            var attachment = frame.state().get('selection').first().toJSON();
                            console.log('Image selected:', attachment);
                            
                            if (attachment.url) {
                                var input = document.getElementById(targetId);
                                if (input) {
                                    input.value = attachment.url;
                                    console.log('URL set to:', attachment.url);
                                } else {
                                    console.error('ERROR: Input field not found:', targetId);
                                }
                            }
                        });
                        
                        frame.open();
                        console.log('Media library opened');
                    } catch(err) {
                        console.error('ERROR:', err);
                        alert('Error: ' + err.message);
                    }
                    
                    return false;
                });
            });
            
            console.log('=== GENERAL OPTIONS TEST SCRIPT READY ===');
        });
    } else {
        console.error('jQuery not available');
    }
})();
