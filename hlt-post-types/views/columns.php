<?php 
global $wpdb,$hlt_pt_fields_table;
 
if(isset($_POST['edit_save'])){
    $column_ids = $_POST['column_ids'];
    $wpdb->update( $hlt_pt_fields_table, array('feature_column' => 0 ), array('hlt_pt_id' => $p_type_id), array( '%d'));
    if(!empty($column_ids) && is_array($column_ids)){
        foreach($column_ids as $field_id)
            $wpdb->update( $hlt_pt_fields_table, array('feature_column' => 1 ), array('id' => $field_id), array( '%d'));
            
    }
    echo hlt_pt_display_msg('Columns have been updated successfully',1);
    
}


$allrows = $wpdb->get_results( "SELECT * FROM $hlt_pt_fields_table WHERE hlt_pt_id = $p_type_id AND status = 1 ORDER BY sort ASC " );

 ?>
<div class="wrap">
<h2><?php echo $hlt_pt->name; ?>&nbsp; &gt;&gt; &nbsp;Set Columns <a href="<?php echo admin_url('admin.php?page=hlt-pt-post-types');?>" class="add-new-h2">Back</a></h2>

<div class="form-wrap">
<form id="addtag" method="post" action="" class="validate">


<table class="wp-list-table widefat fixed posts">
	<thead>
	<tr>
		<th scope="col" id="thumb" class="manage-column column-name" width="5%">No.</th>
        <th scope="col" id="thumb" class="manage-column column-name" width="10%">Field ID</th>
        <th scope="col" id="name" class="manage-column column-name" width="40%">Field Name</th>
        <th scope="col" id="name" class="manage-column column-name" width="15%">Field Type</th>
        <th scope="col" id="name" class="manage-column column-name" width="30%">Show In List</th>
   	</tr>
	</thead>

	<tbody id="the-list">
        <?php 
            if($allrows){
                $count = 1;
                foreach($allrows as $row){
                    ?>
        		<tr id="post-<?php echo $row->id?>" class="post-<?php echo $row->id?> hentry alternate iedit author-self level-0">
        		
        			<td class="name column-name"><?php echo $count++?></td>
                    <td class="name column-name"><?php echo $row->id?></td>
                    <td class="name column-name"><?php echo $row->name?></td>
                    <td class="name column-name"><?php echo $row->type?></td>
                    <td class="name column-name"><input type="checkbox" name="column_ids[]" <?php if($row->feature_column == 1) echo ' checked="checked" ' ?> value="<?php echo $row->id?>" /></td>
                    
        		</tr>
                <?php
                }
            }
            else{
                ?>
                <tr id="post-5796" class="post-5796 hentry alternate iedit author-self level-0">
		
        			<td class="name column-name" colspan="5">No records found</td>
        		</tr>
                <?php
            }
        ?>
			
	</tbody>
</table>
<p class="submit"><input name="edit_save" id="submit" class="button button-primary" value="Save Columns" type="submit"></p>
<style>
.tablenav-pages .current-page {
    padding-top: 0px;
    text-align: center;
}
.widefat td, .widefat th {
    overflow: hidden;
    color: #555;
    border-bottom: 1px solid #e5e5e5;
    border-left: 1px solid #e5e5e5;
}
.alt, .alternate {
    background-color: #fff;
}

.showme {
visibility: hidden;
}

.hoverme:hover > .showme {
visibility: visible ;
}
.margleft{
    margin-left: 10px;
    margin-right: 10px;
}
</style>

</form></div>


<div id="ajax-response"></div>
<br class="clear">
</div>