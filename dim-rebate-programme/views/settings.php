<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' ); ?><?php
    if(isset($_POST['save_btn'])){
        $drp_rebate_amount = $_POST['drp_rebate_amount'];
        $drp_coupon_amount = $_POST['drp_coupon_amount'];
        $drp_expiry_days = $_POST['drp_expiry_days'];
        $drp_admin_name = $_POST['drp_admin_name'];
        $drp_admin_email = $_POST['drp_admin_email'];
        $drp_reply_to_email = $_POST['drp_reply_to_email'];
        $drp_coupon_conjunctions = $_POST['drp_coupon_conjunctions'];
        $drp_apply_coupon_conjunctions = $_POST['drp_apply_coupon_conjunctions'];
        $drp_donot_rebate_emails = $_POST['drp_donot_rebate_emails'];
        if(!empty($drp_rebate_amount) && $drp_rebate_amount>0 && !empty($drp_coupon_amount) && $drp_coupon_amount>0 && !empty($drp_expiry_days) && $drp_expiry_days>0 && !empty($drp_admin_name) && !empty($drp_admin_email) && !empty($drp_reply_to_email)){
            update_option( 'drp_rebate_amount', $drp_rebate_amount);
            update_option( 'drp_coupon_amount', $drp_coupon_amount);
            update_option( 'drp_expiry_days', $drp_expiry_days);
            update_option( 'drp_apply_coupon_conjunctions', $drp_apply_coupon_conjunctions);
            update_option( 'drp_admin_name', $drp_admin_name);
            update_option( 'drp_coupon_conjunctions', str_ireplace(' ','',$drp_coupon_conjunctions));
            update_option( 'drp_donot_rebate_emails', str_ireplace(' ','',$drp_donot_rebate_emails));

            if(is_email($drp_admin_email))
                update_option( 'drp_admin_email', $drp_admin_email);
            if(is_email($drp_reply_to_email))
                update_option( 'drp_reply_to_email', $drp_reply_to_email);
            $msg = $this->display_msg('Settings saved successfully.');
            $conjs = get_option( 'drp_coupon_conjunctions', '' );
            if( !empty($conjs) && !empty($drp_apply_coupon_conjunctions) && $drp_apply_coupon_conjunctions=='YES'){
                $this->apply_coupon_conjunctions($conjs);
            }
        }else{
            $msg = $this->display_msg('Error: All fields must be filled.',2);
        }
    }
    
    
    
?>

<div class="wrap">
    <h2>Rebate Programme Settings</h2>
    <?php if(isset($msg)) echo $msg; ?>
    <form method="post" action="">
        <table class="form-table">
            <tbody>
            <tr>
                <th scope="row"><label for="drp_admin_email">Admin Email Address: </label></th>
                <td><input name="drp_admin_email" type="email" id="drp_admin_email" value="<?php echo get_option( 'drp_admin_email', get_bloginfo('admin_email') ); ?>" class="regular-text ltr"></td>
            </tr>
            <tr>
                <th scope="row"><label for="drp_admin_name">Admin Name: </label></th>
                <td><input name="drp_admin_name" type="text" id="drp_admin_name" value="<?php echo get_option( 'drp_admin_name', get_bloginfo('name') ); ?>" class="regular-text ltr"></td>
            </tr>
            <tr>
                <th scope="row"><label for="drp_admin_email">Reply To Email Address: </label></th>
                <td><input name="drp_reply_to_email" type="email" id="drp_reply_to_email" value="<?php echo get_option( 'drp_reply_to_email', get_bloginfo('admin_email') ); ?>" class="regular-text ltr"></td>
            </tr>
            <tr>
                <th scope="row"><label for="drp_rebate_amount">Rebate Amount: </label></th>
                <td><input name="drp_rebate_amount" type="text" id="drp_rebate_amount" value="<?php echo get_option( 'drp_rebate_amount', '250.00' ); ?>" class="regular-text ltr"></td>
            </tr>
            <tr>
                <th scope="row"><label for="drp_coupon_amount">Coupon Amount: </label></th>
                <td><input name="drp_coupon_amount" type="text" id="drp_coupon_amount" value="<?php echo get_option( 'drp_coupon_amount', '25.00' ); ?>" class="regular-text ltr"></td>
            </tr>
            <tr>
                <th scope="row"><label for="drp_expiry_days">Coupon Expires in: </label></th>
                <td><input name="drp_expiry_days" type="text" id="drp_expiry_days" value="<?php echo get_option( 'drp_expiry_days', '45' ); ?>" class="regular-text ltr"> days</td>
            </tr>
           
           
            <tr>
                <th scope="row"><label for="drp_donot_rebate_emails">Emails Not to Rebate: </label></th>
                <td>
                    <textarea class="regular-text ltr" name="drp_donot_rebate_emails" id="drp_donot_rebate_emails" rows="6" cols="80"><?php echo get_option( 'drp_donot_rebate_emails', '' ); ?></textarea>
                <br /><p>Coma separated list of all emails that will not be rebated during auto rebate processing</p></td>
            </tr>
           
            <tr>
                <th scope="row"><label for="drp_coupon_conjunctions">Coupon Conjunctions: </label></th>
                <td>
                    <textarea class="regular-text ltr" name="drp_coupon_conjunctions" id="drp_coupon_conjunctions" rows="6" cols="80"><?php echo get_option( 'drp_coupon_conjunctions', '' ); ?></textarea><br /><p>Coma separated list of all coupons that can be used together with a Rebate Coupon Code</p></td>
            </tr>
            <tr>
                <th scope="row"><label for="drp_coupon_conjunctions">Apply Coupon Conjunctions To Existing Rebate Coupon Codes: </label></th>
                <td><input name="drp_apply_coupon_conjunctions" type="checkbox" id="drp_apply_coupon_conjunctions" value="YES" <?php $drp_apply_coupon_conjunctions = get_option( 'drp_apply_coupon_conjunctions', 'NO' ); if($drp_apply_coupon_conjunctions=='YES') echo 'checked="checked"' ?> class=""><p> Check to apply coupon conjunctions to existing Rebate Coupon Codes</p></td> 
            </tr> 
            </tbody></table>
            <style>
            </style>

        <p class="submit"><input type="submit" name="save_btn" id="submit" class="button button-primary" value="Save Settings"></p></form>
   

</div>
<style>
    .wrap{padding: 10px 20px;}
</style>