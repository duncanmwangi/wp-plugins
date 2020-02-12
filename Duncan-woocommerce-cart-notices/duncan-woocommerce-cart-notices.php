<?php 
/**
 * Plugin Name: DUNCAN WOOCOMERCE CART NOTICES.
 * Plugin URI: http://gmarkhosting.com
 * Description: This is a pluging having the capabilities to add a UPSELL products to the checkout page
 * Version: 1.0.0
 * Author: Duncan I. Mwangi
 * Author URI: http://gmarkhosting.com
 * License: GPL2
 */
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    die( 'Activate woocommerce plugin first!' );
}

global $wpdb, $duncan_db_version,$notice_table,$notice_cat_table,$charset_collate;
$duncan_db_version = '1.0';
$notice_table = $wpdb->prefix . 'd_cart_notices';
$notice_cat_table = $wpdb->prefix . 'd_notice_categories';
function duncan_install() {
	global $wpdb,$notice_table,$notice_cat_table,$charset_collate;
	global $duncan_db_version;

	
	
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $notice_table (
		id int(9) NOT NULL AUTO_INCREMENT,
        name varchar(50) NOT NULL,
		product_id int(9) NOT NULL,
        text text NOT NULL,
		status int(9) DEFAULT '1' NOT NULL,
		PRIMARY KEY id (id)
	) $charset_collate; ";
    $sql.= " CREATE TABLE $notice_cat_table (
		id int(9) NOT NULL AUTO_INCREMENT,
		notice_id int(9) NOT NULL,
		category_id int(9) NOT NULL,
		PRIMARY KEY id (id)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );

	add_option( 'duncan_db_version', $duncan_db_version );
}
function duncan_uninstall(){
    global $wpdb,$charset_collate,$notice_table,$notice_cat_table;

	$sql = "DROP TABLE $notice_table IF EXISTS; ";
    $sql.= "DROP TABLE $notice_cat_table IF EXISTS; ";
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
}
register_activation_hook( __FILE__, 'duncan_install' );
//register_deactivation_hook( __FILE__, 'duncan_uninstall' );

add_action( 'woocommerce_after_cart_table', 'add_duncan_checkout_notices',1);


function add_duncan_checkout_notices(){
    
    global $wpdb,$notice_table,$notice_cat_table;
    $cart_items = get_all_items_in_cart();
    $active_notices = d_get_all_active_cart_notices();
    if(is_array($active_notices) && !empty($active_notices)){
        $count = 1;
        foreach($active_notices as $active_notice_id){
            $notice = $wpdb->get_row("SELECT * FROM $notice_table WHERE id = $active_notice_id");
            $image_link = wp_get_attachment_image_src( get_post_thumbnail_id($notice->product_id), 'thumbnail' );
            $item_already_in_cart = false;
            if(in_array($notice->product_id,$cart_items)){
                $item_already_in_cart = true;
                continue;
            }
            if($count==1){
                ?>
                <h3 id="special_offer_heading"><?php _e( 'Special offer', 'woocommerce' ); ?></h3>
                <?php
            }
            $count++;
            ?>
            <table class="shop_table">
                <tr>
                    <td width="10%"><img border="0" style="max-height: 100px;" src="<?php echo $image_link[0] ?>" /></td>
                    <td width="70%"><?php if(isset($notice->text)) echo stripslashes($notice->text); else echo get_the_title($notice->product_id); ?>
                    </td>
                    <td width="20%">
                    
                    <!-- <a href="<?php echo get_the_permalink($notice->product_id)?>"><button class="button alt" type="button">Add to cart</button> --> <button type="submit"
    data-quantity="1" data-product_id="<?php echo $notice->product_id; ?>"
    class="button alt add_to_cart_button product_type_simple">
    Add to cart
</button></a>
                    
                    </td>
                </tr>
            </table>
            <?php
        }
    }
}
add_action('admin_menu', 'register_duncan_cart_notices_page');

function register_duncan_cart_notices_page() {
    add_submenu_page( 'woocommerce', 'Cart Notices', 'Cart Notices', 'manage_options', 'duncan-cart-notices-page', 'duncan_cart_notices_page' ); 
}

function duncan_cart_notices_page() {
    if( isset($_GET['id'])){
        $validity = d_validate_id($_GET['id']);
    }
    if( isset($_GET['action']) && $_GET['action'] == 'add'){
        require_once('dim_add.php');
    }elseif( isset($_GET['action']) && $_GET['action'] == 'edit' && $validity){
        require_once('dim_edit.php');
    }
    else{
       require_once('dim_view_all.php'); 
    }
}

