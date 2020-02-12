<?php
/*
 * Plugin Name: Woocommerce Coupon Conjunctions
 * Plugin URI: http://gmarkhosting.com
 * Description: A plugin to make coupons usable with other coupons 
 * Version: 1.0.0
 * Author: Duncan I. Mwangi EMAIL: irungu.mwangi2@gmail.com
 * Author URI: http://gmarkhosting.com
 * License: GPL2
 */
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
add_action('init','ccj_create_tables');
add_action( 'woocommerce_coupon_options_usage_restriction', 'wcc_add_conjuction_fields');
add_action( 'woocommerce_coupon_options_save', 'wcc_save_conjuction_fields');
add_action('wp_loaded','wcc_apply_coupon',30);
add_action('woocommerce_add_to_cart','wcc_apply_coupon');

function wcc_apply_coupon(){
	if(isset($_GET['wcc_code']) && !empty($_GET['wcc_code'])){
		global $woocommerce;
		$code = sanitize_text_field($_GET['wcc_code']);
		if ( $woocommerce->cart->has_discount( $code ) ) return;
		$woocommerce->cart->add_discount( $code );
	}

}
function wcc_add_conjuction_fields(){
	global $thepostid, $post;
echo '<div class="options_group">';
  woocommerce_wp_textarea_input( array( 'id' => 'wcc_coupon_conjunction','value'=>ccj_get_coupon_conjunctions_array($post->post_title,false), 'label' => __( 'Coupon Conjunction', 'woocommerce' ), 'placeholder' => __( 'Coupon Conjunction', 'woocommerce' ), 'description' => __( 'This field allows you to set a coupon that can be used together with this coupon. If more than one coupon, they should be comma separated.', 'woocommerce' ), 'data_type' => 'text', 'desc_tip' => true ) );
      echo '</div>';
}
function wcc_save_conjuction_fields($post_id){
	$wcc_coupon_conjunction = wc_clean( $_POST['wcc_coupon_conjunction'] );
	$wcc_coupon_conjunction = str_replace(' ','',$wcc_coupon_conjunction);
	update_post_meta( $post_id, 'wcc_coupon_conjunction', $wcc_coupon_conjunction );

	ccj_save_coupon_conjunctions($post_id,$wcc_coupon_conjunction);
	//dnc_get_all_sanitized_coupon_conjunctions($post_id);
}

add_filter( 'woocommerce_apply_with_individual_use_coupon', 'wcc_filter_coupon_conjunction_apply_with', 99, 4 );

function wcc_filter_coupon_conjunction_apply_with($bool, $the_coupon, $coupon, $applied_coupons){
	//check if thecoupon has counjunction set
	$the_coupon_conjunctions = wcc_get_conjunctions_array ( $the_coupon->get_id() );
	$coupon_conjunctions = wcc_get_conjunctions_array ( $coupon->get_id() );
	if(in_array(strtolower($coupon->get_code()),$the_coupon_conjunctions) && in_array(strtolower($the_coupon->get_code()),$coupon_conjunctions) ){
		
		$bool = true;
	}
	return $bool; 
	
}

add_filter( 'woocommerce_apply_individual_use_coupon', 'wcc_filter_coupon_conjunction', 99, 3 );

function wcc_filter_coupon_conjunction($arr, $the_coupon, $applied_coupons){
	//check if thecoupon has counjunction set
	$the_coupon_conjunctions = wcc_get_conjunctions_array ( $the_coupon->get_id() );
	$arr = array();
	foreach($applied_coupons as $code){
		 $coupon = new WC_Coupon( $code );
		$coupon_conjunctions = wcc_get_conjunctions_array ( $coupon->get_id() );
		if(in_array(strtolower($coupon->get_code()),$the_coupon_conjunctions) && in_array(strtolower($the_coupon->get_code()),$coupon_conjunctions) ){

			$arr[] = $code;
		}
	}
	
	return $arr;
	
}
function wcc_get_conjunctions_array($coupon_id){
	$post = get_post( $coupon_id );
    	$coupon_code = strtolower($post->post_title);
    	return ccj_get_coupon_conjunctions_array($coupon_code);
}


