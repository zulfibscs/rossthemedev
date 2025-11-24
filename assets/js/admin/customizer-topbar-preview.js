/**
 * Customizer Preview - Top Bar Settings
 * Handles live preview updates in the WordPress Customizer
 */

(function($) {
    'use strict';

    // Enable Top Bar
    wp.customize('ross_topbar_enable', function(value) {
        value.bind(function(to) {
            if (to) {
                $('.site-topbar').fadeIn();
            } else {
                $('.site-topbar').fadeOut();
            }
        });
    });

    // Left Section Content
    wp.customize('ross_topbar_left_content', function(value) {
        value.bind(function(to) {
            $('.topbar-left').html(to);
        });
    });

    // Show Left Section
    wp.customize('ross_topbar_show_left', function(value) {
        value.bind(function(to) {
            if (to) {
                $('.topbar-left').fadeIn();
            } else {
                $('.topbar-left').fadeOut();
            }
        });
    });

    // Phone Number
    wp.customize('ross_topbar_phone', function(value) {
        value.bind(function(to) {
            if (to) {
                $('.topbar-phone').html(to).attr('href', 'tel:' + to).fadeIn();
            } else {
                $('.topbar-phone').fadeOut();
            }
        });
    });

    // Email Address
    wp.customize('ross_topbar_email', function(value) {
        value.bind(function(to) {
            if (to) {
                $('.topbar-email').html(to).attr('href', 'mailto:' + to).fadeIn();
            } else {
                $('.topbar-email').fadeOut();
            }
        });
    });

    // Announcement Text
    wp.customize('ross_topbar_announcement', function(value) {
        value.bind(function(to) {
            $('.site-announcement-strip .announcement-text').html(to);
        });
    });

    // Marquee Animation Toggle
    wp.customize('ross_topbar_marquee_enable', function(value) {
        value.bind(function(to) {
            if (to) {
                $('.site-announcement-strip .announcement-text').addClass('announce-marquee');
            } else {
                $('.site-announcement-strip .announcement-text').removeClass('announce-marquee');
            }
        });
    });

    // Background Color
    wp.customize('ross_topbar_bg_color', function(value) {
        value.bind(function(to) {
            $('.site-topbar').css('background-color', to);
        });
    });

    // Text Color
    wp.customize('ross_topbar_text_color', function(value) {
        value.bind(function(to) {
            $('.site-topbar, .site-topbar a, .topbar-left, .topbar-center, .topbar-right').css('color', to);
        });
    });

    // Icon Color
    wp.customize('ross_topbar_icon_color', function(value) {
        value.bind(function(to) {
            $('.site-topbar .social-link, .site-topbar .topbar-phone').css('color', to);
        });
    });

    // Font Size
    wp.customize('ross_topbar_font_size', function(value) {
        value.bind(function(to) {
            $('.site-topbar').css('font-size', to + 'px');
        });
    });

    // Alignment
    wp.customize('ross_topbar_alignment', function(value) {
        value.bind(function(to) {
            $('.site-topbar').css('text-align', to);
        });
    });

    // Social Icons Enable
    wp.customize('ross_topbar_social_enable', function(value) {
        value.bind(function(to) {
            if (to) {
                $('.topbar-social').fadeIn();
            } else {
                $('.topbar-social').fadeOut();
            }
        });
    });

    // Social Platform URLs and toggles
    var socialPlatforms = ['facebook', 'twitter', 'linkedin', 'instagram', 'youtube'];
    
    socialPlatforms.forEach(function(platform) {
        // URL
        wp.customize('ross_topbar_social_' + platform + '_url', function(value) {
            value.bind(function(to) {
                $('[data-social="' + platform + '"]').attr('href', to);
            });
        });

        // Enabled toggle
        wp.customize('ross_topbar_social_' + platform + '_enabled', function(value) {
            value.bind(function(to) {
                if (to) {
                    $('[data-social="' + platform + '"]').fadeIn();
                } else {
                    $('[data-social="' + platform + '"]').fadeOut();
                }
            });
        });

        // Icon class
        wp.customize('ross_topbar_social_' + platform + '_icon', function(value) {
            value.bind(function(to) {
                var $icon = $('[data-social="' + platform + '"] i');
                if ($icon.length) {
                    $icon.attr('class', to);
                }
            });
        });
    });

    // Shadow Toggle
    wp.customize('ross_topbar_shadow_enable', function(value) {
        value.bind(function(to) {
            if (to) {
                $('.site-topbar').css('box-shadow', '0 2px 8px rgba(0, 0, 0, 0.15)');
            } else {
                $('.site-topbar').css('box-shadow', 'none');
            }
        });
    });

    // Gradient Enable
    wp.customize('ross_topbar_gradient_enable', function(value) {
        value.bind(function(to) {
            updateGradient();
        });
    });

    // Gradient Color 1
    wp.customize('ross_topbar_gradient_color1', function(value) {
        value.bind(function(to) {
            updateGradient();
        });
    });

    // Gradient Color 2
    wp.customize('ross_topbar_gradient_color2', function(value) {
        value.bind(function(to) {
            updateGradient();
        });
    });

    function updateGradient() {
        var enable = wp.customize('ross_topbar_gradient_enable').get();
        var color1 = wp.customize('ross_topbar_gradient_color1').get();
        var color2 = wp.customize('ross_topbar_gradient_color2').get();

        if (enable) {
            $('.site-topbar').css('background', 'linear-gradient(90deg, ' + color1 + ', ' + color2 + ')');
        } else {
            var bgColor = wp.customize('ross_topbar_bg_color').get();
            $('.site-topbar').css('background-color', bgColor);
        }
    }

    // Border Color
    wp.customize('ross_topbar_border_color', function(value) {
        value.bind(function(to) {
            var width = wp.customize('ross_topbar_border_width').get();
            if (width > 0) {
                $('.site-topbar').css('border-bottom', width + 'px solid ' + to);
            }
        });
    });

    // Border Width
    wp.customize('ross_topbar_border_width', function(value) {
        value.bind(function(to) {
            var color = wp.customize('ross_topbar_border_color').get();
            if (to > 0) {
                $('.site-topbar').css('border-bottom', to + 'px solid ' + color);
            } else {
                $('.site-topbar').css('border-bottom', 'none');
            }
        });
    });

})(jQuery);
