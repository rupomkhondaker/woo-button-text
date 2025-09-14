<?php
/**
 * Settings class for Woo Button Text plugin
 *
 * @package Woo_Button_Text
 * @since 7.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * WBT_Settings Class
 */
class WBT_Settings {
    /**
     * Instance of this class
     *
     * @var WBT_Settings
     */
    protected static $instance = null;

    /**
     * Settings tabs
     *
     * @var array
     */
    private $settings_tabs = array();

    /**
     * General settings key
     *
     * @var string
     */
    private $general_settings_key = 'wbt_general_settings';

    /**
     * Advanced settings key
     *
     * @var string
     */
    private $advanced_settings_key = 'wbt_advanced_settings';
    
    /**
     * General settings array
     *
     * @var array
     */
    private $general_settings = array();

    /**
     * Advanced settings array
     *
     * @var array
     */
    private $advanced_settings = array();

    /**
     * Get the singleton instance of this class
     *
     * @return WBT_Settings
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
        // Load settings
        $this->load_settings();

        // Register settings
        add_action('admin_init', array($this, 'register_settings'));

        // Add admin menu
        add_action('admin_menu', array($this, 'add_admin_menu'));
    }

    /**
     * Load settings from database
     */
    private function load_settings() {
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
        
        // Get saved settings (defaults are handled by activation hook)
        $this->general_settings = get_option($this->general_settings_key, $default_general_settings);
        $this->advanced_settings = get_option($this->advanced_settings_key, $default_advanced_settings);
        
        // Ensure all keys exist by merging with defaults
        // This handles cases where new settings are added in updates
        $this->general_settings = array_merge($default_general_settings, $this->general_settings);
        $this->advanced_settings = array_merge($default_advanced_settings, $this->advanced_settings);
    }

