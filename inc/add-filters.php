<?php
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}
//Shop page
add_filter( 'woocommerce_product_add_to_cart_text', 'woo_archive_exclutips_cart_button_text' );    // < 2.1
function woo_archive_exclutips_cart_button_text() {
	$exclutips_value = get_option('exclutips-general-settings');
	$shop_page_text = $exclutips_value['exclutips_archive_add_to_cart'];
		   return __( $shop_page_text , 'woocommerce' );
}

//newer version of woocommerce	
add_filter( 'woocommerce_product_single_add_to_cart_text', 'woo_exclutips_cart_button_text' );    // 2.1 +
function woo_exclutips_cart_button_text() {
	$exclutips_value = get_option('exclutips-general-settings');
	$single_page_text = $exclutips_value['exclutips_single_add_to_cart'];
		   return __( $single_page_text, 'woocommerce' );
}
//Old version of woocommerce
add_filter( 'add_to_cart_text', 'woo_exclutips_cart_button_text_old' );    // < 2.1
function woo_exclutips_cart_button_text_old() {
	$exclutips_value = get_option('exclutips-general-settings');
	$shop_old_page_text = $exclutips_value['exclutips_old_add_to_cart'];
		  return __( $shop_old_page_text, 'woocommerce' );
}

/*Change Place Order Button text********
*/
add_filter( 'woocommerce_order_button_text', 'woo_custom_order_button_text' ); 
function woo_custom_order_button_text() {
	$exclutips_value = get_option('exclutips-general-settings');	
	$place_order = $exclutips_value['exclutips_place_order'];
		return __( $place_order , 'woocommerce' ); 
}


/* wc_remove_related_products
 * Clear the query arguments for related products so none show.
 * Add this code to your theme functions.php file.  
 */
$exclutips_avalue = get_option('exclutips-advanced-settings');	
$remove_related = $exclutips_avalue['exclutips_remove_related_product'];

if ($remove_related ){
	add_filter('woocommerce_related_products_args','wc_remove_related_products', 10); 
	function wc_remove_related_products( $args ) {
		return array();
	}	
}

/* Change Proceed to Checkout text in Cart page**
*/
add_filter( 'gettext', 'ld_custom_paypal_button_text', 20, 3 );
function ld_custom_paypal_button_text( $translated_text, $text, $domain ) {
   $exclutips_avalue = get_option('exclutips-advanced-settings');	
   $proceed_paypal_text = $exclutips_avalue['proceed_paypal_option'];
   $proceed_check_text = $exclutips_avalue['proceed_advanced_option'];
   
		switch ( $translated_text ) {
		case 'Proceed to checkout' :
			$translated_text = __( $proceed_check_text , 'woocommerce' );
		break;
		
		case 'Proceed to PayPal' :
			$translated_text = __( $proceed_paypal_text , 'woocommerce' );
		break;
}
return $translated_text;
}
