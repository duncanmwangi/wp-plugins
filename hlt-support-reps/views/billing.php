<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' ); ?>
<div class="wrap">
<h2><?php if(isset($order_id)) echo 'Order #'.$order_id; else echo 'Add new order';?></h2>
<form id="posts-filter" action="" method="post" onsubmit="">
<input type="hidden" name="page" value="hlt-reps-order" />
<input type="hidden" name="order_id" value="<?php if(isset($order_id)) echo $order_id; else echo '0';?>" />

<div style="width: 70%; float: left;">
    <div id="order_itemsp" class="postbox ">
        <h3 class="hndle ui-sortable-handle"><span>Billing Details</span></h3>
        <table width="100%" cellpadding="5" style="">
            <tr>
                <th width="33%" align="left"><label>First Name</label><abbr class="required" title="required">*</abbr><br /><input type="text" name="billing_first_name" id="billing_first_name" value="<?php if(isset($order->billing_first_name)) echo $order->billing_first_name ?>" /></th>
                <th width="33%" align="left"><label>Last Name</label><abbr class="required" title="required">*</abbr><br /><input type="text" name="billing_last_name" id="billing_last_name" value="<?php if(isset($order->billing_last_name)) echo $order->billing_last_name ?>" /></th>
                <th width="33%" align="left"><label>Company Name</label><br /><input type="text" name="billing_company_name" value="<?php if(isset($order->billing_company)) echo $order->billing_company ?>" /></th>
            </tr>
            <tr>
                <th align="left"><label>Country</label><abbr class="required" title="required">*</abbr><br /><select name="billing_country" id="billing_country" style="width: 200px;"><?php echo get_countries_options(empty($order->billing_country)?'US':$order->billing_country); ?></select></th>
                <th align="left"><label>State</label><abbr class="required" title="required">*</abbr><br />
                
                <?php 
                $options = get_states_options($order->billing_country,$order->billing_state);
                if(strlen(trim($options))==0):
                ?>
                <input type="text"  name="billing_state" id="billing_state" value="<?php if(isset($order->billing_state)) echo $order->shipping_state ?>"/>
                <?php
                else:
                ?>
                <select name="billing_state" id="billing_state"><?php echo $options; ?></select>
                <?php 
                endif;
                ?>
                
                </th>
                <th align="left"><label>Town/City</label><abbr class="required" title="required">*</abbr><br /><input type="text" name="billing_city" id="billing_city" value="<?php if(isset($order->billing_city)) echo $order->billing_city ?>" /></th>
            </tr>
            <tr>
                <th align="left"><label>Address</label><abbr class="required" title="required">*</abbr><br /><input type="text" name="billing_address" id="billing_address" value="<?php if(isset($order->billing_address_1)) echo $order->billing_address_1 ?>" /></th>
                <th align="left"><label>Zip</label><abbr class="required" title="required">*</abbr><br /><input type="text" name="billing_zip" id="billing_zip" value="<?php if(isset($order->billing_postcode)) echo $order->billing_postcode ?>" /></th>
                <th align="left"><label>Phone</label><abbr class="required" title="required">*</abbr><br /><input type="text" name="billing_phone" id="billing_phone" value="<?php if(isset($order->billing_phone)) echo $order->billing_phone ?>" /></th>
            </tr>
            <tr>
                <th align="left" colspan="3" ><label>Email Address</label><abbr class="required" title="required">*</abbr><br /><input type="text" name="billing_email" id="billing_email" value="<?php if(isset($order->billing_email)) echo $order->billing_email ?>" /></th>
            </tr>
        </table>
        <h4 style="font-size: 120%; padding-left: 20px;">Ship to a different address? <input style="margin-left: 10px;" type="checkbox" name="ship_to_a_different_address" id="ship_to_a_different_address" value="YES" /></h4>
        
        <div style="display: none;" id="shipping_dets">
            <h3 class="hndle ui-sortable-handle"><span>Shipping Details</span></h3>
            <table width="100%" cellpadding="5" style="margin-left: 10px;">
                <tr>
                    <th width="33%" align="left"><label>First Name</label><abbr class="required" title="required">*</abbr><br /><input type="text" name="shipping_first_name" id="shipping_first_name" value="<?php if(isset($order->shipping_first_name)) echo $order->shipping_first_name ?>" /></th>
                    <th width="33%" align="left"><label>Last Name</label><abbr class="required" title="required">*</abbr><br /><input type="text" name="shipping_last_name" id="shipping_last_name" value="<?php if(isset($order->shipping_last_name)) echo $order->shipping_last_name ?>" /></th>
                    <th width="33%" align="left"><label>Company Name</label><br /><input type="text" name="shipping_company_name" value="<?php if(isset($order->shipping_company)) echo $order->shipping_company ?>" /></th>
                </tr>
                <tr>
                    <th align="left"><label>Country</label><abbr class="required" title="required">*</abbr><br /><select style="width: 200px;" name="shipping_country" id="shipping_country"><?php echo get_countries_options(empty($order->shipping_country)?'US':$order->shipping_country); ?></select></th>
                    <th align="left"><label>State</label><abbr class="required" title="required">*</abbr><br />
                    
                    <?php 
                $options = get_states_options($order->shipping_country,$order->shipping_state);
                if(strlen(trim($options))==0):
                ?>
                <input type="text"  name="shipping_state" id="shipping_state" value="<?php if(isset($order->shipping_state)) echo $order->shipping_state ?>"/>
                <?php
                else:
                ?>
                <select name="shipping_state" id="shipping_state"><?php echo $options; ?></select>
                <?php 
                endif;
                ?>
                    
                    </th>
                    <th align="left"><label>Town/City</label><abbr class="required" title="required">*</abbr><br /><input type="text" name="shipping_city" id="shipping_city" value="<?php if(isset($order->shipping_city)) echo $order->shipping_city ?>" /></th>
                </tr>
                <tr>
                    <th align="left"><label>Address</label><abbr class="required" title="required">*</abbr><br /><input type="text" name="shipping_address" id="shipping_address" value="<?php if(isset($order->shipping_address_1)) echo $order->shipping_address_1 ?>" /></th>
                    <th align="left"><label>Zip</label><abbr class="required" title="required">*</abbr><br /><input type="text" name="shipping_zip" id="shipping_zip" value="<?php if(isset($order->shipping_postcode)) echo $order->shipping_postcode ?>" /></th>
                </tr>
            </table>
        </div>
        <!--
        <p style="padding-left: 20px;">
            <label style="font-weight: bold; font-size: 110%;">Order Notes</label><br />
            <textarea name="order_comments" class="input-text " id="order_comments" placeholder="Notes about your order, e.g. special notes for delivery." rows="3" cols="80"></textarea>
            
            <?php
            
            
             ?>
        </p>
        -->
        <div style="width: 100%; height: 50px;">
            <input style="margin-left: 20px; float: left;" class="button grant_access" name="action" value="Back To Cart" type="submit">
        </div>
    </div>