function dnc_get_all_sanitized_coupon_conjunctions($post_id){
	global $wpdb;
	$coupon_ids = array($post_id);
	$date_two_months_ago = date("Y-m-d", strtotime("-2 month"));
	foreach ($coupon_ids as $coupon_id) {
		$coupon_conjunctions = get_post_meta ( $coupon_id, 'wcc_coupon_conjunction', true );
		$coupon_conjunctions_arr = explode(',',strtolower($coupon_conjunctions));
		$coupon_conjunctions_arr_new = array();
		foreach ($coupon_conjunctions_arr as $coupon_conjunction_code) {
			//check if rebate coupon is used
			$found = $wpdb->get_row("SELECT * FROM wp_DRP_rebates_tbl WHERE coupon_code LIKE '$coupon_conjunction_code' AND ( coupon_status = 'USED' OR  date_created < '$date_two_months_ago')");
			if(!$found){
				$coupon_conjunctions_arr_new[] = strtoupper($coupon_conjunction_code);
			}
		}
		
		$coupon_conjunctions_arr_new_str = implode(',', $coupon_conjunctions_arr_new);
		update_post_meta( $coupon_id, 'wcc_coupon_conjunction', $coupon_conjunctions_arr_new_str );
		
	}
} 


	function ccj_create_tables(){ 
        global $wpdb;
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        
        $sql = "CREATE TABLE IF NOT EXISTS dnc_woocommerce_coupon_conjunctions_tbl (
        id int(11) NOT NULL AUTO_INCREMENT,
        coupon_code varchar(30) NOT NULL,
        conjunction_coupon_code varchar(30) NOT NULL,
        date_added datetime NOT NULL,
        PRIMARY KEY id (id),
        UNIQUE KEY conjunction (coupon_code,conjunction_coupon_code)
        );
         ";
        dbDelta( $sql );
    }
    function ccj_save_coupon_conjunctions($coupon_id,$coupon_conjunctions_str){
    	global $wpdb;
    	$time = current_time('mysql');
    	$coupon_conjunctions = array_unique(explode(',', $coupon_conjunctions_str));
    	$post = get_post( $coupon_id );
    	$coupon_code = strtolower($post->post_title);
    	$wpdb->get_results("DELETE FROM dnc_woocommerce_coupon_conjunctions_tbl WHERE coupon_code LIKE '$coupon_code' ");
    	if(is_array($coupon_conjunctions) && count($coupon_conjunctions)>0){
    		foreach ($coupon_conjunctions as $conjunction_coupon_code) {
    			$conjunction_coupon_code = strtolower($conjunction_coupon_code);
    			if(!empty($conjunction_coupon_code)){
    				@$wpdb->insert('dnc_woocommerce_coupon_conjunctions_tbl',array('coupon_code'=>$coupon_code, 'conjunction_coupon_code'=>$conjunction_coupon_code,'date_added'=> $time),array('%s','%s','%s'));
    			}
    			
    			
    		}
    	}
    }
    function ccj_add_new_coupon_conjunction($coupon_code,$coupon_conjunction_code){
    	global $wpdb;
    	$time = current_time('mysql');
    	$coupon_code = strtolower($coupon_code);
    	$coupon_conjunction_code = strtolower($coupon_conjunction_code);
    	if(!empty($coupon_conjunction_code) && !empty($coupon_code)){
            $found = $wpdb->get_results("SELECT * FROM dnc_woocommerce_coupon_conjunctions_tbl WHERE coupon_code LIKE '$coupon_code' AND conjunction_coupon_code LIKE '$coupon_conjunction_code' ");
            if(!$found){
                @$wpdb->insert('dnc_woocommerce_coupon_conjunctions_tbl',array('coupon_code'=>$coupon_code, 'conjunction_coupon_code'=>$coupon_conjunction_code,'date_added'=> $time),array('%s','%s','%s'));
            }
		
	   }
    }
    function ccj_get_coupon_conjunctions_array($coupon_code,$array=true){
    	global $wpdb;
    	$coupon_code = strtolower($coupon_code);
    	$coupon_conjunctions = array();
    	$sql = "SELECT * FROM dnc_woocommerce_coupon_conjunctions_tbl WHERE coupon_code = '$coupon_code' ORDER BY id ASC ";
    	$codes = $wpdb->get_results($sql);
    	if($codes){
    		foreach ($codes as $code) {

    			$coupon_conjunctions[] = $code->conjunction_coupon_code ;
    		}
    	}
    	return $array ? $coupon_conjunctions : ccj_get_coupon_conjunctions_str($coupon_conjunctions) ; 
    }

    function ccj_get_coupon_conjunctions_str($coupon_conjunctions_codes_array){
    	return implode(',', $coupon_conjunctions_codes_array);
    } 