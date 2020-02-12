<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' ); ?>
<?php 

function hlt_pt_install() {
	global $wpdb,$hlt_pt_table, $hlt_pt_fields_table, $hlt_pt_charset_collate;
	global $hlt_pt_db_version;
	$hlt_pt_charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $hlt_pt_table (
		id int(9) NOT NULL AUTO_INCREMENT,
        name varchar(50) NOT NULL,
        menu_name varchar(50) NOT NULL,
        singular_name varchar(50) NOT NULL,
        plural_name varchar(50) NOT NULL,
        slug varchar(50) NOT NULL,
		has_title int(9) DEFAULT '1' NOT NULL,
        has_editor int(9) DEFAULT '1' NOT NULL,
        has_categories int(9) DEFAULT '1' NOT NULL,
        has_custom_fields int(9) DEFAULT '1' NOT NULL,
        display_section int(9) DEFAULT '1' NOT NULL,
        description text NOT NULL,
        sort int(9) DEFAULT '1' NOT NULL,
        status int(9) DEFAULT '1' NOT NULL,
        UNIQUE KEY slug (slug),
		PRIMARY KEY id (id)
	) $hlt_pt_charset_collate; ";
    $sql.="CREATE TABLE $hlt_pt_fields_table (
		id int(9) NOT NULL AUTO_INCREMENT,
        hlt_pt_id int(9) NOT NULL,        
        name varchar(50) NOT NULL,
		type varchar(15) DEFAULT 'text' NOT NULL,
        options text NOT NULL,
        description text NOT NULL,
        feature_column int(9) NOT NULL,
        sort int(9) DEFAULT '1' NOT NULL,
        status int(9) DEFAULT '1' NOT NULL,
		PRIMARY KEY id (id)
	) $hlt_pt_charset_collate; ";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
    
	add_option( 'hlt_pt_db_version', $hlt_pt_db_version );
}
function hlt_pt_register_admin_pages() {
    add_menu_page( 'HLT POST TYPES', 'HLT POST TYPES', 'manage_options', 'hlt-pt-post-types', 'hlt_pt_admin_listing_page','dashicons-admin-generic', 6 );
    add_submenu_page( 'hlt-pt-post-types', 'View All', 'View All', 'manage_options', 'hlt-pt-post-types', 'hlt_pt_admin_listing_page' );
    add_submenu_page( 'hlt-pt-post-types', 'Add New', 'Add New', 'manage_options', 'hlt-pt-add', 'hlt_pt_admin_add_page' ); 
}
global $hlt_pt_post_type;

function hlt_pt_create_all_post_types(){
    global $wpdb,$hlt_pt_table,$hlt_pt_post_type;
    $post_types = $wpdb->get_results( "SELECT * FROM $hlt_pt_table WHERE status = 1 " );
    
    if($post_types){
        foreach($post_types as $post_type){
            $hlt_pt_post_type = $post_type;
            //print_r($hlt_pt_post_type);
            hlt_pt_create_post_type($post_type);
            add_filter( 'manage_'.$post_type->slug.'_posts_columns', function($columns) use ($post_type) {
                global $wpdb,$hlt_pt_fields_table,$hlt_pt_post_type;
                $remove_cols = array('taxonomy-'.$post_type->slug.'-cats','date');
                $columnsx =$columns;
                foreach($remove_cols as $r_col){
                    unset($columns[$r_col]);
                }
                $add_cols = array();
                $hpt_idx = $post_type->id;
                $fields = $wpdb->get_results( "SELECT * FROM $hlt_pt_fields_table WHERE status = 1 AND feature_column=1 AND hlt_pt_id=$hpt_idx " );
                if($fields){
                    foreach($fields as $field){
                        $slug = sanitize_title($field->name);
                        $columns['_hlt_value_'.$field->id.'_key'] = $field->name;
                    }
                }
                $columns['date'] = $columnsx['date'];
                return $columns;
                
            });
            add_action( 'manage_'.$post_type->slug.'_posts_custom_column' , function($column, $post_id) use ($post_type){
                if($column != 'date' && $column != 'title' && $column != 'cb'){
                    echo get_post_meta( $post_id , $column , true ); 
                }
            }, 10, 2 );
            
            add_filter("manage_edit-".$post_type->slug."_sortable_columns", function($columns) use ($post_type){
                global $wpdb,$hlt_pt_fields_table,$hlt_pt_post_type;
                $columnsx = array();
                $hpt_idx = $post_type->id;
                $fields = $wpdb->get_results( "SELECT * FROM $hlt_pt_fields_table WHERE status = 1 AND feature_column=1 AND hlt_pt_id=$hpt_idx " );
                if($fields){
                    foreach($fields as $field){
                        $slug = sanitize_title($field->name);
                        $columns['_hlt_value_'.$field->id.'_key'] = '_hlt_value_'.$field->id.'_key';
                    }
                }
            	return $columns;
            });

        }
    }
}


