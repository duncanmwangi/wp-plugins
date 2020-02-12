<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' ); ?><?php 
if(isset($_POST['add_save'])){
    global $wpdb,$dim_hlt_table;
    $field_name = $_POST['field_name'];
    $type = $_POST['type'];
    $options = $_POST['options'];
    $description = $_POST['description'];
    $sort = (int)$_POST['sort'];
    $status = $_POST['status'];
    if(!empty($field_name) && !empty($type) && !empty($status) ){
        $wpdb->insert( $dim_hlt_table, 
        	array( 
        		'name' => $field_name, 
        		'type' => $type, 
                'options' => $options, 
                'description' => $description, 
                'sort' => $sort,
                'refund' => $vtype, 
                'status' => $status 
        	), 
        	array( 
        		'%s', 
        		'%s', 
                '%s', 
                '%s',
                '%d',
                '%d',
                '%d'
        	) 
        );
        
        echo dim_hlt_display_msg('Field record has been added successfully',1);
    }
    else{
        //error
        echo dim_hlt_display_msg('All fields are required. Field record was not added.',2);
    }
    
}
?>

<div class="wrap">
<h2>Add Field</h2>

<div class="form-wrap">
<form id="addtag" method="post" action="" class="validate">
<input name="action" value="add-tag" type="hidden">
<div class="form-field form-required term-name-wrap">
	<label for="tag-name">Field Label</label>
	<input name="field_name" value="" size="40" aria-required="true" type="text">
	<p>A descriptive name to for the field.</p>
</div>

<div class="form-field term-parent-wrap">
	<label for="parent">Field Type</label>
	<select name="type" id="parent" class="postform">
	   <option value="text">Text Field</option>
       <option value="checkbox">Check Box</option>
       <option value="select">Select</option>
       <option value="textarea">Text Area</option>
</select>
			<p>The type of field to be added.</p>
	</div>

<div class="form-field term-description-wrap">
	<label for="tag-description">Field Options</label>
	<textarea name="options" rows="5" cols="70"></textarea>
	<p>Comma separated field options of the field</p>
</div>
<div class="form-field form-required term-name-wrap">
	<label for="tag-name">Description</label>
	<input name="description" value="" size="40" aria-required="true" type="text">
	<p>A description for the field.</p>
</div>
<div class="form-field form-required term-name-wrap">
	<label for="tag-name">Sort Order</label>
	<input name="sort" value="" size="5" aria-required="true" type="text">
	<p>The sort order in which fields will be displayed.</p>
</div>
<div class="form-field term-parent-wrap">
	<label for="parent">Status</label>
	<select name="status" id="parent" class="postform">
	<option value="1">Enabled</option>
    <option value="0">Disabled</option>
</select>
	</div>
<p class="submit"><input name="add_save" id="submit" class="button button-primary" value="Add Field" type="submit"></p></form></div>


<div id="ajax-response"></div>
<br class="clear">
</div>