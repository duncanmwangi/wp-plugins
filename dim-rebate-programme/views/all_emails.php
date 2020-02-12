<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' ); ?><?php


    $qstr = $_SERVER['QUERY_STRING'];
    $qstr = str_replace('&search=search','',$qstr);
    if (isset($_GET['upg'])) { $page = $_GET['upg']; $qstr = str_replace('&upg='.$page,'',$qstr); } else { $page=1; }
    $start_from = ($page-1) * $this->items_per_page;

    $sql_orders = "SELECT email_id as el_id, sum(order_amount) as order_amount FROM $this->rebate_orders_tbl GROUP BY email_id ";
    $sql_rebates = "SELECT email_id as em_id, sum(rebate_amount) as rebate_amount, sum(coupon_amount) as coupon_amount FROM $this->rebates_tbl GROUP BY email_id ";
    $sql = "SELECT a.*,COALESCE(b.order_amount,0) as order_amount,COALESCE(c.rebate_amount,0) as rebate_amount,COALESCE(c.coupon_amount,0) as coupon_amount,COALESCE((COALESCE(b.order_amount,0) - COALESCE(c.rebate_amount,0)),0) as unrebated_amount FROM $this->rebate_emails_tbl as a LEFT JOIN ($sql_orders) as b ON b.el_id = a.email_id LEFT JOIN ($sql_rebates) as c ON c.em_id = a.email_id";
    $where = " WHERE 1 ";
    if(!isset($_GET['fdate']) || empty($_GET['fdate']) ){
        //$_GET['fdate'] = '01-'.date('m-Y');
    }
    if(isset($_GET['email_address']) && !empty($_GET['email_address'])){
        $email_address = $_GET['email_address'];
        $where.=" AND email LIKE '%$email_address%' ";
    }
    

    $order_str = '';
    if(isset($_GET['orderby']) && !empty($_GET['orderby'])){
        $order_by = $_GET['orderby'];

    }else{
        $order_by = 'email';
    }
    if(isset($_GET['order']) && !empty($_GET['order'])){
        $order = $_GET['order'];
    }else{
        $order = 'ASC';
    }
    $order_str.='&orderby='.$order_by.'&order='.$order;
    //$this->items_per_page = 1050;
    $limit = " ORDER BY $order_by $order LIMIT $start_from, $this->items_per_page";
    $limitx = " ORDER BY $order_by $order";
    $allrows = $this->db->get_results( $sql.$where.$limit );
    //echo $sql.$where; 
   
    $this->db_error();
    $this->update_all_phone_numbers();
    ?>
    <div class="wrap">
    <h2>All Emails</h2>
    <form id="posts-filter" action="" method="get">
        <input type="hidden" name="page" value="drp-emails" />
         <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">

<table class="widefat s_tbl">
    <tr class="form-field">
        <td>Email Address</td>
        <td><input type="text" name="email_address" class="email_address" value="<?php if(isset($email_address)) echo $email_address;  ?>" /></td>
        <td><input name="search" id="submit" class="button button-primary" value="Search" type="submit"></td>
    </tr>
</table>

        <table id="pub_table" class="wp-list-table widefat fixed posts display" style="margin-top: 20px;">
            <thead>
            <tr>
                <th scope="col" id="thumb" class="manage-column column-name" width="5%">No.</th>
                <th scope="col" id="thumb" class="manage-column column-name" width="5%">ID</th>
                <th scope="col" id="thumb" class="manage-column column-name"  width="30%">Email Address</th>
                <th scope="col" id="thumb" class="manage-column column-name"  width="15%">Total Order Amount</th>
                <th scope="col" id="thumb" class="manage-column column-name"  width="20%">Total Rebated Amount</th>
                <th scope="col" id="thumb" class="manage-column column-name"  width="15%">Unrebated Amount</th>
                <th scope="col" id="name" class="manage-column column-name"  width="10%">Total Rebates</th>
            </tr>
            </thead>

            <tbody id="the-list">
            <?php
            if($allrows){
                $count = $start_from+1;
                foreach($allrows as $row){
                    /*
                    if($row->unrebated_amount<250 && $row->unrebated_amount > 0):
                    $time = current_time('mysql');
                    $user_array = array(
                        'rebate_code'  =>  '',
                        'rebate_code_expiry_date'  =>  '',
                        'date_added'  =>  $time,
                        'email'  =>  $row->email,
                        'first_name'  =>  '',
                        'last_name'  =>  '',
                        'phone'  =>  '',
                        'user_type'  =>  'REBATE-UNEARNED',
                        'unrebated_amount' => $row->unrebated_amount
                    );
                    $dat = Dim_Transactional_Emails::add_user($user_array);
                    
                    endif;
                    */
                    ?>
                    <tr id="post-<?php echo $row->email_id?>" class="post-<?php echo $row->email_id?> hentry alternate iedit author-self level-0">

                        <td class="name column-name"><?php echo $count++?></td>
                        <td class="name column-name"><?php echo $row->email_id?></td>
                        <td class="name column-name"><a href="<?=admin_url('admin.php?page=drp-rebates&action=view&id='.$row->email_id)?>"><?php echo $row->email?></a></td>
                        <td class="name column-name"><?php echo $row->order_amount?></td>
                        <td class="name column-name"><?php echo $row->rebate_amount?></td>
                        <td class="name column-name"><?php echo $row->unrebated_amount; ?></td>
                        <td class="name column-name"><?php echo $row->coupon_amount; ?></td>
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

        <?php $this->pagination(admin_url('admin.php?'.$qstr),$sql,$where,$page); ?>
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