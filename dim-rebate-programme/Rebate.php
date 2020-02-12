<?php

/**
 * @author Duncan I. Mwangi
 * @copyright 2015
 */
class DRP_Rebate{
    public $db;
    public $prefix;
    public $rebate_orders_tbl;
    public $rebates_tbl;
    public $rebate_emails_tbl;
    public $items_per_page;
    public $rebate_amount;
    public $coupon_amount;
    
    function __construct(){
        global $wpdb;
        $this->db = $wpdb;
        $this->items_per_page = 10;
        $this->rebate_amount = get_option( 'drp_rebate_amount', '250.00' );
        $this->coupon_amount = get_option( 'drp_coupon_amount', '25.00' );
        $this->prefix = $this->db->prefix.'DRP_';
        $this->set_table_names();
        $this->create_tables();
        $this->insert_initial_data();
        
        add_action('admin_menu', array($this,'register_admin_pages'));
        add_action('woocommerce_payment_complete', array($this,'payment_complete'));
        add_action( 'woocommerce_order_status_completed', array($this,'woocommerce_order_status_completed') );
        
        $this->db->show_errors     = true;
        $this->db->suppress_errors = false;
        //add_action('wp_footer', array($this,'edit_all_coupons'));
    }
    function edit_all_coupons(){
        $sql = "SELECT * FROM $this->rebates_tbl WHERE coupon_status = 'UNUSED'";
        $coupons = $this->db->get_results($sql);
        if($coupons)
            foreach ($coupons as $row) {
                $this_coupon = new WC_Coupon( $row->coupon_code );
                update_post_meta( $this_coupon->get_id(), 'individual_use', 'no' );
                echo $row->coupon_code.'<br/>';
            }

    }
    function format_us_date($mysql_date=''){
        return date('m/d/Y', strtotime($mysql_date));
    }
    
