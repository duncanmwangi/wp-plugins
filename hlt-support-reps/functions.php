<?php 
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
function hlt_reps_create_user_roles(){
    $administrator = get_role( 'administrator' );
    $administrator->add_cap('view_hlt_support_menu');
    remove_role( 'hlt_support_reps' );
    $result = add_role(
        'hlt_support_reps',
        __( 'HLT Support Rep' ),
        array(
            'read'         => true,  // true allows this capability
            'edit_posts'   => true,
            'delete_posts' => false,
        )
    );
    $hlt_support_reps = get_role( 'hlt_support_reps' );
    $hlt_support_reps->add_cap('view_hlt_support_menu');
}

function hlt_reps_register_admin_pages() {
    $page1 = add_menu_page( 'HLT SUPPORT REP', 'HLT SUPPORT REP', 'view_hlt_support_menu', 'hlt-reps', 'view_hlt_support_page','dashicons-admin-generic', 8 );
    $page2 = add_submenu_page( 'hlt-reps', 'All Orders', 'All Orders', 'view_hlt_support_menu', 'hlt-reps', 'view_hlt_support_page' );
   add_action( 'admin_print_styles-' . $page2, 'hlt_reps_add_css' );
   //add_action( 'admin_print_styles-' . $page3, 'hlt_reps_add_css' );
    add_action( 'admin_enqueue_scripts', 'enqueue_hlt_reps_js' );
}
function hlt_reps_add_css(){
    wp_register_style( 'hlt_reps',  plugin_dir_url( __FILE__ ) . 'styles.css' );
    wp_enqueue_style( 'hlt_reps' );
}
function hlt_reps_display_msg($msg = '', $type=1){
    $type = $type==1?'updated fade': 'error';
    return '<div class="'.$type.'"><p>'.$msg.'</p></div>';
}
function enqueue_hlt_reps_js(){
                wp_enqueue_script(
			'hlt-reps-js', 
			plugin_dir_url( __FILE__ ) .'hlt_reps.js', 
			array('jquery', 'jquery-ui-core', 'jquery-ui-datepicker'),
			time(),
			true
		);	
        wp_enqueue_script(
			'jquery-autocomplete', 
			plugin_dir_url( __FILE__ ) .'jquery.autocomplete.min.js', 
			array('jquery'),
			time(),
			true
		);

		wp_enqueue_style( 'jquery-ui-datepicker' );
}
function ult_hlt_order_page(){
    global $woocommerce;
    $action = 'add';
    if(isset($_POST['action'])){
        $action = $_POST['action'];
    }
    else{
        if(isset($_SESSION['this_order_id'])){
           // $order_id = $_SESSION['this_order_id'];
        }
        if(isset($_SESSION['this_order_page'])){
          //  $action = $_SESSION['this_order_page'];
        }
        
    }
    $action = strtolower($action);
    require_once('views/order.php');
    switch($action){
        case 'update cart': require_once('views/cart.php'); break;
        case 'proceed to billing': require_once('views/billing.php'); break;
        case 'back to billing': require_once('views/billing.php'); break;
        case 'apply coupon': require_once('views/cart.php'); break;
        case 'back to cart': require_once('views/cart.php'); break;
        case 'proceed to checkout': require_once('views/checkout.php'); break;
        case 'place order': require_once('views/thankyou.php'); break;
        default: require_once('views/cart.php'); break;
        
    }

    
}
function is_product_in_cart($order,$product_id){
    global $woocommerce;
	//return false;
	foreach( $order->get_items() as $item_id => $item ){
		if( $product_id == $item['product_id'] ) {
			return $item_id;
		}
	}
    return false;
	
}
function hlt_apply_coupon_code($order,$coupon_code){
    global $woocommerce, $wpdb,$ult_woocommerce_order_items_tbl,$ult_woocommerce_order_itemmeta_tbl;
    $order_id = $order->id;
    $the_coupon = new WC_Coupon($coupon_code);
    if($the_coupon->is_valid()){
        
        $coupons =$order->get_used_coupons();
        if(!in_array($coupon_code,$coupons) || true){
            
            $total = 0;
            foreach($order->get_items() as $item_id => $item){
                $product_id = $item['product_id'];
                
                $product = new WC_Product($product_id);
                //if($the_coupon->is_valid_for_product($product)){
                    $prod_price = $item['line_subtotal'];
                    $discount_amount = 0;
                    if ( in_array($the_coupon->discount_type, array( 'percent_product', 'percent' ) ) ) {
                        $discount_amount = ( $prod_price / 100 )*$the_coupon->coupon_amount;
                    }
                    $line_total = $prod_price-$discount_amount;
                    $wpdb->update( $ult_woocommerce_order_itemmeta_tbl, 
                    	array('meta_value' => number_format($line_total,2)), 
                        array('order_item_id' => $item_id,'meta_key'=>'_line_total'),
                    	array('%f'));
                    $total+=$discount_amount;
                //}
            }
            $already_added = $wpdb->get_row("SELECT * FROM $ult_woocommerce_order_items_tbl WHERE order_item_name = '$coupon_code' AND  order_id = $order_id AND order_item_type = 'coupon' ");
            if($already_added){
                $item_idx = $already_added->order_item_id;
            }else{
                $item_idt = $wpdb->insert( $ult_woocommerce_order_items_tbl, 
                    	array( 'order_item_name' => $coupon_code, 'order_item_type' => 'coupon','order_id' => $order_id ), 
                    	array( '%s', '%s', '%d' ) );
                if($item_idt) $item_idx = $wpdb->insert_id;
            }
            
            if($item_idx){
                $wpdb->insert( $ult_woocommerce_order_itemmeta_tbl, 
                    	array( 'order_item_id' => $item_idx, 'meta_key' => 'discount_amount','meta_value' => number_format($total,2) ), 
                    	array( '%d', '%s', '%s' ) );
                $wpdb->insert( $ult_woocommerce_order_itemmeta_tbl, 
                    	array( 'order_item_id' => $item_idx, 'meta_key' => 'discount_amount_tax','meta_value' => 0 ), 
                    	array( '%d', '%s', '%s' ) );
                        $order->set_total( $total,'cart_discount' );
                        
            }
            $order->calculate_totals();
        }else{
            //coupon already applied
        }
        
    }else{
        //coupon not valid
    }
    //check if coupon is applied
    //if applied check through
    //if not applied continue
    //check if coupon is valid for product and create that entry
    //create total discount entry
}

