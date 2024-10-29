<?php

add_action( 'template_redirect', 'apptuse_checkout' );

/**
*Capture Url if atinit is present add items in cart
*/
function apptuse_checkout() {
	global $wpdb;
	global $woocommerce;
  
 	$redirect = 0;

	if (!empty($_GET['apptuse_cart'])) {

		$woocommerce->cart->empty_cart();

   		$cart_data = json_decode(urldecode(stripslashes($_GET['apptuse_cart'])),true);	

		foreach($cart_data as $data){

			$product_id   = 0;
			$quantity     = 0;
			$variation_id = 0;
			$variation_params = array();


			foreach($data as $index=>$value)
			{
				switch($index)
				{
					case "pid":

							if(!filter_var($value, FILTER_VALIDATE_INT)){
								apptuse_redirect_checkout();
							}		

							$product_id  = absint(filter_var(intval($value), FILTER_SANITIZE_NUMBER_INT));
						break;

					case "q":
						if(!filter_var($value, FILTER_VALIDATE_INT)){
							apptuse_redirect_checkout();
						}

						$quantity = absint(filter_var(intval($value), FILTER_SANITIZE_NUMBER_INT));
						break;

					case "vid":

						if(!filter_var($value, FILTER_VALIDATE_INT)){
								apptuse_redirect_checkout();
						}
						
						$variation_id = absint(filter_var(intval($value), FILTER_SANITIZE_NUMBER_INT));


						break;	

					default:
						$variation_params[$index] = $value;	
				}

				
			}
			
		

			if($variation_id == 0 ){
				$woocommerce->cart->add_to_cart($product_id,$quantity);	
			}else {
				$woocommerce->cart->add_to_cart($product_id,$quantity,$variation_id);	
			}

			$redirect = 1;

	   	} 
	  	
	  	if ($redirect == 1) {

			apptuse_redirect_checkout();			
	  	}

	}
}

function apptuse_redirect_checkout(){
		
		global $woocommerce;

		$checkout_url = $woocommerce->cart->get_cart_url();
		wp_redirect( $checkout_url );
	exit;
}