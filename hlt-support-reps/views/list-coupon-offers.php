<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' ); ?>
<?php
    if(isset($_GET['action']) && $_GET['action']=='create' && $offer){

        $coupon_code = cop_generate_coupon_code($offer);
        $wpdb->insert( $cop_offer_coupons_tbl, 
                        array( 'offer_id' => $offer->id, 'user_id' => $user_id,'coupon_code' => $coupon_code , 'status' => 0 , 'date_created' => current_time( 'mysql' ),'expiry_date'=>date('Y-m-d h:i:s',strtotime("+ $offer->days_to_expire days")) ) , 
                        array( '%s', '%s', '%s', '%s', '%s', '%s' ) );
        $msg = cop_display_msg('A new coupon code for the <strong>'.$offer->name.'</strong> has been created. See the code below:<br/><br/><strong>'.$coupon_code.'</strong>');
    }
    
    $qstr = $_SERVER['QUERY_STRING'];
    $qstr = str_replace('&search=search','',$qstr);
    if (isset($_GET['upg'])) { $page = $_GET['upg']; $qstr = str_replace('&upg='.$page,'',$qstr); } else { $page=1; }
    $start_from = ($page-1) * 20;
    $sql = "
        SELECT a.*, COALESCE(all_coupons,0) as all_coupons,COALESCE(used,0) as used,COALESCE(unused,0) as unused,COALESCE(unused_unexpired,0) as unused_unexpired,COALESCE(unused_expired,0) as unused_expired FROM $cop_offers_tbl as a
        LEFT JOIN (SELECT offer_id, COALESCE(count(id),0) as all_coupons FROM $cop_offer_coupons_tbl WHERE user_id = $user_id AND 1 GROUP BY offer_id) as b ON b.offer_id = a.id
        LEFT JOIN (SELECT offer_id, COALESCE(count(id),0) as used FROM $cop_offer_coupons_tbl WHERE user_id = $user_id AND status=1 GROUP BY offer_id) as c ON c.offer_id = a.id
        LEFT JOIN (SELECT offer_id, COALESCE(count(id),0) as unused FROM $cop_offer_coupons_tbl WHERE user_id = $user_id AND status=0 GROUP BY offer_id) as d ON d.offer_id = a.id
        LEFT JOIN (SELECT offer_id, COALESCE(count(id),0) as unused_unexpired FROM $cop_offer_coupons_tbl WHERE user_id = $user_id AND status=0 AND expiry_date>CURDATE() GROUP BY offer_id) as e ON e.offer_id = a.id
        LEFT JOIN (SELECT offer_id, COALESCE(count(id),0) as unused_expired FROM $cop_offer_coupons_tbl WHERE user_id = $user_id AND status=0 AND expiry_date<=CURDATE() GROUP BY offer_id) as f ON f.offer_id = a.id

    ";

  
    $where = " WHERE 1 ";
    $limit = " ORDER BY id ASC LIMIT $start_from, 20";
    $allrows = $wpdb->get_results( $sql.$where.$limit );
    //echo $sql.$where.$limit;
    
    if(isset($_GET['action']) && $_GET['action']=='del' && isset($_GET['id']) && !empty($_GET['id']))
     {
        $offer_id = (int)$_GET['id'];
     }
   
    ?>
    <div class="wrap">
    <h2>OFFERS
    </h2>
<?php if(isset($msg)) echo $msg; ?>

        <table id="pub_table" class="wp-list-table widefat fixed posts display" style="margin-top: 20px;">
            <thead>
            <tr>
                <th scope="col" id="thumb" class="manage-column column-name" width="5%">No.</th>
                <th scope="col" id="thumb" class="manage-column column-name" width="5%">ID</th>
                <th scope="col" id="thumb" class="manage-column column-name"  width="20%">Name</th>
                <th scope="col" id="thumb" class="manage-column column-name"  width="10%">All</th>
                <th scope="col" id="thumb" class="manage-column column-name"  width="10%">Used</th>
                <th scope="col" id="thumb" class="manage-column column-name"  width="10%">Unused</th>
                <th scope="col" id="thumb" class="manage-column column-name"  width="10%">Unused Unexpired</th>
                <th scope="col" id="name" class="manage-column column-name"  width="10%">Unused expired</th>
                <th scope="col" id="name" class="manage-column column-name"  width="20%">Action</th>
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
                        <td class="name column-name"><?php echo $row->name?></td>
                        <td class="name column-name"><?php echo $row->all_coupons?></td>
                        <td class="name column-name"><?php echo $row->used?></td>
                        <td class="name column-name"><?php echo $row->unused?></td>
                        <td class="name column-name"><?php echo $row->unused_unexpired; ?></td>
                        <td class="name column-name"><?php echo $row->unused_expired; ?></td>
                        <td class="name column-name"><a href="<?=admin_url('admin.php?page=hlt-offer-coupons&action=create&id='.$row->id)?>" title="Generate New Coupon under this offer"><?php echo 'Create New'?></a> <a style="margin-left:20px; " title="View All Coupons under this offer" href="<?=admin_url('admin.php?page=hlt-offer-coupons&action=view&id='.$row->id)?>"><?php echo 'View All'?></a></td>


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