function hlt_apply_coupons($order_id){
    global $woocommerce, $wpdb,$ult_woocommerce_order_items_tbl,$ult_woocommerce_order_itemmeta_tbl;
    $order = new WC_Order($order_id);
    $coupons =$order->get_used_coupons();
    foreach($coupons as $coupon_code){
            $the_coupon = new WC_Coupon($coupon_code);
            $total = 0;
            foreach($order->get_items() as $item_id => $item){
                $product_id = $item['product_id'];
                
                $product = new WC_Product($product_id);
                //if($the_coupon->is_valid_for_product($product)){
                    $prod_price = $item['line_subtotal'];
                    $discount_amount = 0;
                    if ( in_array($the_coupon->discount_type, array( 'percent_product', 'percent' ) ) ) {
                        $discount_amount = ( $prod_price / 100 )*$the_coupon->coupon_amount;
                        echo $item_id;
                        echo '-';
                    }
                    $line_total = $prod_price-$discount_amount;
                    $wpdb->update( $ult_woocommerce_order_itemmeta_tbl, 
                    	array('meta_value' => number_format($line_total,2)), 
                        array('order_item_id' => $item_id,'meta_key'=>'_line_total'),
                    	array('%f'));
                    $total+=$discount_amount;
                //}
            }
            $already_added = $wpdb->get_row("SELECT * FROM $ult_woocommerce_order_items_tbl WHERE order_item_name = '$coupon_code' AND  order_id = $order_id AND order_item_type = 'coupon' ");
            if($already_added){
                $item_idx = $already_added->order_item_id;
                $wpdb->update( $ult_woocommerce_order_itemmeta_tbl, 
                    	array( 'meta_value' => number_format($total,2) ), array('order_item_id' => $item_idx, 'meta_key' => 'discount_amount'),
                    	array( '%s') );
                $wpdb->update( $ult_woocommerce_order_itemmeta_tbl, 
                    	array( 'meta_value' => number_format(0,2) ), array('order_item_id' => $item_idx, 'meta_key' => 'discount_amount_tax'),
                    	array( '%s') );
            }else{
                $item_idt = $wpdb->insert( $ult_woocommerce_order_items_tbl, 
                    	array( 'order_item_name' => $coupon_code, 'order_item_type' => 'coupon','order_id' => $order_id ), 
                    	array( '%s', '%s', '%d' ) );
                if($item_idt) $item_idx = $wpdb->insert_id;
                $wpdb->insert( $ult_woocommerce_order_itemmeta_tbl, 
                    	array( 'order_item_id' => $item_idx, 'meta_key' => 'discount_amount','meta_value' => number_format($total,2) ), 
                    	array( '%d', '%s', '%s' ) );
                $wpdb->insert( $ult_woocommerce_order_itemmeta_tbl, 
                    	array( 'order_item_id' => $item_idx, 'meta_key' => 'discount_amount_tax','meta_value' => number_format(0,2) ), 
                    	array( '%d', '%s', '%s' ) );
            }
            $order->set_total( $total,'cart_discount' );
            
            if($item_idx){
                
                        
            }
        }
        $order->calculate_totals();
}

