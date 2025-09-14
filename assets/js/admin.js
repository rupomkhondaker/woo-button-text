/**
 * Admin scripts for Woo Button Text plugin
 */
(function($) {
    'use strict';

    $(document).ready(function() {
        // Initialize color pickers with enhanced options
        if ($.fn.wpColorPicker) {
            $('.wbt-color-picker').wpColorPicker({
                change: function(event, ui) {
                    // Preview changes in real-time
                    setTimeout(function() {
                        highlightField($(event.target));
                    }, 100);
                },
                clear: function(event) {
                    setTimeout(function() {
                        highlightField($(event.target).closest('.wp-picker-container').find('input.wbt-color-picker'));
                    }, 100);
                }
            });
        }

        // Add field highlighting when changed
        $('.wbt-settings-wrap input[type="text"], .wbt-settings-wrap input[type="number"]').on('change', function() {
            highlightField($(this));
        });

        // Add checkbox toggle animation
        $('.wbt-settings-wrap input[type="checkbox"]').on('change', function() {
            var $label = $(this).next('label');
            if ($(this).is(':checked')) {
                $label.css('font-weight', 'bold');
                highlightField($(this));
            } else {
                $label.css('font-weight', 'normal');
            }
        });

        // Initialize tooltips for settings fields
        initTooltips();

        // Add save indicator
        $('.wbt-settings-wrap form').on('submit', function() {
            var $button = $(this).find('.button-primary');
            var originalText = $button.val();
            $button.val('Saving...').css('opacity', '0.7');
            
            setTimeout(function() {
                $button.val(originalText).css('opacity', '1');
            }, 1000);
        });

        // Add tab transitions
        $('.wbt-settings-wrap .nav-tab').on('click', function() {
            $('.wbt-settings-wrap .nav-tab').removeClass('nav-tab-active');
            $(this).addClass('nav-tab-active');
        });
    });

    /**
     * Highlight a field that has been changed
     */
    function highlightField($field) {
        $field.css('background-color', '#f7fcfe');
        setTimeout(function() {
            $field.css('background-color', '');
        }, 1500);
    }

    /**
     * Initialize tooltips for settings fields
     */
    function initTooltips() {
        $('.wbt-settings-wrap .form-table th').each(function() {
            var $th = $(this);
            var $label = $th.find('label');
            var labelText = $label.text();
            
            // Only add tooltip if there's a description available
            var $field = $th.next('td').find('input, select');
            var $desc = $th.next('td').find('.description');
            
            if ($desc.length) {
                // Add tooltip icon
                $label.html(labelText + ' <span class="dashicons dashicons-info tooltip-icon"></span>');
                
                // Style the tooltip icon
                $th.find('.tooltip-icon').css({
                    'font-size': '16px',
                    'width': '16px',
                    'height': '16px',
                    'vertical-align': 'middle',
                    'color': '#0073aa',
                    'cursor': 'help'
                });
            }
        });
    }

})(jQuery);
