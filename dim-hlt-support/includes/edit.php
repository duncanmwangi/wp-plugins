<?php 
global $wpdb,$dim_hlt_table;
$field_id = (int)$_GET['id'];
if(isset($_POST['edit_save'])){
    $field_id = $_POST['field_id'];
    $field_name = $_POST['field_name'];
    $type = $_POST['type'];
    $options = $_POST['options'];
    $description = $_POST['description'];
    $sort = (int)$_POST['sort'];
    $status = $_POST['status'];
    if(!empty($field_name) && !empty($type) && !empty($status)  ){
        $wpdb->update( $dim_hlt_table, 
        	array( 
        		'name' => $field_name, 
        		'type' => $type, 
                'options' => $options, 
                'description' => $description, 
                'sort' => $sort,
                'refund' => $vtype,  
                'status' => $status 
        	), array('id' => $field_id),
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
        
        echo d_display_msg('Field Record has been updated successfully',1);
    }
    else{
        //error
        echo d_display_msg('All fields are required. Field Record was not added.',2);
    }
    
}

$field = $wpdb->get_row("SELECT * FROM $dim_hlt_table WHERE id = $field_id");


?>

<div class="wrap">
<h2>Update Field Record </h2>

<div class="form-wrap">
<form id="addtag" method="post" action="" class="validate">
<input name="field_id" value="<?php echo $field->id; ?>" type="hidden">
<div class="form-field form-required term-name-wrap">
	<label for="tag-name">Field Label</label>
	<input name="field_name" value="<?php echo $field->name; ?>" size="40" aria-required="true" type="text">
	<p>A descriptive name to for the field.</p>
</div>

<div class="form-field term-parent-wrap">
	<label for="parent">Field Type</label>
	<select name="type" id="parent" class="postform">
	   <option value="text" <?php if($field->status=='text') echo ' selected="selected"'; ?>>Text Field</option>
       <option value="checkbox" <?php if($field->status=='checkbox') echo ' selected="selected"'; ?>>Check Box</option>
       <option value="select" <?php if($field->status=='select') echo ' selected="selected"'; ?>>Select</option>
       <option value="textarea" <?php if($field->status=='textarea') echo ' selected="selected"'; ?>>Text Area</option>
</select>
			<p>The type of field to be added.</p>
	</div>

<div class="form-field term-description-wrap">
	<label for="tag-description">Field Options</label>
	<textarea name="options" rows="5" cols="70"><?php echo $field->options; ?></textarea>
	<p>Comma separated field options of the field</p>
</div>
<div class="form-field form-required term-name-wrap">
	<label for="tag-name">Description</label>
	<input name="description" value="<?php echo $field->description; ?>" size="40" aria-required="true" type="text">
	<p>A description for the field.</p>
</div>
<div class="form-field form-required term-name-wrap">
	<label for="tag-name">Sort Order</label>
	<input name="sort" value="<?php echo $field->sort; ?>" size="5" aria-required="true" type="text">
	<p>The sort order in which fields will be displayed.</p>
</div>

<div class="form-field term-parent-wrap">
	<label for="parent">Status</label>
	<select name="status" id="parent" class="postform">
	<option value="1" <?php if($field->status==1) echo ' selected="selected"'; ?>>Enabled</option>
    <option value="0" <?php if($field->status==0) echo ' selected="selected"'; ?>>Disabled</option>
</select>
			<p>The product to be added in the Field Record.</p>
	</div>
<p class="submit"><input name="edit_save" id="submit" class="button button-primary" value="Update Field" type="submit"></p></form></div>


<div id="ajax-response"></div>
<br class="clear">
</div>