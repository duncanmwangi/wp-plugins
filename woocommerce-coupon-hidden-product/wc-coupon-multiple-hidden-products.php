<?php
add_action( 'woocommerce_coupon_options_usage_restriction', 'wcc_add_multiple_hidden_product_fields');
add_action( 'woocommerce_coupon_options_save', 'wcc_save_multiple_hidden_product_fields');
add_action( 'woocommerce_before_cart', 'dnc_add_multiple_hidden_product_to_cart' );

//add_action( 'woocommerce_add_to_cart', 'dnc_remove_multiple_addon_products_from_cart',10,2); 
///add_action( 'woocommerce_add_to_cart', 'dnc_remove_addon_products_from_cart_if_coupon_not_found',10,2); 
//add_action( 'woocommerce_before_cart', 'dnc_remove_addon_products_from_cart_if_coupon_not_found_before_cart'); 
add_action('woocommerce_cart_item_removed','dnc_remove_add_on_products_without_correponding_products');

function wcc_add_multiple_hidden_product_fields()
{
	 

      global $thepostid, $post;
      $wcc_multiple_hidden_product_ids = get_post_meta ( $post->ID, 'wcc_multiple_hidden_product_ids', true );
echo '<div class="options_group">';
  woocommerce_wp_textarea_input( array( 'id' => 'wcc_multiple_hidden_product_ids','value'=>$wcc_multiple_hidden_product_ids, 'label' => __( 'Multiple Hidden Product ID Matches', 'woocommerce' ), 'placeholder' => __( 'Multiple Hidden Product ID Matches', 'woocommerce' ), 'description' => __( 'This field allows you to set a product ID Matches of the product that will be added to the cart when this coupon is applied and its matched product exists in cart. If more than one product matches, they should be comma separated. A product can only have one product match. They should be entered in this format: A=>B, where A is the product ID in cart and B is the hidden product ID.', 'woocommerce' ), 'data_type' => 'text', 'desc_tip' => true ) );
      echo '</div>';

     
}

function wcc_save_multiple_hidden_product_fields($post_id)
{
	$wcc_multiple_hidden_product_ids = wc_clean( $_POST['wcc_multiple_hidden_product_ids'] );
	$wcc_multiple_hidden_product_ids = str_replace(' ','',$wcc_multiple_hidden_product_ids);
	update_post_meta( $post_id, 'wcc_multiple_hidden_product_ids', wcc_get_hidden_multiple_product_ids_str($wcc_multiple_hidden_product_ids) );
}

function dnc_add_multiple_hidden_product_to_cart(){
	global $woocommerce;
    if( !empty($woocommerce->cart->applied_coupons) ) {
    	foreach ($woocommerce->cart->applied_coupons as $coupon_id) {
    		$m = wcc_get_hidden_multiple_product_ids_array($coupon_id);
    		if(count($m)>0){
    			if ( sizeof( $woocommerce->cart->get_cart() ) > 0 ) {
		        	$prods_in_cart = array();
		        	$prods_in_cart_qty = array();
		        	$prods_in_cart_key = array();
			      	foreach ( $woocommerce->cart->get_cart() as $cart_item_key => $values ) {

			      		$_product = $values['data'];
			            $prods_in_cart[]=$_product->get_id();
			            $prods_in_cart_qty[$_product->get_id()] = $values['quantity'];
			            $prods_in_cart_key[$_product->get_id()] = $cart_item_key;
			      	}
			      	$matched_in_cart = array();
			      	foreach ($m['cart_products'] as $k => $v) {
			      		if(in_array($v,$prods_in_cart)){
			      			if(in_array($m['match_products'][$k],$prods_in_cart)){
			      				if($prods_in_cart_qty[$m['match_products'][$k]] != $prods_in_cart_qty[$v]){
			      					//change quantity
			      					$woocommerce->cart->remove_cart_item($prods_in_cart_key[$m['match_products'][$k]]);
			      					$woocommerce->cart->add_to_cart( $m['match_products'][$k] ,$prods_in_cart_qty[$v]);
			      				}
			      			}else{
			      				//add product
			      				$woocommerce->cart->add_to_cart( $m['match_products'][$k],$prods_in_cart_qty[$v]);
			      			}
			      			
			      		}
			      	}


			      	



			      }
    		}
    	}
    }
}





