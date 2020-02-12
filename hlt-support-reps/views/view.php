<div class="wrap">
<h2>ORDER #<?php echo $order_id; ?></h2>
<?php
global $wpdb,$woocommerce,$HLT_ITEMS_PER_PAGE;
$orderx = new WC_Order($order_id);
$order_post = get_post($order_id); 
$curr = get_woocommerce_currency_symbol();
 
if(isset($_POST['save_billing_address'])){
    $address = array(
        'first_name'=> $_POST['billing_first_name'],
        'last_name'=> $_POST['billing_last_name'],
        'phone'=> $_POST['billing_phone'],
        'email'=> $_POST['billing_email'],
        'state'=> $_POST['billing_state'],
        'country'=> $_POST['billing_country'],
        'postcode'=> $_POST['billing_postcode'],
        'address_2'=> $_POST['billing_address_2'],
        'address_1'=> $_POST['billing_address_1'],
        'city'=> $_POST['billing_city'],
        'company'=> $_POST['billing_company']
    );
    $orderx->set_address($address,'billing');
    echo hlt_reps_display_msg('Billing Address has been saved successfully');
}

if(isset($_POST['save_shipping_address'])){
    $address2 = array(
        'first_name'=> $_POST['shipping_first_name'],
        'last_name'=> $_POST['shipping_last_name'],
        'state'=> $_POST['shipping_state'],
        'country'=> $_POST['shipping_country'],
        'postcode'=> $_POST['shipping_postcode'],
        'address_2'=> $_POST['shipping_address_2'],
        'address_1'=> $_POST['shipping_address_1'],
        'city'=> $_POST['shipping_city'],
        'company'=> $_POST['shipping_company']
    );
    $orderx->set_address($address2,'shipping');
    echo hlt_reps_display_msg('Shipping Address has been saved successfully');
}
if(isset($_POST['resend_confirmation_email'])){
    $order_id = $_POST['order_idz'];
    hlt_reps_resend_order_confirmation( $order_id );
    echo hlt_reps_display_msg('Confirmation email has been sent successfully');
}
$order = new WC_Order($order_id);
$order_post = get_post($order_id);
?>



<div id="poststuff">
<div id="post-body" class="metabox-holder columns-2">

<div id="postbox-container-1" class="postbox-container">
<div id="side-sortables" class="meta-box-sortables ui-sortable"><div id="woocommerce-order-actions" class="postbox ">
<h3 class="hndle ui-sortable-handle"><span>Order Actions</span></h3>
<div class="inside">
		<form action="<?php echo admin_url('admin.php?page=hlt-reps&action=edit&id='.$order_id)?>" method="post">
            <input type="hidden" name="order_idz" value="<?php echo $order_id; ?>"  />
            <input type="submit" name="resend_confirmation_email" class="button save_order button-primary tips" value="Resend Confirmation Email" />
        </form>
</div>
</div>

</div>

<div id="side-sortablesd" class="meta-box-sortables ui-sortable"><div id="woocommerce-order-actions" class="postbox ">
<h3 class="hndle ui-sortable-handle"><span>Order Details</span></h3>
<div class="inside">
    <table class="orderdetsx">
        <tr>
            <td><strong>Order Date</strong></td>
            <td><?php echo date('d-m-Y H:i:s',strtotime($order->get_date_created())); ?></td>
        </tr>
        <tr>
            <td><strong>Order Status</strong></td>
            <td><?php echo $order->get_status(); ?></td>
        </tr>
        <tr>
            <td><strong>IP Address</strong></td>
            <td><?php echo $order->get_customer_ip_address(); ?></td>
        </tr>
        <tr>
            <td><strong>Payment Method</strong></td>
            <td><?php echo $order->get_payment_method_title(); ?></td>
        </tr>
        <tr>
            <td><strong>Transaction Ref#</strong></td>
            <td><?php echo $order->get_transaction_id(); ?></td>
        </tr>
    </table>
</div>
</div>
</div>
<?php 
$argsz = array(
	   'post_id' => $order_id
);
$comments = get_comments($argsz);
if($comments):
?>
<div id="side-sortablesn" class="meta-box-sortables ui-sortable"><div id="woocommerce-order-actions" class="postbox ">
<h3 class="hndle ui-sortable-handle"><span>Order Notes</span></h3>
<div class="inside">
		<?php 
            foreach($comments as $comment) :
            	echo $comment->comment_content . '<br />';
            endforeach;
        ?>
</div>
</div>
</div>
<?php endif;

?>

