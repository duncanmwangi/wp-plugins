<?php
/*
 * Plugin Name: Ambassador Program Leads
 * Plugin URI: http://gmarkhosting.com
 * Description: Ambassador Program Leads
 * Version: 1.0.0
 * Author: Duncan I. Mwangi EMAIL: irungu.mwangi2@gmail.com
 * Author URI: http://gmarkhosting.com
 * License: GPL2
 */
 defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

 add_action('admin_menu', 'dim_amb_register_admin_pages');


 function dim_amb_register_admin_pages(){
 	add_menu_page( 'Ambassador Program Leads', 'Ambassador Program Leads', 'view_hlt_support_menu', 'hlt-amb-leads', 'view_ambassador_leads_page','dashicons-admin-generic', 8 );
 }

 function view_ambassador_leads_page(){
 	global $wpdb;
 	$my_wpdb = new wpdb(hlt_db_user(), hlt_db_password(), hlt_db_name(), hlt_db_host());
 	$dim_amb_tbl = 'zoho_support_rep_ambassador_leads';
 	if(isset($_GET['view']) && $_GET['view']=='edit' && isset($_GET['id']) && $_GET['id']!=0){
 		$id = (int)$_GET['id'];
 		require_once('edit-leads.php');
 	}elseif(isset($_GET['view']) && $_GET['view']=='add'){
 		require_once("add-leads.php");
 	}elseif(isset($_GET['view']) && $_GET['view']=='del' && isset($_GET['id']) && $_GET['id']!=0){
 		$id = (int)$_GET['id'];
 		$res = $my_wpdb->delete($dim_amb_tbl, ['id'=>$id]);
        $msg = dim_amb_display_msg('Ambassador Lead has been deleted successfully.');
        require_once('leads.php');
 	}else
 	
 	require_once('leads.php');
 }


function dim_amb_display_msg($msg = '', $type=1){
        $type = $type==1?'updated fade': 'error';
        return '<div class="'.$type.'"><p>'.$msg.'</p></div>';
    }