function hlt_remove_product_from_order($item_id=0){
    global $woocommerce, $wpdb,$ult_woocommerce_order_items_tbl,$ult_woocommerce_order_itemmeta_tbl;
    $wpdb->delete($ult_woocommerce_order_items_tbl, array('order_item_id' => $item_id));
    $wpdb->delete($ult_woocommerce_order_itemmeta_tbl, array('order_item_id' => $item_id)); 
}
function hlt_reps_add_special_offer_field(){
    add_meta_box(
			'hlt_reps_special_offer',
			'Special / Regular Product',
			'hlt_reps_add_special_offer_field_html',
			'product',
            'side',
            'high'
		);
}
function hlt_reps_add_special_offer_field_html($post){
	$value = get_post_meta( $post->ID, '_hlt_reps_special_product_fld', true );

	echo '<label for="hlt_reps_special_product_fld">Special product?';
	
	echo '</label> ';
    $yes_checked = $value=='SPECIAL'?'selected="selected"':'';
    $no_checked = $value=='REGULAR'|| $value==''?'selected="selected"':'';
	echo '<select name="hlt_reps_special_product_fld"><option value="SPECIAL" '.$yes_checked.'>SPECIAL PRODUCT</option><option value="REGULAR" '.$no_checked.'>REGULAR PRODUCT</option></select>';
}
function hlt_reps_save_special_offer_field($post_id){
    // Make sure that it is set.
	if ( ! isset( $_POST['hlt_reps_special_product_fld'] ) ) {
		return;
	}

	$my_data = sanitize_text_field( $_POST['hlt_reps_special_product_fld'] );
    

	update_post_meta( $post_id, '_hlt_reps_special_product_fld', $my_data );
}
function view_hlt_support_page(){
    $action = 'manage';
    $order_validity = false;
    if(isset($_GET['id'])){
        $order_id = (int)$_GET['id'];
        $order_validity = hlt_reps_validate_order($order_id);
    }
    if(isset($_GET['action']) && $_GET['action']=='edit'){
        if($order_validity){
            $action = 'edit';
            require_once('views/view.php');
        }
        else{
            $action = 'list';
            require_once('views/list.php');
        }
    }
    else{
        $action = 'list';
        require_once('views/list.php');
    }

}
function hlt_reps_pagination($link,$total_records,$page){
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

function hlt_reps_get_order_count_by_status($status='all'){
    global $wpdb,$woocommerce,$HLT_ITEMS_PER_PAGE;
    $args = array(
         'posts_per_page'=>1,
         'post_type' => 'shop_order',
         'orderby' => 'post_date',
         'order'=> 'DESC'
        );
        if($status!='all'){
            $args['post_status'] = $status;
        }
    $orders=new WP_Query($args);
    $total_records = $orders->found_posts;
    return $total_records;
}
function hlt_reps_validate_order($order_id){
    global $wpdb,$woocommerce;
    $order = new WC_Order($order_id);
    $items_no = $order->get_item_count();
    return $items_no>0?true:false;
}
function get_countries_options($selected = ''){
    $countz = new WC_Countries();
    $countries =$countz->get_countries();
    $html = '';
    foreach($countries as $key => $value){
        $sel = $selected==$key?' selected="selected" ': '';
        $html.='<option '.$sel.' value="'.$key.'">'.$value.'</option>';
    }
    return $html;
     
}
function get_states_options($country = '',$selected = ''){
    $countries = new WC_Countries();
    $states =$countries->get_states($country);
    $html = '';
    foreach($states as $key => $value){
        $sel = $selected==$key?' selected="selected" ': '';
        $html.='<option '.$sel.' value="'.$key.'">'.$value.'</option>';
    }
    return $html;
     
}
function hlt_set_address($order_id,$address,$type ='billing'){
    if(count($address)>0)
    foreach ( $address as $key => $value ) {
        update_post_meta( $order_id, "_{$type}_" . $key, $value );
    }
}

function hlt_reps_get_products_combo($type='regular'){
    $type = strtoupper($type);
    $html = '';
    $args['post_type'] = 'product';
    $args['post_status'] = 'publish';
    $args['posts_per_page'] = -1;
    $args['order'] = 'ASC';
    $args['orderby'] = 'title';
    $args['meta_query'] = array(
		array(
			'key'  => '_hlt_reps_special_product_fld',
			'value'   => $type,
            'compare' => 'LIKE'
		)
	);
    $query1 = new WP_Query( $args );
    while ( $query1->have_posts() ) {
    	$query1->the_post();
    	$html.= '<option value="'.$query1->post->ID.'">' . get_the_title() . '</option>';
    }
    return $html;
}
add_action( 'wp_ajax_hlt_reps_populate_product_action', 'hlt_reps_populate_product_action_callback' );
function hlt_reps_populate_product_action_callback() {
	global $wpdb; // this is how you get access to the database

	$country = $_POST['country'];
    
    echo strlen(trim(get_states_options($country)))==0?'0':trim(get_states_options($country));

	wp_die();
}
add_action( 'wp_ajax_hlt_reps_populate_state_action', 'hlt_reps_populate_state_action_callback' );
function hlt_reps_populate_state_action_callback() {
	global $wpdb; // this is how you get access to the database

	$country = $_POST['country'];
    
    echo strlen(trim(get_states_options($country)))==0?'0':trim(get_states_options($country));

	wp_die();
}
add_action( 'wp_ajax_hlt_reps_populate_product_price_action', 'hlt_reps_populate_product_price_action_callback' );
function hlt_reps_populate_product_price_action_callback() {
	global $wpdb; // this is how you get access to the database
    global $woocommerce;
    $curr = get_woocommerce_currency_symbol();
	$product_id = $_POST['product_id'];
    $product = new WC_Product($product_id);
    $qty = $_POST['qty'];
    $cost = $product->get_price();
    $total = $qty*$cost;
    $cost = $curr.number_format($cost,2);
    
    $total = $curr.number_format($total,2);
    echo json_encode((object)array('cost'=>$cost,'total'=>$total));

	wp_die();
}
add_action( 'wp_ajax_hlt_reps_shipping_state_action', 'hlt_reps_shipping_state_action_callback' );
function hlt_reps_shipping_state_action_callback() {
	global $wpdb; // this is how you get access to the database

	$country = $_POST['country'];
    
    echo strlen(trim(get_states_options($country)))==0?'0':trim(get_states_options($country));

	wp_die();
}

function hlt_reps_set_html_content_type(){
    return 'text/html';
}
function hlt_reps_resend_order_confirmation( $order_id ) {
    add_filter( 'wp_mail_content_type', 'hlt_reps_set_html_content_type' );
    $sent_to_admin = false;
    $plain_text = false;
    $order = new WC_Order( $order_id );
    $to_email = $order->get_billing_email();
    $site_title = get_bloginfo('name');
    $admin_email = get_bloginfo('admin_email');
    $headers = 'From: '.$site_title.' <'.$admin_email.'>' . "\r\n";
    $subject = $site_title.' Customer Receipt/Purchase Confirmation';
    $message = '';
    ob_start();
    ?>
   <h2><?php printf( __( 'Order #%s', 'woocommerce' ), $order->get_order_number() ); ?></h2>
<table width="100%" cellspacing="0" cellpadding="6">
<tbody><tr><td valign="top" width="50%">
<span style="font-size:14px">
<span style="font-weight:bold">Billing Information</span><br />
<strong>Address:</strong><br /> <?php echo $order->get_formatted_billing_address() ?><br />
<strong>Email Address:</strong><br /> <a href="mailto:<?php echo $order->get_billing_email() ?>" target="_blank"><?php echo $order->get_billing_email() ?></a><br>
<strong>Phone:</strong><br /> <?php echo $order->get_billing_phone() ?><br />
</span>
</td>
<td valign="top" width="50%">
<span style="font-size:14px">
<span style="font-weight:bold">Shipping Information</span><br />
<strong>Address:</strong><br /><?php echo $order->get_formatted_billing_address() ?><br />
<?php if($order->get_customer_note()) echo '<strong>Customer Note:</strong><br/>'.$order->get_customer_note().'<br/>'; ?>
</span>
</td></tr>
</tbody></table>
<table cellspacing="0" cellpadding="6" style="width: 100%; border: 1px solid #eee;" border="1" bordercolor="#eee">
	<thead>
		<tr>
			<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e( 'Product', 'woocommerce' ); ?></th>
			<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e( 'Quantity', 'woocommerce' ); ?></th>
			<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e( 'Price', 'woocommerce' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php echo $order->email_order_items_table( $order->is_download_permitted(), true, true ); ?>
	</tbody>
	<tfoot>
		<?php
			if ( $totals = $order->get_order_item_totals() ) {
				$i = 0;
                $count = count($totals)-1;
				foreach ( $totals as $total ) {
					$i++;
					?><tr>
						<th scope="row" colspan="2" style="text-align:left; border: 1px solid #eee; <?php if ( $i == 1 ) echo 'border-top-width: 4px;'; ?>"><?php echo $total['label']; ?></th>
						<td style="text-align:left; border: 1px solid #eee; <?php if ( $i == 1 ) echo 'border-top-width: 4px;'; ?>"><?php echo $total['value']; ?></td>
					</tr><?php
                    
                    if($count==$i){
                        ?><tr>
    						<th scope="row" colspan="2" style="text-align:left; border: 1px solid #eee; <?php if ( $i == 1 ) echo 'border-top-width: 4px;'; ?>">Transaction ID:</th>
    						<td style="text-align:left; border: 1px solid #eee; <?php if ( $i == 1 ) echo 'border-top-width: 4px;'; ?>"><?php echo $order->get_transaction_id( ); ?></td>
    					</tr><?php
                    }
				}
			}
		?>
	</tfoot>
</table>

<?php do_action( 'woocommerce_email_after_order_table', $order, $sent_to_admin, $plain_text ); ?>

<?php do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text ); ?>

<?php do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text ); ?>

<?php do_action( 'woocommerce_email_footer' ); 

    $message = ob_get_contents();
    ob_end_clean();
    wp_mail($to_email, $subject, $message, $headers );
}

function hlt_reps_get_years($year=2013){
    $this_year = date('Y');
    $html = '';
    for($i=$this_year; $i>=2013; $i--){
        $sel = $i==$year?' selected="selected" ':'';
        $html.='<option '.$sel.' value="'.$i.'">'.$i.'</option>';
    }
    return $html;
}

function hlt_reps_get_months($month=0){
    $this_month = (int)date('n');
    $html = '';
    $ms = array('January','February','March','April','May','June','July','August','September','October','November','December');
    $fx = 1;
    for($i=$this_month; $fx<=12; $i--,$fx++){
        $i = $i==0?12:$i;
        $sel = $i==$month?' selected="selected" ':'';
        $html.='<option '.$sel.' value="'.$i.'">'.$ms[$i-1].'</option>';
    }
    return $html;
}
?>