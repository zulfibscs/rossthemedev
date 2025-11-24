<?php
/**
 * Improved Top Bar Admin Interface
 * Modern, clean UI with enhanced functionality
 */

// Don't load directly
if (!defined('ABSPATH')) {
    exit;
}

function ross_theme_render_topbar_admin_improved() {
    $options = get_option('ross_theme_header_options', array());
    $get = function($key, $default = '') use ($options) {
        return isset($options[$key]) ? $options[$key] : $default;
    };
    ?>
    
    <div class="ross-topbar-admin-improved">
        <div class="ross-admin-header">
            <h2>🎯 Top Bar Settings</h2>
            <p>Configure your website's top bar with modern styling and enhanced functionality.</p>
        </div>
        
        <div class="ross-topbar-grid">
            <!-- Left Column: General Settings & Social Icons -->
            <div class="ross-topbar-left">
                <div class="ross-admin-section">
                    <h3>⚙️ General Settings</h3>
                    
                    <div class="ross-field-group">
                        <label class="ross-switch-label">
                            <input type="checkbox" name="ross_theme_header_options[enable_topbar]" value="1" <?php checked(1, $get('enable_topbar', 0)); ?> />
                            <span class="ross-switch"></span>
                            <span class="ross-label-text">Enable Top Bar</span>
                        </label>
                        <p class="ross-field-description">Toggle the top bar visibility on the front-end</p>
                    </div>
                    
                    <div class="ross-field-group">
                        <label class="ross-switch-label">
                            <input type="checkbox" name="ross_theme_header_options[enable_topbar_left]" value="1" <?php checked(1, $get('enable_topbar_left', 1)); ?> />
                            <span class="ross-switch"></span>
                            <span class="ross-label-text">Enable Left Section</span>
                        </label>
                        <p class="ross-field-description">Show or hide the left area (phone/text/custom HTML)</p>
                    </div>
                    
                    <div class="ross-field-group">
                        <label class="ross-field-label">Left Content</label>
                        <?php 
                        $left_val = $get('topbar_left_content', '');
                        $editor_id = 'ross_left_content_editor';
                        wp_editor($left_val, $editor_id, array('textarea_name' => 'ross_theme_header_options[topbar_left_content]', 'textarea_rows' => 3, 'teeny' => true, 'media_buttons' => false));
                        ?>
                    </div>
                    
                    <div class="ross-field-group">
                        <label class="ross-field-label">Phone Number</label>
                        <input type="text" name="ross_theme_header_options[phone_number]" value="<?php echo esc_attr($get('phone_number')); ?>" class="ross-input" placeholder="e.g., +44 20 7123 4567" />
                    </div>
                </div>
                
                <div class="ross-admin-section">
                    <div class="ross-section-header">
                        <h3>🔗 Social Icons</h3>
                        <button type="button" class="ross-button ross-button-small" id="ross-add-social-icon">+ Add Custom Icon</button>
                    </div>
                    
                    <div class="ross-field-group">
                        <label class="ross-switch-label">
                            <input type="checkbox" name="ross_theme_header_options[enable_social]" value="1" <?php checked(1, $get('enable_social', 0)); ?> />
                            <span class="ross-switch"></span>
                            <span class="ross-label-text">Enable Social Icons</span>
                        </label>
                    </div>
                    
                    <div class="ross-social-icons-container">
                        <div class="ross-social-items" id="ross-social-items">
                            <?php
                            $social_platforms = array(
                                'facebook' => array('icon' => 'fab fa-facebook', 'label' => 'Facebook', 'color' => '#1877f2'),
                                'twitter' => array('icon' => 'fab fa-twitter', 'label' => 'Twitter', 'color' => '#1da1f2'),
                                'linkedin' => array('icon' => 'fab fa-linkedin', 'label' => 'LinkedIn', 'color' => '#0077b5'),
                                'instagram' => array('icon' => 'fab fa-instagram', 'label' => 'Instagram', 'color' => '#e4405f'),
                                'youtube' => array('icon' => 'fab fa-youtube', 'label' => 'YouTube', 'color' => '#ff0000')
                            );
                            
                            foreach ($social_platforms as $platform => $data) {
                                $url = $get('social_' . $platform, '');
                                $enabled = $get('social_' . $platform . '_enabled', !empty($url));
                                $icon = $get('social_' . $platform . '_icon', $data['icon']);
                            ?>
                            <div class="ross-social-item" data-platform="<?php echo esc_attr($platform); ?>">
                                <div class="ross-social-drag-handle">⋮⋮</div>
                                <div class="ross-social-toggle">
                                    <label class="ross-switch-label">
                                        <input type="checkbox" name="ross_theme_header_options[social_<?php echo $platform; ?>_enabled]" value="1" <?php checked(1, $enabled); ?> />
                                        <span class="ross-switch"></span>
                                    </label>
                                </div>
                                <div class="ross-social-icon-preview">
                                    <i class="<?php echo esc_attr($icon); ?>" data-default-color="<?php echo esc_attr($data['color']); ?>"></i>
                                </div>
                                <div class="ross-social-fields">
                                    <div class="ross-social-platform"><?php echo esc_html($data['label']); ?></div>
                                    <input type="url" name="ross_theme_header_options[social_<?php echo $platform; ?>]" value="<?php echo esc_attr($url); ?>" class="ross-input ross-input-small" placeholder="URL" />
                                    <input type="text" name="ross_theme_header_options[social_<?php echo $platform; ?>_icon]" value="<?php echo esc_attr($icon); ?>" class="ross-input ross-input-small" placeholder="Icon class" />
                                    <button type="button" class="ross-button ross-button-icon ross-upload-custom-icon" data-platform="<?php echo $platform; ?>" title="Upload Custom Icon">📁</button>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                        
                        <div class="ross-social-options">
                            <div class="ross-field-group">
                                <label class="ross-field-label">Icon Size</label>
                                <select name="ross_theme_header_options[social_icon_size]" class="ross-select">
                                    <option value="small" <?php selected($get('social_icon_size', 'medium'), 'small'); ?>>Small</option>
                                    <option value="medium" <?php selected($get('social_icon_size', 'medium'), 'medium'); ?>>Medium</option>
                                    <option value="large" <?php selected($get('social_icon_size', 'medium'), 'large'); ?>>Large</option>
                                </select>
                            </div>
                            <div class="ross-field-group">
                                <label class="ross-field-label">Icon Shape</label>
                                <select name="ross_theme_header_options[social_icon_shape]" class="ross-select">
                                    <option value="circle" <?php selected($get('social_icon_shape', 'circle'), 'circle'); ?>>Circle</option>
                                    <option value="square" <?php selected($get('social_icon_shape', 'circle'), 'square'); ?>>Square</option>
                                    <option value="rounded" <?php selected($get('social_icon_shape', 'circle'), 'rounded'); ?>>Rounded</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right Column: Colors & Style (announcement moved to its own tab) -->
            <div class="ross-topbar-right">
                <div class="ross-admin-section">
                    <h3>🎨 Colors</h3>
                    
                    <div class="ross-colors-grid">
                        <div class="ross-color-item">
                            <label class="ross-color-label">
                                <span class="ross-color-swatch" id="ross-swatch-bg" style="background-color: <?php echo esc_attr($get('topbar_bg_color', '#001946')); ?>;"></span>
                                <span class="ross-color-name">Top Bar Background</span>
                            </label>
                            <input type="text" class="ross-color-input" name="ross_theme_header_options[topbar_bg_color]" value="<?php echo esc_attr($get('topbar_bg_color', '#001946')); ?>" data-default-color="#001946" />
                        </div>
                        
                        <div class="ross-color-item">
                            <label class="ross-color-label">
                                <span class="ross-color-swatch" id="ross-swatch-text" style="background-color: <?php echo esc_attr($get('topbar_text_color', '#ffffff')); ?>;"></span>
                                <span class="ross-color-name">Text Color</span>
                            </label>
                            <input type="text" class="ross-color-input" name="ross_theme_header_options[topbar_text_color]" value="<?php echo esc_attr($get('topbar_text_color', '#ffffff')); ?>" data-default-color="#ffffff" />
                        </div>
                        
                        <div class="ross-color-item">
                            <label class="ross-color-label">
                                <span class="ross-color-swatch" id="ross-swatch-icon" style="background-color: <?php echo esc_attr($get('topbar_icon_color', '#E5C902')); ?>;"></span>
                                <span class="ross-color-name">Social Icon Color</span>
                            </label>
                            <input type="text" class="ross-color-input" name="ross_theme_header_options[topbar_icon_color]" value="<?php echo esc_attr($get('topbar_icon_color', '#E5C902')); ?>" data-default-color="#E5C902" />
                        </div>
                        
                        <div class="ross-color-item">
                            <label class="ross-color-label">
                                <span class="ross-color-swatch" id="ross-swatch-icon-hover" style="background-color: <?php echo esc_attr($get('topbar_icon_hover_color', '#ffffff')); ?>;"></span>
                                <span class="ross-color-name">Icon Hover Color</span>
                            </label>
                            <input type="text" class="ross-color-input" name="ross_theme_header_options[topbar_icon_hover_color]" value="<?php echo esc_attr($get('topbar_icon_hover_color', '#ffffff')); ?>" data-default-color="#ffffff" />
                        </div>
                        
                        <div class="ross-color-item">
                            <label class="ross-color-label">
                                <span class="ross-color-swatch" id="ross-swatch-border" style="background-color: <?php echo esc_attr($get('topbar_border_color', '#E5C902')); ?>;"></span>
                                <span class="ross-color-name">Border Bottom Color</span>
                            </label>
                            <input type="text" class="ross-color-input" name="ross_theme_header_options[topbar_border_color]" value="<?php echo esc_attr($get('topbar_border_color', '#E5C902')); ?>" data-default-color="#E5C902" />
                        </div>
                        
                        <div class="ross-color-item">
                            <label class="ross-color-label">
                                <span class="ross-color-swatch" id="ross-swatch-grad1" style="background-color: <?php echo esc_attr($get('topbar_gradient_color1', '#001946')); ?>;"></span>
                                <span class="ross-color-name">Gradient Color 1</span>
                            </label>
                            <input type="text" class="ross-color-input" name="ross_theme_header_options[topbar_gradient_color1]" value="<?php echo esc_attr($get('topbar_gradient_color1', '#001946')); ?>" data-default-color="#001946" />
                        </div>
                        
                        <div class="ross-color-item">
                            <label class="ross-color-label">
                                <span class="ross-color-swatch" id="ross-swatch-grad2" style="background-color: <?php echo esc_attr($get('topbar_gradient_color2', '#003d7a')); ?>;"></span>
                                <span class="ross-color-name">Gradient Color 2</span>
                            </label>
                            <input type="text" class="ross-color-input" name="ross_theme_header_options[topbar_gradient_color2]" value="<?php echo esc_attr($get('topbar_gradient_color2', '#003d7a')); ?>" data-default-color="#003d7a" />
                        </div>
                    </div>
                </div>
                
                <div class="ross-admin-section">
                    <h3>⚡ Style Options</h3>
                    
                    <div class="ross-field-group">
                        <label class="ross-switch-label">
                            <input type="checkbox" name="ross_theme_header_options[topbar_gradient_enable]" value="1" <?php checked(1, $get('topbar_gradient_enable', 0)); ?> />
                            <span class="ross-switch"></span>
                            <span class="ross-label-text">Enable Gradient Background</span>
                        </label>
                    </div>
                    
                    <div class="ross-field-group">
                        <label class="ross-field-label">Border Bottom Width (px)</label>
                        <input type="number" min="0" max="5" name="ross_theme_header_options[topbar_border_width]" value="<?php echo esc_attr($get('topbar_border_width', 0)); ?>" class="ross-input ross-input-small" /> px
                    </div>
                    
                    <div class="ross-field-group">
                        <label class="ross-switch-label">
                            <input type="checkbox" name="ross_theme_header_options[topbar_shadow_enable]" value="1" <?php checked(1, $get('topbar_shadow_enable', 0)); ?> />
                            <span class="ross-switch"></span>
                            <span class="ross-label-text">Enable Drop Shadow</span>
                        </label>
                    </div>
                </div>
                
                <!-- Live Preview -->
                <div class="ross-admin-section">
                    <h3>👁️ Live Preview</h3>
                    <div id="ross-topbar-preview">
                        <div id="ross-topbar-preview-bar">
                                    <div id="ross-preview-left">Left Content</div>
                                    <div id="ross-preview-center">Announcement text appears here</div>
                                    <div id="ross-preview-right" class="ross-preview-social"></div>
                                </div>
                        <div class="ross-preview-note">Preview updates live as you change settings</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
    (function(){
        // Live preview for social icons in Top Bar admin — robust selectors and delegation
        function buildPreviewIcons() {
            var container = document.querySelector('#ross-preview-right');
            if (!container) return;
            container.innerHTML = '';

            var adminRoot = document.querySelector('.ross-topbar-admin-improved') || document;

            var platforms = ['facebook','twitter','linkedin','instagram','youtube'];
            platforms.forEach(function(p){
                var urlInput = adminRoot.querySelector('input[name="ross_theme_header_options[social_' + p + ']"]');
                var iconInput = adminRoot.querySelector('input[name="ross_theme_header_options[social_' + p + '_icon]"]');
                var enabledInput = adminRoot.querySelector('input[name="ross_theme_header_options[social_' + p + '_enabled]"]');
                var url = urlInput ? urlInput.value.trim() : '';
                var icon = iconInput ? iconInput.value.trim() : '';
                var enabled = enabledInput ? enabledInput.checked : !!url;

                if (!enabled || !url) return;

                var a = document.createElement('a');
                a.href = url;
                a.target = '_blank';
                a.className = 'preview-social-link';

                if (icon) {
                    if (icon.indexOf('<') === 0) {
                        a.innerHTML = icon;
                    } else {
                        var i = document.createElement('i');
                        i.className = icon;
                        a.appendChild(i);
                    }
                } else {
                    a.textContent = p.charAt(0).toUpperCase();
                }

                container.appendChild(a);
            });

            // Custom icons: find url/icon pairs under topbar_custom_icon_links (supports numeric indexes)
            var customUrlFields = adminRoot.querySelectorAll('input[name^="ross_theme_header_options[topbar_custom_icon_links]"][name$="[url]"]');
            customUrlFields.forEach(function(urlField){
                // Determine corresponding icon field by replacing [url] with [icon] in the name
                var name = urlField.getAttribute('name');
                var iconName = name.replace('[url]','[icon]');
                var iconField = adminRoot.querySelector('input[name="' + iconName + '"]');
                var u = urlField.value.trim();
                var ic = iconField ? iconField.value.trim() : '';
                if (u && ic) {
                    var a = document.createElement('a');
                    a.href = u; a.target = '_blank'; a.className = 'preview-social-link';
                    if (ic.indexOf('<') === 0) {
                        a.innerHTML = ic;
                    } else {
                        var i = document.createElement('i'); i.className = ic; a.appendChild(i);
                    }
                    container.appendChild(a);
                }
            });

            // Apply size/shape classes from the options selects
            var size = adminRoot.querySelector('select[name="ross_theme_header_options[social_icon_size]"]');
            var shape = adminRoot.querySelector('select[name="ross_theme_header_options[social_icon_shape]"]');
            var sizeClass = size ? 'social-link--' + (size.value || 'medium') : 'social-link--medium';
            var shapeClass = shape ? 'social-link--' + (shape.value || 'circle') : 'social-link--circle';
            var links = container.querySelectorAll('a.preview-social-link');
            links.forEach(function(a){
                a.className = 'preview-social-link ' + sizeClass + ' ' + shapeClass;
            });
        }

        // Attach a delegated input/change listener to the admin container for live updates
        document.addEventListener('DOMContentLoaded', function(){
            buildPreviewIcons();
            var adminRoot = document.querySelector('.ross-topbar-admin-improved') || document;
            adminRoot.addEventListener('input', function(e){
                if (!e.target || !e.target.name) return;
                var n = e.target.name;
                if (n.indexOf('social_') !== -1 || n.indexOf('topbar_custom_icon_links') !== -1 || n.indexOf('social_icon_size') !== -1 || n.indexOf('social_icon_shape') !== -1) {
                    buildPreviewIcons();
                }
            });
            adminRoot.addEventListener('change', function(e){
                var n = e.target && e.target.name ? e.target.name : '';
                if (n.indexOf('social_') !== -1 || n.indexOf('topbar_custom_icon_links') !== -1 || n.indexOf('social_icon_size') !== -1 || n.indexOf('social_icon_shape') !== -1) {
                    buildPreviewIcons();
                }
            });
        });
    })();
    </script>

            <?php
}
