<div class="wrap">
<h2><?php if(isset($order_id)) echo 'Order #'.$order_id; else echo 'Add new order';?></h2>
<form id="posts-filter" action="" method="post">
<input type="hidden" name="page" value="hlt-reps-order" />
<input type="hidden" name="order_id" value="<?php if(isset($order_id)) echo $order_id; else echo '0';?>" />


<div id="order_items" class="postbox ">
<h3 class="hndle ui-sortable-handle"><span>Shopping Cart</span></h3>
<div class="inside">
		<div class="order_download_permissions wc-metaboxes-wrapper">
            <table width="100%" class="orderitems" cellpadding="10">
                <thead>
                    <tr>
                        <th width="5%">No.</th>
                        <th width="55%">Item Name</th>
                        <th width="5%">Quantity</th>
                        <th width="15%">Cost</th>
                        <th width="15%">Total</th> 
                        <th width="5%">Action</th>
                    </tr>
                </thead>
                <tbody id="orderitmz">
                <?php
                $count = 1;
                if(isset($order)):
                
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
                        <tr class="items_tr" id="">
                            <td class="hlt_no"><?php echo $count++?>.</td>
                            <td class="hlt_item_name"><a href="<?php echo get_permalink( $item['product_id'] ); ?>" target="_blank"><?php echo get_the_title($item['product_id']) ?></a><input type="hidden" name="jproduct_id[<?php echo $item_id ?>]" value="<?php echo $item['product_id'] ?>" /></td>
                            <td class="hlt_quantity"><input type="number" name="jquantity[<?php echo $item_id ?>]" value="<?php echo $item['qty'] ?>" /></td>
                            <td class="hlt_cost"><span style="text-decoration: line-through;"><?php echo $unit_price_display ?></span> <span><?php echo $discounted_unit_price_display ?></span></td>
                            <td class="hlt_total"><span style="text-decoration: line-through;"><?php echo $total_price_display ?></span> <span><?php echo $discounted_total_price_display ?></span></td>
                            <td><a href="#" style="color: red;" class="xremove">Remove</a></td>
                        </tr>
                        <?php
                    }
                    endif;
                
                ?>
                </tbody>
                
                <tfoot>
                    <tr>
                        <td colspan="5"><button type="button" id="regular_add" class="button grant_access">Add Regular Product</button><button type="button" class="button grant_access" id="special_add" style="margin-left: 20px;">Add Special Product</button></td>
                        <td></td>
                    </tr>
                    <tr id="appx">
                        <td colspan="4">
                            <div class="coupon">
                        
                                <div style="font-size: 15px; font-weight: 700; margin-bottom: 5px;">Please enter your coupon code below...</div>
                                
        						<label for="coupon_code">Coupon:</label> <input style="width: 200px; height: 30px; font-weight: 700;" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="Coupon code" type="text"> <input class="button button-primary" name="action" value="Apply Coupon" type="submit">
        
        						
        					</div>
                        </td>
                        <td><input style="margin-left: 20px;" class="button button-primary" name="action" value="Update Cart" type="submit"></td>
                        <td></td>
                    </tr>
                </tfoot>
                
            </table>
            
            
			

		</div>
		</div>
</div>
<?php if(isset($order_id)): ?>
<div style="width: 400px; float: right;">
                <div id="order_items" class="postbox ">
                    <h3 class="hndle ui-sortable-handle"><span>Cart Totals</span></h3>
                    <?php 
                        $order->calculate_totals();
                        $sale_price = $order->get_total();
                        $sale_price_display = $curr.number_format($sale_price,2);
                        $subtotal = $order->get_subtotal();
                        $subtotal_display = $curr.number_format($subtotal,2);
                        $discount = $order->get_total_discount();
                        $discount_display = $curr.number_format($discount,2);
                        $shipping = $order->get_total_shipping();
                        $shipping_display = $curr.number_format($shipping,2);
                        $coupons =$order->get_used_coupons();
                        foreach($coupons as $key => $coupon){
                            echo '<input type="hidden" name="coupon_codex[]" value="'.$coupon.'"/>';
                        }
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
                            <td colspan="2"><input style="margin-left: 20px;" class="button button-primary" name="action" value="Proceed To Billing" type="submit"></td>
                        </tr>
                    </table>
                </div>
            </div>
            <?php endif; ?>

</form>

<div id="ajax-response"></div>
<br class="clear">
</div>
<input type="hidden" id="xcount" value="<?php echo ($count-1) ?>" />
<table style="display: none;">
<tbody  id="regular_row">
<tr class="items_tr" id="">
    <td class="hlt_no">1.</td>
    <td class="hlt_item_name"><select class="xproduct_id" name="jproduct_id[]" style="width: 500px;"><option value="">Select product here...</option><?php echo hlt_reps_get_products_combo('regular')?></select></td>
    <td class="hlt_quantity"><input type="number" name="jquantity[]" value="1" /></td>
    <td class="hlt_cost"><span>$0.00</span></td>
    <td class="hlt_total"><span>$0.00</span></td>
    <td><a href="#" style="color: red;" class="xremove">Remove</a></td>
</tr>
</tbody>

</table>
<table style="display: none;">
<tbody  id="special_row">
<tr class="items_tr">
    <td class="hlt_no">1.</td>
    <td class="hlt_item_name"><select class="xproduct_id" style="width: 500px;" name="jproduct_id[]"><option value="">Select product here...</option><?php echo hlt_reps_get_products_combo('special')?></select></td>
    <td class="hlt_quantity"><input type="number" name="jquantity[]" value="1" /></td>
    <td class="hlt_cost"><span>$0.00</span></td>
    <td class="hlt_total"><span>$0.00</span></td>
    <td><a href="#" style="color: red;" class="xremove">Remove</a></td>
</tr>
</tbody>
</table>
