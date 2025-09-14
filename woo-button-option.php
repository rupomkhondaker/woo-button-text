<?php
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

class Exclutips_woo_button_text {
	/*
	 * For easier overriding we declared the keys
	 * here as well as our tabs array which is populated
	 * when registering settings
	 */
	private $general_settings_key = 'exclutips-general-settings'; // Option name
	private $advanced_settings_key = 'exclutips-advanced-settings'; // Option name
	private $plugin_options_key = 'exclutips-woo-button-options'; // Option name
	private $plugin_settings_tabs = array();
	
	/*
	 * Fired during plugins_loaded (very very early),
	 * so don't miss-use this, only actions and filters,
	 * current ones speak for themselves.
	 */
	function __construct() {
		add_action( 'init', array( &$this, 'load_settings' ) );
		add_action( 'admin_init', array( &$this, 'register_general_settings' ) );
		add_action( 'admin_init', array( &$this, 'register_advanced_settings' ) );
		add_action( 'admin_menu', array( &$this, 'add_admin_menus' ) );
	}
	
	/*
	 * Loads both the general and advanced settings from
	 * the database into their respective arrays. Uses
	 * array_merge to merge with default values if they're
	 * missing.
	 */
	function load_settings() {
		$this->general_settings = (array) get_option( $this->general_settings_key );
		$this->advanced_settings = (array) get_option( $this->advanced_settings_key );
		
		// Merge with defaults General Tab
		$this->general_settings = array_merge( array(
			'field_general_option_shop' => 'Add To Cart',
			'field_general_option_single' => 'Add To Cart',
			'field_general_option_old' => 'Add To Cart',
			'field_general_option_place_order' => 'Place Order'
		), $this->general_settings );
		
		// Merge with defaults Advanced Tab
		$this->advanced_settings = array_merge( array(
			'proceed_advanced_option' => 'Advanced value'
		), $this->advanced_settings );
	}
	
	/*
	 * Registers the general settings via the Settings API,
	 * appends the setting to the tabs array of the object.
	 */
	function register_general_settings() {
		$this->plugin_settings_tabs[$this->general_settings_key] = 'General Settings';
		
		register_setting( $this->general_settings_key, $this->general_settings_key );
		add_settings_section( 'section_general', 'Woo Button Text Settings', array( &$this, 'section_general_desc' ), $this->general_settings_key );
		
		add_settings_field( 
		'exclutips_archive_add_to_cart',
		'Button Text On Archive/Shop Page',
		array( &$this, 'field_general_option_shop' ),
		$this->general_settings_key, 
		'section_general' 
		);
		
		add_settings_field( 
		'exclutips_single_add_to_cart',
		'Button Text Product Page',
		array( &$this, 'field_general_option_single' ),
		$this->general_settings_key, 
		'section_general' 
		);
		
		add_settings_field( 
		'exclutips_old_add_to_cart',
		'Button Text < 2.1 Version',
		array( &$this, 'field_general_option_old' ),
		$this->general_settings_key, 
		'section_general' 
		);
		
		add_settings_field( 
		'exclutips_place_order',
		'Place Order Text',
		array( &$this, 'field_general_option_place_order' ),
		$this->general_settings_key, 
		'section_general' 
		);
	}
	
	/*
	 * Registers the advanced settings and appends the
	 * key to the plugin settings tabs array.
	 */
	function register_advanced_settings() {
		$this->plugin_settings_tabs[$this->advanced_settings_key] = 'Advanced Settings';
		register_setting( $this->advanced_settings_key, $this->advanced_settings_key );
		add_settings_section( 'section_advanced', 'Advanced Plugin Settings', array( &$this, 'section_advanced_desc' ), $this->advanced_settings_key );
		
		add_settings_field( 
		'woo_button_color',
		'Button Background Color',
		array( &$this, 'field_woo_button_color' ),
		$this->advanced_settings_key, 
		'section_advanced' 
		);
		
		add_settings_field( 
		'woo_button_text_color',
		'Button Text Color',
		array( &$this, 'field_woo_button_text_color' ),
		$this->advanced_settings_key, 
		'section_advanced' 
		);
		
		add_settings_field( 
		'woo_button_border_radius',
		'Button Border Radius',
		array( &$this, 'field_woo_button_border_radius' ),
		$this->advanced_settings_key, 
		'section_advanced' 
		);
			
		add_settings_field(
		'proceed_advanced_option',
		'Proceed To Checkout Text',
		array( &$this, 'field_proceed_advanced_option' ),
		$this->advanced_settings_key,
		'section_advanced'
		);
		
		add_settings_field(
		'proceed_paypal_option',
		'Proceed To Paypal Text',
		array( &$this, 'field_proceed_paypal_option' ),
		$this->advanced_settings_key,
		'section_advanced'
		);
		
		/*add_settings_field(
		'exclutips_remove_related_product',
		'Remove Related Product',
		array( &$this, 'field_remove_related_product' ),
		$this->advanced_settings_key,
		'section_advanced'
		);*/
	}
	
	/*
	 * The following methods provide descriptions
	 * for their respective sections, used as callbacks
	 * with add_settings_section
	 */
	function section_general_desc() { echo 'Bellow are the field for woocommerce button text'; }
	function section_advanced_desc() { echo 'Advanced settings are bellow'; }
	
