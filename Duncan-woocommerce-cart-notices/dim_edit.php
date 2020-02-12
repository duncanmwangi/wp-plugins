<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' ); ?>
<?php 
global $wpdb,$notice_table,$notice_cat_table;
$notice_id = (int)$_GET['id'];
if(isset($_POST['edit_save'])){
    $notice_id = $_POST['notice_id'];
    $product_id = $_POST['product_id'];
    $notice_name = $_POST['notice_name'];
    $notice_text = $_POST['text'];
    $product_cats = $_POST['cats'];
    $status = $_POST['status'];
    if(!empty($product_id) && !empty($notice_name) && !empty($product_cats) ){
        $wpdb->update( $notice_table, 
        	array( 
        		'name' => $notice_name, 
        		'product_id' => $product_id,
                'text' => $notice_text, 
                'status' => $status 
        	), array('id' => $notice_id),
        	array( 
        		'%s', 
        		'%d', 
                '%s',
                '%d'
        	)
             
        );
        $wpdb->delete( $notice_cat_table, array( 'notice_id' => $notice_id ) );
        if($notice_id && is_array($product_cats)){
            foreach($product_cats as $cat_id){
                $wpdb->insert( $notice_cat_table, 
                	array( 
                		'notice_id' => $notice_id, 
                        'category_id' => $cat_id 
                	), 
                	array( 
                		'%d', 
                        '%d'
                	) 
                );
            }
        }
        echo d_display_msg('Cart Notice has been updated successfully',1);
    }
    else{
        //error
        echo d_display_msg('All fields are required. Cart Notice was not added.',2);
    }
    
}

$notice = $wpdb->get_row("SELECT * FROM $notice_table WHERE id = $notice_id");

$catrows = $wpdb->get_results( "SELECT notice_id, category_id FROM $notice_cat_table WHERE notice_id = $notice_id " );
$cats = array();
if($catrows){
    foreach($catrows as $catrow){
        $catrs[] = $catrow->category_id;
    }
}
?>

<div class="wrap">
<h2>Update Cart Notice </h2>

<div class="form-wrap">
<form id="addtag" method="post" action="" class="validate">
<input name="notice_id" value="<?php echo $notice->id; ?>" type="hidden">
<div class="form-field form-required term-name-wrap">
	<label for="tag-name">Name</label>
	<input name="notice_name" id="tag-namem" size="40" value="<?php echo $notice->name?>" type="text">
	<p>A descriptive name to identify this cart notice.</p>
</div>

<div class="form-field term-parent-wrap">
	<label for="parent">Product</label>
	<select name="product_id" id="parent" class="postform">
	<?php echo d_get_products_combo($notice->product_id) ?>
</select>
			<p>The product to be added in the cart notice.</p>
	</div>
<div class="form-field term-description-wrap">
	<label for="tag-description">Categories</label>
	<?php
        $cats = d_get_categories_array();
        if(!empty($cats)){
            foreach($cats as $cat){
                $checked = in_array($cat['id'],$catrs)? 'checked="checked"':'';
                echo ' <input type="checkbox" '.$checked.' value="'.$cat['id'].'" name="cats[]" style="margin-left:20px"/> '.$cat['name'].'('.$cat['count'].')';
            }
        }
    ?>
    
    
    
	<p>Any of categories that are needed in cart to include the notice.</p>
</div>
<div class="form-field term-description-wrap">
	<label for="tag-description">Text to display</label>
	<textarea name="text" id="tag-description" rows="5" cols="80"><?php echo stripslashes($notice->text)?></textarea>
	<p>This is the text to be displayed on the page when the notice is shown</p>
</div>
<div class="form-field term-parent-wrap">
	<label for="parent">Status</label>
	<select name="status" id="parent" class="postform">
	<option value="1" <?php if($notice->status==1) echo ' selected="selected"'; ?>>Enabled</option>
    <option value="0" <?php if($notice->status==0) echo ' selected="selected"'; ?>>Disabled</option>
</select>
			<p>The product to be added in the cart notice.</p>
	</div>
<p class="submit"><input name="edit_save" id="submit" class="button button-primary" value="Update Notice" type="submit"></p></form></div>


<div id="ajax-response"></div>
<br class="clear">
</div>