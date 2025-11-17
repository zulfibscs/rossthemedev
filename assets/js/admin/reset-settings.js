jQuery(document).ready(function($) {
    // Enhanced reset confirmation
    $('.ross-reset-button').on('click', function(e) {
        e.preventDefault();
        
        var form = $(this).closest('form');
        var resetType = form.data('reset-type') || 'settings';
        
        // Custom confirmation dialog
        var confirmed = confirm(
            '⚠️ RESET CONFIRMATION\n\n' +
            'Are you sure you want to reset ' + resetType + ' settings to defaults?\n\n' +
            'This action will:\n' +
            '• Restore all ' + resetType + ' options to factory defaults\n' +
            '• Remove any customizations you have made\n' +
            '• This action cannot be undone!\n\n' +
            'Click OK to proceed with reset, or Cancel to keep your current settings.'
        );
        
        if (confirmed) {
            form.submit();
        }
    });
    
    // Add danger styling to reset buttons
    $('.ross-reset-button').css({
        'background': '#d63638',
        'border-color': '#d63638',
        'color': 'white'
    }).hover(function() {
        $(this).css('background', '#b32d2e');
    }, function() {
        $(this).css('background', '#d63638');
    });
});