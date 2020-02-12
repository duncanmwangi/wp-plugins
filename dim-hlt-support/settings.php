<?php 
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
global $wpdb, $dim_hlt_db_version,$dim_hlt_table,$dim_hlt_charset_collate;
$dim_hlt_db_version = '1.0';
$dim_hlt_table = $wpdb->prefix . 'hlt_support_fields';
function dim_hlt_install() {
	global $wpdb,$dim_hlt_table,$dim_hlt_charset_collate;
	global $dim_hlt_db_version;
	$dim_hlt_charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $dim_hlt_table (
		id int(9) NOT NULL AUTO_INCREMENT,
        name varchar(50) NOT NULL,
		type varchar(15) DEFAULT 'text' NOT NULL,
        options text NOT NULL,
        description text NOT NULL,
        sort int(9) DEFAULT '1' NOT NULL,
        status int(9) DEFAULT '1' NOT NULL,
        refund int(9) DEFAULT '1' NOT NULL,
		PRIMARY KEY id (id)
	) $dim_hlt_charset_collate; ";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
    
	add_option( 'dim_hlt_db_version', $dim_hlt_db_version );
}

add_action('admin_menu', 'register_dim_hlt_settings_page');

function register_dim_hlt_settings_page() {
    add_submenu_page( 'edit.php?post_type=hlt-support', 'Field Settings', 'Field Settings', 'manage_options', 'dim-hlt-settings', 'dim_hlt_settings_page' ); 
    add_submenu_page( 'edit.php?post_type=hlt-returns', 'Field Settings', 'Field Settings', 'manage_options', 'dim-hlt-returns-settings', 'dim_hlt_returns_settings_page' ); 
}

function dim_hlt_settings_page() {
    $dtype = 'refunds';
    $vtype = 1;
    if( isset($_GET['id'])){
        $validity = dim_hlt_validate_id($_GET['id']);
    }
    if( isset($_GET['action']) && $_GET['action'] == 'add'){
        require_once('includes/add.php');
    }elseif( isset($_GET['action']) && $_GET['action'] == 'edit' && $validity){
        require_once('includes/edit.php');
    }
    else{
       require_once('includes/view_all.php'); 
    }
}
function dim_hlt_returns_settings_page() {
    $dtype = 'returns';
    $vtype = 0;
    if( isset($_GET['id'])){
        $validity = dim_hlt_returns_validate_id($_GET['id']);
    }
    if( isset($_GET['action']) && $_GET['action'] == 'add'){
        require_once('includes/add.php');
    }elseif( isset($_GET['action']) && $_GET['action'] == 'edit' && $validity){
        require_once('includes/edit.php');
    }
    else{
       require_once('includes/view_all.php'); 
    }
}
function dim_hlt_validate_id($field_id = 0){
    global $wpdb,$dim_hlt_table;
    $allrows = $wpdb->get_results( "SELECT id FROM $dim_hlt_table WHERE id = $field_id AND refund=1 " );
    $ret = false;
    if($allrows){
        $ret = true;
    }
    return $ret;
}
function dim_hlt_returns_validate_id($field_id = 0){
    global $wpdb,$dim_hlt_table;
    $allrows = $wpdb->get_results( "SELECT id FROM $dim_hlt_table WHERE id = $field_id AND refund=0 " );
    $ret = false;
    if($allrows){
        $ret = true;
    }
    return $ret;
}
function dim_hlt_display_msg($msg = '', $type=1){
    $type = $type==1?'updated fade': 'error';
    return '<div class="'.$type.'"><p>'.$msg.'</p></div>';
}
function dim_hlt_count_rows($type='all'){
    global $wpdb,$dim_hlt_table;
    $where = 1;
    if($type=='all'){
       $where = 1; 
       $allrows = $wpdb->get_var( 
	   $wpdb->prepare(  "SELECT count(id) as count FROM $dim_hlt_table  WHERE refund = %d  ",1 )
    );
    }elseif($type=='active'){
       $allrows = $wpdb->get_var( 
	   $wpdb->prepare(  "SELECT count(id) as count FROM $dim_hlt_table WHERE status = %d  AND refund = %d ", 1,1)
    );
    }
    elseif($type=='active'){
       $allrows = $wpdb->get_var( 
	   $wpdb->prepare(  "SELECT count(id) as count FROM $dim_hlt_table WHERE status = %d AND refund = %d ", 0,1 )
    );
    }
    return (int)$allrows;
}

function dim_hlt_returns_count_rows($type='all'){
    global $wpdb,$dim_hlt_table;
    $where = 1;
    if($type=='all'){
       $where = 1; 
       $allrows = $wpdb->get_var( 
	   $wpdb->prepare(  "SELECT count(id) as count FROM $dim_hlt_table  WHERE refund = %d  ",0 )
    );
    }elseif($type=='active'){
       $allrows = $wpdb->get_var( 
	   $wpdb->prepare(  "SELECT count(id) as count FROM $dim_hlt_table WHERE status = %d AND refund = %d ", 1,0)
    );
    }
    elseif($type=='active'){
       $allrows = $wpdb->get_var( 
	   $wpdb->prepare(  "SELECT count(id) as count FROM $dim_hlt_table WHERE status = %d AND refund = %d ", 0,0 )
    );
    }
    return (int)$allrows;
}

function dim_hlt_delete_field($field_id = 0){
    global $wpdb,$dim_hlt_table;
    $wpdb->delete( $dim_hlt_table, array( 'id' => $field_id ) );
    return true;
}
?>