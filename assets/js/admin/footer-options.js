jQuery(document).ready(function($) {
    // Initialize color pickers
    $('.color-picker').wpColorPicker();

    // Media uploader for background image field (reusable frame per button)
    $(document).on('click', '.ross-upload-button', function(e) {
        e.preventDefault();
        var $btn = $(this);
        var target = $btn.data('target');
        var inputName = $btn.data('input-name') || $btn.attr('data-input-name') || null;
        var $input = null;

        // Primary: selector from data-target (accepts '#id' or 'id')
        if (target) {
            // Allow values like '#ross-styling-bg-image' or 'ross-styling-bg-image' or 'ross_theme_footer_options[styling_bg_image]'
            var normalized = target;
            if (normalized.indexOf('#') === 0) {
                if ($(normalized).length) $input = $(normalized);
                else {
                    var id = normalized.replace('#','');
                    if ($('#' + id).length) $input = $('#' + id);
                }
            } else {
                // try as id
                if ($('#' + normalized).length) $input = $('#' + normalized);
                // try as name inside brackets
                if (!$input || !$input.length) {
                    if ($('input[name="ross_theme_footer_options[' + normalized + ']"]').length) {
                        $input = $('input[name="ross_theme_footer_options[' + normalized + ']"]');
                    }
                }
                if (!$input || !$input.length) {
                    // maybe target is the full name already
                    if ($(normalized).length) $input = $(normalized);
                }
            }
        }

        // Fallback 1: explicit data-input-name attribute
        if ((!$input || !$input.length) && inputName) {
            if ($('input[name="' + inputName + '"]').length) {
                $input = $('input[name="' + inputName + '"]');
            }
        }

        // Fallback 2: an input with the expected option name
        if ((!$input || !$input.length)) {
            $input = $('input[name="ross_theme_footer_options[styling_bg_image]"]');
        }

        // Fallback 2: the first text/url input in the same table row / container / surrounding label
        if (!$input || !$input.length) {
            $input = $btn.closest('tr, .form-field, .form-table, p, div').find('input[type="text"], input[type="url"]').first();
        }

        // Fallback 3: previous sibling input
        if ((!$input || !$input.length) && $btn.prev('input[type="text"]').length) {
            $input = $btn.prev('input[type="text"]');
        }

        // Fallback 4: any input in same container group
        if ((!$input || !$input.length)) {
            var possible = $btn.siblings('input[type="text"], input[type="url"]').first();
            if (possible && possible.length) $input = possible;
        }

        // If still not found, try name ends-with selector (handles variations)
        if ((!$input || !$input.length)) {
            var ends = $('input[name$="[styling_bg_image]"]').first();
            if (ends && ends.length) $input = ends;
        }

        // Log debug info about the resolution
        console.debug('Uploader target: ', {target: target, inputName: inputName, found: ($input && $input.length ? true : false)});

        // If still not found, stop gracefully and inform the developer/admin
        if (!$input || !$input.length) {
            console.error('Target input field not found: ' + (target || 'unknown') + ' (data-input-name: ' + (inputName || 'none') + ')');
            // Do not block further UI; show a non-blocking notice in console only
            return;
        }

        // Use a stored frame on the button if available
        // Use a frame cached per-input selector so multiple uploaders can coexist
        var cacheKey = ($input.attr('id') ? ('#' + $input.attr('id')) : ($input.attr('name') || 'ross_footer_uploader'));
        var frame = $btn.data('frame') || $(document.body).data('ross-frame-' + cacheKey);
        if (!frame) {
            if (typeof wp === 'undefined' || typeof wp.media === 'undefined') {
                alert('Media uploader is not available. Make sure WordPress media scripts are loaded.');
                return;
            }

            frame = wp.media({
                title: 'Select Background Image',
                button: { text: 'Use this image' },
                multiple: false,
                library: { type: 'image' }
            });

            frame.on('select', function() {
                var attachment = frame.state().get('selection').first().toJSON();
                if (attachment && (attachment.url || attachment.sizes)) {
                    var url = attachment.url || (attachment.sizes && (attachment.sizes.full ? attachment.sizes.full.url : (attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : '')));
                    if (url) {
                        $input.val(url).trigger('change');
                        // find preview element adjacent to input using ID or fallback
                        var previewEl = null;
                        var iid = $input.attr('id');
                        if (iid && $('#' + iid + '-preview').length) {
                            previewEl = $('#' + iid + '-preview');
                        } else if ($('#ross-styling-bg-image-preview').length) {
                            previewEl = $('#ross-styling-bg-image-preview');
                        } else {
                            previewEl = $input.siblings('span').first();
                        }
                        if (previewEl && previewEl.length) {
                            previewEl.html('<img src="' + url + '" style="max-height:40px;vertical-align:middle;border:1px solid #ddd;padding:2px;" />');
                        }
                        // set attachment id hidden field if present
                        if (attachment.id) {
                            var idInput = $('#' + $input.attr('id') + '-id');
                            if (idInput && idInput.length) idInput.val(attachment.id).trigger('change');
                            // fallback by name: convert 'ross_theme_footer_options[styling_bg_image]' \n                            // to 'ross_theme_footer_options[styling_bg_image_id]'
                            var nameInput = null;
                            var fname = $input.attr('name') || '';
                            if (fname && fname.indexOf(']') !== -1) {
                                var idx = fname.lastIndexOf('[');
                                if (idx !== -1) {
                                    var prefix = fname.substring(0, idx);
                                    var inner = fname.substring(idx+1, fname.length-1); // without ]
                                    var newName = prefix + '[' + inner + '_id]';
                                    nameInput = $('input[name="' + newName + '"]');
                                }
                            }
                            if (!nameInput || !nameInput.length) {
                                nameInput = $('input[name="' + fname + '_id"]');
                            }
                            if (nameInput && nameInput.length) nameInput.val(attachment.id).trigger('change');
                        }
                    }
                }
            });

            // cache on button and on document body keyed by input id/name
            $btn.data('frame', frame);
            $(document.body).data('ross-frame-' + cacheKey, frame);
        }

        // Open the frame
        $btn.data('frame').open();
    });
    
    // Conditional logic for widgets
    function toggleWidgetsFields() {
        var enabled = $('input[name="ross_theme_footer_options[enable_widgets]"]').is(':checked');
        // cleanup: selectors below are used via .closest('tr') calls

        if (enabled) {
            $('[name="ross_theme_footer_options[widgets_bg_color]"]').closest('tr').show();
            $('[name="ross_theme_footer_options[widgets_text_color]"]').closest('tr').show();
            $('[name="ross_theme_footer_options[widget_title_color]"]').closest('tr').show();
            $('[name="ross_theme_footer_options[use_template_colors]"]').closest('tr').show();
            // show any per-template custom color fields (names like ross_theme_footer_options[template1_bg])
            $('[name*="ross_theme_footer_options[template"]').closest('tr').show();
            // show gradient color inputs if gradient checkbox enabled
            var gradEnabled = $('input[name="ross_theme_footer_options[styling_bg_gradient]"]').is(':checked');
            if (gradEnabled) {
                $('[name="ross_theme_footer_options[styling_bg_gradient_from]"]').closest('tr').show();
                $('[name="ross_theme_footer_options[styling_bg_gradient_to]"]').closest('tr').show();
            } else {
                $('[name="ross_theme_footer_options[styling_bg_gradient_from]"]').closest('tr').hide();
                $('[name="ross_theme_footer_options[styling_bg_gradient_to]"]').closest('tr').hide();
            }
        } else {
            $('[name="ross_theme_footer_options[widgets_bg_color]"]').closest('tr').hide();
            $('[name="ross_theme_footer_options[widgets_text_color]"]').closest('tr').hide();
            $('[name="ross_theme_footer_options[widget_title_color]"]').closest('tr').hide();
            $('[name="ross_theme_footer_options[use_template_colors]"]').closest('tr').hide();
            $('[name*="ross_theme_footer_options[template"]').closest('tr').hide();
            $('[name="ross_theme_footer_options[styling_bg_gradient_from]"]').closest('tr').hide();
            $('[name="ross_theme_footer_options[styling_bg_gradient_to]"]').closest('tr').hide();
        }
    }
    
    // Conditional logic for CTA
    function toggleCTAFields() {
        var enabled = $('input[name="ross_theme_footer_options[enable_footer_cta]"]').is(':checked');
        if (enabled) {
            $('[name="ross_theme_footer_options[cta_title]"]').closest('tr').show();
            $('[name="ross_theme_footer_options[cta_text]"]').closest('tr').show();
            $('[name="ross_theme_footer_options[cta_button_text]"]').closest('tr').show();
            $('[name="ross_theme_footer_options[cta_button_url]"]').closest('tr').show();
            $('[name="ross_theme_footer_options[cta_icon]"]').closest('tr').show();
            $('[name="ross_theme_footer_options[cta_bg_color]"]').closest('tr').show();
            $('[name="ross_theme_footer_options[cta_bg_image]"]').closest('tr').show();
            $('[name="ross_theme_footer_options[cta_bg_gradient_from]"]').closest('tr').show();
            $('[name="ross_theme_footer_options[cta_bg_gradient_to]"]').closest('tr').show();
            $('[name="ross_theme_footer_options[cta_text_color]"]').closest('tr').show();
            $('[name="ross_theme_footer_options[cta_button_bg_color]"]').closest('tr').show();
            $('[name="ross_theme_footer_options[cta_button_text_color]"]').closest('tr').show();
            $('[name="ross_theme_footer_options[cta_alignment]"]').closest('tr').show();
            $('[name="ross_theme_footer_options[cta_layout_direction]"]').closest('tr').show();
            $('[name="ross_theme_footer_options[cta_layout_wrap]"]').closest('tr').show();
            $('[name="ross_theme_footer_options[cta_layout_justify]"]').closest('tr').show();
            $('[name="ross_theme_footer_options[cta_layout_align]"]').closest('tr').show();
            $('[name="ross_theme_footer_options[cta_gap]"]').closest('tr').show();
            $('[name="ross_theme_footer_options[cta_padding_top]"]').closest('tr').show();
            $('[name="ross_theme_footer_options[cta_padding_right]"]').closest('tr').show();
            $('[name="ross_theme_footer_options[cta_padding_bottom]"]').closest('tr').show();
            $('[name="ross_theme_footer_options[cta_padding_left]"]').closest('tr').show();
            $('[name="ross_theme_footer_options[cta_margin_top]"]').closest('tr').show();
            $('[name="ross_theme_footer_options[cta_margin_right]"]').closest('tr').show();
            $('[name="ross_theme_footer_options[cta_margin_bottom]"]').closest('tr').show();
            $('[name="ross_theme_footer_options[cta_margin_left]"]').closest('tr').show();
            $('[name="ross_theme_footer_options[cta_icon_color]"]').closest('tr').show();
            $('[name="ross_theme_footer_options[cta_animation]"]').closest('tr').show();
            $('[name="ross_theme_footer_options[cta_anim_delay]"]').closest('tr').show();
            $('[name="ross_theme_footer_options[enable_custom_cta]"]').closest('tr').show();
                // custom CTA HTML/CSS/JS are controlled by the 'enable_custom_cta' toggle
                toggleCustomCtaFields();
                // refresh preview when showing a CTA subtab
                setTimeout(function(){ updateCtaPreview(); }, 50);
        } else {
            $('[name="ross_theme_footer_options[cta_title]"]').closest('tr').hide();
            $('[name="ross_theme_footer_options[cta_text]"]').closest('tr').hide();
            $('[name="ross_theme_footer_options[cta_button_text]"]').closest('tr').hide();
            $('[name="ross_theme_footer_options[cta_button_url]"]').closest('tr').hide();
            $('[name="ross_theme_footer_options[cta_icon]"]').closest('tr').hide();
            $('[name="ross_theme_footer_options[cta_bg_color]"]').closest('tr').hide();
            $('[name="ross_theme_footer_options[cta_bg_image]"]').closest('tr').hide();
            $('[name="ross_theme_footer_options[cta_bg_gradient_from]"]').closest('tr').hide();
            $('[name="ross_theme_footer_options[cta_bg_gradient_to]"]').closest('tr').hide();
            $('[name="ross_theme_footer_options[cta_text_color]"]').closest('tr').hide();
            $('[name="ross_theme_footer_options[cta_button_bg_color]"]').closest('tr').hide();
            $('[name="ross_theme_footer_options[cta_button_text_color]"]').closest('tr').hide();
            $('[name="ross_theme_footer_options[cta_alignment]"]').closest('tr').hide();
            $('[name="ross_theme_footer_options[cta_layout_direction]"]').closest('tr').hide();
            $('[name="ross_theme_footer_options[cta_layout_wrap]"]').closest('tr').hide();
            $('[name="ross_theme_footer_options[cta_layout_justify]"]').closest('tr').hide();
            $('[name="ross_theme_footer_options[cta_layout_align]"]').closest('tr').hide();
            $('[name="ross_theme_footer_options[cta_gap]"]').closest('tr').hide();
            $('[name="ross_theme_footer_options[cta_padding_top]"]').closest('tr').hide();
            $('[name="ross_theme_footer_options[cta_padding_right]"]').closest('tr').hide();
            $('[name="ross_theme_footer_options[cta_padding_bottom]"]').closest('tr').hide();
            $('[name="ross_theme_footer_options[cta_padding_left]"]').closest('tr').hide();
            $('[name="ross_theme_footer_options[cta_margin_top]"]').closest('tr').hide();
            $('[name="ross_theme_footer_options[cta_margin_right]"]').closest('tr').hide();
            $('[name="ross_theme_footer_options[cta_margin_bottom]"]').closest('tr').hide();
            $('[name="ross_theme_footer_options[cta_margin_left]"]').closest('tr').hide();
            $('[name="ross_theme_footer_options[cta_icon_color]"]').closest('tr').hide();
            $('[name="ross_theme_footer_options[cta_animation]"]').closest('tr').hide();
            $('[name="ross_theme_footer_options[cta_anim_delay]"]').closest('tr').hide();
            $('[name="ross_theme_footer_options[enable_custom_cta]"]').closest('tr').hide();
                // custom CTA HTML/CSS/JS hide/show handled by toggleCustomCtaFields
                toggleCustomCtaFields();
        }
    }
    
    // Conditional logic for social icons
    function toggleSocialFields() {
        var enabled = $('input[name="ross_theme_footer_options[enable_social_icons]"]').is(':checked');
        if (enabled) {
            $('[name="ross_theme_footer_options[facebook_url]"]').closest('tr').show();
            $('[name="ross_theme_footer_options[linkedin_url]"]').closest('tr').show();
            $('[name="ross_theme_footer_options[instagram_url]"]').closest('tr').show();
            $('[name="ross_theme_footer_options[social_icon_color]"]').closest('tr').show();
        } else {
            $('[name="ross_theme_footer_options[facebook_url]"]').closest('tr').hide();
            $('[name="ross_theme_footer_options[linkedin_url]"]').closest('tr').hide();
            $('[name="ross_theme_footer_options[instagram_url]"]').closest('tr').hide();
            $('[name="ross_theme_footer_options[social_icon_color]"]').closest('tr').hide();
        }
    }
    
    // Conditional logic for copyright
    function toggleCopyrightFields() {
        var enabled = $('input[name="ross_theme_footer_options[enable_copyright]"]').is(':checked');
        if (enabled) {
            $('[name="ross_theme_footer_options[copyright_text]"]').closest('tr').show();
            $('[name="ross_theme_footer_options[copyright_bg_color]"]').closest('tr').show();
            $('[name="ross_theme_footer_options[copyright_text_color]"]').closest('tr').show();
            $('[name="ross_theme_footer_options[copyright_alignment]"]').closest('tr').show();
            $('[name="ross_theme_footer_options[copyright_font_size]"]').closest('tr').show();
            $('[name="ross_theme_footer_options[copyright_font_weight]"]').closest('tr').show();
            $('[name="ross_theme_footer_options[copyright_letter_spacing]"]').closest('tr').show();
        } else {
            $('[name="ross_theme_footer_options[copyright_text]"]').closest('tr').hide();
            $('[name="ross_theme_footer_options[copyright_bg_color]"]').closest('tr').hide();
            $('[name="ross_theme_footer_options[copyright_text_color]"]').closest('tr').hide();
            $('[name="ross_theme_footer_options[copyright_alignment]"]').closest('tr').hide();
            $('[name="ross_theme_footer_options[copyright_font_size]"]').closest('tr').hide();
            $('[name="ross_theme_footer_options[copyright_font_weight]"]').closest('tr').hide();
            $('[name="ross_theme_footer_options[copyright_letter_spacing]"]').closest('tr').hide();
        }
    }

    // Toggle when 'enable_custom_footer' is checked: hide default copyright fields
    function toggleCustomFooterFields() {
        var custom = $('input[name="ross_theme_footer_options[enable_custom_footer]"]').is(':checked');
        if (custom) {
            // Hide the default content and styling rows
            $('[name="ross_theme_footer_options[copyright_text]"]').closest('tr').hide();
            $('[name="ross_theme_footer_options[copyright_bg_color]"]').closest('tr').hide();
            $('[name="ross_theme_footer_options[copyright_text_color]"]').closest('tr').hide();
            $('[name="ross_theme_footer_options[copyright_alignment]"]').closest('tr').hide();
            $('[name="ross_theme_footer_options[copyright_font_size]"]').closest('tr').hide();
            $('[name="ross_theme_footer_options[copyright_font_weight]"]').closest('tr').hide();
            $('[name="ross_theme_footer_options[copyright_letter_spacing]"]').closest('tr').hide();
        } else {
            // Only show them if copyright feature is enabled
            toggleCopyrightFields();
        }
        if (custom) {
            $('[name="ross_theme_footer_options[custom_footer_html]"]').closest('tr').show();
            $('[name="ross_theme_footer_options[custom_footer_css]"]').closest('tr').show();
            $('[name="ross_theme_footer_options[custom_footer_js]"]').closest('tr').show();
        } else {
            $('[name="ross_theme_footer_options[custom_footer_html]"]').closest('tr').hide();
            $('[name="ross_theme_footer_options[custom_footer_css]"]').closest('tr').hide();
            $('[name="ross_theme_footer_options[custom_footer_js]"]').closest('tr').hide();
        }
        // Update the preview regardless
        updateCopyrightPreview();
    }

    function toggleCustomCtaFields() {
        var custom = $('input[name="ross_theme_footer_options[enable_custom_cta]"]').is(':checked');
        if (custom) {
            $('[name="ross_theme_footer_options[custom_cta_html]"]').closest('tr').show();
            $('[name="ross_theme_footer_options[custom_cta_css]"]').closest('tr').show();
            $('[name="ross_theme_footer_options[custom_cta_js]"]').closest('tr').show();
        } else {
            $('[name="ross_theme_footer_options[custom_cta_html]"]').closest('tr').hide();
            $('[name="ross_theme_footer_options[custom_cta_css]"]').closest('tr').hide();
            $('[name="ross_theme_footer_options[custom_cta_js]"]').closest('tr').hide();
        }
    }
    // Initialize conditional fields
    toggleWidgetsFields();
    toggleCTAFields();
    toggleSocialFields();
    toggleCopyrightFields();
    toggleCustomCtaFields();
    
    // Bind events
    $(document).on('change', 'input[name="ross_theme_footer_options[enable_widgets]"]', toggleWidgetsFields);
    $(document).on('change', 'input[name="ross_theme_footer_options[enable_footer_cta]"]', toggleCTAFields);
    $(document).on('change', 'input[name="ross_theme_footer_options[enable_custom_cta]"]', toggleCustomCtaFields);
    $(document).on('change', 'input[name="ross_theme_footer_options[enable_social_icons]"]', toggleSocialFields);
    $(document).on('change', 'input[name="ross_theme_footer_options[enable_copyright]"]', toggleCopyrightFields);

    // Background type controls (for Styling tab)
    function updateBgTypeFields() {
        var type = $('select[name="ross_theme_footer_options[styling_bg_type]"]').val();
        // hide all
        $('[name="ross_theme_footer_options[styling_bg_color]"]').closest('tr').hide();
        $('[name="ross_theme_footer_options[styling_bg_image]"]').closest('tr').hide();
        $('[name="ross_theme_footer_options[styling_bg_gradient_from]"]').closest('tr').hide();
        $('[name="ross_theme_footer_options[styling_bg_gradient_to]"]').closest('tr').hide();
        $('[name="ross_theme_footer_options[styling_bg_opacity]"]').closest('tr').hide();

        if (type === 'color') {
            $('[name="ross_theme_footer_options[styling_bg_color]"]').closest('tr').show();
            $('[name="ross_theme_footer_options[styling_bg_opacity]"]').closest('tr').show();
        }
        if (type === 'image') {
            $('[name="ross_theme_footer_options[styling_bg_image]"]').closest('tr').show();
            $('[name="ross_theme_footer_options[styling_bg_opacity]"]').closest('tr').show();
        }
        if (type === 'gradient') {
            $('[name="ross_theme_footer_options[styling_bg_gradient_from]"]').closest('tr').show();
            $('[name="ross_theme_footer_options[styling_bg_gradient_to]"]').closest('tr').show();
        }
    }

    function updateOverlayFields() {
        var enabled = $('input[name="ross_theme_footer_options[styling_overlay_enabled]"]').is(':checked');
        if (!enabled) {
            $('[name="ross_theme_footer_options[styling_overlay_type]"]').closest('tr').hide();
            $('[name="ross_theme_footer_options[styling_overlay_color]"]').closest('tr').hide();
            $('[name="ross_theme_footer_options[styling_overlay_image]"]').closest('tr').hide();
            $('[name="ross_theme_footer_options[styling_overlay_gradient_from]"]').closest('tr').hide();
            $('[name="ross_theme_footer_options[styling_overlay_gradient_to]"]').closest('tr').hide();
            $('[name="ross_theme_footer_options[styling_overlay_opacity]"]').closest('tr').hide();
            return;
        }

        var type = $('select[name="ross_theme_footer_options[styling_overlay_type]"]').val();
        $('[name="ross_theme_footer_options[styling_overlay_type]"]').closest('tr').show();
        $('[name="ross_theme_footer_options[styling_overlay_color]"]').closest('tr').hide();
        $('[name="ross_theme_footer_options[styling_overlay_image]"]').closest('tr').hide();
        $('[name="ross_theme_footer_options[styling_overlay_gradient_from]"]').closest('tr').hide();
        $('[name="ross_theme_footer_options[styling_overlay_gradient_to]"]').closest('tr').hide();
        $('[name="ross_theme_footer_options[styling_overlay_opacity]"]').closest('tr').show();

        if (type === 'color') {
            $('[name="ross_theme_footer_options[styling_overlay_color]"]').closest('tr').show();
        }
        if (type === 'image') {
            $('[name="ross_theme_footer_options[styling_overlay_image]"]').closest('tr').show();
        }
        if (type === 'gradient') {
            $('[name="ross_theme_footer_options[styling_overlay_gradient_from]"]').closest('tr').show();
            $('[name="ross_theme_footer_options[styling_overlay_gradient_to]"]').closest('tr').show();
        }
    }

    // Init bg & overlay fields once
    updateBgTypeFields();
    updateOverlayFields();
    function updateCtaOverlayFields() {
        var enabled = $('input[name="ross_theme_footer_options[cta_overlay_enabled]"]').is(':checked');
        if (!enabled) {
            $('[name="ross_theme_footer_options[cta_overlay_type]"]').closest('tr').hide();
            $('[name="ross_theme_footer_options[cta_overlay_color]"]').closest('tr').hide();
            $('[name="ross_theme_footer_options[cta_overlay_image]"]').closest('tr').hide();
            $('[name="ross_theme_footer_options[cta_overlay_gradient_from]"]').closest('tr').hide();
            $('[name="ross_theme_footer_options[cta_overlay_gradient_to]"]').closest('tr').hide();
            $('[name="ross_theme_footer_options[cta_overlay_opacity]"]').closest('tr').hide();
            return;
        }
        var type = $('select[name="ross_theme_footer_options[cta_overlay_type]"]').val();
        $('[name="ross_theme_footer_options[cta_overlay_type]"]').closest('tr').show();
        $('[name="ross_theme_footer_options[cta_overlay_color]"]').closest('tr').hide();
        $('[name="ross_theme_footer_options[cta_overlay_image]"]').closest('tr').hide();
        $('[name="ross_theme_footer_options[cta_overlay_gradient_from]"]').closest('tr').hide();
        $('[name="ross_theme_footer_options[cta_overlay_gradient_to]"]').closest('tr').hide();
        $('[name="ross_theme_footer_options[cta_overlay_opacity]"]').closest('tr').show();
        if (type === 'color') {
            $('[name="ross_theme_footer_options[cta_overlay_color]"]').closest('tr').show();
        }
        if (type === 'image') {
            $('[name="ross_theme_footer_options[cta_overlay_image]"]').closest('tr').show();
        }
        if (type === 'gradient') {
            $('[name="ross_theme_footer_options[cta_overlay_gradient_from]"]').closest('tr').show();
            $('[name="ross_theme_footer_options[cta_overlay_gradient_to]"]').closest('tr').show();
        }
    }
    updateCtaOverlayFields();
    $(document).on('change', 'input[name="ross_theme_footer_options[cta_overlay_enabled]"]', updateCtaOverlayFields);
    $(document).on('change', 'select[name="ross_theme_footer_options[cta_overlay_type]"]', updateCtaOverlayFields);
    // CTA-specific bg type controls
    function updateCtaBgTypeFields() {
        var type = $('select[name="ross_theme_footer_options[cta_bg_type]"]').val();
        $('[name="ross_theme_footer_options[cta_bg_color]"]').closest('tr').hide();
        $('[name="ross_theme_footer_options[cta_bg_image]"]').closest('tr').hide();
        $('[name="ross_theme_footer_options[cta_bg_gradient_from]"]').closest('tr').hide();
        $('[name="ross_theme_footer_options[cta_bg_gradient_to]"]').closest('tr').hide();
        if (type === 'color') {
            $('[name="ross_theme_footer_options[cta_bg_color]"]').closest('tr').show();
        }
        if (type === 'image') {
            $('[name="ross_theme_footer_options[cta_bg_image]"]').closest('tr').show();
        }
        if (type === 'gradient') {
            $('[name="ross_theme_footer_options[cta_bg_gradient_from]"]').closest('tr').show();
            $('[name="ross_theme_footer_options[cta_bg_gradient_to]"]').closest('tr').show();
        }
    }
    updateCtaBgTypeFields();

    // Live CTA Preview
    function hexToRgb(hex) {
        if (!hex) return '0,0,0';
        hex = hex.replace('#','');
        if (hex.length === 3) hex = hex.split('').map(function(c){ return c + c; }).join('');
        var bigint = parseInt(hex, 16);
        var r = (bigint >> 16) & 255;
        var g = (bigint >> 8) & 255;
        var b = bigint & 255;
        return r + ',' + g + ',' + b;
    }
    function updateCtaPreview() {
        var $preview = $('#ross-cta-preview');
        if (!$preview.length) return;
        var enabled = $('input[name="ross_theme_footer_options[enable_footer_cta]"]').is(':checked');
        if (!enabled) {
            $preview.html('<em>CTA Disabled</em>');
            return;
        }
        var title = $('input[name="ross_theme_footer_options[cta_title]"]').val() || '';
        var text = $('textarea[name="ross_theme_footer_options[cta_text]"]').val() || '';
        var btnText = $('input[name="ross_theme_footer_options[cta_button_text]"]').val() || '';
        var btnUrl = $('input[name="ross_theme_footer_options[cta_button_url]"]').val() || '#';
        var icon = $('input[name="ross_theme_footer_options[cta_icon]"]').val() || '';
        var alignment = $('select[name="ross_theme_footer_options[cta_alignment]"]').val() || 'center';
        var dir = $('select[name="ross_theme_footer_options[cta_layout_direction]"]').val() || 'row';
        var gap = $('input[name="ross_theme_footer_options[cta_gap]"]').val() || 12;
        var bgType = $('select[name="ross_theme_footer_options[cta_bg_type]"]').val() || 'color';
        var bgColor = $('input[name="ross_theme_footer_options[cta_bg_color]"]').val() || '';
        var gradFrom = $('input[name="ross_theme_footer_options[cta_bg_gradient_from]"]').val() || '';
        var gradTo = $('input[name="ross_theme_footer_options[cta_bg_gradient_to]"]').val() || '';
        var bgImg = $('input[name="ross_theme_footer_options[cta_bg_image]"]').val() || '';
        var textColor = $('input[name="ross_theme_footer_options[cta_text_color]"]').val() || '';
        var btnBg = $('input[name="ross_theme_footer_options[cta_button_bg_color]"]').val() || '';
        var btnTextColor = $('input[name="ross_theme_footer_options[cta_button_text_color]"]').val() || '';
        var paddingTop = $('input[name="ross_theme_footer_options[cta_padding_top]"]').val() || 24;
        var paddingRight = $('input[name="ross_theme_footer_options[cta_padding_right]"]').val() || 0;
        var paddingBottom = $('input[name="ross_theme_footer_options[cta_padding_bottom]"]').val() || 24;
        var paddingLeft = $('input[name="ross_theme_footer_options[cta_padding_left]"]').val() || 0;
        var marginTop = $('input[name="ross_theme_footer_options[cta_margin_top]"]').val() || 0;
        var marginRight = $('input[name="ross_theme_footer_options[cta_margin_right]"]').val() || 0;
        var marginBottom = $('input[name="ross_theme_footer_options[cta_margin_bottom]"]').val() || 0;
        var marginLeft = $('input[name="ross_theme_footer_options[cta_margin_left]"]').val() || 0;
        var anim = $('select[name="ross_theme_footer_options[cta_animation]"]').val() || 'none';
            var overlayEnabled = $('input[name="ross_theme_footer_options[cta_overlay_enabled]"]').is(':checked');
            var overlayType = $('select[name="ross_theme_footer_options[cta_overlay_type]"]').val() || 'color';
            var overlayColor = $('input[name="ross_theme_footer_options[cta_overlay_color]"]').val() || '';
            var overlayImg = $('input[name="ross_theme_footer_options[cta_overlay_image]"]').val() || '';
            var overlayGradFrom = $('input[name="ross_theme_footer_options[cta_overlay_gradient_from]"]').val() || '';
            var overlayGradTo = $('input[name="ross_theme_footer_options[cta_overlay_gradient_to]"]').val() || '';
            var overlayOpacity = $('input[name="ross_theme_footer_options[cta_overlay_opacity]"]').val() || 0.5;
        var animDelay = $('input[name="ross_theme_footer_options[cta_anim_delay]"]').val() || 150;
        var animDuration = $('input[name="ross_theme_footer_options[cta_anim_duration]"]').val() || 400;

        // Build style
        var style = '';
        style += 'display:flex; flex-direction:' + dir + '; gap:' + parseInt(gap) + 'px; align-items:center; justify-content:' + (alignment === 'left' ? 'flex-start' : (alignment === 'right' ? 'flex-end' : 'center')) + ';';
        style += ' padding:' + parseInt(paddingTop) + 'px ' + parseInt(paddingRight) + 'px ' + parseInt(paddingBottom) + 'px ' + parseInt(paddingLeft) + 'px;';
        style += ' margin:' + parseInt(marginTop) + 'px ' + parseInt(marginRight) + 'px ' + parseInt(marginBottom) + 'px ' + parseInt(marginLeft) + 'px;';
        if (bgType === 'color' && bgColor) style += ' background:' + bgColor + ';';
        if (bgType === 'gradient' && gradFrom && gradTo) style += ' background: linear-gradient(to right, ' + gradFrom + ', ' + gradTo + ');';
        if (bgType === 'image' && bgImg) style += ' background-image: url(' + bgImg + '); background-size: cover; background-position:center center;';
        // Overlay composition: treat overlay as topmost layer if enabled
        if (overlayEnabled) {
            if (overlayType === 'color' && overlayColor) {
                style += ' background: linear-gradient(rgba(' + hexToRgb(overlayColor) + ',' + overlayOpacity + '), rgba(' + hexToRgb(overlayColor) + ',' + overlayOpacity + ')) , ' + (bgType === 'image' ? 'url(' + bgImg + ')' : (bgType === 'gradient' ? 'linear-gradient(to right, ' + gradFrom + ', ' + gradTo + ')' : bgColor)) + '; background-size: cover; background-position:center center;';
            }
            if (overlayType === 'gradient' && overlayGradFrom && overlayGradTo) {
                style += ' background: linear-gradient(to bottom, ' + overlayGradFrom + ', ' + overlayGradTo + ') , ' + (bgType === 'image' ? 'url(' + bgImg + ')' : (bgType === 'gradient' ? 'linear-gradient(to right, ' + gradFrom + ', ' + gradTo + ')' : bgColor)) + '; background-size: cover; background-position:center center;';
            }
            if (overlayType === 'image' && overlayImg) {
                style += ' background: url(' + overlayImg + ') , ' + (bgType === 'image' ? 'url(' + bgImg + ')' : (bgType === 'gradient' ? 'linear-gradient(to right, ' + gradFrom + ', ' + gradTo + ')' : bgColor)) + '; background-size: cover; background-position:center center;';
            }
        }
        if (textColor) style += ' color:' + textColor + ';';
        if (anim && anim !== 'none') {
            var animClass = 'footer-cta--anim-' + anim + ' visible';
        } else {
            var animClass = '';
        }

        var iconHtml = icon ? '<span class="cta-icon ' + icon + '" style="margin-right:8px; display:inline-block;"></span>' : '';
        var btnStyle = 'text-decoration:none; padding:8px 12px; border-radius:4px;';
        if (btnBg) btnStyle += ' background:' + btnBg + ';';
        if (btnTextColor) btnStyle += ' color:' + btnTextColor + ';';

        var html = '<div class="footer-cta admin-preview ' + animClass + '" style="' + style + '">';
        html += '<div class="footer-cta-inner" style="display:flex; align-items:center; justify-content:inherit; gap:' + parseInt(gap) + 'px;">';
        if (iconHtml) html += iconHtml;
        if (title) html += '<div class="preview-cta-title" style="font-weight:700; margin-right:8px;">' + $('<div>').text(title).html() + '</div>';
        if (text) html += '<div class="preview-cta-text" style="opacity:0.9; margin-right:8px;">' + $('<div>').text(text).html() + '</div>';
        if (btnText) html += '<a class="btn" href="' + $('<div>').text(btnUrl).html() + '" style="' + btnStyle + '" >' + $('<div>').text(btnText).html() + '</a>';
        html += '</div></div>';

        $preview.html(html);
        // Apply CSS transition durations for animation
        $('.admin-preview').css('transition-duration', animDuration + 'ms');
        $('.admin-preview').css('transition-delay', animDelay + 'ms');
    }

    // Bind preview update listeners for CTA inputs
    $(document).on('input change', 'input[name^="ross_theme_footer_options[cta_"], textarea[name^="ross_theme_footer_options[cta_"]], select[name^="ross_theme_footer_options[cta_"]', function(){
        updateCtaPreview();
    });
    $(document).ready(function(){ updateCtaPreview(); });

    // ---------- Copyright Preview & toggles ----------
    function sanitizePreviewHtml(html) {
        if (!html) return '';
        return html.replace(/<script[\s\S]*?>[\s\S]*?<\/script>/gi, '');
    }
    function updateCopyrightPreview() {
        var $preview = $('#ross-copyright-preview');
        if (!$preview.length) return;
        var custom = $('input[name="ross_theme_footer_options[enable_custom_footer]"]').is(':checked');
        if (custom) {
            var customHtml = $('textarea[name="ross_theme_footer_options[custom_footer_html]"]').val() || '';
            $preview.html('<div class="custom-footer-preview">' + sanitizePreviewHtml(customHtml) + '</div>');
            return;
        }
        var enabled = $('input[name="ross_theme_footer_options[enable_copyright]"]').is(':checked');
        if (!enabled) {
            $preview.html('<em>Copyright Disabled</em>');
            return;
        }
        var text = $('textarea[name="ross_theme_footer_options[copyright_text]"]').val() || '';
        var year = new Date().getFullYear();
        var site = (typeof rossFooterAdmin !== 'undefined' && rossFooterAdmin.site_name) ? rossFooterAdmin.site_name : document.title;
        text = text.replace(/\{year\}/g, year).replace(/\{site_name\}/g, site);
        text = sanitizePreviewHtml(text);
        var bg = $('input[name="ross_theme_footer_options[copyright_bg_color]"]').val() || '';
        var color = $('input[name="ross_theme_footer_options[copyright_text_color]"]').val() || '';
        var align = $('select[name="ross_theme_footer_options[copyright_alignment]"]').val() || 'center';
        var fz = $('input[name="ross_theme_footer_options[copyright_font_size]"]').val() || 14;
        var fw = $('select[name="ross_theme_footer_options[copyright_font_weight]"]').val() || 'normal';
        var ls = $('input[name="ross_theme_footer_options[copyright_letter_spacing]"]').val() || 0;
        var fw_map = { 'light': 300, 'normal': 400, 'bold': 700 };
        var style = '';
        if (bg) style += 'background:' + bg + ';';
        if (color) style += 'color:' + color + ';';
        style += 'text-align:' + align + '; padding:8px;';
        style += 'font-size:' + parseInt(fz) + 'px;';
        style += 'font-weight:' + (fw_map[fw] || 400) + ';';
        style += 'letter-spacing:' + parseFloat(ls) + 'px;';
        var html = '<div class="copyright-live" style="' + style + '">' + text + '</div>';
        $preview.html(html);
    }
    // Bind copyright preview updates
    $(document).ready(function(){ updateCopyrightPreview(); });
    $(document).on('input change', 'input[name^="ross_theme_footer_options[copyright_"] , textarea[name^="ross_theme_footer_options[copyright_"] , select[name^="ross_theme_footer_options[copyright_"] , textarea[name="ross_theme_footer_options[custom_footer_html]"]', function(){
        updateCopyrightPreview();
    });
    $(document).on('change', 'select[name="ross_theme_footer_options[cta_bg_type]"]', updateCtaBgTypeFields);

    // CTA subtab: robust mapping of fields per section to ensure correct tab behavior
    (function(){
        var ctaFieldsBySection = {
            'ross_footer_cta_visibility': ['enable_footer_cta','cta_always_visible','cta_display_on'],
            'ross_footer_cta_content': ['cta_title','cta_text','cta_button_text','cta_button_url','cta_icon'],
            'ross_footer_cta_layout': ['cta_alignment','cta_layout_direction','cta_layout_wrap','cta_layout_justify','cta_layout_align','cta_gap'],
            'ross_footer_cta_styling': ['cta_bg_type','cta_bg_color','cta_bg_gradient_from','cta_bg_gradient_to','cta_bg_image','cta_text_color','cta_button_bg_color','cta_button_text_color','cta_icon_color','cta_overlay_enabled','cta_overlay_type','cta_overlay_color','cta_overlay_image','cta_overlay_gradient_from','cta_overlay_gradient_to','cta_overlay_opacity'],
            'ross_footer_cta_spacing': ['cta_padding_top','cta_padding_right','cta_padding_bottom','cta_padding_left','cta_margin_top','cta_margin_right','cta_margin_bottom','cta_margin_left'],
            'ross_footer_cta_animation': ['cta_animation','cta_anim_delay','cta_anim_duration'],
            'ross_footer_cta_advanced': ['enable_custom_cta','custom_cta_html','custom_cta_css','custom_cta_js']
        };

        function hideAllCtaRows() {
            $('input, select, textarea').filter(function(){
                var n = $(this).attr('name')||'';
                return n.indexOf('ross_theme_footer_options[cta_') === 0;
            }).closest('tr').hide();
        }

        function showFieldsForSection(section) {
            hideAllCtaRows();
            var fields = ctaFieldsBySection[section] || [];
            if (!fields.length) return;
            $('input, select, textarea').filter(function(){
                var n = $(this).attr('name') || '';
                for (var i=0;i<fields.length;i++){
                    var f = fields[i];
                    if (n === 'ross_theme_footer_options[' + f + ']' || n === 'ross_theme_footer_options[' + f + '][]' || n.indexOf('ross_theme_footer_options[' + f) === 0) return true;
                }
                return false;
            }).closest('tr').show();
            // Always show primary CTA enable checkbox row
            $('input[name="ross_theme_footer_options[enable_footer_cta]"]').closest('tr').show();
        }

        window.rossShowCtaFields = function(section) {
            try {
                showFieldsForSection(section);
                // re-apply conditional toggles so dependent fields stay consistent
                try { if (typeof toggleCTAFields === 'function') toggleCTAFields(); } catch(e) {}
                try { if (typeof toggleCustomCtaFields === 'function') toggleCustomCtaFields(); } catch(e) {}
                try { if (typeof updateCtaBgTypeFields === 'function') updateCtaBgTypeFields(); } catch(e) {}
                try { if (typeof updateCtaOverlayFields === 'function') updateCtaOverlayFields(); } catch(e) {}
            } catch(e) { console.warn('rossShowCtaFields error', e); }
        };

        // Bind click on subtab buttons (admin-pages.php toggles wrappers but we show/hide rows explicitly here)
        $(document).on('click', '.ross-cta-tab-btn', function(){
            var section = $(this).data('section');
            if (section) window.rossShowCtaFields(section);
        });

        // Default at load
        $(document).ready(function(){ window.rossShowCtaFields('ross_footer_cta_visibility'); });
    })();

    $(document).on('change', 'select[name="ross_theme_footer_options[styling_bg_type]"]', updateBgTypeFields);
    $(document).on('change', 'input[name="ross_theme_footer_options[styling_overlay_enabled]"]', updateOverlayFields);
    $(document).on('change', 'select[name="ross_theme_footer_options[styling_overlay_type]"]', updateOverlayFields);

    // Toggle gradient fields when checkbox toggled
    $(document).on('change', 'input[name="ross_theme_footer_options[styling_bg_gradient]"]', function(){
        var on = $(this).is(':checked');
        if (on) {
            $('[name="ross_theme_footer_options[styling_bg_gradient_from]"]').closest('tr').show();
            $('[name="ross_theme_footer_options[styling_bg_gradient_to]"]').closest('tr').show();
        } else {
            $('[name="ross_theme_footer_options[styling_bg_gradient_from]"]').closest('tr').hide();
            $('[name="ross_theme_footer_options[styling_bg_gradient_to]"]').closest('tr').hide();
        }
    });

    // Footer template preview (delegated handler + debug)
    $(document).on('click', '#ross-preview-template', function(e) {
        console.log('ross-preview-template clicked');
        e.preventDefault();
        var selected = $('input[name="ross_theme_footer_options[footer_template]"]:checked').val();
        if (!selected) return alert('Please select a template to preview.');
        var container = $('#ross-template-preview');
        var serverPreview = $('#ross-hidden-previews').find('#ross-preview-' + selected);
        if (serverPreview.length) {
            container.html(serverPreview.html());
        } else {
            // Attempt to fetch preview via AJAX if it's not embedded
            if (typeof rossFooterAdmin !== 'undefined' && rossFooterAdmin.ajax_url) {
                var payload = { action: 'ross_get_footer_template_preview', template: selected, nonce: rossFooterAdmin.nonce };
                console.log('Requesting server-side preview for', selected);
                $.post(rossFooterAdmin.ajax_url, payload, function(resp) {
                    if (resp && resp.success && resp.data && resp.data.html) {
                        container.html(resp.data.html);
                    } else {
                        console.warn('Server preview fetch failed', resp);
                        container.html('<em>No preview available</em>');
                    }
                }).fail(function(xhr) {
                    console.error('Preview AJAX call failed', xhr);
                    container.html('<em>Error fetching preview</em>');
                });
            } else {
                container.html('<em>No preview available</em>');
            }
        }
        container.show();
    });

    // Auto-preview when switching template selection for faster UX and enable Apply button
    function footerTemplateSelectionChanged() {
        $('#ross-preview-template').trigger('click');
        var selected = $('input[name="ross_theme_footer_options[footer_template]"]:checked').val();
        if (selected) {
            $('#ross-apply-template').prop('disabled', false);
        } else {
            $('#ross-apply-template').prop('disabled', true);
        }
    }
    $(document).on('change', 'input[name="ross_theme_footer_options[footer_template]"]', footerTemplateSelectionChanged);

    // Initialize Apply button enabled state
    $(document).ready(function(){
        var selected = $('input[name="ross_theme_footer_options[footer_template]"]:checked').val();
        $('#ross-apply-template').prop('disabled', !selected);
    });

    // Helper: reload backups list via AJAX and replace markup
    function reloadFooterBackups() {
        if (typeof rossFooterAdmin === 'undefined' || !rossFooterAdmin.ajax_url) return;
        var payload = { action: 'ross_list_footer_backups', nonce: rossFooterAdmin.nonce };
        $.post(rossFooterAdmin.ajax_url, payload, function(resp){
            if (resp && resp.success && resp.data && resp.data.html) {
                $('#ross-footer-backups').html('<h4>Recent Footer Backups</h4>' + resp.data.html);
            }
        });
    }

    // On load, show preview for the currently-selected template if any
    $(document).ready(function() {
        var selected = $('input[name="ross_theme_footer_options[footer_template]"]:checked').val();
        if (selected) {
            $('#ross-preview-template').trigger('click');
        }
    });

    // Apply template (delegated handler + debug)
    $(document).on('click', '#ross-apply-template', function(e) {
        console.log('ross-apply-template clicked');
        e.preventDefault();
        if (!confirm('This will replace your current footer widgets with the selected template sample content. Continue?')) return;

        var selected = $('input[name="ross_theme_footer_options[footer_template]"]:checked').val();
        if (!selected) return alert('Please select a template first.');

        var data = {
            action: 'ross_apply_footer_template',
            template: selected,
            nonce: (typeof rossFooterAdmin !== 'undefined' ? rossFooterAdmin.nonce : '')
        };

        $.post((typeof rossFooterAdmin !== 'undefined' ? rossFooterAdmin.ajax_url : ajaxurl), data, function(resp) {
            console.log('Apply template response', resp);
            if (!resp) return alert('Unexpected response from server');
            if (resp.success) {
                var msg = $('<div class="notice notice-success inline" id="ross-footer-apply-notice" style="margin-top:10px;"></div>');
                var widgetsLink = (typeof rossFooterAdmin !== 'undefined' && rossFooterAdmin.widgets_url) ? rossFooterAdmin.widgets_url : (window.location.origin + '/wp-admin/widgets.php');
                msg.append('<p>Template applied. <a href="' + widgetsLink + '">Edit widgets</a> — <button class="button-link" id="ross-undo-apply" data-backup-id="' + (resp.data && resp.data.backup_id ? resp.data.backup_id : '') + '">Undo</button></p>');
                $('#ross-template-preview').after(msg);

                // Bind undo click (delegated) - ensure single handler registration
                $(document).off('click', '#ross-undo-apply');
                $(document).on('click', '#ross-undo-apply', function(ev) {
                    ev.preventDefault();
                    if (!confirm('Restore the previous footer (undo)?')) return;

                    var backupId = $(this).data('backup-id');
                    var restoreData = { action: 'ross_restore_footer_backup', backup_id: backupId, nonce: (typeof rossFooterAdmin !== 'undefined' ? rossFooterAdmin.nonce : '') };
                    $.post((typeof rossFooterAdmin !== 'undefined' ? rossFooterAdmin.ajax_url : ajaxurl), restoreData, function(res) {
                        console.log('Undo restore response', res);
                        if (res && res.success) {
                            alert('Footer restored from backup.');
                            // Update backups list if server included HTML
                            if (res.data && res.data.backups_html) {
                                $('#ross-footer-backups').html('<h4>Recent Footer Backups</h4>' + res.data.backups_html);
                            } else {
                                try { reloadFooterBackups(); } catch(e) { console.warn('Unable to reload backups after undo', e); }
                            }
                        } else {
                            alert('Failed to restore: ' + (res && res.data ? res.data : 'unknown'));
                        }
                    });
                });

                // auto-dismiss the notification after a short delay
                setTimeout(function(){ $('#ross-footer-apply-notice').fadeOut(300, function(){ $(this).remove(); }); }, 30000);
                // Update backups HTML if provided by server to reduce extra calls
                if (resp.data && resp.data.backups_html) {
                    $('#ross-footer-backups').html('<h4>Recent Footer Backups</h4>' + resp.data.backups_html);
                } else {
                    try { reloadFooterBackups(); } catch(e) { console.warn('Unable to reload footer backups', e); }
                }
            } else {
                alert('Error applying template: ' + (resp.data || 'unknown'));
            }
        });
    });

    // Delegated handlers for backups list Restore/Delete
    $('#ross-footer-backups').on('click', '.ross-restore-backup', function(e) {
        e.preventDefault();
        var id = $(this).data('backup-id');
        if (!id) return alert('Missing backup id');
        if (!confirm('Restore this backup? This will replace current footer widgets.')) return;

        var data = { action: 'ross_restore_footer_backup', backup_id: id, nonce: (typeof rossFooterAdmin !== 'undefined' ? rossFooterAdmin.nonce : '') };
        $.post((typeof rossFooterAdmin !== 'undefined' ? rossFooterAdmin.ajax_url : ajaxurl), data, function(resp){
            console.log('Restore action response', resp);
            if (resp && resp.success) {
                alert('Backup restored successfully.');
                if (resp.data && resp.data.backups_html) {
                    $('#ross-footer-backups').html('<h4>Recent Footer Backups</h4>' + resp.data.backups_html);
                } else {
                    try { reloadFooterBackups(); } catch(e) { console.warn('Unable to reload backups after restore', e); }
                }
            } else {
                alert('Restore failed: ' + (resp && resp.data ? resp.data : 'unknown'));
            }
        });
    });

    $('#ross-footer-backups').on('click', '.ross-delete-backup', function(e) {
        e.preventDefault();
        var $row = $(this).closest('tr');
        var id = $(this).data('backup-id');
        if (!id) return alert('Missing backup id');
        if (!confirm('Delete this backup?')) return;

        var data = { action: 'ross_delete_footer_backup', backup_id: id, nonce: (typeof rossFooterAdmin !== 'undefined' ? rossFooterAdmin.nonce : '') };
        $.post((typeof rossFooterAdmin !== 'undefined' ? rossFooterAdmin.ajax_url : ajaxurl), data, function(resp){
            console.log('Delete action response', resp);
            if (resp && resp.success) {
                $row.fadeOut(200, function(){ $(this).remove(); });
                if (resp.data && resp.data.backups_html) {
                    $('#ross-footer-backups').html('<h4>Recent Footer Backups</h4>' + resp.data.backups_html);
                } else {
                    try { reloadFooterBackups(); } catch(e) { console.warn('Unable to reload backups after delete', e); }
                }
            } else {
                alert('Delete failed: ' + (resp && resp.data ? resp.data : 'unknown'));
            }
        });
    });

        // small helper to provide a link to the Widgets admin (can't access PHP here). Build link to Appearance -> Widgets.
        function admin_url_placeholder() {
            try {
                return window.location.origin + '/wp-admin/widgets.php';
            } catch(e) {
                return '/wp-admin/widgets.php';
            }
        }
});