<?php
/**
 * Button styling class for Woo Button Text plugin
 *
 * @package Woo_Button_Text
 * @since 7.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * WBT_Button_Style Class
 */
class WBT_Button_Style {
    /**
     * Instance of this class
     *
     * @var WBT_Button_Style
     */
    protected static $instance = null;

    /**
     * Settings instance
     *
     * @var WBT_Settings
     */
    protected $settings;

    /**
     * Advanced settings
     *
     * @var array
     */
    protected $advanced_settings;

    /**
     * Get the singleton instance of this class
     *
     * @return WBT_Button_Style
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    public function __construct() {
        // Get settings
        $this->settings = WBT_Settings::get_instance();
        $this->refresh_settings();

        // Initialize hooks
        $this->init_hooks();
        
        // Add action to refresh settings when they are updated
        add_action('update_option_wbt_advanced_settings', array($this, 'refresh_settings'));
    }
    
    /**
     * Refresh settings from the database
     */
    public function refresh_settings() {
        $this->advanced_settings = $this->settings->get_advanced_settings();
    }

    /**
     * Initialize hooks
     */
    private function init_hooks() {
        // Add custom CSS to frontend
        add_action('wp_head', array($this, 'add_custom_button_styles'));
    }

    /**
     * Add custom button styles to frontend
     */
    public function add_custom_button_styles() {
        // Get style settings
        $bg_color = !empty($this->advanced_settings['button_bg_color']) ? $this->advanced_settings['button_bg_color'] : '#a46497';
        $text_color = !empty($this->advanced_settings['button_text_color']) ? $this->advanced_settings['button_text_color'] : '#ffffff';
        $border_radius = !empty($this->advanced_settings['button_border_radius']) ? intval($this->advanced_settings['button_border_radius']) : 3;
        $font_size = !empty($this->advanced_settings['button_font_size']) ? intval($this->advanced_settings['button_font_size']) : 14;
        $padding = !empty($this->advanced_settings['button_padding']) ? $this->advanced_settings['button_padding'] : '10px 20px';
        
        // CSS output
        ?>
        <style type="text/css" id="wbt-custom-button-styles">
            /* WooCommerce Button Styles */
            .woocommerce #respond input#submit, 
            .woocommerce a.button, 
            .woocommerce button.button, 
            .woocommerce input.button,
            .woocommerce #respond input#submit.alt, 
            .woocommerce a.button.alt, 
            .woocommerce button.button.alt, 
            .woocommerce input.button.alt {
                background-color: <?php echo esc_attr($bg_color); ?> !important;
                color: <?php echo esc_attr($text_color); ?> !important;
                border-radius: <?php echo esc_attr($border_radius); ?>px !important;
                font-size: <?php echo esc_attr($font_size); ?>px !important;
                padding: <?php echo esc_attr($padding); ?> !important;
                border-color: <?php echo esc_attr($bg_color); ?> !important;
            }
            
            .woocommerce #respond input#submit:hover, 
            .woocommerce a.button:hover, 
            .woocommerce button.button:hover, 
            .woocommerce input.button:hover,
            .woocommerce #respond input#submit.alt:hover, 
            .woocommerce a.button.alt:hover, 
            .woocommerce button.button.alt:hover, 
            .woocommerce input.button.alt:hover {
                background-color: <?php echo esc_attr($this->adjust_brightness($bg_color, -15)); ?> !important;
                color: <?php echo esc_attr($text_color); ?> !important;
                border-color: <?php echo esc_attr($this->adjust_brightness($bg_color, -15)); ?> !important;
            }
        </style>
        <?php
    }

    /**
     * Adjust brightness of a hex color
     *
     * @param string $hex Hex color code
     * @param int $steps Steps to adjust brightness (negative = darker, positive = lighter)
     * @return string Adjusted hex color
     */
    private function adjust_brightness($hex, $steps) {
        // Remove # if present
        $hex = ltrim($hex, '#');
        
        // Convert to RGB
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        
        // Adjust brightness
        $r = max(0, min(255, $r + $steps));
        $g = max(0, min(255, $g + $steps));
        $b = max(0, min(255, $b + $steps));
        
        // Convert back to hex
        return sprintf('#%02x%02x%02x', $r, $g, $b);
    }
}

// Initialize the button style
return WBT_Button_Style::get_instance();
