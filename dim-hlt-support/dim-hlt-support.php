<?php 
/**
 * Plugin Name: HLT SUPPORT.
 * Plugin URI: http://gmarkhosting.com
 * Description: This is a plugin to handle returns and refunds logging. 
 * Version: 1.0.0
 * Author: Duncan I. Mwangi EMAIL: irungu.mwangi2@gmail.com
 * Author URI: http://gmarkhosting.com
 * License: GPL2
 */
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

register_activation_hook( __FILE__, 'dim_hlt_install' );

function dim_custom_post_type() {

	$labels = array(
		'name'                => _x( 'HLT REFUNDS', 'Post Type General Name'),
		'singular_name'       => _x( 'Refund', 'Post Type Singular Name' ),
		'menu_name'           => __( 'HLT REFUNDS' ),
		'parent_item_colon'   => __( 'Parent Refund' ),
		'all_items'           => __( 'All Refunds' ),
		'view_item'           => __( 'View Refund' ),
		'add_new_item'        => __( 'Add New Refund' ),
		'add_new'             => __( 'Add New' ),
		'edit_item'           => __( 'Edit' ),
		'update_item'         => __( 'Update Refund' ),
		'search_items'        => __( 'Search' ),
		'not_found'           => __( 'Not Found' ),
		'not_found_in_trash'  => __( 'Not found in Trash' ),
	);
	
	
	$args = array(
		'label'               => __( 'Refunds' ),
		'description'         => __( 'Refund' ),
		'labels'              => $labels,
		'supports'            => array( 'title','custom-fields' ),
		'taxonomies'          => array( 'hlt-categories' ),
		'hierarchical'        => false,
		'public'              => false,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 5,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => true,
		'publicly_queryable'  => false,
		'capability_type'     => 'page',
	);
	
	// Registering your Custom Post Type
	register_post_type( 'hlt-support', $args );


    $labels2 = array(
		'name'                => _x( 'HLT RETURNS', 'Post Type General Name'),
		'singular_name'       => _x( 'Return', 'Post Type Singular Name' ),
		'menu_name'           => __( 'HLT RETURNS' ),
		'parent_item_colon'   => __( 'Parent RETURNS' ),
		'all_items'           => __( 'All Returns' ),
		'view_item'           => __( 'View Return' ),
		'add_new_item'        => __( 'Add New Return' ),
		'add_new'             => __( 'Add New' ),
		'edit_item'           => __( 'Edit' ),
		'update_item'         => __( 'Update Return' ),
		'search_items'        => __( 'Search' ),
		'not_found'           => __( 'Not Found' ),
		'not_found_in_trash'  => __( 'Not found in Trash' ),
	);
	
	
	$args2 = array(
		'label'               => __( 'Returns' ),
		'description'         => __( 'Return' ),
		'labels'              => $labels2,
		'supports'            => array( 'title','custom-fields' ),
		'taxonomies'          => array( 'hlt-categories-returns' ),
		'hierarchical'        => false,
		'public'              => false,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 5,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => true,
		'publicly_queryable'  => false,
		'capability_type'     => 'page',
	);
	
	// Registering your Custom Post Type
	register_post_type( 'hlt-returns', $args2 );
}

function create_hlt_support_taxonomies() {
	// Add new taxonomy, make it hierarchical (like categories)
	$labels = array(
		'name'              => _x( 'Categories', 'taxonomy general name' ),
		'singular_name'     => _x( 'Category', 'taxonomy singular name' ),
		'search_items'      => __( 'Search Categories' ),
		'all_items'         => __( 'All Categories' ),
		'parent_item'       => __( 'Parent Category' ),
		'parent_item_colon' => __( 'Parent Category:' ),
		'edit_item'         => __( 'Edit Category' ),
		'update_item'       => __( 'Update Category' ),
		'add_new_item'      => __( 'Add New Category' ),
		'new_item_name'     => __( 'New Category Name' ),
		'menu_name'         => __( 'Category' ),
	);

	$args = array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'hlt-categories' ),
	);

	register_taxonomy( 'hlt-categories', array( 'hlt-support' ), $args );
    
    // Add new taxonomy, make it hierarchical (like categories)
	$labels2 = array(
		'name'              => _x( 'Categories', 'taxonomy general name' ),
		'singular_name'     => _x( 'Category', 'taxonomy singular name' ),
		'search_items'      => __( 'Search Categories' ),
		'all_items'         => __( 'All Categories' ),
		'parent_item'       => __( 'Parent Category' ),
		'parent_item_colon' => __( 'Parent Category:' ),
		'edit_item'         => __( 'Edit Category' ),
		'update_item'       => __( 'Update Category' ),
		'add_new_item'      => __( 'Add New Category' ),
		'new_item_name'     => __( 'New Category Name' ),
		'menu_name'         => __( 'Category' ),
	);

	$args2 = array(
		'hierarchical'      => true,
		'labels'            => $labels2,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'hlt-categories-returns' ),
	);

	register_taxonomy( 'hlt-categories-returns', array( 'hlt-returns' ), $args2 );
    
    
}