    function db_error(){
        if ($this->db->last_error) {
  die('error=' . var_dump($this->db->last_query) . ',' . var_dump($this->db->error));
}
    }
    public function register_admin_pages(){
        add_menu_page( 'Rebate Programme', 'Rebate Programme', 'manage_options', 'drp-rebates', array($this,'drp_pending_rebates_page'),'dashicons-admin-generic', 71 );
        add_submenu_page( 'drp-rebates', 'Pending Rebates', 'Pending Rebates', 'manage_options', 'drp-rebates', array($this,'drp_pending_rebates_page') );
        add_submenu_page( 'drp-rebates', 'Approved Rebates', 'Approved Rebates', 'manage_options', 'drp-approved', array($this,'drp_approved_rebates_page') );
        //add_submenu_page( 'drp-rebates', 'Cancelled Rebates', 'Cancelled Rebates', 'manage_options', 'drp-cancelled', array($this,'drp_cancelled_rebates_page') );
        add_submenu_page( 'drp-rebates', 'All Emails', 'All Emails', 'manage_options', 'drp-emails', array($this,'drp_all_emails_page') );
        add_submenu_page( 'drp-rebates', 'Settings', 'Settings', 'manage_options', 'drp-settings', array($this,'drp_settings_page') );
    }
    public function drp_pending_rebates_page()
    {
    
    if(isset($_GET['action']) && isset($_GET['id']) && !empty($_GET['action']) && !empty($_GET['id'])){
        $email_id = $_GET['id'];
        $action = strtolower($_GET['action']);
        $valid = $this->validate_email_id($email_id);
        if($action=='view' && $valid){
            require_once('views/view.php');
        }
        if($action=='award' && $valid){
            //award
            if( isset($_POST['email_id']) && $_POST['email_id'] == $email_id ){
                $total_rebate_amount = 0;
                $sql_1 = "SELECT email_id, COALESCE(sum(rebate_amount),0) as rebate_amount FROM $this->rebates_tbl WHERE email_id = $email_id GROUP BY email_id ";
                $rebate = $this->db->get_row($sql_1);
                
                if($rebate) $total_rebate_amount = $rebate->rebate_amount;
                
                
                
                $total_order_amount = 0;
                $sql_2 = "SELECT email_id, COALESCE(sum(order_amount),0) as order_amount FROM $this->rebate_orders_tbl WHERE email_id = $email_id GROUP BY email_id ";
                $orders = $this->db->get_row($sql_2);
                
                if($orders) $total_order_amount = $orders->order_amount;
                
                $unrebated_amount = $total_order_amount-$total_rebate_amount;
                if($unrebated_amount >= $this->rebate_amount){
                    $this->issue_rebate($email_id);
                    $msg = $this->display_msg('Rebate was awarded successfully and emailed to the client.');
                }
                
            }
            require_once('views/view.php');
        }
        if($action=='cancel' && $valid){
            //cancel
            require_once('views/view.php');
        }
    }
    else require_once('views/pending.php');
    }
    public function auto_issue_rebates($allrows)
    {
        if($allrows)
            foreach($allrows as $row){
                $email_id = $row->email_id;
                $total_rebate_amount = 0;
                $sql_1 = "SELECT email_id, COALESCE(sum(rebate_amount),0) as rebate_amount FROM $this->rebates_tbl WHERE email_id = $email_id GROUP BY email_id ";
                $rebate = $this->db->get_row($sql_1);
                
                if($rebate) $total_rebate_amount = $rebate->rebate_amount;
                
                
                
                $total_order_amount = 0;
                $sql_2 = "SELECT email_id, COALESCE(sum(order_amount),0) as order_amount FROM $this->rebate_orders_tbl WHERE email_id = $email_id GROUP BY email_id ";
                $orders = $this->db->get_row($sql_2);
                
                if($orders) $total_order_amount = $orders->order_amount;
                
                $unrebated_amount = $total_order_amount-$total_rebate_amount;
                $drp_donot_rebate_emails = get_option( 'drp_donot_rebate_emails', '' );
                $emails_not_to_award = explode(',', $drp_donot_rebate_emails);
                //$emails_not_to_award = array('russell.hemplife@gmail.com','omar.hemplifetoday@gmail.com');
                if($unrebated_amount >= $this->rebate_amount && $email_id!=317 && !in_array($row->email, $emails_not_to_award)){
                    $this->issue_rebate($email_id);
                    echo $this->display_msg('Rebate was awarded successfully and emailed to the client.'.$row->email.'<br/>');
                }
            }
    }
    public function issue_rebate($email_id=0){
        $time = current_time('mysql');
        $coupon_code = $this->create_coupon();
        $this->db->insert($this->rebates_tbl,
            array(
                'email_id' => $email_id,
                'rebate_amount' => $this->rebate_amount,
                'coupon_amount' => $this->coupon_amount,
                'coupon_code' => $coupon_code,
                'status' => 'APPROVED',
                'date_created' => $time
            ),
            array(
                '%d',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s'
            )
        );
        $expiry_days = get_option( 'drp_expiry_days', '45' );
        $replyto_email = get_option( 'drp_reply_to_email', '' );
        $headers = 'From: '.$this->admin_name().' <'.$this->admin_email().'>' . "\r\n Reply-to: ".$this->admin_name().' <'.$replyto_email.'>';
        //$mesage = 'Hello,<br/><br/>You have been awarded a rebate of USD $'.$this->coupon_amount.' for buying products worth above USD $'.$this->rebate_amount.'. <br/>After adding your order to your cart, use the coupon code: <strong>'.$coupon_code.'</strong> to redeem the rebate.  <br/><br/>The coupon code is only valid for the next '.$expiry_days.' days (Until: '.date('d-m-Y',strtotime("+$expiry_days day")).'). <br/><br/>Thanks,<br/><br/>HempLife Today&trade;'; 
        
        $mesage = 'Hello,<br/><br/><br/>Congratulations... Because of your purchases of quality CannazALL&trade; products you&#39;ve been awarded a Rebate of $'.$this->coupon_amount.'!<br/><br/>To redeem your rebate...<br/><br/>1. Just go to <a href="https://www.hemplifetoday.com/choose-quantity/">www.HempLifeToday.com/choose-quantity/</a><br/>2. Add the items you desire to your cart.<br/>3. Use your custom Rebate Code:  <strong>'.$coupon_code.'</strong> and your $'.$this->coupon_amount.' Rebate will be applied (deducted) from your order.<br/><br/>Remember... Ever time you meet the $'.$this->rebate_amount.' threshold on a single or multiple orders, you earn an additional $'.$this->coupon_amount.' Rebate!<br/><br/>&#42;This current Rebate is valid for the next '.$expiry_days.' days (Until: '.date('m-d-Y',strtotime("+$expiry_days day")).'). <br/><br/>Thank you!<br/><br/>';
        $email = $this->db->get_row("SELECT * FROM $this->rebate_emails_tbl WHERE email_id=$email_id LIMIT 0,1");
        add_filter( 'wp_mail_content_type', array($this,'set_html_content_type') );
        if($email) wp_mail($email->email, get_bloginfo('name').': Rebate Coupon Code ', $mesage, $headers );
        remove_filter('wp_mail_content_type', array($this,'set_html_content_type') );
    }
    function set_html_content_type() {
        return 'text/html';
    }
    public function create_coupon(){
        $coupon_str = $this->get_coupon_string();
        $coupon = array(
            'post_title' => $coupon_str,
            'post_content' => 'Created by REBATE programme plugin',
            'post_status' => 'publish',
            'post_excerpt' => 'Created by REBATE programme plugin',
            'post_author' => 1,
            'post_type' => 'shop_coupon'
        );
        $new_coupon_id = wp_insert_post( $coupon );
        // Add meta
        update_post_meta( $new_coupon_id, 'discount_type', 'fixed_cart' );
        update_post_meta( $new_coupon_id, 'coupon_amount', $this->coupon_amount );
        update_post_meta( $new_coupon_id, 'individual_use', 'no' );
        update_post_meta( $new_coupon_id, 'exclude_product_ids', '' );
        update_post_meta( $new_coupon_id, 'usage_limit', '1' );
        $expiry_days = get_option( 'drp_expiry_days', '45' );
        update_post_meta( $new_coupon_id, 'expiry_date', date('Y-m-d',strtotime("+$expiry_days day")) );
        update_post_meta( $new_coupon_id, 'apply_before_tax', 'yes' );
        update_post_meta( $new_coupon_id, 'free_shipping', 'no' );
        $drp_coupon_conjunctions = get_option( 'drp_coupon_conjunctions', '' );
        
        
        //update_post_meta( $new_coupon_id, 'wcc_coupon_conjunction', $drp_coupon_conjunctions );
        ccj_save_coupon_conjunctions($new_coupon_id,$drp_coupon_conjunctions);
        $ccn = array_unique(explode(',',$drp_coupon_conjunctions));
        if(count($ccn)>0){
            foreach($ccn as $key => $code){
                ccj_add_new_coupon_conjunction($code,$coupon_str);
                /*
                $coupon = new WC_Coupon( $code );
                $coupon_conjunctions = get_post_meta ( $coupon->id, 'wcc_coupon_conjunction', true );
                $arr = array();
                $arr = explode(',',$coupon_conjunctions);
                if(!in_array($coupon_str,$arr)){
                    $new_val = implode(',',$arr);
                    $new_val=$coupon_str.','.$new_val;
                    update_post_meta( $coupon->id, 'wcc_coupon_conjunction', $new_val );
                }
                */
            }

        }
        
        return $coupon_str;
    }
    public function apply_coupon_conjunctions($coupon_conjunctions_str = ''){
        //$time = get_option( 'drp_coupon_amount', '25.00' );
        $ccn = explode(',',$coupon_conjunctions_str);
        $sql = "SELECT * FROM $this->rebates_tbl WHERE status = 'APPROVED' AND coupon_status = 'UNUSED' AND date_created >= DATE_SUB(NOW(),INTERVAL 47 DAY) ";
        $all_unused_coupons = $this->db->get_results($sql);
        if(count($all_unused_coupons)>0){
            foreach ($all_unused_coupons as $row) {
                $coupon_str = $row->coupon_code;
                $this_coupon = new WC_Coupon( $coupon_str );
                //update_post_meta( $this_coupon->id, 'wcc_coupon_conjunction', $coupon_conjunctions_str );
                @ccj_save_coupon_conjunctions($this_coupon->get_id(),$coupon_conjunctions_str);
                if(count($ccn)>0){
                    foreach($ccn as $key => $code){
                        @ccj_add_new_coupon_conjunction($code,$coupon_str);

                        /*
                        $coupon = new WC_Coupon( $code );
                        $coupon_conjunctions = get_post_meta ( $coupon->id, 'wcc_coupon_conjunction', true );
                        $arr = array();
                        $arr = explode(',',$coupon_conjunctions);
                        if(!in_array($coupon_str,$arr)){
                            $new_val = implode(',',$arr);
                            $new_val=$coupon_str.','.$new_val;
                            update_post_meta( $coupon->id, 'wcc_coupon_conjunction', $new_val );
                        }
                        */
                    }

                }
            }
        }

        //update_option( 'drp_admin_name', $drp_admin_name);
    }
    public function get_coupon_string()
    {
        $coupon = 'DRB-'.strtoupper(wp_generate_password( 10, false ));
        $c = $this->db->get_row("SELECT coupon_code FROM $this->rebates_tbl WHERE coupon_code='$coupon' LIMIT 0,1");
        if($c){
            $coupon = $this->get_coupon_string();
        }
        return $coupon;
    }
    public function drp_approved_rebates_page()
    {
      require_once('views/approved.php');
    }
    public function drp_cancelled_rebates_page()
    {
      require_once('views/cancelled.php');
    }
    public function drp_settings_page()
    {
      require_once('views/settings.php');
    }
    public function drp_all_emails_page()
    {
      require_once('views/all_emails.php');
    }
    public function validate_email_id($email_id=0){
        $obj = $this->db->get_row("SELECT * FROM $this->rebate_emails_tbl WHERE email_id=$email_id LIMIT 0,1");
        if($obj) return true; else return false;
    }
    public function payment_complete($order_id)
    {

        $order = new WC_Order($order_id);
        $time = current_time('mysql');
        $type = get_post_meta( $order->get_id(), 'dim_order_type', true );
        if($type!='WHOLESALE'):
            /*$email_id = $this->get_email_id($order->get_billing_email());
            $this->db->insert($this->rebate_orders_tbl,
                array(
                    'email_id' => $email_id,
                    'order_no' => $order_id,
                    'order_amount' => $order->get_total(),
                    'order_date' => $order->get_date_created()
                ),
                array(
                    '%d',
                    '%s',
                    '%f',
                    '%s'
                )
            );*/
        endif;
        $this->mark_coupon_used($order,$order_id);
    }
    public function woocommerce_order_status_completed($order_id){
    	//$order_id = 17879;
    	$order = new WC_Order($order_id);
        $time = current_time('mysql');
        $type = get_post_meta( $order->get_id(), 'dim_order_type', true );
        if($type!='WHOLESALE' && !empty($type)):
            //if($order->get_payment_method()=='phone_manual_dummy'){

            	/*$email_id = $this->get_email_id($order->get_billing_email());
            	$this->db->insert($this->rebate_orders_tbl,
    	            array(
    	                'email_id' => $email_id,
    	                'order_no' => $order_id,
    	                'order_amount' => $order->get_total(),
    	                'order_date' => $order->get_date_created()
    	            ),
    	            array(
    	                '%d',
    	                '%s',
    	                '%f',
    	                '%s'
    	            )
    	        );*/
            //}
        endif;

    }
    public function update_all_phone_numbers(){
        $sql = "SELECT * FROM $this->rebate_emails_tbl WHERE phone = '' ";
        $results = $this->db->get_results($sql);
        if($results){
            foreach($results as $row){
                $sql2 = "SELECT * FROM $this->rebate_orders_tbl WHERE email_id= $row->email_id ORDER BY order_no ASC";
                $results2 = $this->db->get_results($sql2);
                $billing_phone = '';
                 if($results2){
                    foreach($results2 as $row2){
                        $order = new WC_Order($row2->order_no);
                        if(!empty($order->get_billing_phone())){
                            $billing_phone = $order->get_billing_phone();
                        }
                    }
                 }
                 $billing_phone = str_ireplace(array('(',')','-',' '),'',$billing_phone);
                 $sql3 = "UPDATE $this->rebate_emails_tbl SET phone='$billing_phone' WHERE email_id= $row->email_id ";
                 $this->db->query($sql3);
            }
        }
    }
    public function mark_coupon_used($order,$order_id){
        $coupons = $order->get_used_coupons();
        $time = current_time('mysql');
        if(count($coupons)>0){
            foreach($coupons as $coupon){
                $obj = $this->db->get_row("SELECT * FROM $this->rebates_tbl WHERE coupon_code='$coupon' LIMIT 0,1");
                if($obj){
                    $this->db->update($this->rebates_tbl,
                        array(
                            'coupon_status' => 'USED',
                            'coupon_use_order_id' => $order_id,
                            'date_coupon_used' => $time
                        ),
                        array(
                            'rebate_id'=>$obj->rebate_id
                        ),
                        array(
                            '%s',
                            '%s',
                            '%s'
                        )
                    );
                }
            }
        }
    }
    public function get_email_id($email){
        $obj = $this->db->get_row("SELECT email_id FROM $this->rebate_emails_tbl WHERE email='$email' LIMIT 0,1");
        if($obj) return $obj->email_id;
        $time = current_time('mysql');
        $this->db->insert($this->rebate_emails_tbl,
            array(
                'email' => $email,
                'date_added' => $time
            ),
            array(
                '%s',
                '%s'
            )
        );
        return $this->db->insert_id;
    }
    public function admin_email()
    {
        return get_option( 'drp_admin_email',get_bloginfo('admin_email') );
    }
    public function admin_name()
    {
        return get_option( 'drp_admin_name',get_bloginfo('name') );
    }
    
    
    public function display_msg($msg = '', $type=1){
        $type = $type==1?'updated fade': 'error';
        return '<div class="'.$type.'"><p>'.$msg.'</p></div>';
    }