function dnc_remove_add_on_products_without_correponding_products(){
	global $woocommerce;
	if( !empty($woocommerce->cart->applied_coupons) ) {
    	foreach ($woocommerce->cart->applied_coupons as $coupon_id) {
    		$m = wcc_get_hidden_multiple_product_ids_array($coupon_id);
    		if(count($m)>0){
				$prods_in_cart = array();
	        	$prods_in_cart_qty = array();
	        	$prods_in_cart_key = array();
		      	foreach ( $woocommerce->cart->get_cart() as $cart_item_key => $values ) {

		      		$_product = $values['data'];
		            $prods_in_cart[]=$_product->get_id();
		            $prods_in_cart_qty[$_product->get_id()] = $values['quantity'];
		            $prods_in_cart_key[$_product->get_id()] = $cart_item_key;
		      	}
		      	foreach ($m['match_products'] as $s => $t) {
		      		if(in_array($t,$prods_in_cart)){
		      			if(!in_array($m['cart_products'][$s],$prods_in_cart)){
		      				$woocommerce->cart->remove_cart_item($prods_in_cart_key[$m['match_products'][$s]]);

		      				$name = get_the_title($m['match_products'][$s]);
	      				
		      				wc_add_notice( __("Sorry, $name has also been removed from the cart because it is an addon product only.", 'woocommerce'), 'error');
		      			}
		      		}
		      	}
		      }
		  }
		}
}

function wcc_get_hidden_multiple_product_ids_array($coupon_code){
	$coupon = new WC_Coupon($coupon_code);
	$wcc_multiple_hidden_product_ids = get_post_meta ( $coupon->get_id(), 'wcc_multiple_hidden_product_ids', true );
	$arr = array();
	$r = array();
	$arr = explode(',',strtolower($wcc_multiple_hidden_product_ids));
	foreach ($arr as $p) {
		$x = explode('=>',strtolower($p));
		if(is_array($x) && count($x)==2){
			$x[0]= (int)$x[0];
			$x[1]= (int)$x[1];
			$r['matches'][] = array('A'=>$x[0],'B'=>$x[1]);
			$r['cart_products'][] = $x[0];
			$r['match_products'][] = $x[1];
		}
	}
	return $r;
}


function wcc_get_hidden_multiple_product_ids_str($str){
	$arr = array();
	$r = array();
	$arr = explode(',',strtolower($str));
	foreach ($arr as $p) {
		$x = explode('=>',strtolower($p));
		if(is_array($x) && count($x)==2){
			$x[0]= (int)$x[0];
			$x[1]= (int)$x[1];
			$r[] = array('A'=>$x[0],'B'=>$x[1]);
		}
	}
	$new_str='';
	if(count($r)>0)
		foreach ($r as $v) {
			if(isset($v['A']) && isset($v['B']))
				$new_str.=$v['A'].'=>'.$v['B'].', ';
		}
	return substr($new_str,0,(strlen($new_str)-2));
}

function dnc_multiple_display_msg($msg='',$type=1){
		if($type==1):
		?>
			<div class="woocommerce">
				<div class="woocommerce-message"><?=$msg?></div>
			</div>
			
		<?php

		else:

			?>
			<div class="woocommerce">
				<ul class="woocommerce-error">
					<li><?=$msg?></li>
				</ul>
			</div>
			
		<?php

		endif;

	}


