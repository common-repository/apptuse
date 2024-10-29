<?php

add_action( 'template_redirect', 'apptuse_product_info' );


/**Returns json of individual product id **/
function apptuse_product_info(){
	global $wpdb;
	global $woocommerce;
	
	$product_id = '';

	if (!empty($_GET['apptuse_product_id'])) {

		if($_GET['token'] !== get_option('apptuse_token')){
			echo json_encode(array('success'=>false,'message'=>'Invalid Authentication Key'));
			exit;
			
		}

		if(!filter_var($_GET['apptuse_product_id'], FILTER_VALIDATE_INT)){
			echo json_encode(array('success'=>false,'message'=>'Invalid Product id'));
			exit;
			
		}else{

			$product_id = absint(filter_var(intval($_GET['apptuse_product_id']), FILTER_SANITIZE_NUMBER_INT));
		}
		

		$product_data =array();
	

		$temp_array = array();	

		$product = wc_get_product( $product_id );

		$temp_array ['product_id']= $product_id;
		$temp_array ['name']= $product->get_name(); 
		$temp_array ['description']= $product->get_description(); 
		$temp_array ['image']= '';
		$temp_array ['other_images']= '';

		$temp_array ['price']= '';
		$temp_array ['special']= '';
		$temp_array ['best_price']= '';
		$temp_array ['quantity']= '';

		$temp_array['options']='';			
		$temp_array ['variants']= '';

		$temp_array ['optionscount']= '';
		$temp_array ['variantscount']= '';

		$temp_array['brand']='';	
		$temp_array['product_code']='';	
		$temp_array['attribute_groups']='';	

		$temp_array ['availability']= '';
		$temp_array ['shareurl']= $product->get_permalink();

		$temp_array['date_added']= $product->get_date_created()->format('Y-m-d\TH:i:s\Z');
		$temp_array['date_modified']= $product->get_date_modified()->format('Y-m-d\TH:i:s\Z');
		$temp_array['call_for_pricing']= 0;

		//Image

		$image_url = wp_get_attachment_image_src( get_post_thumbnail_id( $product_id), 'single-post-thumbnail' );

		$temp_array ['image']= $image_url[0];

		//others_image

		$other_images = array();


		$other_images[] = $image_url[0];

        if(!empty($product->get_gallery_image_ids())){

            foreach ($product->get_gallery_image_ids() as $image) {

	            if(!(wp_get_attachment_url($image) == $image_url[0])){
	            	
	            	$other_images[] = wp_get_attachment_url($image); 
	            }
            }
        }
		
 		$temp_array['other_images']=$other_images;


		//Price


		$price='';
		$sale_price ='';
		$best_price = '';

		if(strlen($product->get_regular_price()) == 0 ){

			$temp_array ['price']= $product->get_price();
			$price = $product->get_price();
		
		}else {
		
			if(strlen($product->get_sale_price()) != 0) { 
				$temp_array['special'] = $product->get_sale_price();
				$sale_price = $product->get_sale_price();
			}
		
			$temp_array ['price']= $product->get_regular_price();
			$price = $product->get_regular_price();
		}

		$temp_array['best_price'] = $product->get_price();
		$best_price = $product->get_price();


		//Quantity
		$quantity= '';
		if($product->is_in_stock( )){
			$temp_array ['availability']= '1';
			if(empty($product->get_stock_quantity())){
				$temp_array['quantity'] = 1000;
				$quantity = 1000;
			}else{
				$temp_array['quantity'] = $product->get_stock_quantity();
				$quantity = $product->get_stock_quantity();
			}
			
		}else { 
			$temp_array['quantity'] = 0;
			$temp_array ['availability']= '0';
			$quantity = 0;
		}

		/*options count*/

		$option_array = array();
		$option = $product->get_attributes();

		$temp_array ['optionscount']= count($option);	
			
		if(!empty($option)){	
			$optpos=1;
			foreach ($option as $key => $value) {
				$temp_option_array = array();
				$temp_option_array['id']=  $product_id.sprintf("%02u",$value->get_position( )+1) ;
				$temp_option_array['name']=wc_attribute_label($value->get_name());
				$temp_option_array['position']=$optpos++;
				$temp_option_array['product_id']=$product_id;

				$option_array[]=$temp_option_array;
			}
		}else{
			$temp_options['id'] = $product_id."173";
        	$temp_options['name'] = 'Title';
        	$temp_options['position'] = "1";
        	$temp_options['product_id'] = $product_id;
	       	$option_array[]=$temp_options;
		}

		$temp_array['options'] = $option_array;


		/*variants*/

		if($product->get_type() == "variable"){

			$variations=$product->get_children();	

			$temp_array['variantscount'] = count($variations);

			$option_counter =0;
			foreach ($variations as $value) {
				$option_counter ++;

				$single_variation=new WC_Product_Variation($value);

				$current_variant['product_id'] = $product_id;
			    $current_variant['variant_id'] = $value;
	        	$current_variant['title'] = implode(" / ", $single_variation->get_variation_attributes());
	            $current_variant['options'] = explode("/", $current_variant['title'] );
		        
		            
	            $other_values_array =array();
	            $data = $single_variation->get_variation_attributes();
	            
	            foreach( $data as $k => $v) {
	            	$other_values_array[]=$k.':'.$v;
	            }
		        $current_variant['other_values'] = $other_values_array;    
		        
		        $current_variant['price'] = $single_variation->get_regular_price();
	            $current_variant['special'] = $single_variation->get_sale_price();

	            $temp_quantity='';
				//Quantity
				if($product->is_in_stock( )){
					if(empty($product->get_stock_quantity())){
						$temp_quantity = 1000;
					}else{
						$temp_quantity = $product->get_stock_quantity();
					}
					
				}else { 
					$temp_quantity = 0;
				}

	            $current_variant['quantity'] = $temp_quantity;
            	$current_variant['position'] = $option_counter;

            	//Price

				
				if($option_counter == 1){						
					$temp_array['price'] = $single_variation->get_regular_price();
            		$temp_array['special'] = $single_variation->get_sale_price();
            		$temp_array['best_price'] = $single_variation->get_price();
            		$temp_array['quantity'] = $temp_quantity;
				} 

				$temp_array['variants'][] = $current_variant;
			}
		}else{
			//non variable data

			$current_variant['product_id'] = $product_id;
	        $current_variant['variant_id'] = $product_id;
    		$current_variant['title'] = "Default Title";
        	$current_variant['options'] = array("Default Title");
            $current_variant['other_values'] = array();
            $current_variant['price'] = $price;
            $current_variant['special'] = $sale_price;
            $current_variant['quantity'] = $quantity;
        	$current_variant['position'] = 1;

            $temp_array['variants'][] = $current_variant;

            $temp_array['price'] = $price;
            $temp_array['special'] = $sale_price;
            $temp_array['best_price'] = $best_price;
            $temp_array['quantity'] = $quantity;

            $temp_array['variantscount'] = 1;

		}
		
 		echo json_encode($temp_array);
 		exit;
 	}

}
