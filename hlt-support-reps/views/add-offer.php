<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
    $cop_error = false;
    if(isset($_POST['save_btn'])){
        $cop_name = $_POST['cop_name'];
        $cop_product_ids = $_POST['cop_product_ids'];
        $cop_discount_type = $_POST['cop_discount_type'];
        $cop_amount = $_POST['cop_amount'];
        $cop_days_to_expire = $_POST['cop_days_to_expire'];
        if(!empty($cop_name)  && !empty($cop_product_ids) && !empty($cop_discount_type) &&  !empty($cop_amount) && !empty($cop_days_to_expire)){
           $wpdb->insert( $cop_offers_tbl, 
                        array( 'name' => $cop_name, 'product_ids' => str_ireplace(' ', '', $cop_product_ids),'discount_type' => $cop_discount_type , 'amount' => $cop_amount , 'days_to_expire' => $cop_days_to_expire,'date_created'=>current_time( 'mysql' ) ) , 
                        array( '%s', '%s', '%s', '%s', '%s', '%s' ) );
            $msg = cop_display_msg('Offer was saved successfully.');
        }else{
            $cop_error = true;
            $msg = cop_display_msg('Error: All fields must be filled.',2);
        }
    }
    
    
    
?>

<div class="wrap">
    <h2>Add Offer</h2>
    <?php if(isset($msg)) echo $msg; ?>
    <form method="post" action="">
        <table class="form-table">
            <tbody>
            <tr>
                <th scope="row"><label for="cop_name">Name: </label></th>
                <td><input name="cop_name" type="text" id="cop_name" value="<?php echo $cop_error ? $cop_name : ''; ?>" class="regular-text ltr"></td>
            </tr>
            <tr>
                <th scope="row"><label for="cop_product_ids">Products On Offer: </label></th>
                <td><textarea class="regular-text ltr" name="cop_product_ids" id="cop_product_ids" rows="6" cols="80"><?php echo $cop_error ? $cop_product_ids : ''; ?></textarea></td>
            </tr>
            <tr>
                <th scope="row"><label for="cop_discount_type">Discount Type: </label></th>
                <td>
                    <select name="cop_discount_type" id="cop_discount_type" class="select short">
                        <?php
                        $options = [
                                //'percent'=>'Percentage discount',
                                //'fixed_cart'=>'Fixed cart discount',
                                'fixed_product'=>'Fixed product discount'
                            ];
                            foreach ($options as $key => $option) {
                               ?><option <?php if($cop_error && $key==$cop_discount_type) echo ' selected="selected" ' ?> value="<?=$key?>"><?=$option?></option><?php
                            }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="cop_amount">Discount Amount: </label></th>
                <td><input name="cop_amount" type="decimal" id="cop_amount" value="<?php echo $cop_error ? $cop_amount : ''; ?>" class="regular-text ltr"></td>
            </tr>
            <tr>
                <th scope="row"><label for="cop_days_to_expire">Days to Expire: </label></th>
                <td><input name="cop_days_to_expire" type="number" id="cop_days_to_expire" value="<?php echo $cop_error ? $cop_days_to_expire : ''; ?>" class="regular-text ltr"></td>
            </tr>
           
           
            </tbody></table>
            <style>
            </style>

        <p class="submit"><input type="submit" name="save_btn" id="submit" class="button button-primary" value="Save Offer"></p></form>
   
<div id="ajax-response"></div>
    <br class="clear">
</div>
<style>
    .wrap{padding: 10px 20px;}
</style>