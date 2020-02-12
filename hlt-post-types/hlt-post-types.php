<?php 
/**
 * Plugin Name: HLT POST TYPES
 * Plugin URI: http://gmarkhosting.com
 * Description: This is a plugin to handle returns and refunds logging. 
 * Version: 1.0.0
 * Author: Duncan I. Mwangi EMAIL: irungu.mwangi2@gmail.com
 * Author URI: http://gmarkhosting.com
 * License: GPL2
 */
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

global $wpdb, $hlt_pt_db_version,$hlt_pt_table,$hlt_pt_fields_table,$hlt_pt_charset_collate;
$hlt_pt_db_version = '1.0';
$hlt_pt_fields_table = $wpdb->prefix . 'hlt_pt_fields';
$hlt_pt_table = $wpdb->prefix . 'hlt_pt';

register_activation_hook( __FILE__, 'hlt_pt_install' );

register_activation_hook( __FILE__, 'hlt_pt_create_user_roles' );

add_action( 'init', 'hlt_pt_create_all_post_types' );

add_action('admin_menu', 'hlt_pt_register_admin_pages');
//add fields to post type
add_action( 'add_meta_boxes', 'hlt_pt_create_fields' );

//add save fields for post type
add_action( 'save_post', 'hlt_pt_save_fields');

add_action( 'wp_trash_post', 'hlt_pt_delete_post', 10 );

add_action('untrash_post', 'hlt_pt_restore_post');

require_once('functions.php');

?>