<?php

function apptuse_r_activate_plugin() {
	 if(version_compare(get_bloginfo('version'), '4.2' , '<')){

	 	wp_die( __('Kindly update wordpress','apptuse') );
	 }

	 /**
	 * Check if WooCommerce is active
	  **/
	if (  !(in_array( 'woocommerce/woocommerce.php',apply_filters( 'active_plugins', get_option( 'active_plugins' ))))) {
    	wp_die( __('Kindly install woocommerce','apptuse') );
	}

	 register_apptuse();

}


/* Register with the Apptuse */
function register_apptuse(){

 if(empty(get_option('apptuse_token'))){

	$param['email'] = get_option('admin_email');
	$param['store_url']=get_option('siteurl');

	$api_data = apptuse_generateWCAPIKeys();

	$param['consumer_key']=$api_data['consumer_key'];
	$param['consumer_secret']=$api_data['consumer_secret'];
	

	$url = "https://app.apptuse.com/plugin-signup/6";

	$response = wp_remote_post( $url, array(
		'method' => 'POST',
		'timeout' => 45,
		'redirection' => 5,
		'httpversion' => '1.0',
		'blocking' => true,
		'headers' => array(),
		'body' => $param,
		'cookies' => array()
	    )
	);

	$json_response= json_decode($response['body'],true);

		
		if($json_response['success'] == true){
			add_option('apptuse_token',$json_response['token']);
		}
	
	}
}

/*Generate Wordpress API keys */
function apptuse_generateWCAPIKeys(){

    global $wpdb;

   
    $api_data["shop_url"] = get_option('siteurl');

    $api_data["consumer_key"] = 'ck_'.wc_rand_hash();
    $api_data["consumer_secret"] = 'cs_'.wc_rand_hash();

    $consumer_key    = $api_data["consumer_key"];
    $consumer_secret = $api_data["consumer_secret"];

 	$user = get_user_by('email' ,get_option('admin_email'));

    $data = array(
        'user_id' => $user->ID,
        'description' => 'apptuse',
        'permissions' => "read_write",
        'consumer_key' => wc_api_hash($api_data["consumer_key"]),
        'consumer_secret' => $api_data["consumer_secret"],
        'truncated_key' => substr($api_data["consumer_key"], -7)
    );

    $response= $wpdb->insert( $wpdb->prefix.'woocommerce_api_keys', $data, array( '%d','%s','%s','%s','%s','%s'));

 
    return $api_data;
}