    /**
     * Register settings
     */
    public function register_settings() {
        // Register settings tabs
        $this->settings_tabs[$this->general_settings_key] = __('General Settings', 'woo-button-text');
        $this->settings_tabs[$this->advanced_settings_key] = __('Advanced Settings', 'woo-button-text');

        // Register general settings
        register_setting(
            $this->general_settings_key, 
            $this->general_settings_key,
            array($this, 'sanitize_general_settings')
        );
        add_settings_section(
            'wbt_general_section',
            __('Button Text Settings', 'woo-button-text'),
            array($this, 'general_section_callback'),
            $this->general_settings_key
        );

        // Add general settings fields with descriptions
        $general_fields = array(
            'shop_button_text' => array(
                'label' => __('Shop/Archive Button Text', 'woo-button-text'),
                'desc' => __('Text displayed on buttons in shop and archive pages.', 'woo-button-text')
            ),
            'single_button_text' => array(
                'label' => __('Single Product Button Text', 'woo-button-text'),
                'desc' => __('Text displayed on the Add to Cart button on single product pages.', 'woo-button-text')
            ),
            'variable_button_text' => array(
                'label' => __('Variable Product Button Text', 'woo-button-text'),
                'desc' => __('Text displayed on buttons for variable products (products with options).', 'woo-button-text')
            ),
            'grouped_button_text' => array(
                'label' => __('Grouped Product Button Text', 'woo-button-text'),
                'desc' => __('Text displayed on buttons for grouped products.', 'woo-button-text')
            ),
            'external_button_text' => array(
                'label' => __('External Product Button Text', 'woo-button-text'),
                'desc' => __('Text displayed on buttons for external/affiliate products.', 'woo-button-text')
            ),
            'place_order_button_text' => array(
                'label' => __('Place Order Button Text', 'woo-button-text'),
                'desc' => __('Text displayed on the final checkout button.', 'woo-button-text')
            ),
            'return_to_shop_text' => array(
                'label' => __('Return to Shop Button Text', 'woo-button-text'),
                'desc' => __('Text displayed on the button that returns customers to the shop.', 'woo-button-text')
            ),
        );

        foreach ($general_fields as $field_id => $field_data) {
            add_settings_field(
                $field_id,
                $field_data['label'],
                array($this, 'text_field_callback'),
                $this->general_settings_key,
                'wbt_general_section',
                array(
                    'id' => $field_id,
                    'key' => $this->general_settings_key,
                    'desc' => $field_data['desc'],
                )
            );
        }

        // Register advanced settings
        register_setting(
            $this->advanced_settings_key, 
            $this->advanced_settings_key,
            array($this, 'sanitize_advanced_settings')
        );
        add_settings_section(
            'wbt_advanced_section',
            __('Button Style & Advanced Settings', 'woo-button-text'),
            array($this, 'advanced_section_callback'),
            $this->advanced_settings_key
        );

        // Add advanced settings fields
        add_settings_field(
            'button_bg_color',
            __('Button Background Color', 'woo-button-text'),
            array($this, 'color_field_callback'),
            $this->advanced_settings_key,
            'wbt_advanced_section',
            array(
                'id' => 'button_bg_color',
                'key' => $this->advanced_settings_key,
                'desc' => __('Choose the background color for all WooCommerce buttons.', 'woo-button-text'),
            )
        );

        add_settings_field(
            'button_text_color',
            __('Button Text Color', 'woo-button-text'),
            array($this, 'color_field_callback'),
            $this->advanced_settings_key,
            'wbt_advanced_section',
            array(
                'id' => 'button_text_color',
                'key' => $this->advanced_settings_key,
                'desc' => __('Choose the text color for all WooCommerce buttons.', 'woo-button-text'),
            )
        );

        add_settings_field(
            'button_border_radius',
            __('Button Border Radius (px)', 'woo-button-text'),
            array($this, 'number_field_callback'),
            $this->advanced_settings_key,
            'wbt_advanced_section',
            array(
                'id' => 'button_border_radius',
                'key' => $this->advanced_settings_key,
                'min' => 0,
                'max' => 50,
                'desc' => __('Set the border radius to control how rounded the button corners are. 0 = square corners.', 'woo-button-text'),
            )
        );

        add_settings_field(
            'button_font_size',
            __('Button Font Size (px)', 'woo-button-text'),
            array($this, 'number_field_callback'),
            $this->advanced_settings_key,
            'wbt_advanced_section',
            array(
                'id' => 'button_font_size',
                'key' => $this->advanced_settings_key,
                'min' => 10,
                'max' => 30,
                'desc' => __('Set the font size for button text. Recommended range: 12-20px.', 'woo-button-text'),
            )
        );

        add_settings_field(
            'button_padding',
            __('Button Padding (CSS format)', 'woo-button-text'),
            array($this, 'text_field_callback'),
            $this->advanced_settings_key,
            'wbt_advanced_section',
            array(
                'id' => 'button_padding',
                'key' => $this->advanced_settings_key,
                'desc' => __('Set the padding in CSS format (e.g., "10px 20px" for top/bottom and left/right).', 'woo-button-text'),
            )
        );

        // Cart and checkout text fields with descriptions
        $advanced_text_fields = array(
            'proceed_checkout_text' => array(
                'label' => __('Proceed to Checkout Text', 'woo-button-text'),
                'desc' => __('Text displayed on the button that takes customers from cart to checkout.', 'woo-button-text')
            ),
            'proceed_paypal_text' => array(
                'label' => __('Proceed to PayPal Text', 'woo-button-text'),
                'desc' => __('Text displayed on the PayPal checkout button (if PayPal is enabled).', 'woo-button-text')
            ),
            'apply_coupon_text' => array(
                'label' => __('Apply Coupon Text', 'woo-button-text'),
                'desc' => __('Text displayed on the button that applies coupon codes.', 'woo-button-text')
            ),
            'update_cart_text' => array(
                'label' => __('Update Cart Text', 'woo-button-text'),
                'desc' => __('Text displayed on the button that updates the cart after quantity changes.', 'woo-button-text')
            ),
        );

        foreach ($advanced_text_fields as $field_id => $field_data) {
            add_settings_field(
                $field_id,
                $field_data['label'],
                array($this, 'text_field_callback'),
                $this->advanced_settings_key,
                'wbt_advanced_section',
                array(
                    'id' => $field_id,
                    'key' => $this->advanced_settings_key,
                    'desc' => $field_data['desc'],
                )
            );
        }

        add_settings_field(
            'remove_related_products',
            __('Remove Related Products', 'woo-button-text'),
            array($this, 'checkbox_field_callback'),
            $this->advanced_settings_key,
            'wbt_advanced_section',
            array(
                'id' => 'remove_related_products',
                'key' => $this->advanced_settings_key,
                'label' => __('Enable to remove related products from single product page', 'woo-button-text'),
                'desc' => __('When checked, this will remove the related products section from individual product pages.', 'woo-button-text'),
            )
        );
    }

