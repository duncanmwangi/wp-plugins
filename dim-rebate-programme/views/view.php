
<?php
$email = $this->db->get_row("SELECT * FROM $this->rebate_emails_tbl WHERE email_id=$email_id LIMIT 0,1");
$total_order_amount = 0;
$total_rebate_amount = 0;
?>
<div class="wrap">
    <h1>Email: <?=$email->email?> ID:<?=$email->email_id?></h1>
    <?php if(isset($msg)) echo $msg; ?>
    <h3>All Orders</h3>

        <table id="pub_table" class="wp-list-table widefat fixed posts display" style="margin-top: 20px;">
            <thead>
            <tr>
                <th scope="col" id="thumb" class="manage-column column-name" width="10%">No.</th>
                <th scope="col" id="thumb" class="manage-column column-name" width="40%">Date</th>
                <th scope="col" id="thumb" class="manage-column column-name"  width="25%">Order No.</th>
                <th scope="col" id="thumb" class="manage-column column-name"  width="25%">Amount</th>
            </tr>
            </thead>

            <tbody id="the-list">
            <?php
            $sql = "SELECT * FROM $this->rebate_orders_tbl WHERE email_id = $email->email_id ORDER BY order_date ASC";
            $allrows = $this->db->get_results($sql);
            if($allrows){
                $count = 1;
                foreach($allrows as $row){
                    $total_order_amount+=$row->order_amount;
                    ?>
                    <tr id="post-<?php echo $row->rebate_order_id?>" class="post-<?php echo $row->rebate_order_id?> hentry alternate iedit author-self level-0">

                        <td class="name column-name"><?php echo $count++?></td>
                        <td class="name column-name"><?php echo date('m-d-Y',strtotime($row->order_date))?></td>
                        <td class="name column-name"><?php echo $row->order_no?></td>
                        <td class="name column-name"><?php echo $row->order_amount?></td>
                    </tr>
                    <?php
                }
                ?>
                <tr id="post-z" class="post-z hentry alternate iedit author-self level-0">

                        <td class="name column-name" colspan="3" align="right"><strong>Total Order Amount</strong></td>
                        <td class="name column-name"><strong><ins><?php echo number_format($total_order_amount,2)?></ins></strong></td>
                    </tr>
                <?php
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

    <h3>All Rebates</h3>

         <table id="pub_table" class="wp-list-table widefat fixed posts display" style="margin-top: 20px;">
            <thead>
            <tr>
                <th scope="col" id="thumb" class="manage-column column-name" width="10%">No.</th>
                <th scope="col" id="thumb" class="manage-column column-name" width="20%">Date</th>
                <th scope="col" id="thumb" class="manage-column column-name"  width="20%">Rebate Amount</th>
                <th scope="col" id="thumb" class="manage-column column-name"  width="20%">Coupon Amount</th>
                <th scope="col" id="thumb" class="manage-column column-name"  width="15%">Coupon Code</th>
                <th scope="col" id="thumb" class="manage-column column-name"  width="15%">Coupon Status</th>
            </tr>
            </thead>

            <tbody id="the-list">
            <?php
            $sql = "SELECT rebate_id,date_created,coupon_code,coupon_status,COALESCE(rebate_amount,0) as rebate_amount, COALESCE(coupon_amount,0) as coupon_amount FROM $this->rebates_tbl WHERE email_id = $email->email_id ORDER BY date_created ASC";
            $allrows = $this->db->get_results($sql);
            if($allrows){
                $count = 1;
                $total_coupon_amount= 0;
                foreach($allrows as $row){
                    $total_rebate_amount+=$row->rebate_amount;
                    $total_coupon_amount+=$row->coupon_amount;
                    
                    ?>
                    <tr id="post-<?php echo $row->rebate_id?>" class="post-<?php echo $row->rebate_id?> hentry alternate iedit author-self level-0">

                        <td class="name column-name"><?php echo $count++?></td>
                        <td class="name column-name"><?php echo date('m - d - Y',strtotime($row->date_created));?></td>
                        <td class="name column-name"><?php echo $row->rebate_amount?></td>
                        <td class="name column-name"><?php echo $row->coupon_amount?></td>
                        <td class="name column-name"><?php echo $row->coupon_code?></td>
                        <td class="name column-name"><?php echo $row->coupon_status?></td>
                    </tr>
                    <?php
                }
                ?>
                <tr id="post-z" class="post-z hentry alternate iedit author-self level-0">

                        <td class="name column-name" colspan="2" align="right"><strong>Totals</strong></td>
                        <td class="name column-name"><strong><ins><?php echo number_format($total_rebate_amount,2)?></ins></strong></td>
                        <td class="name column-name"><strong><ins><?php echo number_format($total_coupon_amount,2)?></ins></strong></td>
                        <td class="name column-name" colspan="2">&nbsp;</td>
                    </tr>
                <?php
            }
            else{
                ?>
                <tr id="post-5796" class="post-5796 hentry alternate iedit author-self level-0">

                    <td class="name column-name" colspan="6">No records found</td>
                </tr>
                <?php
            }
            ?>

            </tbody>
        </table>

    <h2>Total Unrebated Amount: <strong><?php echo number_format(($total_order_amount-$total_rebate_amount),2)?></strong></h2>
    <?php 
    if(($total_order_amount-$total_rebate_amount)>=$this->rebate_amount):
    ?>
    
    <form action="<?=admin_url('admin.php?page=drp-rebates&action=award&id='.$email->email_id)?>" method="post">
        <input type="hidden" name="email_id" value="<?=$email->email_id?>" />
        <p class="submit"><input type="submit" name="award_rebate_btn" id="submit" class="button button-primary" value="Award $<?=$this->coupon_amount?> Rebate"></p>
    </form>
    <?php
    endif;
    ?>
    <div id="ajax-response"></div>
    <br class="clear">
</div>