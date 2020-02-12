<?php
/*
 * Plugin Name: Woocommerce Coupon Hidden Products
 * Plugin URI: http://gmarkhosting.com
 * Description: A plugin to make coupons usable with other coupons 
 * Version: 1.0.0
 * Author: Duncan I. Mwangi EMAIL: irungu.mwangi2@gmail.com
 * Author URI: http://gmarkhosting.com
 * License: GPL2
 */
add_action( 'woocommerce_coupon_options_usage_restriction', 'wcc_add_hidden_product_fields');
add_action( 'woocommerce_coupon_options_save', 'wcc_save_hidden_product_fields');
add_action( 'woocommerce_before_cart', 'dnc_add_product_to_cart' );
add_action( 'woocommerce_before_cart', 'dnc_remove_addon_products_from_cart2' );
add_action( 'woocommerce_add_to_cart', 'dnc_remove_addon_products_from_cart',10,2); 
require_once 'wc-coupon-multiple-hidden-products.php';
function wcc_add_hidden_product_fields()
{
	echo '<div class="options_group">';
  woocommerce_wp_text_input( array( 'id' => 'wcc_hidden_product_ids', 'label' => __( 'Hidden Product IDs', 'woocommerce' ), 'placeholder' => __( 'Hidden Product IDs', 'woocommerce' ), 'description' => __( 'This field allows you to set a product ID of a product that will be added to the cart when this coupon is applied. If more than one product, they should be comma separated.', 'woocommerce' ), 'data_type' => 'text', 'desc_tip' => true ) );
      echo '</div>';
	echo '<div class="options_group options_groupx">';
  woocommerce_wp_radio( array( 'id' => 'wcc_hidden_product_number_per_order', 'label' => __( 'Hidden Product Maximum Quantity Per Order', 'woocommerce' ), 'options' => array('one'=>'Only One Hidden Product','many'=>'Equal to key products in cart'), 'description' => __( 'This field allows you to set the number of addon products allowed in cart.', 'woocommerce' ), 'data_type' => 'text', 'desc_tip' => true ) );
      echo '</div><style type="text/css">
      	.options_groupx{font-weight: bold;}
      </style>';
	echo '<div class="options_group options_groupk">';
  woocommerce_wp_radio( array( 'id' => 'wcc_hide_double_tripple_offer', 'label' => __( 'Hide Double-Tripple Offer Section', 'woocommerce' ), 'options' => array('yes'=>'YES','no'=>'NO'), 'description' => __( 'This field allows you to set the Double-Tripple offer section visible or hidden in cart.', 'woocommerce' ), 'data_type' => 'text', 'desc_tip' => true ) );
      echo '</div><style type="text/css">
      	.options_groupk{font-weight: bold;}
      </style>';
	/*echo '<div class="options_group options_groupx">';
  woocommerce_wp_radio( array( 'id' => 'wcc_is_add_on_product_only', 'label' => __( 'Is an ADDON product', 'woocommerce' ), 'options' => array('yes'=>'YES','no'=>'NO'), 'description' => __( 'This field allows you to set if a product is an addon product or a normal product. Add-on products cannot be purchased alone in the cart.', 'woocommerce' ), 'data_type' => 'text', 'desc_tip' => true ) );
      echo '</div><style type="text/css">
      	.options_groupx{font-weight: bold;}
      </style>';*/

     
}

function wcc_save_hidden_product_fields($post_id)
{
	$wcc_hidden_product_ids = wc_clean( $_POST['wcc_hidden_product_ids'] );
	$wcc_hidden_product_ids = str_replace(' ','',$wcc_hidden_product_ids);
	update_post_meta( $post_id, 'wcc_hidden_product_ids', $wcc_hidden_product_ids );

	$wcc_hidden_product_number_per_order = wc_clean( $_POST['wcc_hidden_product_number_per_order'] );
	update_post_meta( $post_id, 'wcc_hidden_product_number_per_order', $wcc_hidden_product_number_per_order );
	$wcc_hide_double_tripple_offer = wc_clean( $_POST['wcc_hide_double_tripple_offer'] );
	update_post_meta( $post_id, 'wcc_hide_double_tripple_offer', $wcc_hide_double_tripple_offer );
}
function wcc_hide_double_tripple_offer(){
	global $woocommerce;
	if( !empty($woocommerce->cart->applied_coupons) ) {
    	foreach ($woocommerce->cart->applied_coupons as $coupon_code) {
    		$coupon = new WC_Coupon($coupon_code);
			$wcc_hide_double_tripple_offer = get_post_meta ( $coupon->get_id(), 'wcc_hide_double_tripple_offer', true );
			if(strtoupper($wcc_hide_double_tripple_offer)=='YES'){
				return true;
			}
    	}
    }
    return false;
}

