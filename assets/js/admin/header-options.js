jQuery(document).ready(function($) {
    console.log('=== Header Options Script Loaded ===');
    console.log('wp object:', typeof wp);
    console.log('wp.media available:', typeof wp.media);
    
    // Initialize color pickers
    $('.color-picker').wpColorPicker();
    console.log('Color pickers initialized');
    
    // Check if upload buttons exist
    var uploadButtons = $('.ross-upload-button');
    console.log('Upload buttons found:', uploadButtons.length);
    uploadButtons.each(function(i) {
        console.log('Button ' + i + ' target:', $(this).data('target'));
    });
    
    // Media uploader for logos - with fallback and better error handling
    $(document).on('click', '.ross-upload-button', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        console.log('=== Upload button clicked ===');
        
        var button = $(this);
        var target = button.data('target');
        
        console.log('Target field:', target);
        console.log('jQuery version:', $.fn.jquery);
        console.log('wp object:', typeof wp);
        console.log('wp.media:', typeof wp.media);
        
        // Validation checks
        if (!target) {
            console.error('ERROR: No target specified for upload button');
            alert('Error: No target field specified for this upload button. Please contact support.');
            return false;
        }
        
        if (typeof wp === 'undefined') {
            console.error('ERROR: wp object is not available');
            alert('Error: WordPress object is not loaded. Please refresh the page.');
            return false;
        }
        
        if (typeof wp.media === 'undefined') {
            console.error('ERROR: wp.media is not available');
            alert('Error: Media library is not available. Please ensure you are logged in and have permission to upload files.');
            return false;
        }
        
        console.log('All validation checks passed, opening media library...');
        
        // Create media uploader instance
        var custom_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Select Image',
            button: {
                text: 'Select Image'
            },
            multiple: false,
            library: {
                type: 'image'
            }
        });
        
        // Handle selection
        custom_uploader.on('select', function() {
            console.log('Image selected');
            try {
                var attachment = custom_uploader.state().get('selection').first().toJSON();
                console.log('Attachment data:', attachment);
                
                if (attachment && attachment.url) {
                    console.log('Setting URL to:', attachment.url);
                    $('#' + target).val(attachment.url);
                    $('#' + target).trigger('change');
                    console.log('SUCCESS: Image URL set and change event triggered');
                } else {
                    console.error('ERROR: No URL in attachment:', attachment);
                    alert('Error: Could not retrieve image URL. Please try again.');
                }
            } catch (err) {
                console.error('ERROR in select handler:', err);
                alert('Error: ' + err.message);
            }
        });
        
        // Handle close event
        custom_uploader.on('close', function() {
            console.log('Media library closed');
        });
        
        // Open media library
        try {
            custom_uploader.open();
            console.log('Media library opened successfully');
        } catch (err) {
            console.error('ERROR opening media library:', err);
            alert('Error: Could not open media library: ' + err.message);
        }
        
        return false;
    });
    
    // Conditional logic for top bar
    function toggleTopBarFields() {
        var isChecked = $('input[name="ross_theme_header_options[enable_topbar]"]').is(':checked');
        console.log('Toggling topbar fields, checked:', isChecked);
        if (isChecked) {
            $('#topbar_left_content').closest('tr').show();
            $('#topbar_bg_color').closest('tr').show();
            $('#topbar_text_color').closest('tr').show();
        } else {
            $('#topbar_left_content').closest('tr').hide();
            $('#topbar_bg_color').closest('tr').hide();
            $('#topbar_text_color').closest('tr').hide();
        }
    }
    
    // Conditional logic for CTA button
    function toggleCTAFields() {
        var isChecked = $('input[name="ross_theme_header_options[enable_cta_button]"]').is(':checked');
        console.log('Toggling CTA fields, checked:', isChecked);
        if (isChecked) {
            $('#cta_button_text').closest('tr').show();
            $('#cta_button_color').closest('tr').show();
        } else {
            $('#cta_button_text').closest('tr').hide();
            $('#cta_button_color').closest('tr').hide();
        }
    }
    
    // Initialize conditional fields
    toggleTopBarFields();
    toggleCTAFields();
    console.log('Conditional fields initialized');
    
    // Bind events
    $('input[name="ross_theme_header_options[enable_topbar]"]').on('change', toggleTopBarFields);
    $('input[name="ross_theme_header_options[enable_cta_button]"]').on('change', toggleCTAFields);
    
    // Color picker preview
    $('.color-picker').on('change', function() {
        console.log('Color changed:', $(this).val());
    });
    
    console.log('=== Header Options Script Ready ===');
});