</div>
<div id="postbox-container-2" class="postbox-container">
<div id="normal-sortables" class="meta-box-sortables ui-sortable"><div id="woocommerce-order-data" class="postbox ">
<h3 class="hndle ui-sortable-handle"><span>Billing &amp; Shipping Information</span></h3>
<div class="inside">
		<div class="panel-wrap woocommerce">
			<div id="order_dataxs" class="panel">
                <div style="width: 48%; float: left; padding-left: 20px;">
                    <h3>Billing Details <a href="#TB_inline?width=550&height=300&inlineId=bill_<?php echo $order_id;?>" class="thickbox"><img src="<?php echo plugin_dir_url( __FILE__ ) ?>edit.png" alt="Edit" width="14"></a></h3>
                    <p><strong>Address:</strong><br /> <?php echo $order->get_formatted_billing_address() ?></p>
                    <p><strong>Email:</strong><br /> <?php echo $order->get_billing_email() ?></p>
                    <p><strong>Phone:</strong><br /> <?php echo $order->get_billing_phone() ?></p>
                    <div id="bill_<?php echo $order_id;?>" style="display:none;">
                        <h4>Edit Billing Details</h4>
                        <form action="<?php echo admin_url('admin.php?page=hlt-reps&action=edit&id='.$order_id)?>" method="post">
                        <table>
                            <tr>
                                <td width="25%">First Name</td>
                                <td width="25%"><input name="billing_first_name" type="text" value="<?php echo $order->get_billing_first_name() ?>" /></td>
                                <td width="25%">Last Name</td>
                                <td width="25%"><input name="billing_last_name" type="text" value="<?php echo $order->get_billing_last_name() ?>" /></td>
                            </tr>
                            <tr>
                                <td>Company Name</td>
                                <td colspan="3"><input name="billing_company_name" type="text" style="width: 100%;" value="<?php echo $order->get_billing_company() ?>" /></td>
                            </tr>
                            <tr>
                                <td>Address 1</td>
                                <td><input name="billing_address_1" type="text" value="<?php echo $order->get_billing_address_1() ?>" /></td>
                                <td>Address 2</td>
                                <td><input name="billing_address_2" type="text" value="<?php echo $order->get_billing_address_2() ?>" /></td>
                            </tr>
                            <tr>
                                <td>City</td>
                                <td><input name="billing_city" type="text" value="<?php echo $order->get_billing_city() ?>" /></td>
                                <td>Post Code</td>
                                <td><input name="billing_postcode" type="text" value="<?php echo $order->get_billing_postcode() ?>" /></td>
                            </tr>
                            <tr>
                                <td>Country</td>
                                <td><select name="billing_country" id="xbilling_country" style="width: 190px;"><?php echo get_countries_options($order->get_billing_country()); ?></select></td>
                                <td>State/County</td>
                                <td>
                                <?php 
                                $options = get_states_options($order->get_shipping_country(),$order->get_shipping_state());
                                if(strlen(trim($options))==0):
                                ?>
                                <input type="text" name="billing_state" id="xbilling_state" style="width: 190px;">
                                <?php
                                else:
                                ?>
                                <select name="billing_state" id="xbilling_state" style="width: 190px;"><?php echo $options; ?></select>
                                <?php 
                                endif;
                                ?>
                                </td>
                            </tr>
                            <tr>
                                <td>Email</td>
                                <td><input name="billing_email" type="text" value="<?php echo $order->get_billing_email() ?>" /></td>
                                <td>Phone</td>
                                <td><input name="billing_phone" type="text" value="<?php echo $order->get_billing_phone();
                                
                                ?>" /></td>
                            </tr>
                        </table>
                        <script type="text/javascript" >
                        	jQuery(document).ready(function($) {
                               
                                
                                $('#xbilling_country').change(function(){
                                    var country = $('#xbilling_country').val();
                            		$.post(ajaxurl, {'action': 'hlt_reps_shipping_state_action', 'country': country }, function(response) {
                            		      if(response==0){
                            		          $('#xbilling_state').replaceWith('<input type="text" name="billing_state" id="xbilling_state" style="width: 190px;">');
                            		      }else{
                            		          $('#xbilling_state').replaceWith('<select name="billing_state" id="xbilling_state" style="width: 190px;">');
                            		          $('#xbilling_state').html(response);
                            		      }
                            			
                            		});
                                });
                        		
                        	});
                        	</script>
                        <input class="button save_order button-primary tips" name="save_billing_address" style="margin-top: 10px; margin-left: 20px;" value="Update" type="submit">
                        </form>
                    </div>
                </div>
                <div style="width: 48%; float: right;">
                <?php add_thickbox(); ?>
                    <h3>Shipping Details <a href="#TB_inline?width=720&height=300&inlineId=shp_<?php echo $order_id;?>" class="thickbox"><img src="<?php echo plugin_dir_url( __FILE__ ) ?>edit.png" alt="Edit" width="14"></a></h3>
                    <p><strong>Address:</strong><br /> <?php echo $order->get_formatted_shipping_address() ?></p>
                    <?php if(!empty($order->get_customer_note())):  ?>
                    <p><strong>Customer Note:</strong><br /> <?php echo $order->get_customer_note() ?></p>
                    <?php endif;  ?>
                    
                    <div id="shp_<?php echo $order_id;?>" style="display:none;">
                        <h4>Edit Shipping Details</h4>
                        <form action="<?php echo admin_url('admin.php?page=hlt-reps&action=edit&id='.$order_id)?>" method="post">
                        <table style="width: 100%;">
                            <tr>
                                <td width="25%">First Name</td>
                                <td width="25%"><input name="shipping_first_name" type="text" value="<?php echo $order->get_shipping_first_name() ?>" /></td>
                                <td width="25%">Last Name</td>
                                <td width="25%"><input name="shipping_last_name" type="text" value="<?php echo $order->get_shipping_last_name() ?>" /></td>
                            </tr>
                            <tr>
                                <td>Company Name</td>
                                <td colspan="3"><input name="shipping_company" type="text" style="width: 100%;" value="<?php echo $order->get_shipping_company() ?>" /></td>
                            </tr>
                            <tr>
                                <td>Address 1</td>
                                <td><input name="shipping_address_1" type="text" value="<?php echo $order->get_shipping_address_1() ?>" /></td>
                                <td>Address 2</td>
                                <td><input name="shipping_address_2" type="text" value="<?php echo $order->get_shipping_address_2() ?>" /></td>
                            </tr>
                            <tr>
                                <td>City</td>
                                <td><input name="shipping_city" type="text" value="<?php echo $order->get_shipping_city() ?>" /></td>
                                <td>Post Code</td>
                                <td><input name="shipping_postcode" type="text" value="<?php echo $order->get_shipping_postcode() ?>" /></td>
                            </tr>
                            <tr>
                                <td>Country</td>
                                <td><select id="xshipping_country" name="shipping_country" style="width: 190px;"><?php echo get_countries_options($order->get_shipping_country()); ?></select></td>
                                <td>State/County</td>
                                <td>
                                <?php 
                                $options = get_states_options($order->get_shipping_country(),$order->get_shipping_state());
                                if(strlen(trim($options))==0):
                                ?>
                                <input type="text" name="shipping_state" id="xshipping_state" style="width: 190px;">
                                <?php
                                else:
                                ?>
                                <select name="shipping_state" id="xshipping_state" style="width: 190px;"><?php echo $options; ?></select>
                                <?php 
                                endif;
                                ?>
                                </td>
                            </tr>
                        </table>
                        
	<script type="text/javascript" >
	jQuery(document).ready(function($) {
        $('#xshipping_country').change(function(){
            var country = $('#xshipping_country').val();
    		$.post(ajaxurl, {'action': 'hlt_reps_shipping_state_action', 'country': country }, function(response) {
    		      if(response==0){
    		          $('#xshipping_state').replaceWith('<input type="text" name="shipping_state" id="xshipping_state" style="width: 190px;">');
    		      }else{
    		          $('#xshipping_state').replaceWith('<select name="shipping_state" id="xshipping_state" style="width: 190px;">');
    		          $('#xshipping_state').html(response);
    		      }
    			
    		});
        });
        
        
		
	});
	</script>
                        <input class="button save_order button-primary tips" name="save_shipping_address" style="margin-top: 10px; margin-left: 20px;" value="Update" type="submit">
                        </form>
                    </div>


                </div>
				<div class="clear"></div>
			</div>
		</div>
		</div>