function set_custom_hlt_columns($columns) {
    
                global $wpdb,$hlt_pt_fields_table,$hlt_pt_post_type;
                $remove_cols = array('taxonomy-'.$hlt_pt_post_type->slug.'-cats');
                foreach($remove_cols as $r_col){
                    unset($columns[$r_col]);
                }
                $add_cols = array();
                $hpt_idx = $hlt_pt_post_type->id;
                $fields = $wpdb->get_results( "SELECT * FROM $hlt_pt_fields_table WHERE status = 1 AND feature_column=1 AND hlt_pt_id=$hpt_idx " );
                if($fields){
                    foreach($fields as $field){
                        $slug = sanitize_title($field->name);
                        $columns[$slug] = $field->name;
                    }
                }
                $columns['miti'] = 'MIIIIII';
                return $columns;
}



function hlt_pt_create_post_type($post_type){
    //create post type
    $labels = array(
		'name'                => _x( $post_type->name, 'Post Type General Name'),
		'singular_name'       => _x( $post_type->singular_name, 'Post Type Singular Name' ),
		'menu_name'           => __( $post_type->menu_name ),
		'parent_item_colon'   => __( 'Parent '.$post_type->singular_name ),
		'all_items'           => __( 'All '.$post_type->plural_name ),
		'view_item'           => __( 'View '.$post_type->singular_name ),
		'add_new_item'        => __( 'Add New '.$post_type->singular_name ),
		'add_new'             => __( 'Add New' ),
		'edit_item'           => __( 'Edit' ),
		'update_item'         => __( 'Update '.$post_type->singular_name ),
		'search_items'        => __( 'Search' ),
		'not_found'           => __( 'Not Found' ),
		'not_found_in_trash'  => __( 'Not found in Trash' ),
	);
	
	$supports = array();
    if($post_type->has_editor==1) $supports[]='editor';
    if($post_type->has_title==1) $supports[]='title';
    if($post_type->has_custom_fields==1) $supports[]='custom-fields';
    $capability_type = $post_type->slug;
    $caps = array(
        'edit_post'		         => "edit_{$capability_type}",
    	'read_post'		         => "read_{$capability_type}",
    	'delete_post'		     => "delete_{$capability_type}",
    	'edit_posts'		     => "edit_{$capability_type}s",
    	'edit_others_posts'	     => "edit_others_{$capability_type}s",
    	'publish_posts'		     => "publish_{$capability_type}s",
    	'read_private_posts'	 => "read_private_{$capability_type}s",
        'delete_posts'           => "delete_{$capability_type}s",
        'delete_private_posts'   => "delete_private_{$capability_type}s",
        'delete_published_posts' => "delete_published_{$capability_type}s",
        'delete_others_posts'    => "delete_others_{$capability_type}s",
        'edit_private_posts'     => "edit_private_{$capability_type}s",
        'edit_published_posts'   => "edit_published_{$capability_type}s",
        'create_posts'           => "edit_{$capability_type}s",
    );
	$args = array(
		'label'               => __( $post_type->plural_name ),
		'description'         => __( $post_type->singular_name ),
		'labels'              => $labels,
		'supports'            => $supports,
		'taxonomies'          => array(),
		'hierarchical'        => true,
		'public'              => false,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => $post_type->display_section,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => true,
		'publicly_queryable'  => false,
		'capability_type'     => $capability_type,
        'capabilities' => $caps,
	);
	
	// Registering your Custom Post Type
	register_post_type( $post_type->slug, $args );
    //create post type category taxonomy
    $tax_labels = array(
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

	$tax_args = array(
		'hierarchical'      => true,
		'labels'            => $tax_labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => $post_type->slug.'-cats' ),
	);

	register_taxonomy( $post_type->slug.'-cats', array( $post_type->slug ), $tax_args );
    
    //set capabilities
    
    if(is_array($caps)){
        $hlt_support_reps = get_role( 'hlt_support_reps' );
        $hlt_administrator = get_role( 'administrator' );
        foreach($caps as $cap_key => $cap_value){
            if($hlt_support_reps)
                $hlt_support_reps->add_cap($cap_value);
            if($hlt_administrator)
                $hlt_administrator->add_cap($cap_value);
        }
    }
    
    
}

