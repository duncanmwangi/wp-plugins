<?php
//add_action('wp_footer','tests_load_all_orders_to_table');
//add_action('wp_footer','tests_load_all_orders_to_table_with_correct_phone_number');
//add_action('wp_footer','tests_get_phone_csv');
global $tests_tbl;
$tests_tbl = 'duncan_order_details';
function tests_get_phone_csv(){
    global $tests_tbl,$wpdb;
    if(isset($_GET['csvxddf']) && $_GET['csvxddf'] == 'yes'){
        $time = current_time('mysql');
        ob_clean();
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: private", false);
        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=\"Unique Phone Numbers $time.csv\";" );
        header("Content-Transfer-Encoding: binary");
        $output= "Phone Number \n";
        $sql = "SELECT DISTINCT phone_var FROM $tests_tbl WHERE phone_var!=''";
        $csvrows = $wpdb->get_results($sql);
        if($csvrows){
            $count = 1;
            foreach($csvrows as $csvrow){
                $output.="$csvrow->phone_var \n";
            }
            $output.="\n\n";
        }
        else{
            $output.="No records found\n";
        }

        echo $output;
    }
    if(isset($_GET['csv500plus']) && $_GET['csv500plus'] == 'yes'){
        $time = current_time('mysql');
        ob_clean();
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: private", false);
        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=\"$500 and above Unique Phone Numbers $time.csv\";" );
        header("Content-Transfer-Encoding: binary");
        $output= "Phone Number \n";
        //$sql = "SELECT DISTINCT phone_var FROM $tests_tbl WHERE phone_var!=''";
        $sql = "SELECT DISTINCT phone_var FROM duncan_order_details as a
        LEFT JOIN (SELECT sum(amount) as totalx,order_idp,count(id) as count FROM wp_mailchimp_hlt_clients_orders Group by hlt_id) as b ON b.order_idp = a.order_id
         WHERE totalx > 499.99 GROUP BY phone_var ORDER BY id";
        $csvrows = $wpdb->get_results($sql);
        if($csvrows){
            $count = 1;
            foreach($csvrows as $csvrow){
                $output.="$csvrow->phone_var \n";
            }
            $output.="\n\n";
        }
        else{
            $output.="No records found\n";
        }

        echo $output;
    }

    if(isset($_GET['xlf']) && $_GET['xlf'] == 'yes' && isset($_GET['status'])){
        $status = $_GET['status'];
        $time = current_time('mysql');
        ob_clean();
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: private", false);
        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=\"$status ORDERS Phone Numbers $time.csv\";" );
        header("Content-Transfer-Encoding: binary");
        $output= "No.,Name,Phone Number,Formatted phone number, Email, Order Number,Products,Order Date\n";
        $sql = "SELECT * FROM $tests_tbl WHERE status='$status'";
        $csvrows = $wpdb->get_results($sql);
        if($csvrows){
            $count = 1;
            foreach($csvrows as $csvrow){
                $prods = array();
                $prod_ids = explode(',',$csvrow->product_id);
                foreach($prod_ids as $prod_id){
                    $prods[] = html_entity_decode(strip_tags(get_the_title($prod_id)));
                }
                $prod_names = implode(', ',$prods);
                $output.=$count++.",$csvrow->name,$csvrow->phone_var,$csvrow->phone,$csvrow->email,$csvrow->order_id,$prod_names,".date('d-m-Y H:i:s',strtotime($csvrow->date)).",   \n";
            }
            $output.="\n\n";
        }
        else{
            $output.="No records found\n";
        }

        echo $output;
    }
    if(isset($_GET['xlf']) && $_GET['xlf'] == 'yes' && !isset($_GET['status'])){
        $time = current_time('mysql');
        ob_clean();
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: private", false);
        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=\"Phone Numbers $time.csv\";" );
        header("Content-Transfer-Encoding: binary");
        $output= "No.,Name,Phone Number,Formatted phone number, Email, Order Number,Products,Order Date\n";
        $sql = "SELECT * FROM $tests_tbl";
        $csvrows = $wpdb->get_results($sql);
        if($csvrows){
            $count = 1;
            foreach($csvrows as $csvrow){
                $prods = array();
                $prod_ids = explode(',',$csvrow->product_id);
                foreach($prod_ids as $prod_id){
                    $prods[] = html_entity_decode(strip_tags(get_the_title($prod_id)));
                }
                $prod_names = implode(', ',$prods);
                $output.=$count++.",$csvrow->name,$csvrow->phone_var,$csvrow->phone,$csvrow->email,$csvrow->order_id,$prod_names,".date('d-m-Y H:i:s',strtotime($csvrow->date)).",   \n";
            }
            $output.="\n\n";
        }
        else{
            $output.="No records found\n";
        }

        echo $output;
    }
}
function tests_load_all_orders_to_table_with_correct_phone_number(){
    global $tests_tbl,$wpdb;
    $all = $wpdb->get_results("SELECT * FROM $tests_tbl");
    foreach($all as $row){
        $phone = str_ireplace(array(' ','-','+1','(',')','_'),'',$row->phone);
        $wpdb->update($tests_tbl, array('phone_var'=>$phone), array('id'=>$row->id),array('%s','%s'));
    }
}
function tests_load_all_orders_to_table(){
    global $tests_tbl,$wpdb;
    global $woocommerce;
    $args = array(
        'post_type' => 'shop_order',
        'post_status' => 'any',
        'posts_per_page' => '100',
        'paged' => 1
    );
    $loop = new WP_Query( $args );
    //print_r($loop);
    while ( $loop->have_posts() ) : $loop->the_post();

        $order_id = $loop->post->ID;

        //print_r($order_id);
        $order = new WC_Order($order_id);
        $items = $order->get_items();
        $product_ids = array();
        foreach($items as $item){
            $product_ids[]=$item['product_id'];
        }
        $prod_ids = implode(',',$product_ids);
        $exists = $wpdb->get_row("SELECT * FROM $tests_tbl WHERE order_id=$order_id");
        if(!$exists)
        $wpdb->insert($tests_tbl,array('order_id'=>$order_id,'name'=>$order->billing_first_name.' '.$order->billing_last_name,'phone'=>$order->billing_phone,'email'=>$order->billing_email,'status'=>$order->post_status,'date'=>$order->order_date,'product_id'=>$prod_ids),array('%d','%s','%s','%s','%s','%s','%s'));

    endwhile;
    //echo 'hlpx';
    //$page = $page+1;
    //if($page<=100)
    //tests_load_all_orders_to_table($page);
}
