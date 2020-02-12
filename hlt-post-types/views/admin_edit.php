<?php 
global $wpdb,$hlt_pt_table;
$hlt_pt_id = (int)$_GET['id'];
if(isset($_POST['edit_save'])){
    $hlt_pt_id = $_POST['hlt_pt_id'];
    $name = $_POST['name'];
    $menu_name = $_POST['menu_name'];
    $singular_name = $_POST['singular_name'];
    $slug = $_POST['slug'];
    $has_title = $_POST['has_title'];
    $has_editor = $_POST['has_editor'];
    $has_categories = $_POST['has_categories'];
    $has_custom_fields = $_POST['has_custom_fields'];
    $hlt_pt->display_section = $_POST['display_section'];
    $description = $_POST['description'];
    $status = $_POST['status'];
    $plural_name = $_POST['plural_name'];
    if(!empty($name) && !empty($menu_name) && !empty($singular_name) && !empty($plural_name) ){
        $wpdb->update( $hlt_pt_table, 
        	array( 
        		'name' => $name, 
        		'menu_name' => $menu_name, 
                'plural_name' => $plural_name,
                'singular_name' => $singular_name, 
                'has_title' => $has_title,
                'has_editor' => $has_editor,
                'has_categories' => $has_categories,
                'has_custom_fields' => $has_custom_fields,
                'display_section' => $hlt_pt->display_section,
                'description' => $description,
                'status' => $status 
        	), array('id' => $hlt_pt_id),
        	array( 
        		'%s',
                '%s',
        		'%s', 
                '%s', 
                '%d',
                '%d',
                '%d',
                '%d',
                '%d',
                '%s',
                '%d'
        	)
             
        );
        
        echo hlt_pt_display_msg('Post Type has been updated successfully',1);
    }
    else{
        //error
        echo hlt_pt_display_msg('All fields are required. Post Type was not added.',2);
    }
    
}

$hlt_pt = $wpdb->get_row("SELECT * FROM $hlt_pt_table WHERE id = $hlt_pt_id");
$already_saved = true;


?>

<div class="wrap">
<h2>Update Post Type <a style="margin-left: 20px;" class="add-new-h2" href="<?php echo admin_url('admin.php?page=hlt-pt-post-types&hlt_pg=field-settings&id='.$hlt_pt_id); ?>" >Fields Setup</a></h2>

<div class="form-wrap">
<form id="addtag" method="post" action="" class="validate">
<input name="hlt_pt_id" value="<?php echo $hlt_pt->id; ?>" type="hidden">

<div class="form-field">
    <div class="hlt-left">
        <div class="inner-hlt-left">
            <label for="parent">Post Type Name</label>
        </div>
        <div class="inner-hlt-right">
        	<input type="text" name="name" value="<?php if(isset($hlt_pt) && isset($hlt_pt->name)) echo $hlt_pt->name; ?>" />
        </div>
        <div style="clear: both;"></div>
    </div>
    <div class="hlt-right">
        <div class="inner-hlt-left">
            <label for="parent">Menu Name</label>
        </div>
        <div class="inner-hlt-right">
        	<input type="text" name="menu_name" value="<?php if(isset($hlt_pt) && isset($hlt_pt->menu_name)) echo $hlt_pt->menu_name; ?>" />
        </div>
        <div style="clear: both;"></div>
    </div>
</div>
<div style="clear: both;"></div>

<div class="form-field">
    <div class="hlt-left">
        <div class="inner-hlt-left">
            <label for="parent">Singular Name</label>
        </div>
        <div class="inner-hlt-right">
        	<input type="text" name="singular_name" value="<?php if(isset($hlt_pt) && isset($hlt_pt->singular_name)) echo $hlt_pt->singular_name; ?>" />
        </div>
        <div style="clear: both;"></div>
    </div>
    <div class="hlt-right">
        <div class="inner-hlt-left">
            <label for="parent">Plural Name</label>
        </div>
        <div class="inner-hlt-right">
        	<input type="text" name="plural_name" value="<?php if(isset($hlt_pt) && isset($hlt_pt->plural_name)) echo $hlt_pt->plural_name; ?>" />
        </div>
        <div style="clear: both;"></div>
    </div>
