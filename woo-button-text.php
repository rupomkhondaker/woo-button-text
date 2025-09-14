<?php
/**
* Plugin Name: Woo Button Text
* Plugin URI: http://www.exclutips.com/plugins/woo-button-text/
* Description: WooCommerce Button text changer with Button Color styler. 
* Version: 6.0.3
* Author: Rupom Khondaker
* Author URI: http://rupomkhondaker.com

/*Copyright 2015 Rupom Khondaker (email: rupomkhondaker at gmail.com)
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/
?>
<?php
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

add_action( 'admin_enqueue_scripts', 'exwoo_enqueue_color_picker' );
function exwoo_enqueue_color_picker( $hook_suffix ) {
    // first check that $hook_suffix is appropriate for your admin page
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'exclutips-script-handle', plugins_url('js/exclutips-jquery.js', __FILE__ ), array( 'wp-color-picker' ), false, true );
}

//Inlucde the style
include ('styles/woobuttonstyle.php');

// Call Main Plugin option page
if(!class_exists('EXCLUTIPS_Settings_Page')){
   include ('exclutips-option.php');
}

// Call function page
if(!class_exists('Exclutips_woo_button_text')){
include ('woo-button-option.php');
}
// Call function page
include ('inc/add-filters.php');