    /**
     * General section callback
     */
    public function general_section_callback() {
        ?>
        <div class="section-description">
            <p><?php _e('Customize the text for various WooCommerce buttons throughout your store. These settings allow you to change button text on shop pages, product pages, and checkout.', 'woo-button-text'); ?></p>
            <p><?php _e('Enter your preferred text for each button type below. Changes will be applied immediately after saving.', 'woo-button-text'); ?></p>
        </div>
        <?php
    }

    /**
     * Advanced section callback
     */
    public function advanced_section_callback() {
        ?>
        <div class="section-description">
            <p><?php _e('Customize the style and appearance of WooCommerce buttons and additional text options.', 'woo-button-text'); ?></p>
            <p><?php _e('Here you can change button colors, border radius, font size, and other styling options. You can also customize additional button texts like "Proceed to Checkout" and "Apply Coupon".', 'woo-button-text'); ?></p>
        </div>
        <?php
    }

    /**
     * Text field callback
     */
    public function text_field_callback($args) {
        $id = $args['id'];
        $key = $args['key'];
        $settings_array = $key === $this->general_settings_key ? $this->general_settings : $this->advanced_settings;
        $value = isset($settings_array[$id]) ? $settings_array[$id] : '';
        $desc = isset($args['desc']) ? $args['desc'] : '';
        ?>
        <input type="text" name="<?php echo esc_attr($key); ?>[<?php echo esc_attr($id); ?>]" value="<?php echo esc_attr($value); ?>" class="regular-text" />
        <?php if (!empty($desc)) : ?>
            <p class="description"><?php echo esc_html($desc); ?></p>
        <?php endif; ?>
        <?php
    }

    /**
     * Color field callback
     */
    public function color_field_callback($args) {
        $id = $args['id'];
        $key = $args['key'];
        $settings_array = $key === $this->general_settings_key ? $this->general_settings : $this->advanced_settings;
        $value = isset($settings_array[$id]) ? $settings_array[$id] : '';
        $desc = isset($args['desc']) ? $args['desc'] : '';
        ?>
        <input type="text" name="<?php echo esc_attr($key); ?>[<?php echo esc_attr($id); ?>]" value="<?php echo esc_attr($value); ?>" class="wbt-color-picker" data-default-color="<?php echo esc_attr($value); ?>" />
        <?php if (!empty($desc)) : ?>
            <p class="description"><?php echo esc_html($desc); ?></p>
        <?php endif; ?>
        <?php
    }

    /**
     * Number field callback
     */
    public function number_field_callback($args) {
        $id = $args['id'];
        $key = $args['key'];
        $settings_array = $key === $this->general_settings_key ? $this->general_settings : $this->advanced_settings;
        $value = isset($settings_array[$id]) ? $settings_array[$id] : '';
        $min = isset($args['min']) ? $args['min'] : 0;
        $max = isset($args['max']) ? $args['max'] : 100;
        $desc = isset($args['desc']) ? $args['desc'] : '';
        ?>
        <input type="number" name="<?php echo esc_attr($key); ?>[<?php echo esc_attr($id); ?>]" value="<?php echo esc_attr($value); ?>" class="small-text" min="<?php echo esc_attr($min); ?>" max="<?php echo esc_attr($max); ?>" />
        <?php if (!empty($desc)) : ?>
            <p class="description"><?php echo esc_html($desc); ?></p>
        <?php endif; ?>
        <?php
    }

