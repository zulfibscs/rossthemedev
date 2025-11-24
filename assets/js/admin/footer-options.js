jQuery(document).ready(function($) {
    // Initialize color pickers
    $('.color-picker').wpColorPicker();

    // Media uploader for background image field (reusable frame per button)
    $(document).on('click', '.ross-upload-button', function(e) {
        e.preventDefault();
        var $btn = $(this);
        var target = $btn.data('target');
        var $input = null;

        // Primary: selector from data-target
        if (target && $(target).length) {
            $input = $(target);
        }

        // Fallback 1: an input with the expected option name
        if (!$input || !$input.length) {
            $input = $('input[name="ross_theme_footer_options[styling_bg_image]"]');
        }

        // Fallback 2: the first text input in the same table row / container
        if (!$input || !$input.length) {
            $input = $btn.closest('tr').find('input[type="text"], input[type="url"]').first();
        }

        // If still not found, stop gracefully and inform the developer/admin
        if (!$input || !$input.length) {
            console.error('Target input field not found: ' + (target || 'unknown'));
            alert('Target input field not found for the uploader. Please refresh the page and try again.');
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
                        var $preview = $('#ross-styling-bg-image-preview');
                        $preview.html('<img src="' + url + '" style="max-height:40px;vertical-align:middle;border:1px solid #ddd;padding:2px;" />');
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
    
    // Bind events
    $(document).on('change', 'input[name="ross_theme_footer_options[enable_widgets]"]', toggleWidgetsFields);
    $(document).on('change', 'input[name="ross_theme_footer_options[enable_footer_cta]"]', toggleCTAFields);
    $(document).on('change', 'input[name="ross_theme_footer_options[enable_social_icons]"]', toggleSocialFields);
    $(document).on('change', 'input[name="ross_theme_footer_options[enable_copyright]"]', toggleCopyrightFields);

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