<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' ); ?>
<div class="wrap">
<h2>Cart Notices <a href="<?php echo admin_url('admin.php?page=duncan-cart-notices-page&action=add'); ?>" class="add-new-h2">Add Notice</a></h2>

<?php 
    global $wpdb,$notice_table,$notice_cat_table;
    if(isset($_GET['action']) && isset($_GET['id']) && isset($validity) && !$validity){
            echo d_display_msg('Cart Notice was not found',0);
    }elseif(isset($_GET['action']) && $_GET['action'] == 'del' && isset($_GET['id'])){
        if(d_delete_notice($_GET['id'])){
            echo d_display_msg('Cart Notice has been deleted successfully',1);
        }
        else{
            echo d_display_msg('Cart Notice was not found',0);
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
    $allrows = $wpdb->get_results( "SELECT id, name, product_id, status FROM $notice_table WHERE $where" );
    $all_count = d_count_rows('all');
    $active_count = d_count_rows('active');
    $disabled_count = d_count_rows('disabled');
    
    
?>




<ul class="subsubsub">
	<li class="all"><a href="<?php echo admin_url('admin.php?page=duncan-cart-notices-page'); ?>" class="<?php if($sts==1) echo 'current'; ?>">All <span class="count">(<?php echo $all_count ?>)</span></a> |</li>
	<li class="publish"><a href="<?php echo admin_url('admin.php?page=duncan-cart-notices-page&sort=active'); ?>" class="<?php if($sts==2) echo 'current'; ?>">Active <span class="count">(<?php echo $active_count ?>)</span></a> |</li>
	<li class="trash"><a href="<?php echo admin_url('admin.php?page=duncan-cart-notices-page&sort=disabled'); ?>" class="<?php if($sts==3) echo 'current'; ?>">Disabled <span class="count">(<?php echo $disabled_count ?>)</span></a> </li>
	
</ul>
<form id="posts-filter" action="" method="get">


<table class="wp-list-table widefat fixed posts">
	<thead>
	<tr>
		<th scope="col" id="cb" class="manage-column column-cb check-column" style="">ID</th>
        <th scope="col" id="thumb" class="manage-column column-name" style="">Name</th>
        <th scope="col" id="name" class="manage-column column-name" style="">Product</th>
        <th scope="col" id="name" class="manage-column column-name" style="">Categories</th>
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
                    <td class="name column-name"><a href="<?php echo admin_url('admin.php?page=duncan-cart-notices-page&action=edit&id='.$row->id); ?>"><?php echo $row->name?></a> <br /> <br /> <a href="<?php echo admin_url('admin.php?page=duncan-cart-notices-page&action=edit&id='.$row->id); ?>">Edit</a> <a style="color: red; margin-left: 50px;" href="<?php echo admin_url('admin.php?page=duncan-cart-notices-page&action=del&id='.$row->id); ?>" onclick="return confirm('Are you sure you want to delete this notice?')">Delete</a> </td>
                    <td class="name column-name"><a href="<?php echo admin_url('admin.php?page=duncan-cart-notices-page&action=edit&id='.$row->id)?>"><?php echo get_the_title($row->product_id)?></a> <br /><br /> <a href="<?php echo get_the_permalink($row->product_id)?>" target="_blank">View Product page</a></td>
                    <td class="name column-name"><?php echo d_get_cats($row->id)?></td>
                    <td class="name column-name"><?php echo $row->status==0?'Disabled':'Enabled'?></td>
                    
        		</tr>
                <?php
                }
            }
            else{
                ?>
                <tr id="post-5796" class="post-5796 hentry alternate iedit author-self level-0">
		
        			<td class="name column-name" colspan="6">No records found</td>
        		</tr>
                <?php
            }
        ?>
		<tr id="post-5796" class="post-5796 hentry alternate iedit author-self level-0">
		
			<td class="name column-name"></td>
		</tr>
			
	</tbody>
</table>


</form>

<div id="ajax-response"></div>
<br class="clear">
</div>