    /**
     * Checkbox field callback
     */
    public function checkbox_field_callback($args) {
        $id = $args['id'];
        $key = $args['key'];
        $settings_array = $key === $this->general_settings_key ? $this->general_settings : $this->advanced_settings;
        $value = isset($settings_array[$id]) ? $settings_array[$id] : false;
        $desc = isset($args['desc']) ? $args['desc'] : '';
        ?>
        <input type="checkbox" id="<?php echo esc_attr($key . '_' . $id); ?>" name="<?php echo esc_attr($key); ?>[<?php echo esc_attr($id); ?>]" value="1" <?php checked($value, 1); ?> />
        <label for="<?php echo esc_attr($key . '_' . $id); ?>"><?php echo esc_html($args['label']); ?></label>
        <?php if (!empty($desc)) : ?>
            <p class="description"><?php echo esc_html($desc); ?></p>
        <?php endif; ?>
        <?php
    }

    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_menu_page(
            __('Woo Button Text', 'woo-button-text'),
            __('Woo Button Text', 'woo-button-text'),
            'manage_options',
            'woo-button-text-settings',
            array($this, 'settings_page'),
            'dashicons-button',
            58
        );
    }

    /**
     * Settings page
     */
    public function settings_page() {
        $current_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : $this->general_settings_key;
        ?>
        <div class="wrap wbt-settings-wrap">
            <h1>
                <span class="dashicons dashicons-button" style="font-size: 30px; height: 30px; width: 30px; padding-right: 10px;"></span>
                <?php _e('Woo Button Text Settings', 'woo-button-text'); ?>
            </h1>
            
            <h2 class="nav-tab-wrapper">
                <?php foreach ($this->settings_tabs as $tab_key => $tab_label) : ?>
                    <a href="?page=woo-button-text-settings&tab=<?php echo esc_attr($tab_key); ?>" class="nav-tab <?php echo $current_tab === $tab_key ? 'nav-tab-active' : ''; ?>">
                        <?php if ($tab_key === $this->general_settings_key): ?>
                            <span class="dashicons dashicons-edit" style="margin-right: 5px;"></span>
                        <?php else: ?>
                            <span class="dashicons dashicons-admin-appearance" style="margin-right: 5px;"></span>
                        <?php endif; ?>
                        <?php echo esc_html($tab_label); ?>
                    </a>
                <?php endforeach; ?>
            </h2>
            
            <form method="post" action="options.php">
                <?php
                settings_fields($current_tab);
                do_settings_sections($current_tab);
                ?>
                
                <div class="wbt-submit-section">
                    <hr>
                    <p class="description">
                        <?php _e('Click the button below to save your settings. Changes will be applied immediately to your store.', 'woo-button-text'); ?>
                    </p>
                    <?php submit_button(__('Save Button Settings', 'woo-button-text'), 'primary', 'submit', false); ?>
                </div>
            </form>
            
            <div class="wbt-footer">
                <p>
                    <?php printf(
                        __('Woo Button Text v%s | Made with %s by %s', 'woo-button-text'),
                        WBT_VERSION,
                        '<span class="dashicons dashicons-heart" style="color: #ff6b6b;"></span>',
                        '<a href="http://rupomkhondaker.com" target="_blank">Rupom Khondaker</a>'
                    ); ?>
                </p>
            </div>
        </div>
        <?php
    }

    /**
     * Get general settings
     *
     * @return array
     */
    public function get_general_settings() {
        return $this->general_settings;
    }

    /**
     * Get advanced settings
     *
     * @return array
     */
    public function get_advanced_settings() {
        return $this->advanced_settings;
    }
    
    /**
     * Sanitize general settings
     *
     * @param array $input The settings array to sanitize
     * @return array Sanitized settings
     */
    public function sanitize_general_settings($input) {
        $sanitized = array();
        
        // Text fields
        $text_fields = array(
            'shop_button_text',
            'single_button_text',
            'variable_button_text',
            'grouped_button_text',
            'external_button_text',
            'place_order_button_text',
            'return_to_shop_text'
        );
        
        foreach ($text_fields as $field) {
            if (isset($input[$field])) {
                $sanitized[$field] = sanitize_text_field($input[$field]);
            }
        }
        
        return $sanitized;
    }
    
    /**
     * Sanitize advanced settings
     *
     * @param array $input The settings array to sanitize
     * @return array Sanitized settings
     */
    public function sanitize_advanced_settings($input) {
        $sanitized = array();
        
        // Text fields
        $text_fields = array(
            'button_padding',
            'proceed_checkout_text',
            'proceed_paypal_text',
            'apply_coupon_text',
            'update_cart_text'
        );
        
        foreach ($text_fields as $field) {
            if (isset($input[$field])) {
                $sanitized[$field] = sanitize_text_field($input[$field]);
            }
        }
        
        // Color fields
        $color_fields = array(
            'button_bg_color',
            'button_text_color'
        );
        
        foreach ($color_fields as $field) {
            if (isset($input[$field])) {
                $sanitized[$field] = sanitize_hex_color($input[$field]);
            }
        }
        
        // Number fields
        if (isset($input['button_border_radius'])) {
            $sanitized['button_border_radius'] = absint($input['button_border_radius']);
        }
        
        if (isset($input['button_font_size'])) {
            $sanitized['button_font_size'] = absint($input['button_font_size']);
        }
        
        // Checkbox
        $sanitized['remove_related_products'] = isset($input['remove_related_products']) ? 1 : 0;
        
        return $sanitized;
    }
}

// Initialize the settings
return WBT_Settings::get_instance();