function hlt_pt_settings_page() {
    $dtype = 'refunds';
    $vtype = 1;
    if( isset($_GET['id'])){
        //$validity = dim_hlt_validate_id($_GET['id']);
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


function hlt_pt_create_fields(){
    
    global $wpdb,$hlt_pt_table,$hlt_pt_fields_table;
    $p_type_rows = $wpdb->get_results( "SELECT * FROM $hlt_pt_table WHERE status=1 " );
    if($p_type_rows){
        foreach($p_type_rows as $p_type_row){
            $allrows = $wpdb->get_results( "SELECT * FROM $hlt_pt_fields_table WHERE status=1 AND hlt_pt_id = $p_type_row->id ORDER BY sort ASC" );
            if($allrows){
                
                add_meta_box(
        			$p_type_row->slug,
        			'Details',
        			function($post, $allrows){
        			     ?>
                            <table cellpadding="10">
                         <?php
        			     foreach($allrows['args'] as $row){
        			         $value = get_post_meta( $post->ID, '_hlt_value_'.$row->id.'_key', true );
                             if($row->type=='text'){
                                hlt_pt_create_textfield($value,$row);
                             }
                             if($row->type=='select'){
                                hlt_pt_create_selectfield($value,$row);
                             }
                             if($row->type=='checkbox'){
                                hlt_pt_create_checkbox($value,$row);
                             }
                             if($row->type=='textarea'){
                                hlt_pt_create_textarea($value,$row);
                             }
                                
        		         }
                         ?>
                            </table>
                         <?php
        			},
        			$p_type_row->slug,
                    'normal',
                   'high',
                   $allrows
        		);
            }
        }
    }
    
}
function hlt_pt_delete_post($post_id){
    if(function_exists('ult_delete_tracking_refunds')){
        ult_delete_tracking_refunds($post_id);
    }
}

function hlt_pt_restore_post($post_id){
    if(function_exists('ult_restore_tracking_refunds')){
        ult_restore_tracking_refunds($post_id);
    }
}
function hlt_pt_save_fields($post_id){
    global $wpdb,$hlt_pt_table,$hlt_pt_fields_table;
    $p_type_rows = $wpdb->get_results( "SELECT * FROM $hlt_pt_table WHERE status=1 " );
    if($p_type_rows){
        foreach($p_type_rows as $p_type_row){
            $allrows = $wpdb->get_results( "SELECT * FROM $hlt_pt_fields_table WHERE status=1 AND hlt_pt_id = $p_type_row->id ORDER BY sort ASC" );
            if($allrows){
                $exec = 0;
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
                        
                        //log refunds on the tracking plugin
                        if($p_type_row->slug == 'refunds-main' && function_exists('ult_log_tracking_refunds') && $exec==0){
                            $_order_id = sanitize_text_field( $_POST['field15']);
                            
                            $refund_amount = sanitize_text_field( $_POST['field17']);
                            $refund_amount = str_replace('$','',$refund_amount);
                            $date_added = get_the_date( 'Y-m-d H:i:s', $post_id );
                            $refund_data = array('order_id'=>$_order_id,'post_id'=>$post_id,'refund_amount'=>$refund_amount,'date_added'=>$date_added);
                            ult_log_tracking_refunds($refund_data);
                            $exec++;
                        }
                    }
                    
                }
            }
        }
    }
}
function hlt_pt_admin_listing_page(){
    global $wpdb,$hlt_pt_table;
    if( isset($_GET['id'])){
        $validity = hlt_pt_validate_id($_GET['id']);
    }
    if( isset($_GET['hlt_pg']) && $_GET['hlt_pg'] == 'field-settings' && $validity ){
        $p_type_id =$_GET['id'];
        $hlt_pt = $wpdb->get_row("SELECT * FROM $hlt_pt_table WHERE id = $p_type_id");
        if( isset($_GET['fid'])){
            $field_validity = hlt_pt_field_validate_id($_GET['fid']);
        }
        if( isset($_GET['action']) && $_GET['action'] == 'add'){
            require_once('views/add.php');
        }elseif( isset($_GET['action']) && $_GET['action'] == 'edit' && $field_validity){
            require_once('views/edit.php');
        }
        else{
           require_once('views/view_all.php'); 
        }
    }
    elseif( isset($_GET['hlt_pg']) && $_GET['hlt_pg'] == 'column-settings' && $validity ){
        $p_type_id =$_GET['id'];
        $hlt_pt = $wpdb->get_row("SELECT * FROM $hlt_pt_table WHERE id = $p_type_id");
        require_once('views/columns.php'); 
    }
    elseif( isset($_GET['action']) && $_GET['action'] == 'edit' && $validity){
        require_once('views/admin_edit.php');
    }
    
    else{
       require_once('views/admin_view_all.php'); 
    }
}
function hlt_pt_admin_add_page(){
    require_once('views/admin_add.php');
}
function hlt_pt_field_validate_id($field_id = 0){
    global $wpdb,$hlt_pt_fields_table;
    $allrows = $wpdb->get_results( "SELECT id FROM $hlt_pt_fields_table WHERE id = $field_id " );
    $ret = false;
    if($allrows){
        $ret = true;
    }
    return $ret;
}
function hlt_pt_validate_id($hlt_pt_id = 0){
    global $wpdb,$hlt_pt_table;
    $allrows = $wpdb->get_results( "SELECT id FROM $hlt_pt_table WHERE id = $hlt_pt_id " );
    $ret = false;
    if($allrows){
        $ret = true;
    }
    return $ret;
}
function hlt_pt_display_msg($msg = '', $type=1){
    $type = $type==1?'updated fade': 'error';
    return '<div class="'.$type.'"><p>'.$msg.'</p></div>';
}
function hlt_pt_delete_post_type($hlt_pt_id = 0){
    global $wpdb,$hlt_pt_table;
    $wpdb->delete( $hlt_pt_table, array( 'id' => $hlt_pt_id ) );
    return true;
}
function hlt_pt_delete_field($field_id = 0){
    global $wpdb,$hlt_pt_fields_table;
    $wpdb->delete( $hlt_pt_fields_table, array( 'id' => $field_id ) );
    return true;
}
function hlt_pt_fields_count_rows($type='all',$p_type_id){
    global $wpdb,$hlt_pt_fields_table;
    $where = 1;
    if($type=='all'){
       $where = 1; 
       $allrows = $wpdb->get_var( 
	   $wpdb->prepare(  "SELECT count(id) as count FROM $hlt_pt_fields_table WHERE hlt_pt_id = $p_type_id " )
    );
    }elseif($type=='active'){
       $allrows = $wpdb->get_var( 
	   $wpdb->prepare(  "SELECT count(id) as count FROM $hlt_pt_fields_table WHERE status = %d  AND hlt_pt_id = %d  ", 1,$p_type_id)
    );
    }
    elseif($type=='disabled'){
       $allrows = $wpdb->get_var( 
	   $wpdb->prepare(  "SELECT count(id) as count FROM $hlt_pt_fields_table WHERE status = %d  AND hlt_pt_id = %d ", 0,$p_type_id )
    );
    }
    return (int)$allrows;
}
function hlt_pt_count_rows($type='all'){
    global $wpdb,$hlt_pt_table;
    $where = 1;
    if($type=='all'){
       $where = 1; 
       $allrows = $wpdb->get_var( 
	   $wpdb->prepare(  "SELECT count(id) as count FROM $hlt_pt_table " )
    );
    }elseif($type=='active'){
       $allrows = $wpdb->get_var( 
	   $wpdb->prepare(  "SELECT count(id) as count FROM $hlt_pt_table WHERE status = %d   ", 1)
    );
    }
    elseif($type=='disabled'){
       $allrows = $wpdb->get_var( 
	   $wpdb->prepare(  "SELECT count(id) as count FROM $hlt_pt_table WHERE status = %d  ", 0 )
    );
    }
    return (int)$allrows;
}
function hlt_pt_slug($string) {
    global $wpdb,$hlt_pt_table;
   $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
   $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
   $string = preg_replace('/-+/', '-', strtolower($string)); // Replaces multiple hyphens with single one.
   $allrows = $wpdb->get_results( "SELECT id FROM $hlt_pt_table WHERE slug = '$string' " );
   if($allrows){
    return hlt_pt_slug($string.'-2');
   }
   return $string;
}
function hlt_pt_create_textfield($value,$row){
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

function hlt_pt_create_selectfield($value,$row){
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
function hlt_pt_create_checkbox($value,$row){
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
function hlt_pt_create_textarea($value,$row){
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

function hlt_pt_create_user_roles(){
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

?>