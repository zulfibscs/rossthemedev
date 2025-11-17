jQuery(document).ready(function($) {
    // Initialize color pickers
    $('.color-picker').wpColorPicker();
    
    // Conditional logic for widgets
    function toggleWidgetsFields() {
        if ($('input[name="ross_theme_footer_options[enable_widgets]"]').is(':checked')) {
            $('#widgets_bg_color').closest('tr').show();
            $('#widgets_text_color').closest('tr').show();
            $('#widget_title_color').closest('tr').show();
        } else {
            $('#widgets_bg_color').closest('tr').hide();
            $('#widgets_text_color').closest('tr').hide();
            $('#widget_title_color').closest('tr').hide();
        }
    }
    
    // Conditional logic for CTA
    function toggleCTAFields() {
        if ($('input[name="ross_theme_footer_options[enable_footer_cta]"]').is(':checked')) {
            $('#cta_title').closest('tr').show();
            $('#cta_button_text').closest('tr').show();
            $('#cta_bg_color').closest('tr').show();
        } else {
            $('#cta_title').closest('tr').hide();
            $('#cta_button_text').closest('tr').hide();
            $('#cta_bg_color').closest('tr').hide();
        }
    }
    
    // Conditional logic for social icons
    function toggleSocialFields() {
        if ($('input[name="ross_theme_footer_options[enable_social_icons]"]').is(':checked')) {
            $('#facebook_url').closest('tr').show();
            $('#linkedin_url').closest('tr').show();
            $('#instagram_url').closest('tr').show();
            $('#social_icon_color').closest('tr').show();
        } else {
            $('#facebook_url').closest('tr').hide();
            $('#linkedin_url').closest('tr').hide();
            $('#instagram_url').closest('tr').hide();
            $('#social_icon_color').closest('tr').hide();
        }
    }
    
    // Conditional logic for copyright
    function toggleCopyrightFields() {
        if ($('input[name="ross_theme_footer_options[enable_copyright]"]').is(':checked')) {
            $('#copyright_text').closest('tr').show();
            $('#copyright_bg_color').closest('tr').show();
            $('#copyright_text_color').closest('tr').show();
            $('#copyright_alignment').closest('tr').show();
        } else {
            $('#copyright_text').closest('tr').hide();
            $('#copyright_bg_color').closest('tr').hide();
            $('#copyright_text_color').closest('tr').hide();
            $('#copyright_alignment').closest('tr').hide();
        }
    }
    
    // Initialize conditional fields
    toggleWidgetsFields();
    toggleCTAFields();
    toggleSocialFields();
    toggleCopyrightFields();
    
    // Bind events
    $('input[name="ross_theme_footer_options[enable_widgets]"]').on('change', toggleWidgetsFields);
    $('input[name="ross_theme_footer_options[enable_footer_cta]"]').on('change', toggleCTAFields);
    $('input[name="ross_theme_footer_options[enable_social_icons]"]').on('change', toggleSocialFields);
    $('input[name="ross_theme_footer_options[enable_copyright]"]').on('change', toggleCopyrightFields);
});