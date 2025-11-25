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

    // Remove uploaded image (clear url, id and preview)
    $(document).on('click', '.ross-remove-upload', function(e) {
        e.preventDefault();
        var $btn = $(this);
        var target = $btn.data('target');
        var $input = null;
        if (target) {
            var normalized = target;
            if (normalized.indexOf('#') === 0) normalized = normalized.substring(1);
            if ($('#' + normalized).length) $input = $('#' + normalized);
            if (!$input || !$input.length) $input = $('input[name="ross_theme_footer_options[' + normalized + ']"]');
        }
        // else: try to find input in same container
        if ((!$input || !$input.length)) {
            $input = $btn.closest('tr, .form-field, .field, .form-table, div').find('input[type="text"], input[type="url"]').first();
        }
        if ($input && $input.length) {
            // find hidden id input
            var idInput = $('#' + $input.attr('id') + '-id');
            if (idInput && idInput.length) idInput.val('').trigger('change');
            // clear url
            $input.val('').trigger('change');
            // clear preview
            var previewEl = null;
            var iid = $input.attr('id');
            if (iid && $('#' + iid + '-preview').length) {
                previewEl = $('#' + iid + '-preview');
            } else if ($input.attr('name') && $input.attr('name').indexOf('[styling_overlay_image]') !== -1 && $('#ross-styling-overlay-image-preview').length) {
                previewEl = $('#ross-styling-overlay-image-preview');
            } else if ($('#ross-styling-bg-image-preview').length) {
                previewEl = $('#ross-styling-bg-image-preview');
            }
            if (previewEl && previewEl.length) previewEl.html('');
        }
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
            $('[name="ross_theme_footer_options[cta_button_text]"]').closest('tr').show();
            $('[name="ross_theme_footer_options[cta_bg_color]"]').closest('tr').show();
        } else {
            $('[name="ross_theme_footer_options[cta_title]"]').closest('tr').hide();
            $('[name="ross_theme_footer_options[cta_button_text]"]').closest('tr').hide();
            $('[name="ross_theme_footer_options[cta_bg_color]"]').closest('tr').hide();
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
        } else {
            $('[name="ross_theme_footer_options[copyright_text]"]').closest('tr').hide();
            $('[name="ross_theme_footer_options[copyright_bg_color]"]').closest('tr').hide();
            $('[name="ross_theme_footer_options[copyright_text_color]"]').closest('tr').hide();
            $('[name="ross_theme_footer_options[copyright_alignment]"]').closest('tr').hide();
        }
    }
    // Initialize conditional fields
    toggleWidgetsFields();
    toggleCTAFields();
    toggleSocialFields();
    toggleCopyrightFields();

    // Initialize bg & overlay fields and bind events
    updateBgTypeFields();
    updateOverlayFields();
    $(document).on('change', 'select[name="ross_theme_footer_options[styling_bg_type]"]', updateBgTypeFields);
    $(document).on('change', 'input[name="ross_theme_footer_options[styling_overlay_enabled]"]', updateOverlayFields);
    $(document).on('change', 'select[name="ross_theme_footer_options[styling_overlay_type]"]', updateOverlayFields);
    
    // Bind events
    $(document).on('change', 'input[name="ross_theme_footer_options[enable_widgets]"]', toggleWidgetsFields);
    $(document).on('change', 'input[name="ross_theme_footer_options[enable_footer_cta]"]', toggleCTAFields);
    $(document).on('change', 'input[name="ross_theme_footer_options[enable_social_icons]"]', toggleSocialFields);
    $(document).on('change', 'input[name="ross_theme_footer_options[enable_copyright]"]', toggleCopyrightFields);

    // Background type and overlay controls
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
        // if not image, clear image url / id and preview so we don't leave stale values
        if (type !== 'image') {
            var $bgInput = $('input[name="ross_theme_footer_options[styling_bg_image]"]');
            if ($bgInput.length) {
                var bgId = $('#' + $bgInput.attr('id') + '-id');
                if (bgId && bgId.length) bgId.val('').trigger('change');
                $bgInput.val('').trigger('change');
            }
            var $preview = $('#ross-styling-bg-image-preview');
            if ($preview.length) $preview.html('');
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
        // if overlay disabled or not image type, clear overlay image fields and preview
        if (!enabled || type !== 'image') {
            var $overlayInput = $('input[name="ross_theme_footer_options[styling_overlay_image]"]');
            if ($overlayInput.length) {
                var ovId = $('#' + $overlayInput.attr('id') + '-id');
                if (ovId && ovId.length) ovId.val('').trigger('change');
                $overlayInput.val('').trigger('change');
            }
            var $overlayPreview = $('#ross-styling-overlay-image-preview');
            if ($overlayPreview.length) $overlayPreview.html('');
        }
    }

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
            container.html('<em>No preview available</em>');
        }
        container.show();
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
            if (!resp) return alert('Unexpected response from server');
            if (resp.success) {
                var msg = $('<div class="notice notice-success inline" id="ross-footer-apply-notice" style="margin-top:10px;"></div>');
                var widgetsLink = (typeof rossFooterAdmin !== 'undefined' && rossFooterAdmin.widgets_url) ? rossFooterAdmin.widgets_url : (window.location.origin + '/wp-admin/widgets.php');
                msg.append('<p>Template applied. <a href="' + widgetsLink + '">Edit widgets</a> — <button class="button-link" id="ross-undo-apply">Undo</button></p>');
                $('#ross-template-preview').after(msg);

                // Bind undo click (delegated)
                $(document).on('click', '#ross-undo-apply', function(ev) {
                    ev.preventDefault();
                    if (!confirm('Restore the previous footer (undo)?')) return;

                    var restoreData = { action: 'ross_restore_footer_backup', nonce: (typeof rossFooterAdmin !== 'undefined' ? rossFooterAdmin.nonce : '') };
                    $.post((typeof rossFooterAdmin !== 'undefined' ? rossFooterAdmin.ajax_url : ajaxurl), restoreData, function(res) {
                        if (res && res.success) {
                            alert('Footer restored from backup. Refreshing page.');
                            location.reload();
                        } else {
                            alert('Failed to restore: ' + (res && res.data ? res.data : 'unknown'));
                        }
                    });
                });

                // auto-dismiss and redirect to frontend after a short delay so user can see success message
                setTimeout(function(){ $('#ross-footer-apply-notice').fadeOut(300, function(){ $(this).remove(); }); }, 30000);

                // Redirect to the site front-end after 1.5s so user can immediately view changes
                try {
                    var siteUrl = (typeof rossFooterAdmin !== 'undefined' && rossFooterAdmin.site_url) ? rossFooterAdmin.site_url : window.location.origin + '/';
                    setTimeout(function(){ window.location.href = siteUrl; }, 1500);
                } catch(e) {
                    // ignore redirection errors
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
            if (resp && resp.success) {
                alert('Backup restored. Reloading.');
                location.reload();
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
            if (resp && resp.success) {
                $row.fadeOut(200, function(){ $(this).remove(); });
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