    public function get_message($msg = '', $type=1){
        $type = $type==1?'alert alert-success': 'alert alert-danger';
        return '<div class="'.$type.'">'.$msg.'</div>';
    }
    
   public function pagination($link,$sql,$where,$page){
        $this->db->query($sql.$where);
        $total_records = $this->db->num_rows;

        $total_pages = ceil($total_records / $this->items_per_page);
        $first_link = $page!=1 && $total_pages>1 ? $link.'&upg=1':'#';
        $prev_link = $page>2 && $total_pages>2 ? $link.'&upg='.($page-1):'#';
        $next_link = $page<$total_pages && $total_pages>1 ? $link.'&upg='.($page+1):'#';
        $last_link = $page<$total_pages && $total_pages>1 ? $link.'&upg='.($total_pages):'#';
        if($total_pages>1):
            ?>
            <div class="rp_tablenav_pages">
                <span class="displaying-num">Page <?php echo $page ?> of <?php echo $total_pages ?> Pages</span>
    <span class="pagination-links">
        <a class="first-page <?php if($first_link == '#') echo 'disabled' ?>" title="Go to the first page" href="<?php echo $first_link ?>">&lt;&lt;</a>

        <a class="prev-page <?php if($prev_link == '#') echo 'disabled' ?>" title="Go to the previous page" href="<?php echo $prev_link ?>">&lt;</a>

        <a class="next-page <?php if($next_link == '#') echo 'disabled' ?>" title="Go to the next page" href="<?php echo $next_link ?>">&gt;</a>

        <a class="last-page <?php if($last_link == '#') echo 'disabled' ?>" title="Go to the last page" href="<?php echo $last_link ?>">&gt;&gt;</a>
    </span>
                <span class="displaying-num"><?php echo $total_records ?> items</span>
            </div>
            <?php
        endif;
    }

