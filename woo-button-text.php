<?php
/**
 * Plugin Name: Woo Button Text
 * Plugin URI: https://wordpress.org/plugins/woo-button-text/
 * Description: WooCommerce Button text changer with Button Color styler. Change all button texts in your WooCommerce store.
 * Version: 7.0.0
 * Author: Rupom Khondaker
 * Author URI: http://rupomkhondaker.com
 * Text Domain: woo-button-text
 * Domain Path: /languages
 * WC requires at least: 3.0.0
 * WC tested up to: 8.0.0
 * Requires at least: 4.9
 * Requires PHP: 7.4
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('WBT_VERSION', '7.0.0');
define('WBT_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('WBT_PLUGIN_URL', plugin_dir_url(__FILE__));
define('WBT_PLUGIN_BASENAME', plugin_basename(__FILE__));
define('WBT_PLUGIN_PATH', __FILE__);

/**
 * Check if WooCommerce is active
 */
function wbt_is_woocommerce_active() {
    $active_plugins = (array) get_option('active_plugins', array());
    
    if (is_multisite()) {
        $active_plugins = array_merge($active_plugins, get_site_option('active_sitewide_plugins', array()));
    }
    
    return in_array('woocommerce/woocommerce.php', $active_plugins) || array_key_exists('woocommerce/woocommerce.php', $active_plugins);
}

/**
 * Main Plugin Class
 */
class Woo_Button_Text {
    /**
     * Instance of this class
     *
     * @var Woo_Button_Text
     */
    protected static $instance = null;

    /**
     * Get the singleton instance of this class
     *
     * @return Woo_Button_Text
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
        // Load plugin text domain
        add_action('plugins_loaded', array($this, 'load_plugin_textdomain'));
        
        // Check if WooCommerce is active
        if (wbt_is_woocommerce_active()) {
            $this->includes();
            $this->init_hooks();
        } else {
            add_action('admin_notices', array($this, 'woocommerce_missing_notice'));
        }
    }

    /**
     * Load plugin text domain
     */
    public function load_plugin_textdomain() {
        load_plugin_textdomain('woo-button-text', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }

    /**
     * Include required files
     */
    private function includes() {
        // Include settings class
        require_once WBT_PLUGIN_DIR . 'includes/class-wbt-settings.php';
        
        // Include button text filters
        require_once WBT_PLUGIN_DIR . 'includes/class-wbt-button-text.php';
        
        // Include button styling
        require_once WBT_PLUGIN_DIR . 'includes/class-wbt-button-style.php';
    }

    /**
     * Initialize hooks
     */
    private function init_hooks() {
        // Enqueue admin scripts and styles
        add_action('admin_enqueue_scripts', array($this, 'admin_scripts'));
        
        // Add settings link to plugins page
        add_filter('plugin_action_links_' . WBT_PLUGIN_BASENAME, array($this, 'add_settings_link'));
    }

    /**
     * Enqueue admin scripts and styles
     */
    public function admin_scripts($hook) {
        $screen = get_current_screen();
        
        if (strpos($hook, 'woo-button-text') !== false) {
            // Color picker
            wp_enqueue_style('wp-color-picker');
            wp_enqueue_script('wp-color-picker');
            
            // Admin styles
            wp_enqueue_style('wbt-admin-style', WBT_PLUGIN_URL . 'assets/css/admin.css', array(), WBT_VERSION);
            
            // Admin scripts
            wp_enqueue_script('wbt-admin-script', WBT_PLUGIN_URL . 'assets/js/admin.js', array('jquery', 'wp-color-picker'), WBT_VERSION, true);
        }
    }

    /**
     * Add settings link to plugin list table
     *
     * @param array $links Existing links
     * @return array Modified links
     */
    public function add_settings_link($links) {
        $settings_link = '<a href="' . admin_url('admin.php?page=woo-button-text-settings') . '">' . __('Settings', 'woo-button-text') . '</a>';
        array_unshift($links, $settings_link);
        return $links;
    }

    /**
     * WooCommerce missing notice
     */
    public function woocommerce_missing_notice() {
        ?>
        <div class="error">
            <p><?php _e('Woo Button Text requires WooCommerce to be installed and active.', 'woo-button-text'); ?></p>
        </div>
        <?php
    }
}

// Initialize the plugin
function woo_button_text() {
    return Woo_Button_Text::get_instance();
}

// Include activator and deactivator files directly in the main plugin file
// This ensures they're available when the activation/deactivation hooks are triggered
require_once plugin_dir_path(__FILE__) . 'includes/class-wbt-activator.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-wbt-deactivator.php';

// Start the plugin
add_action('plugins_loaded', 'woo_button_text');

// Register activation and deactivation hooks
register_activation_hook(WBT_PLUGIN_PATH, array('WBT_Activator', 'activate'));
register_deactivation_hook(WBT_PLUGIN_PATH, array('WBT_Deactivator', 'deactivate'));
