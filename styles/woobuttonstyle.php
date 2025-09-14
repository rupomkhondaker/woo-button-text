<?php 
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}


function woo_button_color_style() {
$exclutips_avalue = get_option('exclutips-advanced-settings');	

$button_color = $exclutips_avalue['woo_button_color'];
$text_color = $exclutips_avalue['woo_button_text_color'];
$button_radius = $exclutips_avalue['woo_button_border_radius'];
?>
<style type="text/css" id="twentytwelve-admin-header-css">
.woocommerce #respond input#submit.alt, .woocommerce a.button.alt, .woocommerce button.button.alt, .woocommerce input.button.alt {
background:<?php echo $button_color;?>!important;
border-color:<?php echo $button_color;?>!important;
color:<?php echo $text_color;?>!important;

border-radius:<?php echo $button_radius;?>px!important;
-moz-border-radius:<?php echo $button_radius;?>px!important;
-webkit-border-radius:<?php echo $button_radius;?>px!important;
-o-border-radius:<?php echo $button_radius;?>px!important;
}


.woocommerce #respond input#submit, .woocommerce a.button, .woocommerce button.button, .woocommerce input.button{
background:<?php echo $button_color;?>!important;
border-color:<?php echo $button_color;?>!important;
color:<?php echo $text_color;?>!important;

border-radius:<?php echo $button_radius;?>px!important;
-moz-border-radius:<?php echo $button_radius;?>px!important;
-webkit-border-radius:<?php echo $button_radius;?>px!important;
-o-border-radius:<?php echo $button_radius;?>px!important;
}


</style>
<?php
}
add_action( 'wp_enqueue_scripts', 'woo_button_color_style' );