    public function set_table_names(){
        $this->rebate_emails_tbl = $this->prefix.'rebate_emails_tbl';
        $this->rebates_tbl = $this->prefix.'rebates_tbl';
        $this->rebate_orders_tbl = $this->prefix.'rebate_orders_tbl';
    }
    public function create_tables(){ 
        $wpdb = $this->db;
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        
        
        $sql = "CREATE TABLE IF NOT EXISTS $this->rebate_emails_tbl (
        email_id int(11) NOT NULL AUTO_INCREMENT,
        email varchar(200) NOT NULL,
        date_added datetime NOT NULL,
        PRIMARY KEY email_id (email_id),
        UNIQUE KEY email (email)
        );
         ";
        dbDelta( $sql );
        
        $sql = "CREATE TABLE IF NOT EXISTS $this->rebates_tbl (
        rebate_id int(11) NOT NULL AUTO_INCREMENT,
        email_id int(11) NOT NULL,
        rebate_amount decimal(9,2) NOT NULL,
        coupon_amount decimal(9,2) NOT NULL,
        coupon_code varchar(20) NOT NULL,
        coupon_use_order_id varchar(10) NOT NULL,
        status ENUM('PENDING','APPROVED','CANCELLED') NOT NULL DEFAULT 'PENDING',
        coupon_status ENUM('USED','UNUSED') NOT NULL DEFAULT 'UNUSED',
        date_created datetime NOT NULL,
        date_modified datetime NOT NULL,
        date_coupon_used datetime NOT NULL,
        PRIMARY KEY rebate_id (rebate_id)
        );
         ";
        dbDelta( $sql );
        
        $sql = "CREATE TABLE IF NOT EXISTS $this->rebate_orders_tbl (
        rebate_order_id int(11) NOT NULL AUTO_INCREMENT,
        email_id int(11) NOT NULL,
        order_no varchar(20) NOT NULL,
        order_amount decimal(9,2) NOT NULL,
        order_date datetime NOT NULL,
        PRIMARY KEY rebate_order_id (rebate_order_id)
        );
         ";
        dbDelta( $sql );
    }
    public function insert_initial_data(){
        
    }
}