function dnc_add_product_to_cart() {
  
    global $woocommerce;
    $all_products_in_cart = array();
    $hidden_products_in_coupons = array();
    $hidden_products_to_add_to_cart = array();
    if( !empty($woocommerce->cart->applied_coupons) ) {
    	foreach ($woocommerce->cart->applied_coupons as $coupon_id) {
    		$product_ids = wcc_get_hidden_product_ids_array($coupon_id);
    		if(!empty($product_ids)){
		        //check if product already in cart
		        if ( sizeof( $woocommerce->cart->get_cart() ) > 0 ) {
		        	$prods_in_cart = array();
			      	foreach ( $woocommerce->cart->get_cart() as $cart_item_key => $values ) {
			                $_product = $values['data'];
			                $prods_in_cart[]=$_product->get_id();
			                if(!in_array($_product->get_id(), $all_products_in_cart)) $all_products_in_cart[]=$_product->get_id();
			                
			            }
			            foreach ($product_ids as $product_id) {
			            	if(!in_array($product_id, $hidden_products_in_coupons)) $hidden_products_in_coupons[]=$product_id;

			            	if(!in_array($product_id, $prods_in_cart)){
			            		$woocommerce->cart->add_to_cart( $product_id );
			            		$hidden_products_to_add_to_cart[] = $product_id;
			            	}
			            }
		        } else {
		            // if no products in cart, add it
		        	foreach ($product_ids as $product_id) {
		        		//$woocommerce->cart->add_to_cart( $product_id );
		        	}
		                
		        }
	        }
        }
     }else{

     }


    
}
function dnc_remove_addon_products_from_cart($cart_item_key, $product_id){
	global $woocommerce;
	if ( sizeof( $woocommerce->cart->get_cart() ) > 0 ) {
		$prods_in_cart = array();
		$prods_in_cart_items = array();
		$keys = array();
		foreach ( $woocommerce->cart->get_cart() as $cart_item_keyx => $values ) {
		        $_product = $values['data'];
		        $prods_in_cart[]=$_product->get_id();
		        $prods_in_cart_items[$cart_item_keyx]=$_product->get_id();
		        $keys[$_product->get_id()] = $cart_item_keyx;
		}
		$products_never_sold_alone = array(23613,37980);
		$not_sold_together_with = array(21095);
		$has_product_not_sold_together = false;
		$product_not_sold_together = 0;
		foreach ($prods_in_cart as $prod_id_in_cart) {
			if(in_array($prod_id_in_cart, $not_sold_together_with)) {
				$has_product_not_sold_together = true;
				$product_not_sold_together = $prod_id_in_cart;
			}
		}
		if(count($prods_in_cart)==1 && in_array($prods_in_cart[0], $products_never_sold_alone)){
			$cart_item_key = $keys[$prods_in_cart[0]];
			$woocommerce->cart->remove_cart_item($cart_item_key);
			$name = get_the_title($prods_in_cart[0]);
			//dcwfxs_display_msg($msg,2);
			throw new Exception( __( "Sorry, $name has been removed from cart because it is an addon product only. You will need to add other products in cart. ", 'woocommerce' ) );
		}elseif(in_array(21095, $prods_in_cart) && in_array($prods_in_cart_items[$cart_item_key], $products_never_sold_alone)){
			$name = get_the_title(23613);
			throw new Exception( __( "Sorry, $name cannot be added to cart because it is an addon product only.", 'woocommerce' ) );
		}

	}
}
function dnc_remove_addon_products_from_cart2(){
	global $woocommerce;
	if ( sizeof( $woocommerce->cart->get_cart() ) > 0 ) {
		$prods_in_cart = array();
		$keys = array();
		foreach ( $woocommerce->cart->get_cart() as $cart_item_key => $values ) {
		        $_product = $values['data'];
		        $prods_in_cart[]=$_product->get_id();
		        $keys[$_product->get_id()] = $cart_item_key;
		}
		$products_never_sold_alone = array(23613,37980);
		$not_sold_together_with = array(21095);
		$has_product_not_sold_together = false;
		$product_not_sold_together = 0;
		foreach ($prods_in_cart as $prod_id_in_cart) {
			if(in_array($prod_id_in_cart, $not_sold_together_with)) {
				$has_product_not_sold_together = true;
				$product_not_sold_together = $prod_id_in_cart;
			}
		}
		if(count($prods_in_cart)==1 && in_array($prods_in_cart[0], $products_never_sold_alone)){
			$cart_item_key = $keys[$prods_in_cart[0]];
			$woocommerce->cart->remove_cart_item($cart_item_key);
			$name = get_the_title($prods_in_cart[0]);
			$msg = "Sorry, $name has been removed from cart because it is an addon product only. You will need to add other products in cart. ";
			dcwfxs_display_msg($msg,2);
		}elseif(in_array(21095, $prods_in_cart) && in_array(23613, $prods_in_cart)){
			$cart_item_key = $keys[23613];
			$woocommerce->cart->remove_cart_item($cart_item_key);
			$name = get_the_title(23613);
			$msg = "Sorry, $name has been removed from cart because it is an addon product only. ";
			dcwfxs_display_msg($msg,2);
		} 

	}
}

function wcc_get_hidden_product_ids_array($coupon_code){
	$coupon = new WC_Coupon($coupon_code);
	$wcc_hidden_product_ids = get_post_meta ( $coupon->get_id(), 'wcc_hidden_product_ids', true );
	$arr = array();
	$arr = explode(',',strtolower($wcc_hidden_product_ids));
	return $arr;
}

function dcwfxs_display_msg($msg='',$type=1){
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

add_action( 'restrict_manage_posts', 'dnc_filter_by_coupon_type' ); 
function dnc_filter_by_coupon_type(){
	global $typenow;  
	if( $typenow == 'shop_coupon' ){
		?>
		<select name="cptyp" id="dnc_cp_type">
			<option value="">All Coupons</option>
			<option <?php if(isset($_GET['cptyp']) && $_GET['cptyp']=='drb-'): ?> selected="selected" <?php endif;?> value="drb-">Rebate Coupons</option>
			<option <?php if(isset($_GET['cptyp']) && $_GET['cptyp']=='rvsms-'): ?> selected="selected" <?php endif;?> value="rvsms-">Review Coupons</option>
		</select>
		<?php

	}
}

add_filter( 'pre_get_posts', 'dnc_get_posts_filter_by_coupon_type' );
function dnc_get_posts_filter_by_coupon_type( $query ) {
	
if (isset($_GET['cptyp']) && !empty($_GET['cptyp']) && $query->query_vars['post_type']=='shop_coupon') {
	$cptyp = (int)$_GET['cptyp'];
  $query->set( 's', $cptyp);
  }
  return $query;
}