</div>
<div style="clear: both;"></div>
<div class="form-field">
    <div class="hlt-left">
        <div class="inner-hlt-left">
            <label for="parent">Slug</label>
        </div>
        <div class="inner-hlt-right">
        	<input type="text" disabled="disabled" name="slug" value="<?php if(isset($hlt_pt) && isset($hlt_pt->slug)) echo $hlt_pt->slug; ?>" />
        </div>
        <div style="clear: both;"></div>
    </div>
    <div class="hlt-right">
        <div class="inner-hlt-left">
            <label for="parent">Menu Position</label>
        </div>
        <div class="inner-hlt-right">
        	<select name="display_section" class="postform">
        	   <option value="3"  <?php if(isset($hlt_pt) && isset($hlt_pt->display_section) && $hlt_pt->display_section==3) echo ' selected="selected" '; ?>>After Dashboard</option>
               <option value="6"  <?php if(isset($hlt_pt) && isset($hlt_pt->display_section) && $hlt_pt->display_section==6) echo ' selected="selected" '; ?>>After Posts</option>
               <option value="11"  <?php if(isset($hlt_pt) && isset($hlt_pt->display_section) && $hlt_pt->display_section==11) echo ' selected="selected" '; ?>>After Media</option>
               <option value="16"  <?php if(isset($hlt_pt) && isset($hlt_pt->display_section) && $hlt_pt->display_section==16) echo ' selected="selected" '; ?>>After Links</option>
               <option value="21"  <?php if(isset($hlt_pt) && isset($hlt_pt->display_section) && $hlt_pt->display_section==21) echo ' selected="selected" '; ?>>After Pages</option>
               <option value="26"  <?php if(isset($hlt_pt) && isset($hlt_pt->display_section) && $hlt_pt->display_section==26) echo ' selected="selected" '; ?>>After Comments</option>
               <option value="61"  <?php if(isset($hlt_pt) && isset($hlt_pt->display_section) && $hlt_pt->display_section==61) echo ' selected="selected" '; ?>>After Appearance</option>
               <option value="66"  <?php if(isset($hlt_pt) && isset($hlt_pt->display_section) && $hlt_pt->display_section==66) echo ' selected="selected" '; ?>>After Plugins</option>7
               <option value="71"  <?php if(isset($hlt_pt) && isset($hlt_pt->display_section) && $hlt_pt->display_section==71) echo ' selected="selected" '; ?>>After Users</option>
               <option value="76"  <?php if(isset($hlt_pt) && isset($hlt_pt->display_section) && $hlt_pt->display_section==76) echo ' selected="selected" '; ?>>After Tools</option>
               <option value="81"  <?php if(isset($hlt_pt) && isset($hlt_pt->display_section) && $hlt_pt->display_section==81) echo ' selected="selected" '; ?>>After Settings</option>
            </select>
        </div>
        <div style="clear: both;"></div>
    </div>
</div>
<div style="clear: both;"></div>

