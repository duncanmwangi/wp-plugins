<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' ); ?>
<div class="wrap">
<h2><?php if(isset($order_id)) echo 'Order #'.$order_id; else echo 'Add new order';?></h2>
<form id="posts-filter" action="" method="post">
<input type="hidden" name="page" value="hlt-reps-order" />
<input type="hidden" name="order_id" value="<?php if(isset($order_id)) echo $order_id; else echo '0';?>" />

<div style="">
    <div id="order_itemsp" class="postbox ">
        <h3 class="hndle ui-sortable-handle"><span>Order Details</span></h3>
        <table width="98%" cellpadding="10" cellspacing="0" style="margin-left: 10px;" class="order_tbl">
        <tr>
                <th width="75%" align="left">
                <strong>Product</strong>
                </th>
                <th width="25%" align="left">
                    <strong>Total</strong>
                </th>
            </tr>
            <?php 
            if(isset($order)){
                foreach($order->get_items() as $item_id => $item){
                    $total_price = $item['line_subtotal'];
                    $total_price_display = $curr.number_format($total_price,2);
                    $unit_price = $total_price/$item['qty'];
                    $unit_price_display = $curr.number_format($unit_price,2);
                    $discounted_total_price = $item['line_total'];
                    $discounted_total_price_display = $curr.number_format($discounted_total_price,2);
                    $discounted_unit_price = $discounted_total_price/$item['qty'];
                    $discounted_unit_price_display = $curr.number_format($discounted_unit_price,2);
                    
                    ?>
                    <tr>
                        <td><?php echo get_the_title($item['product_id']) ?> <strong>X <?php echo $item['qty'] ?></strong></td>
                        <td><?php echo $discounted_total_price_display ?></td>
                    </tr>
                    <?php
                }
            }
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
            
            <tr>
                <th align="left">Cart Subtotal</th>
                <th align="left"><?php echo $subtotal_display ?></th>
            </tr>
            <?php if(count($coupons)>0): ?>
                <tr>
                    <th align="right">Coupon(s):<br /><?php echo implode(',',$coupons) ?></th>
                    <td><?php echo $discount_display; ?></td>
                </tr>
                <?php endif; ?>
            <tr>
                <th align="left">Shipping and Handling</th>
                <th align="left"><?php echo $shipping_display ?></th>
            </tr>
            <tr>
                <th align="left">New Order Total</th>
                <th align="left"><?php echo $sale_price_display ?></th>
            </tr>
        </table>
        
        
    </div>
    <div id="order_itemsp" class="postbox ">
            <h3 class="hndle ui-sortable-handle"><span>Payment Details</span></h3>
            
	<ul class="payment_methods methods" style="margin-left: 20px;">
		<?php
        $payment = new WC_Payment_Gateways();
        $available_gateways = $payment->get_available_payment_gateways( );
			if ( ! empty( $available_gateways ) ) {
				foreach ( $available_gateways as $gateway ) {
					wc_get_template( 'checkout/payment-method.php', array( 'gateway' => $gateway ) );
				}
			} else {
				if ( ! WC()->customer->get_country() ) {
					$no_gateways_message = __( 'Please fill in your details above to see available payment methods.', 'woocommerce' );
				} else {
					$no_gateways_message = __( 'Sorry, it seems that there are no available payment methods for your state. Please contact us if you require assistance or wish to make alternate arrangements.', 'woocommerce' );
				}

				echo '<p>' . apply_filters( 'woocommerce_no_available_payment_methods_message', $no_gateways_message ) . '</p>';
			}
		?>
	</ul>
        </div>
        <div style="width: 90%; height: 50px;">
            <input style="margin-left: 20px; float: left;" class="button grant_access" name="action" value="Back To Billing" type="submit">
            <input style="margin-right: 20px; float: right;" class="button button-primary" name="action" value="Place Order" type="submit">
        </div>
</div>

</form>

<div id="ajax-response"></div>
<br class="clear">
</div>
<style>
.order_tbl td,.order_tbl th{
    border-top: 1px solid #ccc;
}
.order_tbl{
    border: 1px solid #ccc;
    margin-top: 10px;
    margin-bottom: 10px;
}
.payment_method_authorize_net_aim{
    display: block !important;
}
#posts-filter fieldset {float: none;}
</style>
<script type="text/javascript">
jQuery(document).ready(function(){

    jQuery('#payment_method_authorize_net_aim').attr('checked',true);
});
</script>
