<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' ); ?><?php

    
    $qstr = $_SERVER['QUERY_STRING'];
    $qstr = str_replace('&search=search','',$qstr);
    if (isset($_GET['upg'])) { $page = $_GET['upg']; $qstr = str_replace('&upg='.$page,'',$qstr); } else { $page=1; }
    $start_from = ($page-1) * 20;

    $sql = "SELECT * FROM $dim_amb_tbl ";
    $where = " WHERE 1 ";
    $order_str = '';
    if(isset($_GET['orderby']) && !empty($_GET['orderby'])){
        $order_by = $_GET['orderby'];

    }else{
        $order_by = 'id';
    }
    if(isset($_GET['order']) && !empty($_GET['order'])){
        $order = $_GET['order'];
    }else{
        $order = 'DESC';
    }
    $order_str.='&orderby='.$order_by.'&order='.$order;

    $limit = " ORDER BY $order_by $order LIMIT $start_from, 20";

    $limitx = " ORDER BY $order_by $order";

    $allrows = $my_wpdb->get_results( $sql.$where.$limit );
    
    $allrowsxx = $my_wpdb->get_results( $sql.$where );

    ?>
    <div class="wrap">
    <h2>Ambassador Program Leads
    <a href="<?=admin_url('admin.php?page=hlt-amb-leads&view=add')?>" class="page-title-action">Add New</a>
    </h2>
    <?php if(isset($msg)) echo $msg; ?>
    <form id="posts-filter" action="" method="get">
        <input type="hidden" name="page" value="hlt-amb-leads" />

        <table id="pub_table" class="wp-list-table widefat fixed posts display" style="margin-top: 20px;">
            <thead>
            <tr>
                <th scope="col" id="thumb" class="manage-column column-name" width="5%">No.</th>
                <th scope="col" id="thumb" class="manage-column column-name" width="5%">ID</th>
                <th scope="col" id="thumb" class="manage-column column-name"  width="30%">Email Address</th>
                <th scope="col" id="thumb" class="manage-column column-name"  width="15%">First Name</th>
                <th scope="col" id="thumb" class="manage-column column-name"  width="20%">Last Name</th>
                <th scope="col" id="thumb" class="manage-column column-name"  width="15%">User</th>
                <th scope="col" id="name" class="manage-column column-name"  width="10%">Action</th>
            </tr>
            </thead>

            <tbody id="the-list">
            <?php
            if($allrows){
                $count = $start_from+1;
                
                foreach($allrows as $row){

                    ?>
                    <tr id="post-<?php echo $row->id?>" class="post-<?php echo $row->id?> hentry alternate iedit author-self level-0">

                        <td class="name column-name"><?php echo $count++?></td>
                        <td class="name column-name"><?php echo $row->id?></td>
                        <td class="name column-name"><a href="<?=admin_url('admin.php?page=hlt-amb-leads&view=edit&id='.$row->id)?>"><?php echo $row->Email?></a></td>
                        <td class="name column-name"><?php echo $row->First_Name?></td>
                        <td class="name column-name"><?php echo $row->Last_Name?></td>
                        <td class="name column-name"><?php echo $row->Support_Rep; ?></td>
                        <td class="name column-name"><a style="color: green;" href="<?=admin_url('admin.php?page=hlt-amb-leads&view=edit&id='.$row->id)?>">Edit</a> <a onclick="return confirm('Are you sure you want to delete this Lead?');" style="margin-left: 20px; color: red;" href="<?=admin_url('admin.php?page=hlt-amb-leads&view=del&id='.$row->id)?>">Delete</a></td>

                    </tr>
                    <?php
                }
            }
            else{
                ?>
                <tr id="post-5796" class="post-5796 hentry alternate iedit author-self level-0">

                    <td class="name column-name" colspan="7">No records found</td>
                </tr>
                <?php
            }
            ?>

            </tbody>
        </table>

        <?php //$this->pagination(admin_url('admin.php?'.$qstr),$sql,$where,$page); ?>
        <style>
            #pub_table th a{
                color: #80C421;
            }
            #pub_table th a:hover{
                text-decoration: underline;
            }
        </style>

    </form>




    <div id="ajax-response"></div>
    <br class="clear">
</div>