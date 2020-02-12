<?php 
if(isset($_POST['add_save'])){
    global $wpdb,$notice_table,$notice_cat_table,$charset_collate;
    $product_id = $_POST['product_id'];
    $notice_name = $_POST['notice_name'];
    $notice_text = $_POST['text'];
    $product_cats = $_POST['cats'];
    $status = $_POST['status'];
    if(!empty($product_id) && !empty($notice_name) && !empty($product_cats) ){
        $wpdb->insert( $notice_table, 
        	array( 
        		'name' => $notice_name, 
        		'product_id' => $product_id, 
                'text' => $notice_text, 
                'status' => $status 
        	), 
        	array( 
        		'%s', 
        		'%d', 
                '%s', 
                '%d'
        	) 
        );
        
        $notice_id = $wpdb->insert_id;
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
        echo d_display_msg('Cart Notice has been added successfully',1);
    }
    else{
        //error
        echo d_display_msg('All fields are required. Cart Notice was not added.',2);
    }
    
}
?>

<div class="wrap">
<h2>Add Cart Notice </h2>

<div class="form-wrap">
<form id="addtag" method="post" action="" class="validate">
<input name="action" value="add-tag" type="hidden">
<div class="form-field form-required term-name-wrap">
	<label for="tag-name">Name</label>
	<input name="notice_name" id="tag-name" value="" size="40" aria-required="true" type="text">
	<p>A descriptive name to identify this cart notice.</p>
</div>

<div class="form-field term-parent-wrap">
	<label for="parent">Product</label>
	<select name="product_id" id="parent" class="postform">
	<?php echo d_get_products_combo(0) ?>
</select>
			<p>The product to be added in the cart notice.</p>
	</div>
<div class="form-field term-description-wrap">
	<label for="tag-description">Categories</label>
	<?php
        $cats = d_get_categories_array();
        if(!empty($cats)){
            foreach($cats as $cat){
                echo ' <input type="checkbox" value="'.$cat['id'].'" name="cats[]" style="margin-left:20px"/> '.$cat['name'].'('.$cat['count'].')';
            }
        }
    ?>
    
    
    
	<p>Any of categories that are needed in cart to include the notice.</p>
</div>
<div class="form-field term-description-wrap">
	<label for="tag-description">Text to display</label>
	<textarea name="text" id="tag-description" rows="5" cols="80"></textarea>
	<p>This is the text to be displayed on the page when the notice is shown</p>
</div>
<div class="form-field term-parent-wrap">
	<label for="parent">Status</label>
	<select name="status" id="parent" class="postform">
	<option value="1">Enabled</option>
    <option value="0">Disabled</option>
</select>
			<p>The product to be added in the cart notice.</p>
	</div>
<p class="submit"><input name="add_save" id="submit" class="button button-primary" value="Add New Notice" type="submit"></p></form></div>


<div id="ajax-response"></div>
<br class="clear">
</div>