</div>
<div style="width: 28%; float: right;">
    <div id="order_items" class="postbox ">
        <h3 class="hndle ui-sortable-handle"><span>Cart Totals</span></h3>
                    <?php 
                    
                        $sale_price = $order->get_total();
                        $sale_price_display = $curr.number_format($sale_price,2);
                        $subtotal = $order->get_subtotal();
                        $subtotal_display = $curr.number_format($subtotal,2);
                        $discount = $order->get_total_discount();
                        $discount_display = $curr.number_format($discount,2);
                        $shipping = $order->get_total_shipping();
                        $shipping_display = $curr.number_format($shipping,2);
                        $coupons =$order->get_used_coupons();
                    ?>
                    <table class="cart_totalsx" width="100%" cellpadding="10">
                        <tr>
                            <th width="60%" align="right">Cart Subtotal:</th>
                            <td width="40%"><?php echo $subtotal_display; ?></td>
                        </tr>
                        <?php if(count($coupons)>0): ?>
                        <tr>
                            <th align="right">Coupon(s):<br /><?php echo implode(',',$coupons) ?></th>
                            <td><?php echo $discount_display; ?></td>
                        </tr>
                        <?php endif; ?>
                        <tr>
                            <th align="right">Shipping and Handling:</th>
                            <td><?php echo $shipping_display; ?></td>
                        </tr>
                        <tr>
                            <th align="right">Order Total:</th>
                            <td><?php echo $sale_price_display; ?></td>
                        </tr>
                        <tr class="cart_totalscc">
                            <td colspan="2"><input style="margin-left: 20px;" id="proceed_to_checkout" class="button button-primary" name="action" value="Proceed To Checkout" type="submit"/></td>
                        </tr>
                    </table>
        
    </div>
</div>

</form>
<script type="text/javascript">
jQuery(document).ready(function(){
    jQuery('#billing_country').change(function(){
        var country_id = jQuery(this).val();
		jQuery.post(ajaxurl, {'action': 'hlt_reps_populate_state_action', 'country': country_id }, function(response) {
		      if(response==0){
		          jQuery('#billing_state').replaceWith('<input type="text" name="billing_state" id="billing_state" style="width: 200px;">');
		      }else{
		          jQuery('#billing_state').replaceWith('<select name="billing_state" id="billing_state" style="width: 200px;">');
		          jQuery('#billing_state').html(response);
		      }
			
		});
    });
    jQuery('#ship_to_a_different_address').change(function(){
		if(jQuery(this).is(':checked')){
		  jQuery('#shipping_dets').show();
		}
        else{
            jQuery('#shipping_dets').hide();
        }
    });
    
    jQuery('#proceed_to_checkout').click(function(event){
        if(!check_validation())
            event.preventDefault();
        
    });
});

function check_validation(){
    var error = false;
    var value;
    value = jQuery('#billing_first_name').val();
    if(value.length==0){
        error = true;
    }
    value = jQuery('#billing_last_name').val();
    if(value.length==0){
        error = true;
    }
    value = jQuery('#billing_country').val();
    if(value.length==0){
        error = true;
    }
    value = jQuery('#billing_state').val();
    if(value.length==0){
        error = true;
    }
    value = jQuery('#billing_city').val();
    if(value.length==0){
        error = true;
    }
    value = jQuery('#billing_address').val();
    if(value.length==0){
        error = true;
    }
    value = jQuery('#billing_zip').val();
    if(value.length==0){
        error = true;
    }
    value = jQuery('#billing_phone').val();
    if(value.length==0){
        error = true;
    }
    value = jQuery('#billing_email').val();
    if(value.length==0){
        error = true;
    }
    
    if(jQuery('#ship_to_a_different_address').is(':checked')){
	  value = jQuery('#shipping_first_name').val();
        if(value.length==0){
            error = true;
        }
        value = jQuery('#shipping_last_name').val();
        if(value.length==0){
            error = true;
        }
        value = jQuery('#shipping_country').val();
        if(value.length==0){
            error = true;
        }
        value = jQuery('#shipping_state').val();
        if(value.length==0){
            error = true;
        }
        value = jQuery('#shipping_city').val();
        if(value.length==0){
            error = true;
        }value = jQuery('#shipping_address').val();
        if(value.length==0){
            error = true;
        }
        value = jQuery('#shipping_zip').val();
        if(value.length==0){
            error = true;
        }
	}
    if(error){
        alert('Please fill all required fields.');
        return false;
    }else{
        return true;
    }
        
}
</script>
<div id="ajax-response"></div>
<br class="clear">
</div>