	/*
	 * General Option field callback, renders a
	 * text input, note the name and value.
	 */
	function field_general_option_shop() {
		?>
		<input type="text" name="<?php echo $this->general_settings_key; ?>[exclutips_archive_add_to_cart]" value="<?php echo esc_attr( $this->general_settings['exclutips_archive_add_to_cart'] ); ?>" />
		<?php
	}
	
	function field_general_option_single() {
		?>
		<input type="text" name="<?php echo $this->general_settings_key; ?>[exclutips_single_add_to_cart]" value="<?php echo esc_attr( $this->general_settings['exclutips_single_add_to_cart'] ); ?>" />
		<?php
	}
	
	function field_general_option_old() {
		?>
		<input type="text" name="<?php echo $this->general_settings_key; ?>[exclutips_old_add_to_cart]" value="<?php echo esc_attr( $this->general_settings['exclutips_old_add_to_cart'] ); ?>" />
		<?php
	}
	
	function field_general_option_place_order() {
		?>
		<input type="text" name="<?php echo $this->general_settings_key; ?>[exclutips_place_order]" value="<?php echo esc_attr( $this->general_settings['exclutips_place_order'] ); ?>" />
		<?php
	}
	/*
	function field_general_option_place_order() {
		?>
		<input type="checkbox" name="<?php echo $this->general_settings_key; ?>[exclutips_remove_related_product]" <?php checked( $this->general_settings['exclutips_remove_related_product'], 1 ); ?> value='1' />
		<?php
	}
	*/
	/*
	 * Advanced Option field callback, same as above.
	 */
	
	function field_woo_button_color() {
		?>
		<input type="text" name="<?php echo $this->advanced_settings_key; ?>[woo_button_color]" value="<?php echo esc_attr( $this->advanced_settings['woo_button_color'] ); ?>" class="my-color-picker" data-default-color="#00008B"/>
		<?php
	}
	function field_woo_button_text_color() {
		?>
		<input type="text" name="<?php echo $this->advanced_settings_key; ?>[woo_button_text_color]" value="<?php echo esc_attr( $this->advanced_settings['woo_button_text_color'] ); ?>" class="my-color-picker" data-default-color="#fff"/>
		<?php
	}
	function field_woo_button_border_radius() {
		?>
		<input type="number" name="<?php echo $this->advanced_settings_key; ?>[woo_button_border_radius]" value="<?php echo esc_attr( $this->advanced_settings['woo_button_border_radius'] ); ?>" />
		<?php
	}
	
	function field_proceed_advanced_option() {
		?>
		<input type="text" name="<?php echo $this->advanced_settings_key; ?>[proceed_advanced_option]" value="<?php echo esc_attr( $this->advanced_settings['proceed_advanced_option'] ); ?>" />
		<?php
	}
	
	function field_remove_related_product() {
		?>
		<input type="checkbox" name="<?php echo $this->advanced_settings_key; ?>[exclutips_remove_related_product]" <?php checked( $this->advanced_settings['exclutips_remove_related_product'], 1 ); ?> value='1' />	
	     <?php
	}
	
	function field_proceed_paypal_option() {
		?>
		<input type="text" name="<?php echo $this->advanced_settings_key; ?>[proceed_paypal_option]" value="<?php echo esc_attr( $this->advanced_settings['proceed_paypal_option'] ); ?>" />
		<?php
	}
	
	/*
	 * Called during admin_menu, adds an options
	 * page under Settings called My Settings, rendered
	 * using the plugin_options_page method.
	 */
	function add_admin_menus() {
		add_submenu_page( 'exclutips-settings','Woo Button Settings', 'Woo Button Text', 'administrator', $this->plugin_options_key, array( &$this, 'plugin_options_page' ) );
	}
	
	/*
	 * Plugin Options page rendering goes here, checks
	 * for active tab and replaces key with the related
	 * settings key. Uses the plugin_options_tabs method
	 * to render the tabs.
	 */
	function plugin_options_page() {
		$tab = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->general_settings_key;
		?>
		<div class="wrap">
			<?php $this->plugin_options_tabs(); ?>
			<form method="post" action="options.php">
				<?php wp_nonce_field( 'update-options' ); ?>
				<?php settings_fields( $tab ); ?>
				<?php do_settings_sections( $tab ); ?>
				<?php submit_button('Update'); ?>
			</form>
		</div>
		<?php
	}
	
	/*
	 * Renders our tabs in the plugin options page,
	 * walks through the object's tabs array and prints
	 * them one by one. Provides the heading for the
	 * plugin_options_page method.
	 */
	function plugin_options_tabs() {
		$current_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->general_settings_key;

		screen_icon();
		echo '<h2 class="nav-tab-wrapper">';
		foreach ( $this->plugin_settings_tabs as $tab_key => $tab_caption ) {
			$active = $current_tab == $tab_key ? 'nav-tab-active' : '';
			echo '<a class="nav-tab ' . $active . '" href="?page=' . $this->plugin_options_key . '&tab=' . $tab_key . '">' . $tab_caption . '</a>';	
		}
		echo '</h2>';
	}
};

// Initialize the plugin
add_action( 'plugins_loaded', create_function( '', '$exclutips_woo_button_text = new Exclutips_woo_button_text;' ) );