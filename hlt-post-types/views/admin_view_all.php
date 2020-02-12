<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' ); ?>
<div class="wrap">
<h2>HLT POST TYPES <a href="<?php echo admin_url('admin.php?page=hlt-pt-add'); ?>" class="add-new-h2">Add New</a></h2>

<?php 
    global $wpdb,$hlt_pt_table;
    if(isset($_GET['action']) && isset($_GET['id']) && isset($validity) && !$validity){
            echo hlt_pt_display_msg('Post Type was not found',0);
    }elseif(isset($_GET['action']) && $_GET['action'] == 'del' && isset($_GET['id'])){
        if(hlt_pt_delete_post_type($_GET['id'])){
            echo hlt_pt_display_msg('Post Type has been deleted successfully',1);
        }
        else{
            echo hlt_pt_display_msg('Post Type was not found',0);
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
    $allrows = $wpdb->get_results( "SELECT * FROM $hlt_pt_table WHERE $where" );
    $all_count = hlt_pt_count_rows('all');
    $active_count = hlt_pt_count_rows('active');
    $disabled_count = hlt_pt_count_rows('disabled');
    
    
?>




<ul class="subsubsub">
	<li class="all"><a href="<?php echo admin_url('admin.php?page=hlt-pt-post-types'); ?>" class="<?php if($sts==1) echo 'current'; ?>">All <span class="count">(<?php echo $all_count ?>)</span></a> |</li>
	<li class="publish"><a href="<?php echo admin_url('admin.php?page=hlt-pt-post-types&sort=active'); ?>" class="<?php if($sts==2) echo 'current'; ?>">Active <span class="count">(<?php echo $active_count ?>)</span></a> |</li>
	<li class="trash"><a href="<?php echo admin_url('admin.php?page=hlt-pt-post-types&sort=disabled'); ?>" class="<?php if($sts==3) echo 'current'; ?>">Disabled <span class="count">(<?php echo $disabled_count ?>)</span></a> </li>
	
</ul>
<form id="posts-filter" action="" method="get">


<table class="wp-list-table widefat fixed posts">
	<thead>
	<tr>
		<th scope="col" id="thumb" class="manage-column column-name" width="5%">ID</th>
        <th scope="col" id="thumb" class="manage-column column-name" width="40%">Name</th>
        <th scope="col" id="name" class="manage-column column-name" width="20%">Menu Name</th>
        <th scope="col" id="name" class="manage-column column-name" width="10%">Slug</th>
        <th scope="col" id="name" class="manage-column column-name" width="15%">Singular Name</th>
        <th scope="col" id="name" class="manage-column column-name" width="10%">Status</th>
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
                        <a  href="<?php echo admin_url('admin.php?page=hlt-pt-post-types&action=edit&id='.$row->id); ?>"><strong><?php echo $row->name?></strong></a> 
                        <div class="showme"> 
                            <a class="margleft" style="color: green;" href="<?php echo admin_url('admin.php?page=hlt-pt-post-types&action=edit&id='.$row->id); ?>">Edit</a>| 
                            <?php if($row->id!=6): ?>
                            <a class="margleft" style="color: red;"  href="<?php echo admin_url('admin.php?page=hlt-pt-post-types&action=del&id='.$row->id); ?>" onclick="return confirm('Are you sure you want to delete this field?')">Delete</a>|
                            <?php endif; ?>
                            <a class="margleft" href="<?php echo admin_url('admin.php?page=hlt-pt-post-types&hlt_pg=field-settings&id='.$row->id); ?>">Fields Setup</a>|
                            <a class="margleft" href="<?php echo admin_url('admin.php?page=hlt-pt-post-types&hlt_pg=column-settings&id='.$row->id); ?>">Columns Setup</a>
                            
                        </div> 
                    </td>
                    <td class="name column-name"><?php echo $row->menu_name?></td>
                    <td class="name column-name"><?php echo $row->slug?></td>
                    <td class="name column-name"><?php echo $row->singular_name?></td>
                    <td class="name column-name"><?php echo $row->status==0?'Disabled':'Enabled'?></td>
                    
        		</tr>
                <?php
                }
            }
            else{
                ?>
                <tr id="post-1" class="post-1 hentry alternate iedit author-self level-0">
		
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