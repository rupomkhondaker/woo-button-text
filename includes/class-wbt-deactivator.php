<?php
/**
 * Plugin deactivation functionality
 *
 * @package Woo_Button_Text
 * @since 7.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * WBT_Deactivator Class
 * Handles plugin deactivation tasks
 */
class WBT_Deactivator {
    /**
     * Deactivate the plugin
     * Performs cleanup tasks when the plugin is deactivated
     */
    public static function deactivate() {
        // We'll keep the settings in the database
        // This way users won't lose their customizations if they reactivate the plugin
        
        // Clear any caches
        if (function_exists('wp_cache_flush')) {
            wp_cache_flush();
        }
        
        // Delete plugin options
        delete_option('wbt_general_settings');
        delete_option('wbt_advanced_settings');
        
        // You could add additional cleanup tasks here if needed in the future
    }
}