function create_hlt_support_fields(){
    
    global $wpdb,$dim_hlt_table;
    $allrows = $wpdb->get_results( "SELECT * FROM $dim_hlt_table WHERE status=1 AND refund = 1 ORDER BY sort ASC" );
    if($allrows){
        
        add_meta_box(
			'hlt_support_sect',
			'Details',
			function($post, $allrows){
			     ?>
                    <table cellpadding="10">
                 <?php
			     foreach($allrows['args'] as $row){
			         $value = get_post_meta( $post->ID, '_hlt_value_'.$row->id.'_key', true );
                     if($row->type=='text'){
                        dim_hlt_create_textfield($value,$row);
                     }
                     if($row->type=='select'){
                        dim_hlt_create_selectfield($value,$row);
                     }
                     if($row->type=='checkbox'){
                        dim_hlt_create_checkbox($value,$row);
                     }
                     if($row->type=='textarea'){
                        dim_hlt_create_textarea($value,$row);
                     }
                        
		         }
                 ?>
                    </table>
                 <?php
			},
			'hlt-support',
            'normal',
           'high',
           $allrows
		);
    }
}
function create_hlt__returns_support_fields(){
    
    global $wpdb,$dim_hlt_table;
    $allrows = $wpdb->get_results( "SELECT * FROM $dim_hlt_table WHERE status=1 AND refund = 0 ORDER BY sort ASC" );
    if($allrows){
        
        add_meta_box(
			'hlt_support_sect',
			'Details',
			function($post, $allrows){
			     ?>
                    <table cellpadding="10">
                 <?php
			     foreach($allrows['args'] as $row){
			         $value = get_post_meta( $post->ID, '_hlt_value_'.$row->id.'_key', true );
                     if($row->type=='text'){
                        dim_hlt_create_textfield($value,$row);
                     }
                     if($row->type=='select'){
                        dim_hlt_create_selectfield($value,$row);
                     }
                     if($row->type=='checkbox'){
                        dim_hlt_create_checkbox($value,$row);
                     }
                     if($row->type=='textarea'){
                        dim_hlt_create_textarea($value,$row);
                     }
                        
		         }
                 ?>
                    </table>
                 <?php
			},
			'hlt-returns',
            'normal',
           'high',
           $allrows
		);
    }
}
function save_hlt_support_fields_data($post_id){
    
    global $wpdb,$dim_hlt_table;
    $allrows = $wpdb->get_results( "SELECT * FROM $dim_hlt_table WHERE status=1 ORDER BY sort ASC" );
    if($allrows){
        foreach($allrows as $row){
            $field_name = 'field'.$row->id;
            // Make sure that field is set.
            if(! empty( $_POST[$field_name] )){
                 // Sanitize user input.
                 if(is_array($_POST[$field_name])){
                    $_POST[$field_name] = implode(',',$_POST[$field_name]);
                 }
            	$my_data = sanitize_text_field( $_POST[$field_name] );
                // Update the meta field in the database.
            	update_post_meta( $post_id, '_hlt_value_'.$row->id.'_key', $my_data );
            }
        }
    }
}
add_action( 'init', 'dim_custom_post_type', 0 );
add_action( 'init', 'create_hlt_support_taxonomies', 0 );
add_action( 'add_meta_boxes', 'create_hlt_support_fields' );
add_action( 'add_meta_boxes', 'create_hlt__returns_support_fields' );
add_action( 'save_post', 'save_hlt_support_fields_data');
require_once('settings.php');
function dim_hlt_create_textfield($value,$row){
    ?>
    <tr>
        <td>
    	<label for="field<?php echo $row->id ?>"><?php echo $row->name ?></label>
        </td>
        <td>
    	<input name="field<?php echo $row->id ?>" value="<?php echo $value ?>" size="50" aria-required="true" type="text"/>
        </td>
    </tr>
    <?php
}

function dim_hlt_create_selectfield($value,$row){
    $options = explode(',',$row->options);
    ?>
    <tr>
        <td>
    	<label for="field<?php echo $row->id ?>"><?php echo $row->name ?></label>
        </td>
        <td>
    	<select name="field<?php echo $row->id ?>">
            <?php
                if(is_array($options))
                    foreach($options as $option){
                        $option = trim($option);
                        $selectd = $option==$value ?' selected="selected" ': '';
                        echo '<option value="'.$option.'" '.$selectd.'>'.$option.'</option>';
                    }
             ?>
        </select>
        </td>
    </tr>
    <?php
}
function dim_hlt_create_checkbox($value,$row){
    $options = explode(',',$row->options);
    ?>
    <tr>
        <td>
    	<label for="field<?php echo $row->id ?>"><?php echo $row->name ?></label>
        </td>
        <td>
    	       <?php
               $value = explode(',', $value);
                if(is_array($options))
                    foreach($options as $option){
                        $option = trim($option);
                        
                        $selectd = in_array($option, $value) ?' checked="checked" ': '';
                        echo '<input name="field'.$row->id.'[]" type="checkbox" value="'.$option.'" '.$selectd.'/> '.$option.' ';
                    }
             ?>
        </td>
    </tr>
    <?php
}
function dim_hlt_create_textarea($value,$row){
    ?>
    <tr>
        <td style="vertical-align: text-top;">
    	   <label for="field<?php echo $row->id ?>"><?php echo $row->name ?></label>
        </td>
        <td>
    	   <textarea name="field<?php echo $row->id ?>" cols="50" rows="5"><?php echo $value ?></textarea>
        </td>
    </tr>
    <?php
}
remove_action('wp_head','noindex',1);
add_action('wp_head', 'xx_my_no_follow', 1);

function xx_my_no_follow() {
    if ( '0' == get_option('blog_public') ) {
        echo "<meta name='robots' content='noindex,nofollow' />\n";
    }
} 
?>