add_action( 'woocommerce_product_options_general_product_data', 'wc_custom_addon_coupons_fields' );
function wc_custom_addon_coupons_fields() {
    // Print a custom text field
    echo '<div class="options_group options_groupx">';
  woocommerce_wp_radio( array( 'id' => 'dnc_is_addon_product_only', 'label' => __( 'Is AddOn Product Only?', 'woocommerce' ), 'options' => array('YES'=>'YES','NO'=>'NO'), 'description' => __( 'This field allows you to a product an AddOn only product.', 'woocommerce' ), 'data_type' => 'text', 'desc_tip' => true ) );
      echo '</div><style type="text/css">
      	.options_groupx{font-weight: bold;}
      </style>';
    woocommerce_wp_textarea_input( array(
        'id' => 'dnc_addon_allowed_coupons',
        'label' => 'Addon Allowed Coupon Codes',
        'description' => 'This product can only be added into the cart if these coupons are added to the cart. If more than one coupon they should be comma separated.',
        'desc_tip' => 'true',
        'placeholder' => 'Addon Allowed Coupon Codes'
    ) );
}

add_action( 'woocommerce_process_product_meta', 'wc_custom_addon_coupons_save_custom_fields' );
function wc_custom_addon_coupons_save_custom_fields( $post_id ) {
	$dnc_is_addon_product_only = wc_clean( $_POST['dnc_is_addon_product_only'] );
	$dnc_addon_allowed_coupons = wc_clean( $_POST['dnc_addon_allowed_coupons'] );
	$dnc_addon_allowed_coupons = strtolower(str_replace(' ','',$dnc_addon_allowed_coupons));
     update_post_meta( $post_id, 'dnc_addon_allowed_coupons', esc_attr( $dnc_addon_allowed_coupons ) );
     update_post_meta( $post_id, 'dnc_is_addon_product_only', esc_attr( $dnc_is_addon_product_only ) );
}
function dnc_remove_addon_products_from_cart_if_coupon_not_found($cart_item_key, $product_id){
	global $woocommerce;
	$is_add_on = get_post_meta ( $product_id, 'dnc_is_addon_product_only', true )=='YES'?true:false;
	if($is_add_on){
		$allowed_coupons_str = get_post_meta ( $product_id, 'dnc_addon_allowed_coupons', true );
		$allowed_coupons = explode(',',strtolower($allowed_coupons_str));
		$coupon_found = false;
		if(!empty($woocommerce->cart->applied_coupons)){
			foreach ($woocommerce->cart->applied_coupons as $coupon_code){
				$coupon_code==strtolower($coupon_code);
				if(in_array($coupon_code,$allowed_coupons)){
					$coupon_found = true;
				}
			}
		}
		if(!$coupon_found){
			$name = get_the_title($product_id);
		    throw new Exception( __( "Sorry, $name cannot be added to cart because it is an addon product only.", 'woocommerce' ) );
		}
		
	}
}

function dnc_remove_multiple_addon_products_from_cart($cart_item_key, $product_id){
	global $woocommerce;
	if( !empty($woocommerce->cart->applied_coupons) ) {
    	foreach ($woocommerce->cart->applied_coupons as $coupon_id) {
    		$m = wcc_get_hidden_multiple_product_ids_array($coupon_id);
    		if(count($m)>0){
    			$prods_in_cart = array();
	        	$prods_in_cart_qty = array();
	        	$prods_in_cart_key = array();
		      	foreach ( $woocommerce->cart->get_cart() as $cart_item_key => $values ) {

		      		$_product = $values['data'];
		            $prods_in_cart[]=$_product->get_id();
		      	}
		      	foreach ($m['match_products'] as $s => $t) {
		      		if($product_id==$t)
		      		if(in_array($t,$prods_in_cart)){
		      			if(!in_array($m['cart_products'][$s],$prods_in_cart)){
		      				$name = get_the_title($product_id);
		      				throw new Exception( __( "Sorry, $name cannot be added to cart because it is an addon product only.", 'woocommerce' ) );

		      			}
		      		}
		      	}


    		}
    	}
    }
}

