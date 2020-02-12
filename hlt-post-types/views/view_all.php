<div class="wrap">
<h2><?php echo $hlt_pt->name; ?>&nbsp; &gt;&gt; &nbsp;Field Settings <a href="<?php echo admin_url('admin.php?page=hlt-pt-post-types&hlt_pg=field-settings&id='.$p_type_id.'&action=add');?>" class="add-new-h2">Add Field</a></h2>

<?php 
    global $wpdb,$hlt_pt_fields_table;
    if(isset($_GET['action']) && isset($_GET['fid']) && isset($validity) && !$validity){
            echo hlt_pt_display_msg('Field record was not found',0);
    }elseif(isset($_GET['action']) && $_GET['action'] == 'del' && isset($_GET['fid'])){
        if(hlt_pt_delete_field($_GET['fid'])){
            echo hlt_pt_display_msg('Field record has been deleted successfully',1);
        }
        else{
            echo hlt_pt_display_msg('Field record was not found',0);
        }
    }
    
    
    $where = 1;
    $sts = 1;
    if(isset($_GET['sort']) && $_GET['sort'] == 'active'){
        $where = 'status = 1 ';
        $sts = 2;
    }elseif(isset($_GET['sort']) && $_GET['sort'] == 'disabled'){
        $where = 'status = 0 ';
        $sts = 3;
    }
    $allrows = $wpdb->get_results( "SELECT * FROM $hlt_pt_fields_table WHERE $where AND hlt_pt_id = $p_type_id " );
    $all_count = hlt_pt_fields_count_rows('all',$p_type_id);
    $active_count = hlt_pt_fields_count_rows('active',$p_type_id);
    $disabled_count = hlt_pt_fields_count_rows('disabled',$p_type_id);
    
    
?>




<ul class="subsubsub">
	<li class="all"><a href="<?php echo admin_url('admin.php?page=hlt-pt-post-types&action=field-settings&id='.$p_type_id); ?>" class="<?php if($sts==1) echo 'current'; ?>">All <span class="count">(<?php echo $all_count ?>)</span></a> |</li>
	<li class="publish"><a href="<?php echo admin_url('admin.php?page=hlt-pt-post-types&action=field-settings&id='.$p_type_id.'&sort=active'); ?>" class="<?php if($sts==2) echo 'current'; ?>">Active <span class="count">(<?php echo $active_count ?>)</span></a> |</li>
	<li class="trash"><a href="<?php echo admin_url('admin.php?page=hlt-pt-post-types&action=field-settings&id='.$p_type_id.'&sort=disabled'); ?>" class="<?php if($sts==3) echo 'current'; ?>">Disabled <span class="count">(<?php echo $disabled_count ?>)</span></a> </li>
	
</ul>
<form id="posts-filter" action="" method="get">


<table class="wp-list-table widefat fixed posts">
	<thead>
	<tr>
		<th scope="col" id="thumb" class="manage-column column-name" width="5%">ID</th>
        <th scope="col" id="thumb" class="manage-column column-name" width="45%">Name</th>
        <th scope="col" id="name" class="manage-column column-name" width="20%">Type</th>
        <th scope="col" id="name" class="manage-column column-name" width="15%">Order</th>
        <th scope="col" id="name" class="manage-column column-name" width="15%">Status</th>
   	</tr>
	</thead>

	<tbody id="the-list">
        <?php 
            if($allrows){
                foreach($allrows as $row){
                    ?>
        		<tr id="post-<?php echo $row->id?>" class="post-<?php echo $row->id?> hentry alternate iedit author-self level-0">
        		
        			<td class="name column-name"><?php echo $row->id?></td>
                    
                    <td class="name column-name hoverme">
                        <a href="<?php echo admin_url('admin.php?page=hlt-pt-post-types&hlt_pg=field-settings&id='.$p_type_id.'&action=edit&fid='.$row->id); ?>"><strong><?php echo $row->name?></strong></a> 
                        <div class="showme"> 
                            <a class="margleft" style="color: green;" href="<?php echo admin_url('admin.php?page=hlt-pt-post-types&hlt_pg=field-settings&id='.$p_type_id.'&action=edit&fid='.$row->id); ?>">Edit</a> 
                            <?php if($p_type_id!=6): ?>| 
                            <a class="margleft" style="color: red;" href="<?php echo admin_url('admin.php?page=hlt-pt-post-types&hlt_pg=field-settings&id='.$p_type_id.'&action=del&fid='.$row->id); ?>" onclick="return confirm('Are you sure you want to delete this field?')">Delete</a>
                            <?php endif; ?>
                            
                        </div> 
                    </td>
                    <td class="name column-name"><?php echo $row->type?></td>
                    <td class="name column-name"><?php echo $row->sort?></td>
                    <td class="name column-name"><?php echo $row->status==0?'Disabled':'Enabled'?></td>
                    
        		</tr>
                <?php
                }
            }
            else{
                ?>
                <tr id="post-5796" class="post-5796 hentry alternate iedit author-self level-0">
		
        			<td class="name column-name" colspan="4">No records found</td>
        		</tr>
                <?php
            }
        ?>
			
	</tbody>
</table>
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

</form>

<div id="ajax-response"></div>
<br class="clear">
</div>