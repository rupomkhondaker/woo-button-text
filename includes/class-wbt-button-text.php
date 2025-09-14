<?php
/**
 * Button text modification class for Woo Button Text plugin
 *
 * @package Woo_Button_Text
 * @since 7.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * WBT_Button_Text Class
 */
class WBT_Button_Text {
    /**
     * Instance of this class
     *
     * @var WBT_Button_Text
     */
    protected static $instance = null;

    /**
     * Settings instance
     *
     * @var WBT_Settings
     */
    protected $settings;

    /**
     * General settings
     *
     * @var array
     */
    protected $general_settings;

    /**
     * Advanced settings
     *
     * @var array
     */
    protected $advanced_settings;

    /**
     * Get the singleton instance of this class
     *
     * @return WBT_Button_Text
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
        add_action('update_option_wbt_general_settings', array($this, 'refresh_settings'));
        add_action('update_option_wbt_advanced_settings', array($this, 'refresh_settings'));
    }
    
    /**
     * Refresh settings from the database
     */
    public function refresh_settings() {
        $this->general_settings = $this->settings->get_general_settings();
        $this->advanced_settings = $this->settings->get_advanced_settings();
    }

    /**
     * Initialize hooks
     */
    private function init_hooks() {
        // Shop/Archive page button text
        add_filter('woocommerce_product_add_to_cart_text', array($this, 'change_add_to_cart_button_text'), 10, 2);
        
        // Single product page button text
        add_filter('woocommerce_product_single_add_to_cart_text', array($this, 'change_single_add_to_cart_button_text'), 10, 2);
        
        // Place order button text
        add_filter('woocommerce_order_button_text', array($this, 'change_place_order_button_text'));
        
        // Return to shop text
        add_filter('woocommerce_return_to_shop_text', array($this, 'change_return_to_shop_text'));
        
        // Cart and checkout button texts
        add_filter('gettext', array($this, 'change_cart_checkout_button_text'), 20, 3);
        
        // Remove related products if enabled
        if (!empty($this->advanced_settings['remove_related_products'])) {
            add_filter('woocommerce_related_products_args', array($this, 'remove_related_products'));
        }
    }

    /**
     * Change add to cart button text on shop/archive pages
     *
     * @param string $text Button text
     * @param WC_Product $product Product object
     * @return string Modified button text
     */
    public function change_add_to_cart_button_text($text, $product) {
        $product_type = $product->get_type();
        
        switch ($product_type) {
            case 'variable':
                return !empty($this->general_settings['variable_button_text']) 
                    ? $this->general_settings['variable_button_text'] 
                    : $text;
                
            case 'grouped':
                return !empty($this->general_settings['grouped_button_text']) 
                    ? $this->general_settings['grouped_button_text'] 
                    : $text;
                
            case 'external':
                return !empty($this->general_settings['external_button_text']) 
                    ? $this->general_settings['external_button_text'] 
                    : $text;
                
            default:
                return !empty($this->general_settings['shop_button_text']) 
                    ? $this->general_settings['shop_button_text'] 
                    : $text;
        }
    }

    /**
     * Change add to cart button text on single product pages
     *
     * @param string $text Button text
     * @param WC_Product $product Product object
     * @return string Modified button text
     */
    public function change_single_add_to_cart_button_text($text, $product = null) {
        // If no product is provided, get the global product
        if (!$product) {
            global $product;
        }
        
        if (!$product) {
            return $text;
        }
        
        $product_type = $product->get_type();
        
        switch ($product_type) {
            case 'variable':
                return !empty($this->general_settings['variable_button_text']) 
                    ? $this->general_settings['variable_button_text'] 
                    : $text;
                
            case 'grouped':
                return !empty($this->general_settings['grouped_button_text']) 
                    ? $this->general_settings['grouped_button_text'] 
                    : $text;
                
            case 'external':
                return !empty($this->general_settings['external_button_text']) 
                    ? $this->general_settings['external_button_text'] 
                    : $text;
                
            default:
                return !empty($this->general_settings['single_button_text']) 
                    ? $this->general_settings['single_button_text'] 
                    : $text;
        }
    }

    /**
     * Change place order button text
     *
     * @param string $text Button text
     * @return string Modified button text
     */
    public function change_place_order_button_text($text) {
        return !empty($this->general_settings['place_order_button_text']) 
            ? $this->general_settings['place_order_button_text'] 
            : $text;
    }

    /**
     * Change return to shop text
     *
     * @param string $text Button text
     * @return string Modified button text
     */
    public function change_return_to_shop_text($text) {
        return !empty($this->general_settings['return_to_shop_text']) 
            ? $this->general_settings['return_to_shop_text'] 
            : $text;
    }

    /**
     * Change cart and checkout button text
     *
     * @param string $translated_text Translated text
     * @param string $text Original text
     * @param string $domain Text domain
     * @return string Modified text
     */
    public function change_cart_checkout_button_text($translated_text, $text, $domain) {
        if ($domain !== 'woocommerce') {
            return $translated_text;
        }
        
        switch ($text) {
            case 'Proceed to checkout':
                return !empty($this->advanced_settings['proceed_checkout_text']) 
                    ? $this->advanced_settings['proceed_checkout_text'] 
                    : $translated_text;
                
            case 'Proceed to PayPal':
                return !empty($this->advanced_settings['proceed_paypal_text']) 
                    ? $this->advanced_settings['proceed_paypal_text'] 
                    : $translated_text;
                
            case 'Apply coupon':
                return !empty($this->advanced_settings['apply_coupon_text']) 
                    ? $this->advanced_settings['apply_coupon_text'] 
                    : $translated_text;
                
            case 'Update cart':
                return !empty($this->advanced_settings['update_cart_text']) 
                    ? $this->advanced_settings['update_cart_text'] 
                    : $translated_text;
        }
        
        return $translated_text;
    }

    /**
     * Remove related products
     *
     * @param array $args Related products args
     * @return array Empty array to remove related products
     */
    public function remove_related_products($args) {
        return array();
    }
}

// Initialize the button text
return WBT_Button_Text::get_instance();