<div class="form-field">
    <div class="hlt-left">
        <div class="inner-hlt-left">
            <label for="parent">Has Title?</label>
        </div>
        <div class="inner-hlt-right">
        	<select name="has_title" class="postform">
        	   <option value="1" <?php if(isset($hlt_pt) && isset($hlt_pt->has_title) && $hlt_pt->has_title==1) echo ' selected="selected" '; ?>>Yes</option>
               <option value="0"<?php if(isset($hlt_pt) && isset($hlt_pt->has_title) && $hlt_pt->has_title!=1) echo ' selected="selected" '; ?>>No</option>
            </select>
        </div>
        <div style="clear: both;"></div>
    </div>
    <div class="hlt-right">
        <div class="inner-hlt-left">
            <label for="parent">Has Editor?</label>
        </div>
        <div class="inner-hlt-right">
        	<select name="has_editor" class="postform">
        	   <option value="1" <?php if(isset($hlt_pt) && isset($hlt_pt->has_editor) && $hlt_pt->has_editor==1) echo ' selected="selected" '; ?>>Yes</option>
               <option value="0"<?php if(isset($hlt_pt) && isset($hlt_pt->has_editor) && $hlt_pt->has_editor!=1) echo ' selected="selected" '; ?>>No</option>
            </select>
        </div>
        <div style="clear: both;"></div>
    </div>
</div>
<div style="clear: both;"></div>

<div class="form-field">
    <div class="hlt-left">
        <div class="inner-hlt-left">
            <label for="parent">Has Categories?</label>
        </div>
        <div class="inner-hlt-right">
        	<select name="has_categories" class="postform">
        	   <option value="1" <?php if(isset($hlt_pt) && isset($hlt_pt->has_categories) && $hlt_pt->has_categories==1) echo ' selected="selected" '; ?>>Yes</option>
               <option value="0"<?php if(isset($hlt_pt) && isset($hlt_pt->has_categories) && $hlt_pt->has_categories!=1) echo ' selected="selected" '; ?>>No</option>
            </select>
        </div>
        <div style="clear: both;"></div>
    </div>
    <div class="hlt-right">
        <div class="inner-hlt-left">
            <label for="parent">Has Custom Fields?</label>
        </div>
        <div class="inner-hlt-right">
        	<select name="has_custom_fields" class="postform">
        	   <option value="1" <?php if(isset($hlt_pt) && isset($hlt_pt->has_custom_fields) && $hlt_pt->has_custom_fields==1) echo ' selected="selected" '; ?>>Yes</option>
               <option value="0" <?php if(isset($hlt_pt) && isset($hlt_pt->has_custom_fields) && $hlt_pt->has_custom_fields!=1) echo ' selected="selected" '; ?>>No</option>
            </select>
        </div>
        <div style="clear: both;"></div>
    </div>
</div>
<div style="clear: both;"></div>

<div class="form-field">
    <div class="hlt-left">
        <div class="inner-hlt-left">
            <label for="parent">Description</label>
        </div>
        <div class="inner-hlt-right">
        	<textarea name="description" cols="40" rows="5"><?php if(isset($hlt_pt) && isset($hlt_pt->description)) echo $hlt_pt->description; ?></textarea>
        </div>
        <div style="clear: both;"></div>
    </div>
    <div class="hlt-right">
        <div class="inner-hlt-left">
            <label for="parent">Status</label>
        </div>
        <div class="inner-hlt-right">
        	<select name="status" id="parent" class="postform">
        	   <option value="1" <?php if(isset($hlt_pt) && isset($hlt_pt->status) && $hlt_pt->status==1) echo ' selected="selected" '; ?>>Enabled</option>
               <option value="0" <?php if(isset($hlt_pt) && isset($hlt_pt->status) && $hlt_pt->status!=1) echo ' selected="selected" '; ?>>Disabled</option>
            </select>
        </div>
        <div style="clear: both;"></div>
    </div>
</div>
<div style="clear: both;"></div>


<p class="submit"><input name="edit_save" id="submit" class="button button-primary" value="Update Post Type" type="submit"></p></form></div>


<div id="ajax-response"></div>
<br class="clear">
</div>
<style type="text/css">
.hlt-right{
    float: right;
    width: 49%;
}
.hlt-left{
    float: left;
    width: 49%;
}
.inner-hlt-right{
    float: right;
    width: 70%;
}
.inner-hlt-left{
    
    float: left;
    width: 30%;
}
.inner-hlt-left label{
    font-size: 15px;
}
</style>