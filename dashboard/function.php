<?php

//Add admin menu

add_action( 'admin_menu', 'apptuse_admin_menu' );

function apptuse_admin_menu() {
	add_menu_page( 'Mobile App for Woocommerce Web Store', 'Apptuse', 'manage_options', 'apptuse_dashboard', 'apptuse_display_dashboard',plugin_dir_url( __FILE__ ).'/assets/logo.png', 6  );
}


function apptuse_display_dashboard(){
	$email = get_option('admin_email');
	$token = get_option('apptuse_token');;
	require_once('admin_apptuse.php');
}