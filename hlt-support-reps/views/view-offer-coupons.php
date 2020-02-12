<?php

    
    $qstr = $_SERVER['QUERY_STRING'];
    $qstr = str_replace('&search=search','',$qstr);
    if (isset($_GET['upg'])) { $page = $_GET['upg']; $qstr = str_replace('&upg='.$page,'',$qstr); } else { $page=1; }
    $start_from = ($page-1) * 20;
    $sql = "
        SELECT *,IF(expiry_date<curdate(), 'YES', 'NO') as expired, IF(status=0, 'NO', 'YES') as used FROM $cop_offer_coupons_tbl 
    ";

  
    $where = " WHERE user_id = $user_id ";
    $limit = " ORDER BY id DESC LIMIT $start_from, 20";
    $allrows = $wpdb->get_results( $sql.$where.$limit );
    //echo $sql.$where.$limit;
    
    if(isset($_GET['action']) && $_GET['action']=='del' && isset($_GET['id']) && !empty($_GET['id']))
     {
        $offer_id = (int)$_GET['id'];
     }
   
    ?>
    <div class="wrap">
    <h2>OFFER COUPONS
    </h2>
<?php if(isset($msg)) echo $msg; ?>

        <table id="pub_table" class="wp-list-table widefat fixed posts display" style="margin-top: 20px;">
            <thead>
            <tr>
                <th scope="col" id="thumb" class="manage-column column-name" width="5%">No.</th>
                <th scope="col" id="thumb" class="manage-column column-name" width="5%">ID</th>
                <th scope="col" id="thumb" class="manage-column column-name"  width="20%">Coupon</th>
                <th scope="col" id="thumb" class="manage-column column-name"  width="10%">Date Created</th>
                <th scope="col" id="thumb" class="manage-column column-name"  width="10%">Expired?</th>
                <th scope="col" id="thumb" class="manage-column column-name"  width="10%">Used?</th>
                <th scope="col" id="thumb" class="manage-column column-name"  width="10%">Date Used</th>
                <th scope="col" id="name" class="manage-column column-name"  width="10%">Order ID</th>
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
                        <td class="name column-name"><?php echo $row->coupon_code?></td>
                        <td class="name column-name"><?php echo date('d-m-Y',strtotime($row->date_created))?></td>
                        <td class="name column-name"><?php echo $row->expired?></td>
                        <td class="name column-name"><?php echo $row->used?></td>
                        <td class="name column-name"><?php echo $row->date_used!='0000-00-00 00:00:00'?date('d-m-Y',strtotime($row->date_used)):''; ?></td>
                        <td class="name column-name"><?php echo $row->order_id; ?></td>


                    </tr>
                    <?php
                }
            }
            else{
                ?>
                <tr id="post-5796" class="post-5796 hentry alternate iedit author-self level-0">

                    <td class="name column-name" colspan="8">No records found</td>
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




    <div id="ajax-response"></div>
    <br class="clear">
</div>