<?php

add_action( 'template_redirect', 'apptuse_store_data' );

/*
	Fetch all product data API
*/
function apptuse_store_data(){
	global $wpdb;
	global $woocommerce;

	if (!empty($_GET['apptuse_pull'])) {

		if($_GET['token'] !== get_option('apptuse_token')){
			echo json_encode(array('success'=>false,'message'=>'Invalid Authentication Key'));
			exit;
			
		}
		
		$all_product_data = $wpdb->get_results("SELECT * FROM `" . $wpdb->prefix . "posts` where post_type='product' and post_status = 'publish'");

		$data = array();

		$product_array = array();

		$category_array= apptuse_get_category_data();

		$currency  = get_option('woocommerce_currency');

		$data['currency'] = $currency;
		$data['category'] = $category_array;
		$data['products'] ='';


		foreach ($all_product_data as $key => $value) {
			$temp_array = array();	

			$product = wc_get_product( $value->ID );

			$temp_array ['product_id']= $value->ID;
			$temp_array ['name']= $value->post_title; 
			$temp_array ['image']= '';
			$temp_array ['price']= '';
			$temp_array ['special']= '';
			$temp_array ['best_price']= '';
			$temp_array ['quantity']= '';
			$temp_array ['category']= '';
			$temp_array ['variant_id']= '';
			$temp_array ['has_variants']= '';
			$temp_array['call_for_pricing']= 0;
			$temp_array['date_added']= strtotime($value->post_date);

			
			//Price
			if(strlen($product->get_regular_price()) == 0 ){

				$temp_array ['price']= $product->get_price();
			
			}else {
			
				if(strlen($product->get_sale_price()) != 0) { 
					$temp_array['special'] = $product->get_sale_price();
				}
			
				$temp_array ['price']= $product->get_regular_price();
			}

			$temp_array['best_price'] = $product->get_price();
			
			//Quantity
			if($product->is_in_stock( )){
				if(empty($product->get_stock_quantity())){
					$temp_array['quantity'] = '-1';
				}else{
					$temp_array['quantity'] = $product->get_stock_quantity();
				}
				
			}else { 
				$temp_array['quantity'] = 0;
			}

			//Image

			$image_url = wp_get_attachment_image_src( get_post_thumbnail_id( $value->ID ), 'single-post-thumbnail' );
			$temp_array ['image']= $image_url[0];
			
			

			//variants 
		
			if($product->get_type() == "variable"){
				$var_array = $product->get_visible_children();

				$temp_array ['variant_id']= $var_array[0];
				$temp_array ['has_variants']= true;
			}else{
				$temp_array ['variant_id']= $value->ID;
				$temp_array ['has_variants']= false;
			}

			//categories
			$terms = get_the_terms( $value->ID, 'product_cat' );
				
			if(empty($terms)){
					$temp_array ['category']= ':-1:';
			}else {
				$category_id = ':';
				foreach ($terms as $term) {
				    $product_cat_id = $term->term_id;
				    $category_id .= $product_cat_id.":";
				}


				$temp_array ['category']= $category_id;

			}
			
			$product_array[]= $temp_array; 
		}
			
		$data['products']= $product_array; 
		echo json_encode($data);
		exit;
	}
}


function apptuse_get_category_data(){
	$taxonomy     = 'product_cat';
	$orderby      = 'name';  
	$show_count   = 0;      // 1 for yes, 0 for no
	$pad_counts   = 0;      // 1 for yes, 0 for no
	$hierarchical = 1;      // 1 for yes, 0 for no  
	$title        = '';  
	$empty        = 0;

  	$args = array(
         'taxonomy'     => $taxonomy,
         'orderby'      => $orderby,
         'show_count'   => $show_count,
         'pad_counts'   => $pad_counts,
         'hierarchical' => $hierarchical,
         'title_li'     => $title,
         'hide_empty'   => $empty
  	);
	
	$all_categories = get_categories( $args );

    $categoryData = array();
    foreach ($all_categories as $category) {
        $each_category = array();
        $each_category['category_id'] = $category->term_id;
        $each_category['parent_id'] = $category->category_parent;
        $each_category['name'] = $category->name;
        $categoryData[] = $each_category;
    }

    $each_category['category_id'] = '-1';
    $each_category['parent_id'] = '0';
    $each_category['name'] = 'Uncategorized';
    $categoryData[] = $each_category;

	return $categoryData;	
}