function dnc_remove_addon_products_from_cart_if_coupon_not_found_before_cart(){
	global $woocommerce;
    if ( sizeof( $woocommerce->cart->get_cart() ) > 0 ) {
    	
	  	foreach ( $woocommerce->cart->get_cart() as $cart_item_key => $values ) {

	  		$_product = $values['data'];
	        $product_id=$_product->get_id();
	        $is_add_on = get_post_meta ( $product_id, 'dnc_is_addon_product_only', true )=='YES'?true:false;

	        if($is_add_on){
				$allowed_coupons_str = get_post_meta ( $product_id, 'dnc_addon_allowed_coupons', true );
				$allowed_coupons = explode(',',strtolower($allowed_coupons_str));
				$coupon_found = false;
				if(!empty($woocommerce->cart->applied_coupons)){
					foreach ($woocommerce->cart->applied_coupons as $coupon_code){
						$coupon_code==strtolower($coupon_code);
						if(in_array($coupon_code,$allowed_coupons)){
							$coupon_found = true;
						}
					}
				}
				if(!$coupon_found){
					//$woocommerce->cart->remove_cart_item($cart_item_key);
				}
				
			}
	  	}

	  }
}

//New items

function so_validate_add_cart_item( $passed, $product_id, $quantity, $variation_id = '', $variations= '' ) {


    global $woocommerce;
	$is_add_on = get_post_meta ( $product_id, 'dnc_is_addon_product_only', true )=='YES'?true:false;
	if($is_add_on){
		$allowed_coupons_str = get_post_meta ( $product_id, 'dnc_addon_allowed_coupons', true );
		$allowed_coupons = explode(',',strtolower($allowed_coupons_str));
		$coupon_found = false;
		if(!empty($woocommerce->cart->applied_coupons)){
			foreach ($woocommerce->cart->applied_coupons as $coupon_code){
				$coupon_code==strtolower($coupon_code);
				if(in_array($coupon_code,$allowed_coupons)){
					$coupon_found = true;
				}
			}
		}
		if(!$coupon_found){
		    $passed = false;
		    $name = get_the_title($product_id);
        	wc_add_notice( __("Sorry, $name cannot be added to cart because it is an addon product only.", 'woocommerce'), 'error');
		}
		
	}
	return $passed;

}
add_filter( 'woocommerce_add_to_cart_validation', 'so_validate_add_cart_item', 10, 5 );

add_action('woocommerce_check_cart_items', 'validate_all_cart_contents');

function validate_all_cart_contents(){
	global $woocommerce;
    if ( sizeof( $woocommerce->cart->get_cart() ) > 0 ) {
    	
	  	foreach ( $woocommerce->cart->get_cart() as $cart_item_key => $values ) {

	  		$_product = $values['data'];
	        $product_id=$_product->get_id();
	        $is_add_on = get_post_meta ( $product_id, 'dnc_is_addon_product_only', true )=='YES'?true:false;

	        if($is_add_on){
				$allowed_coupons_str = get_post_meta ( $product_id, 'dnc_addon_allowed_coupons', true );
				$allowed_coupons = explode(',',strtolower($allowed_coupons_str));
				$coupon_found = false;
				if(!empty($woocommerce->cart->applied_coupons)){
					foreach ($woocommerce->cart->applied_coupons as $coupon_code){
						$coupon_code==strtolower($coupon_code);
						if(in_array($coupon_code,$allowed_coupons)){
							$coupon_found = true;
						}
					}
				}
				if(!$coupon_found){
					$woocommerce->cart->remove_cart_item($cart_item_key);
					$name = get_the_title($product_id);
        			wc_add_notice( __("Sorry, $name cannot be added to cart because it is an addon product only.", 'woocommerce'), 'error');
				}
				
			}
	  	}

	  }
}