function d_get_categories_array(){
    $taxonomy     = 'product_cat';
      $orderby      = 'name';  
      $show_count   = 1;      // 1 for yes, 0 for no
      $pad_counts   = 1;      // 1 for yes, 0 for no
      $hierarchical = 1;      // 1 for yes, 0 for no  
      $title        = '';  
      $empty        = 0;
        $args = array(
          'taxonomy'     => $taxonomy,
          'orderby'      => $orderby,
          'show_count'   => $show_count,
          'pad_counts'   => $pad_counts,
          'hierarchical' => $hierarchical,
          'title_li'     => $title,
          'hide_empty'   => $empty
        );
     $all_categories = get_categories( $args );
     $d_cats = array();
    foreach ($all_categories as $cat) {
        if($cat->category_parent == 0) {
            $category_id = $cat->term_id;
            $d_cats[] = array('id'=>$category_id, 'name' => $cat->name, 'count' => $cat->count);
            $args2 = array(
              'taxonomy'     => $taxonomy,
              'parent'       => $category_id,
              'orderby'      => $orderby,
              'show_count'   => $show_count,
              'pad_counts'   => $pad_counts,
              'hierarchical' => $hierarchical,
              'title_li'     => $title,
              'hide_empty'   => $empty
            );
            $sub_cats = get_categories( $args2 );
            if($sub_cats) {
                foreach($sub_cats as $sub_category) {
                    $d_cats[] = array('id'=>$sub_category->term_id, 'name' => $sub_category->name, 'count' => $sub_category->count);
                    $args2['parent'] = $sub_category->term_id;
                    $sub_cats2 = get_categories( $args2 );
                    foreach($sub_cats2 as $sub_category2) {
                        $d_cats[] = array('id'=>$sub_category2->term_id, 'name' => $sub_category2->name, 'count' => $sub_category2->count);
                    }
                }
    
            }  }     
    }
    return $d_cats;
}

function d_get_products_combo($selected=''){
     $args = array( 'post_type' => 'product', 'posts_per_page' => -1 );

    $loop = new WP_Query( $args );

    $html = '';
    while ( $loop->have_posts() ) : $loop->the_post(); 
    
    $id = get_the_ID();;
    if($selected==$id) $sel = ' selected="selected" ' ; else $sel = '';
    $html.='<option value="'.$id.'" '.$sel.'>'.get_the_title().'</option>';
    endwhile; 


    wp_reset_query(); 
    return $html;
}

function d_display_msg($msg = '', $type=1){
    $type = $type==1?'updated fade': 'error';
    return '<div class="'.$type.'"><p>'.$msg.'</p></div>';
}
function d_count_rows($type='all'){
    global $wpdb,$notice_table;
    $where = 1;
    if($type=='all'){
       $where = 1; 
       $allrows = $wpdb->get_var( 
	   $wpdb->prepare(  "SELECT count(id) as count FROM $notice_table ",0,0 )
    );
    }elseif($type=='active'){
       $allrows = $wpdb->get_var( 
	   $wpdb->prepare(  "SELECT count(id) as count FROM $notice_table WHERE status = %d ", 1)
    );
    }
    elseif($type=='active'){
       $allrows = $wpdb->get_var( 
	   $wpdb->prepare(  "SELECT count(id) as count FROM $notice_table WHERE status = %d  ", 0 )
    );
    }
    return (int)$allrows;
}
function d_get_cats($notice_id = 0 ){
    global $wpdb,$notice_cat_table;
    $html = array();
    $allrows = $wpdb->get_results( "SELECT notice_id, category_id FROM $notice_cat_table WHERE notice_id = $notice_id " );
    if($allrows){
         foreach($allrows as $row){
            $term = get_term_by('id',$row->category_id,'product_cat');
            $html[] = $term->name;
        }
    }
    return implode(', ',$html);
}
function d_validate_id($notice_id = 0){
    global $wpdb,$notice_table;
    $allrows = $wpdb->get_results( "SELECT id FROM $notice_table WHERE id = $notice_id " );
    $ret = false;
    if($allrows){
        $ret = true;
    }
    return $ret;
}
function d_delete_notice($notice_id = 0){
    global $wpdb,$notice_cat_table, $notice_table;
    $wpdb->delete( $notice_table, array( 'id' => $notice_id ) );
    $wpdb->delete( $notice_cat_table, array( 'notice_id' => $notice_id ) );
    return true;
}

function d_get_all_active_cart_notices(){
    global $wpdb,$notice_table,$notice_cat_table;
    $cart_cats = d_get_cart_categories();
    $cart_products = get_all_items_in_cart();
    $active_notice_ids = array();
    if(is_array($cart_cats) && !empty($cart_cats)){
        foreach($cart_cats as $cart_cat_id){
            
            $allrows = $wpdb->get_results( "SELECT a.notice_id as notice_id, category_id, b.product_id as product_id FROM $notice_cat_table as a LEFT JOIN  $notice_table as b ON a.notice_id = b.id WHERE a.category_id = $cart_cat_id AND b.status = 1 " );
            if($allrows){
                 foreach($allrows as $row){
                    $active_notice_ids[] = $row->notice_id;
                }
            }
            
            
        }
    }
    $active_notice_ids = array_unique($active_notice_ids);
    return $active_notice_ids;
    
}
function d_get_cart_categories(){
    $cart_prod_ids = get_all_items_in_cart();
    $product_cats = array();
    if($cart_prod_ids){
        foreach($cart_prod_ids as $cart_prod_id){
            $this_product_cats = wp_get_object_terms( $cart_prod_id , 'product_cat' );
            if ( ! empty( $this_product_cats ) && ! is_wp_error( $this_product_cats ) ) {
                foreach( $this_product_cats as $this_product_cat ){
                    $product_cats[] = $this_product_cat->term_id;
                }
            }
        }
    }
    return $product_cats;
    
    
}
function get_all_items_in_cart(){
     global $woocommerce;
     $d_products = array();
     $products = array();
    foreach($woocommerce->cart->get_cart() as $cart_item_key => $values ) {
        $_product = $values['data'];
        $products[] = $_product->get_id();
    }
    $products = array_unique($products);
    return $products;

}
?>