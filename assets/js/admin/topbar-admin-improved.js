/**
 * Improved Top Bar Admin JavaScript
 * Modern UI interactions, live preview, drag-and-drop
 */

(function($) {
    'use strict';

    // Initialize when DOM is ready
    $(document).ready(function() {
        initTopBarAdmin();
    });

    function initTopBarAdmin() {
        initColorPickers();
        initLivePreview();
        initSocialIcons();
        initDragAndDrop();
        initMediaUploaders();
        initToggleSwitches();
        initAnimations();
    }

    // Color Picker Initialization
    function initColorPickers() {
        $('.ross-color-input').each(function() {
            var $input = $(this);
            var $swatch = $input.closest('.ross-color-item').find('.ross-color-swatch');
            
            // Initialize WordPress color picker
            if ($.fn.wpColorPicker) {
                $input.wpColorPicker({
                    change: function(event, ui) {
                        updateColorSwatch($swatch, ui.color.toString());
                        updateLivePreview();
                    },
                    clear: function() {
                        var defaultColor = $input.data('default-color') || '#ffffff';
                        updateColorSwatch($swatch, defaultColor);
                        updateLivePreview();
                    }
                });
            } else {
                // Fallback to basic color input
                $input.on('input change', function() {
                    updateColorSwatch($swatch, $(this).val());
                    updateLivePreview();
                });
            }
        });
    }

    function updateColorSwatch($swatch, color) {
        if (color && $swatch.length) {
            $swatch.css('background-color', color);
        }
    }

    // Live Preview Updates
    function initLivePreview() {
        // Watch all relevant inputs for changes
        $('.ross-color-input, .ross-input, .ross-select, input[type="checkbox"]').on('change keyup', function() {
            updateLivePreview();
        });

        // Watch WordPress editor changes
        if (typeof tinyMCE !== 'undefined') {
            tinyMCE.on('AddEditor', function(e) {
                e.editor.on('change', function() {
                    updateLivePreview();
                });
            });
        }

        // Watch textarea changes (fallback for editors)
        $('textarea').on('input', function() {
            updateLivePreview();
        });

        // Initial preview update
        updateLivePreview();
    }

    function updateLivePreview() {
        var $previewBar = $('#ross-topbar-preview-bar');
        var $previewLeft = $('#ross-preview-left');
        var $previewCenter = $('#ross-preview-center');
        var $previewRight = $('#ross-preview-right');

        if (!$previewBar.length) return;

        // Get current values
        var bgColor = $('input[name="ross_theme_header_options[topbar_bg_color]"]').val() || '#001946';
        var textColor = $('input[name="ross_theme_header_options[topbar_text_color]"]').val() || '#ffffff';
        var iconColor = $('input[name="ross_theme_header_options[topbar_icon_color]"]').val() || '#E5C902';
        var borderColor = $('input[name="ross_theme_header_options[topbar_border_color]"]').val() || '#E5C902';
        var borderWidth = $('input[name="ross_theme_header_options[topbar_border_width]"]').val() || '2';
        var gradientEnabled = $('input[name="ross_theme_header_options[topbar_gradient_enable]"]').is(':checked');
        var gradientColor1 = $('input[name="ross_theme_header_options[topbar_gradient_color1]"]').val() || '#001946';
        var gradientColor2 = $('input[name="ross_theme_header_options[topbar_gradient_color2]"]').val() || '#003d7a';
        var shadowEnabled = $('input[name="ross_theme_header_options[topbar_shadow_enable]"]').is(':checked');
        var announcementEnabled = $('input[name="ross_theme_header_options[enable_announcement]"]').is(':checked');
        var announcementText = getEditorContent('ross_announcement_editor') || 'Announcement text appears here';
        var announcementAnimation = $('select[name="ross_theme_header_options[announcement_animation]"]').val() || 'none';
        var leftEnabled = $('input[name="ross_theme_header_options[enable_topbar_left]"]').is(':checked');
        var leftContent = getEditorContent('ross_left_content_editor') || 'Left Content';
        var socialEnabled = $('input[name="ross_theme_header_options[enable_social]"]').is(':checked');
        var iconSize = $('select[name="ross_theme_header_options[social_icon_size]"]').val() || 'medium';

        // Apply background
        if (gradientEnabled) {
            $previewBar.css('background', 'linear-gradient(90deg, ' + gradientColor1 + ', ' + gradientColor2 + ')');
        } else {
            $previewBar.css('background', bgColor);
        }

        // Apply text color
        $previewBar.css('color', textColor);

        // Apply border
        $previewBar.css('border-bottom', borderWidth + 'px solid ' + borderColor);

        // Apply shadow
        if (shadowEnabled) {
            $previewBar.css('box-shadow', '0 2px 4px rgba(0,0,0,0.1)');
        } else {
            $previewBar.css('box-shadow', 'none');
        }

        // Update left content
        if (leftEnabled) {
            $previewLeft.html(leftContent.length > 50 ? leftContent.substring(0, 50) + '...' : leftContent).show();
        } else {
            $previewLeft.hide();
        }

        // Update announcement
        if (announcementEnabled) {
            $previewCenter.html(announcementText.length > 60 ? announcementText.substring(0, 60) + '...' : announcementText).show();
            
            // Apply animation
            $previewCenter.removeClass('ross-preview-marquee ross-preview-fade ross-preview-slide');
            if (announcementAnimation !== 'none') {
                $previewCenter.addClass('ross-preview-' + announcementAnimation);
            }
        } else {
            $previewCenter.hide();
        }

        // Update social icons
        if (socialEnabled) {
            var iconHtml = '';
            var iconSizeMap = {
                'small': '12px',
                'medium': '14px',
                'large': '16px'
            };
            
            $('.ross-social-item').each(function() {
                var $item = $(this);
                var enabled = $item.find('input[type="checkbox"]').is(':checked');
                var url = $item.find('input[type="url"]').val();
                var platform = $item.data('platform');
                
                if (enabled && url) {
                    iconHtml += '<span class="preview-icon" style="font-size: ' + (iconSizeMap[iconSize] || '14px') + '; color: ' + iconColor + ';">';
                    
                    // Use platform-specific icons
                    var iconMap = {
                        'facebook': '📘',
                        'twitter': '🐦', 
                        'linkedin': '💼',
                        'instagram': '📷',
                        'youtube': '📺'
                    };
                    
                    iconHtml += iconMap[platform] || '🔗';
                    iconHtml += '</span>';
                }
            });
            
            $previewRight.html(iconHtml || '<span style="color: ' + textColor + '; font-size: 12px;">No icons enabled</span>').show();
        } else {
            $previewRight.hide();
        }

        // Adjust layout based on visible sections
        var visibleSections = 0;
        if (leftEnabled) visibleSections++;
        if (announcementEnabled) visibleSections++;
        if (socialEnabled) visibleSections++;

        if (visibleSections === 1) {
            $previewBar.css('justify-content', 'center');
        } else {
            $previewBar.css('justify-content', 'space-between');
        }
    }

    function getEditorContent(editorId) {
        // Try TinyMCE first
        if (typeof tinyMCE !== 'undefined' && tinyMCE.get(editorId)) {
            return tinyMCE.get(editorId).getContent();
        }
        
        // Fallback to textarea
        var $textarea = $('#' + editorId);
        if ($textarea.length) {
            return $textarea.val();
        }
        
        return '';
    }

    // Social Icons Management
    function initSocialIcons() {
        // Add custom icon button
        $('#ross-add-social-icon').on('click', function() {
            addCustomSocialIcon();
        });

        // Toggle social icon visibility based on URL
        $('.ross-social-item input[type="url"]').on('input change', function() {
            var $item = $(this).closest('.ross-social-item');
            var hasUrl = $(this).val().trim() !== '';
            var $checkbox = $item.find('input[type="checkbox"]');
            
            if (hasUrl && !$checkbox.is(':checked')) {
                $checkbox.prop('checked', true);
            }
            
            updateLivePreview();
        });

        // Icon size and shape changes
        $('select[name="ross_theme_header_options[social_icon_size]"], select[name="ross_theme_header_options[social_icon_shape]"]').on('change', function() {
            updateLivePreview();
        });
    }

    function addCustomSocialIcon() {
        var $container = $('#ross-social-items');
        var iconCount = $container.find('.ross-social-item').length;
        var platform = 'custom_' + iconCount;
        
        var $newItem = $(
            '<div class="ross-social-item" data-platform="' + platform + '">' +
                '<div class="ross-social-drag-handle">⋮⋮</div>' +
                '<div class="ross-social-toggle">' +
                    '<label class="ross-switch-label">' +
                        '<input type="checkbox" name="ross_theme_header_options[social_' + platform + '_enabled]" value="1" />' +
                        '<span class="ross-switch"></span>' +
                    '</label>' +
                '</div>' +
                '<div class="ross-social-icon-preview">' +
                    '<i class="fas fa-link" style="color: #6b7280;"></i>' +
                '</div>' +
                '<div class="ross-social-fields">' +
                    '<input type="text" name="ross_theme_header_options[social_' + platform + '_name]" value="Custom" class="ross-input ross-input-small" placeholder="Name" />' +
                    '<input type="url" name="ross_theme_header_options[social_' + platform + ']" class="ross-input ross-input-small" placeholder="URL" />' +
                    '<input type="text" name="ross_theme_header_options[social_' + platform + '_icon]" value="fas fa-link" class="ross-input ross-input-small" placeholder="Icon class" />' +
                    '<button type="button" class="ross-button ross-button-icon ross-upload-custom-icon" data-platform="' + platform + '" title="Upload Custom Icon">📁</button>' +
                    '<button type="button" class="ross-button ross-button-icon ross-remove-social-icon" title="Remove Icon">🗑️</button>' +
                '</div>' +
            '</div>'
        );
        
        $container.append($newItem);
        initDragAndDrop();
        initMediaUploaders();
        updateLivePreview();
    }

    // Drag and Drop for Social Icons
    function initDragAndDrop() {
        var $container = $('#ross-social-items');
        
        $container.sortable({
            handle: '.ross-social-drag-handle',
            placeholder: 'ross-social-item-placeholder',
            tolerance: 'pointer',
            start: function(e, ui) {
                ui.placeholder.height(ui.item.height());
            },
            update: function(e, ui) {
                updateSocialIconOrder();
            }
        });
    }

    function updateSocialIconOrder() {
        // This would typically update hidden inputs or make an AJAX call
        // For now, just update the preview
        updateLivePreview();
    }

    // Media Uploaders
    function initMediaUploaders() {
        $('.ross-upload-custom-icon').off('click').on('click', function(e) {
            e.preventDefault();
            
            var $button = $(this);
            var platform = $button.data('platform');
            
            if (!platform) return;
            
            // Create media frame
            var frame = wp.media({
                title: 'Select Custom Icon',
                button: { text: 'Select Icon' },
                multiple: false,
                library: {
                    type: 'image'
                }
            });
            
            frame.on('select', function() {
                var attachment = frame.state().get('selection').first().toJSON();
                var $iconInput = $('input[name="ross_theme_header_options[social_' + platform + '_icon]"]');
                var $preview = $button.closest('.ross-social-item').find('.ross-social-icon-preview i');
                
                // Update icon input with image URL
                $iconInput.val(attachment.url);
                
                // Update preview
                $preview.attr('class', '').css({
                    'background-image': 'url(' + attachment.url + ')',
                    'background-size': 'cover',
                    'background-position': 'center',
                    'width': '20px',
                    'height': '20px',
                    'border-radius': '50%'
                });
                
                updateLivePreview();
            });
            
            frame.open();
        });

        // Remove custom icon
        $(document).on('click', '.ross-remove-social-icon', function(e) {
            e.preventDefault();
            var $item = $(this).closest('.ross-social-item');
            $item.fadeOut(200, function() {
                $(this).remove();
                updateLivePreview();
            });
        });
    }

    // Toggle Switches
    function initToggleSwitches() {
        // Add ripple effect to switches
        $('.ross-switch-label').on('click', function(e) {
            var $switch = $(this).find('.ross-switch');
            
            // Create ripple effect
            var ripple = $('<span class="ross-switch-ripple"></span>');
            $switch.append(ripple);
            
            setTimeout(function() {
                ripple.remove();
            }, 600);
        });
    }

    // Animations
    function initAnimations() {
        // Add smooth transitions to all interactive elements
        $('.ross-admin-section, .ross-social-item, .ross-color-item').addClass('ross-animate-on-load');
        
        // Stagger animation on load
        $('.ross-animate-on-load').each(function(index) {
            $(this).css({
                'animation-delay': (index * 50) + 'ms',
                'animation': 'rossFadeInUp 0.4s ease forwards'
            });
        });
    }

    // Utility functions
    function debounce(func, wait) {
        var timeout;
        return function executedFunction() {
            var context = this;
            var args = arguments;
            var later = function() {
                timeout = null;
                func.apply(context, args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Debounce live preview updates for performance
    var debouncedPreview = debounce(updateLivePreview, 100);
    
    // Apply debounced preview to frequently triggered events
    $('.ross-input').on('keyup', debouncedPreview);

})(jQuery);

// CSS animations
var style = document.createElement('style');
style.textContent = `
    @keyframes rossFadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .ross-switch-ripple {
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.6);
        transform: scale(0);
        animation: rossRipple 0.6s ease-out;
        pointer-events: none;
    }
    
    @keyframes rossRipple {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
    
    .ross-social-item-placeholder {
        background: #f1f5f9;
        border: 2px dashed #cbd5e1;
        border-radius: 8px;
        margin: 6px 0;
    }
`;
document.head.appendChild(style);
