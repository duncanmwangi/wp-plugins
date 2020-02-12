<?php
/*
 * Plugin Name: HLT SUPPORT REPS
 * Plugin URI: http://gmarkhosting.com
 * Description: A HLT SUPPORT REPS plugin 
 * Version: 1.0.0
 * Author: Duncan I. Mwangi EMAIL: irungu.mwangi2@gmail.com
 * Author URI: http://gmarkhosting.com
 * License: GPL2
 */
 defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
 global  $HLT_ITEMS_PER_PAGE,$ult_woocommerce_order_items_tbl,$ult_woocommerce_order_itemmeta_tbl;
 $ult_woocommerce_order_items_tbl = $wpdb->prefix . 'woocommerce_order_items';
 $ult_woocommerce_order_itemmeta_tbl = $wpdb->prefix . 'woocommerce_order_itemmeta';
 $HLT_ITEMS_PER_PAGE = 20;
 register_activation_hook( __FILE__, 'hlt_reps_create_user_roles' );
 add_action( 'admin_init', 'hlt_reps_create_user_roles');
 add_action('admin_menu', 'hlt_reps_register_admin_pages');
 
 if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    add_action( 'add_meta_boxes', 'hlt_reps_add_special_offer_field' );
    add_action( 'save_post', 'hlt_reps_save_special_offer_field' );
}

 require_once 'functions.php'; 
 require_once 'coupon-offers.php';

 ?>