</div>


</div>
<div id="normal-sortables-x" class="meta-box-sortables ui-sortable"><div id="woocommerce-order-items" class="postbox ">
<h3 class="hndle ui-sortable-handle"><span>Order Items</span></h3>
<div class="inside">
		<div class="panel-wrap woocommerce">
			<div id="order_data_x" class="panel">
				<table class="widefat s_tbl">
                    <tr>
                        <th width="5%">No.</th>
                        <th width="45%">Item</th>
                        <th width="20%">Cost</th>
                        <th width="10%">Qty</th>
                        <th width="20%">Total</th>
                    </tr>
                    <?php
                        $items = $order->get_items();
                        $count = 1;
                        $shipping_method_title = $order->get_shipping_method();
                        $shipping_cost= $order->get_total_shipping();
                        if(is_array($items) && count($items)>0){
                            foreach($items as $item){
                                $product_id = $item->get_product_id();
                                $qty = $item->get_quantity();
                                $total_before_discount = $item->get_subtotal();
                                $price_before_discount = $total_before_discount/$qty;
                                $total_after_discount = $item->get_total();
                                $price_after_discount = $total_after_discount/$qty;
                                $has_discount = $total_after_discount!=$price_before_discount?true:false;
                                ?>
                                        <tr>
                                            <td><?php echo $count++ ?></td>
                                            <td align="left"><a href="<?php echo get_permalink($product_id) ?>" target="_blank"><?php echo get_the_title($product_id) ?></a></td>
                                            
                                            <td align="left"><?php if($has_discount) echo '<span class="strikethrough">'.$curr.number_format($price_before_discount,2).'</span>'; ?> <span class="amount"><?php echo $curr.number_format($price_after_discount,2) ?></span></td>
                                            <td><?php echo $qty ?></td>
                                            <td align="left"><?php if($has_discount) echo '<span class="strikethrough">'.$curr.number_format($total_before_discount,2).'</span>'; ?> <span class="amount"><?php echo $curr.number_format($total_after_discount,2) ?></span></td>
                                        </tr>
                                        <?php
                            }

                            /*foreach($items as $item){
                                print_r($item);
                                   $product_ids = $item['item_meta']['_product_id'];
                                   foreach($product_ids as $key => $product_id){
                                        $qty = $item['item_meta']['_qty'][$key];
                                        $total_before_discount = $item['item_meta']['_line_subtotal'][$key];
                                        $price_before_discount = $total_before_discount/$qty;
                                        $total_after_discount = $item['item_meta']['_line_total'][$key];
                                        $price_after_discount = $total_after_discount/$qty;
                                        $has_discount = $total_after_discount!=$price_before_discount?true:false;
                                        ?>
                                        <tr>
                                            <td><?php echo $count++ ?></td>
                                            <td align="left"><a href="<?php echo get_permalink($product_id) ?>" target="_blank"><?php echo get_the_title($product_id) ?></a></td>
                                            
                                            <td align="left"><?php if($has_discount) echo '<span class="strikethrough">'.$curr.number_format($price_before_discount,2).'</span>'; ?> <span class="amount"><?php echo $curr.number_format($price_after_discount,2) ?></span></td>
                                            <td><?php echo $qty ?></td>
                                            <td align="left"><?php if($has_discount) echo '<span class="strikethrough">'.$curr.number_format($total_before_discount,2).'</span>'; ?> <span class="amount"><?php echo $curr.number_format($total_after_discount,2) ?></span></td>
                                        </tr>
                                        <?php
                                   }
                                   
                               
                            }*/
                            ?>
                            <tr>
                                <td>-</td>
                                <td colspan="3" align="left"><?php echo $shipping_method_title ?></td>
                                <td><?php echo $curr.number_format($shipping_cost,2) ?></td>
                            </tr>
                            <tr>
                                <td rowspan="4" colspan="2" style="vertical-align: top;" align="left">
                                <strong>Coupon(s) Used:</strong>
                                <?php 
                                $coupons = $order->get_used_coupons();
                                if(count($coupons)>0){
                                    foreach($coupons as $coupon){
                                        echo '<br/>'.$coupon;
                                    }
                                }
                                ?>
                                </td>
                                <td colspan="2">Discount</td>
                                <td><strong><?php echo $curr.number_format($order->get_total_discount(),2) ?></strong></td>
                            </tr>
                            <tr>
                                <td colspan="2">Shipping</td>
                                <td><?php echo $curr.number_format($shipping_cost,2) ?></td>
                            </tr>
                            <tr>
                                <td colspan="2">Order Total</td>
                                <td><?php echo $curr.number_format($order->get_total(),2);  ?></td>
                            </tr>
                            <tr>
                                <td colspan="2" style="color: red;">Refunded</td>
                                <td style="color: red;"><?php echo $curr.number_format($order->get_total_refunded(),2) ?></td>
                            </tr>
                            
                            <?php
                            
                        }
                        else{
                            ?>
                            
                            <tr>
                                <td colspan="5">No records found.</td>
                            </tr><?php
                        }
                    ?>
                </table>
				
				<div class="clear"></div>
			</div>
		</div>
		</div>
</div>


</div>
</div>


</div>
</div><!-- /post-body -->
<br class="clear">

</div>