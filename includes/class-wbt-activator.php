<?php
/**
 * Plugin activation functionality
 *
 * @package Woo_Button_Text
 * @since 7.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * WBT_Activator Class
 * Handles plugin activation tasks
 */
class WBT_Activator {
    /**
     * Activate the plugin
     * Sets up default settings when the plugin is activated
     */
    public static function activate() {
        // Define default settings
        $default_general_settings = array(
            'shop_button_text' => 'Add to cart',
            'single_button_text' => 'Add to cart',
            'variable_button_text' => 'Select options',
            'grouped_button_text' => 'View products',
            'external_button_text' => 'Buy product',
            'place_order_button_text' => 'Place order',
            'return_to_shop_text' => 'Return to shop',
        );
        
        $default_advanced_settings = array(
            'button_bg_color' => '#a46497',
            'button_text_color' => '#ffffff',
            'button_border_radius' => '3',
            'button_font_size' => '14',
            'button_padding' => '10px 20px',
            'proceed_checkout_text' => 'Proceed to checkout',
            'proceed_paypal_text' => 'Proceed to PayPal',
            'apply_coupon_text' => 'Apply coupon',
            'update_cart_text' => 'Update cart',
            'remove_related_products' => false,
        );
        
        // Only add the options if they don't exist
        if (get_option('wbt_general_settings') === false) {
            add_option('wbt_general_settings', $default_general_settings);
        }
        
        if (get_option('wbt_advanced_settings') === false) {
            add_option('wbt_advanced_settings', $default_advanced_settings);
        }
        
        // Clear any caches
        if (function_exists('wp_cache_flush')) {
            wp_cache_flush();
        }
    }
}
