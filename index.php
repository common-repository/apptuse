<?php 

/**
* Plugin Name: Apptuse 
* Description: Apptuse is a mobile platform that powers individuals & global brands to make native shopping apps. Have your app ready in a few hours with no technical knowledge required.
* Version: 1.0.3
* Author: Apptuse Developer
* Author URI: https://apptuse.com
* License: GPLv2 or later
* License URI: https://www.gnu.org/licenses/gpl-2.0.html
*
*/


// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


//Setup


//Includes

include('includes/activate.php');
include('api/checkout.php');
include('api/store-data.php');
include('api/product_individual.php');
include('dashboard/function.php');


//Hooks
register_activation_hook( __FILE__ , 'apptuse_r_activate_plugin');

add_action('template_redirect', 'register_apptuse');






//Shortcodes
