<?php 
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
global $cop_offers_tbl, $cop_offer_coupons_tbl;
$cop_offers_tbl = 'wp_cop_offers'; 
$cop_offer_coupons_tbl = 'wp_cop_offer_coupons';

 add_action('admin_menu', 'cop_register_coupon_offers_admin_pages');
 add_action('woocommerce_payment_complete', 'cop_conversion_coupon_log_usage');

    
    // adds the coupon filtering dropdown to the orders page
    add_action( 'restrict_manage_posts', 'cop_filter_orders_by_coupon_used' );
    
    // makes coupons filterable
    add_filter( 'posts_join',  'cop_add_order_items_join' );
    add_filter( 'posts_where', 'cop_add_filterable_where' );


 
    function cop_filter_orders_by_coupon_used() {

        global $typenow;

        if ( 'shop_order' != $typenow ) {
    
            return;
        }
    
        $args = array(
            'role__in'         => array('hlt_support_reps','administrator'),
        );
    

        $users = get_users( $args );;

        
    
        if ( ! empty( $users ) ) {
        ?>
        
        <select name="s_rep_coupon" id="s_rep_coupon">
            <option value="">Support Rep Offer Coupons</option>
            <?php foreach ( $users as $user ) : 
                    $prefix = substr($user->user_login,0,3).'-';
            ?>
            <option value="<?=urlencode($prefix); ?>" <?php if(!empty( urldecode($_GET['s_rep_coupon']) ) && urldecode($_GET['s_rep_coupon'])==$prefix) echo 'selected="selected"';?>>
                <?=strtoupper($user->user_login); ?>
            </option>
            <?php endforeach; ?>
        </select>
        <?php }
    }
    function cop_add_order_items_join( $join ) {

        global $typenow, $wpdb, $wc_coupon_names;

        if ( 'shop_order' != $typenow ) {
            return $join;
        }
    
        if ( ! empty( $_GET['s_rep_coupon'] ) ) {
            $join .=  "
                LEFT JOIN {$wpdb->prefix}woocommerce_order_items scop ON {$wpdb->posts}.ID = scop.order_id";
        }

        return $join;
    }
    function cop_add_filterable_where( $where ) {
        global $typenow, $wpdb, $wc_coupon_names;
    
        if ( 'shop_order' != $typenow ) {
            return $where;
        }
    
        if ( ! empty( $_GET['s_rep_coupon'] ) ) {
            $s_rep_coupon = wc_clean( urldecode($_GET['s_rep_coupon']) );
            $s_rep_coupon = $s_rep_coupon.'%';
            // Main WHERE query part
            $where .= $wpdb->prepare( " AND scop.order_item_type='coupon' AND scop.order_item_name LIKE '%s'",$s_rep_coupon);
        }
    
        return $where;
    }

 function cop_register_coupon_offers_admin_pages(){
    add_submenu_page( 'hlt-reps', 'Offer Coupons', 'Offer Coupons', 'view_hlt_support_menu', 'hlt-offer-coupons', 'cop_offer_coupons_page' ); 
    add_submenu_page( 'hlt-reps', 'Manage Offers', 'Manage Offers', 'manage_options', 'hlt-manage-offers', 'cop_manage_offers_page' ); 
    cop_install();
 }

 function cop_offer_coupons_page(){
 	global $wpdb,$cop_offers_tbl, $cop_offer_coupons_tbl;
 	$offer = false;
 	if(isset($_GET['id'])){
        $offer_id = (int)$_GET['id'];
        $offer = cop_validate_offer($offer_id);
    }
    $user_id = get_current_user_id();
 	if(isset($_GET['action']) && $_GET['action']=='view' && $offer){
        require_once('views/view-offer-coupons.php');
    }
    else{
        require_once('views/list-coupon-offers.php');
    }
 }

  function cop_manage_offers_page(){
  	global $wpdb,$cop_offers_tbl, $cop_offer_coupons_tbl;
 	$offer = false;
 	if(isset($_GET['id'])){
        $offer_id = (int)$_GET['id'];
        $offer = cop_validate_offer($offer_id);
    }
 	if(isset($_GET['action']) && $_GET['action']=='edit' && $offer){
        require_once('views/edit-offer.php');
    }elseif(isset($_GET['action']) && $_GET['action']=='add'){ 
        require_once('views/add-offer.php');
    }
    else{
        require_once('views/list-offers.php');
    }
 }

 function cop_validate_offer($offer_id=0){
 	global $wpdb,$cop_offers_tbl;
 	return $wpdb->get_row("SELECT * FROM $cop_offers_tbl WHERE id = $offer_id ");
 }
 function cop_conversion_coupon_log_usage($order_id = 0){
	global $wpdb, $woocommerce,$cop_offer_coupons_tbl;
	$order = new WC_Order( $order_id );
	$coupons = $order->get_used_coupons( );
	if(is_array($coupons)){
		foreach($coupons as $coupon){
			$found = $wpdb->get_row("SELECT * FROM $cop_offer_coupons_tbl WHERE coupon_code LIKE '$coupon' ORDER BY id ASC LIMIT 0,1 ");
			if($found) $wpdb->update($cop_offer_coupons_tbl,array('date_used'=>$order->order_date, 'order_id'=>$order_id,'status'=>1),array('id'=>$found->id),array('%s','%s','%s'));
		}
	}
	

}
 function cop_get_unique_coupon_code_string(){
 	global $wpdb;
 	$user = wp_get_current_user();
 	$prefix = substr($user->user_login,0,3).'-';
 	$coupon_code = $prefix.wp_generate_password(10,false);
 	$coupon_code = strtoupper($coupon_code);
 	$exists = $wpdb->get_row("SELECT * FROM wp_posts WHERE post_title LIKE '$coupon_code' ");
 	if($exists) $coupon_code = cop_get_unique_coupon_code_string();
 	return $coupon_code;
 }
 function cop_generate_coupon_code($offer){
 	$coupon_code = cop_get_unique_coupon_code_string();

 	$amount = $offer->amount; // Amount
$discount_type = $offer->discount_type; // Type: fixed_cart, percent, fixed_product, percent_product
					
$coupon = array(
	'post_title' => $coupon_code,
	'post_content' => '',
	'post_status' => 'publish',
	'post_author' => 1,
	'post_type'		=> 'shop_coupon'
);
					
$new_coupon_id = wp_insert_post( $coupon );
					
// Add meta
update_post_meta( $new_coupon_id, 'discount_type', $discount_type );
update_post_meta( $new_coupon_id, 'coupon_amount', $amount );
update_post_meta( $new_coupon_id, 'individual_use', 'no' );
update_post_meta( $new_coupon_id, 'product_ids', $offer->product_ids );
update_post_meta( $new_coupon_id, 'exclude_product_ids', '' );
update_post_meta( $new_coupon_id, 'usage_limit', '1' );
update_post_meta( $new_coupon_id, 'expiry_date', strtotime("+ $offer->days_to_expire days") );
update_post_meta( $new_coupon_id, 'apply_before_tax', 'yes' );
update_post_meta( $new_coupon_id, 'free_shipping', 'no' );

 	return $coupon_code;
 }
 function cop_pagination($link,$total_records,$page){
    global $wpdb, $HLT_ITEMS_PER_PAGE;
    
    $total_pages = ceil($total_records / $HLT_ITEMS_PER_PAGE);
    $first_link = $page!=1 && $total_pages>1 ? $link.'&upg=1':'#';
    $prev_link = $page>2 && $total_pages>2 ? $link.'&upg='.($page-1):'#';
    $next_link = $page<$total_pages && $total_pages>1 ? $link.'&upg='.($page+1):'#';
    $last_link = $page<$total_pages && $total_pages>1 ? $link.'&upg='.($total_pages):'#';
    if($total_pages>1):
    ?>
<div class="tablenav-pages">
    <span class="displaying-num"><?php echo $total_records ?> items</span>
    <span class="pagination-links">
        <a class="first-page <?php if($first_link == '#') echo 'disabled' ?>" title="Go to the first page" href="<?php echo $first_link ?>">&lt;&lt;</a>
        
        <a class="prev-page <?php if($prev_link == '#') echo 'disabled' ?>" title="Go to the previous page" href="<?php echo $prev_link ?>">&lt;</a>
        
        <span class="paging-input">
            <label for="current-page-selector" class="screen-reader-text">Select Page</label>
            <input class="current-page" id="current-page-selector" title="Current page" name="upg" value="<?php echo $page; ?>" size="1" type="text"> of <span class="total-pages"><?php echo $total_pages;?></span>
        </span>
        <a class="next-page <?php if($next_link == '#') echo 'disabled' ?>" title="Go to the next page" href="<?php echo $next_link ?>">&gt;</a>
        
        <a class="last-page <?php if($last_link == '#') echo 'disabled' ?>" title="Go to the last page" href="<?php echo $last_link ?>">&gt;&gt;</a>
    </span>
</div>
    <?php
    endif;
}
function cop_display_msg($msg = '', $type=1){
    $type = $type==1?'updated fade': 'error';
    return '<div class="'.$type.'"><p>'.$msg.'</p></div>';
}
function cop_install() {
	global $wpdb,$cop_offers_tbl, $cop_offer_coupons_tbl;
	$cop_charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $cop_offers_tbl (
		id int(9) NOT NULL AUTO_INCREMENT,
        name varchar(50) NOT NULL,
        discount_type varchar(50) NOT NULL,
        amount decimal(9,2) NOT NULL,
        product_ids text NOT NULL,
		days_to_expire int(9) DEFAULT '7' NOT NULL,
        date_created datetime NOT NULL,
		PRIMARY KEY id (id)
	) $cop_charset_collate; ";

    $sql.="CREATE TABLE $cop_offer_coupons_tbl (
		id int(9) NOT NULL AUTO_INCREMENT,
        offer_id int(9) NOT NULL,        
        user_id int(9) NOT NULL,
		coupon_code varchar(30) NOT NULL,
		order_id varchar(20) NOT NULL,
        status int(9) DEFAULT '0' NOT NULL,
        date_created datetime NOT NULL,
        expiry_date datetime NOT NULL,
        date_used datetime NOT NULL,
		PRIMARY KEY id (id)
	) $cop_charset_collate; ";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
    
}