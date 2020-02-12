<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' ); ?>
<div class="wrap">
<h2>Field Settings <a href="<?php echo $vtype==1? admin_url('edit.php?post_type=hlt-support&page=dim-hlt-settings&action=add') :  admin_url('edit.php?post_type=hlt-returns&page=dim-hlt-returns-settings&action=add'); ?>" class="add-new-h2">Add Field</a></h2>

<?php 
    global $wpdb,$dim_hlt_table;
    if(isset($_GET['action']) && isset($_GET['id']) && isset($validity) && !$validity){
            echo dim_hlt_display_msg('Field record was not found',0);
    }elseif(isset($_GET['action']) && $_GET['action'] == 'del' && isset($_GET['id'])){
        if(dim_hlt_delete_field($_GET['id'])){
            echo dim_hlt_display_msg('Field record has been deleted successfully',1);
        }
        else{
            echo dim_hlt_display_msg('Field record was not found',0);
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
    $allrows = $wpdb->get_results( "SELECT * FROM $dim_hlt_table WHERE $where AND refund=$vtype" );
    $all_count = $vtype==1? dim_hlt_count_rows('all') :  dim_hlt_returns_count_rows('all');
    $active_count = $vtype==1? dim_hlt_count_rows('active')  :  dim_hlt_returns_count_rows('active');
    $disabled_count = $vtype==1? dim_hlt_count_rows('disabled') :  dim_hlt_returns_count_rows('disabled');
    
    
?>




<ul class="subsubsub">
	<li class="all"><a href="<?php echo admin_url('edit.php?post_type=hlt-support&page=dim-hlt-settings'); ?>" class="<?php if($sts==1) echo 'current'; ?>">All <span class="count">(<?php echo $all_count ?>)</span></a> |</li>
	<li class="publish"><a href="<?php echo admin_url('edit.php?post_type=hlt-support&page=dim-hlt-settings&sort=active'); ?>" class="<?php if($sts==2) echo 'current'; ?>">Active <span class="count">(<?php echo $active_count ?>)</span></a> |</li>
	<li class="trash"><a href="<?php echo admin_url('edit.php?post_type=hlt-support&page=dim-hlt-settings&sort=disabled'); ?>" class="<?php if($sts==3) echo 'current'; ?>">Disabled <span class="count">(<?php echo $disabled_count ?>)</span></a> </li>
	
</ul>
<form id="posts-filter" action="" method="get">


<table class="wp-list-table widefat fixed posts">
	<thead>
	<tr>
		<th scope="col" id="cb" class="manage-column column-cb check-column" style="">ID</th>
        <th scope="col" id="thumb" class="manage-column column-name" style="">Name</th>
        <th scope="col" id="name" class="manage-column column-name" style="">Type</th>
        <th scope="col" id="name" class="manage-column column-name" style="">Order</th>
        <th scope="col" id="name" class="manage-column column-name" style="">Status</th>
   	</tr>
	</thead>

	<tbody id="the-list">
        <?php 
            if($allrows){
                foreach($allrows as $row){
                    ?>
        		<tr id="post-<?php echo $row->id?>" class="post-<?php echo $row->id?> hentry alternate iedit author-self level-0">
        		
        			<td class="name column-name"><?php echo $row->id?></td>
                    <td class="name column-name"><a href="<?php echo admin_url('edit.php?post_type=hlt-support&page=dim-hlt-settings&action=edit&id='.$row->id); ?>"><?php echo $row->name?></a> <br /> <br /> <a href="<?php echo admin_url('edit.php?post_type=hlt-support&page=dim-hlt-settings&action=edit&id='.$row->id); ?>">Edit</a> <a style="color: red; margin-left: 50px;" href="<?php echo admin_url('edit.php?post_type=hlt-support&page=dim-hlt-settings&action=del&id='.$row->id); ?>" onclick="return confirm('Are you sure you want to delete this field?')">Delete</a> </td>
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


</form>

<div id="ajax-response"></div>
<br class="clear">
</div>