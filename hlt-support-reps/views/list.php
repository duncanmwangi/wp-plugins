<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' ); ?>
<?php 
global $wpdb,$woocommerce,$HLT_ITEMS_PER_PAGE;
$qstr = $_SERVER['QUERY_STRING'];
$qstr = str_replace('&search=search','',$qstr);
if (isset($_GET['upg'])) { $page = $_GET['upg']; $qstr = str_replace('&upg='.$page,'',$qstr); } else { $page=1; }
$start_from = ($page-1) * $HLT_ITEMS_PER_PAGE;
$args = array(
         'posts_per_page'=>$HLT_ITEMS_PER_PAGE,
         'post_type' => 'shop_order',
         'orderby' => 'post_date',
         'order'=> 'DESC',
         'paged' => $page
        );
        $post_status = array_keys( wc_get_order_statuses() ); 
if(isset($_GET['post_status'])){
    $post_status = $_GET['post_status'];
    if($post_status=='trash' || $post_status=='wc-on-hold' || $post_status=='wc-completed' || $post_status=='wc-processing' || $post_status=='wc-cancelled' || $post_status=='wc-refunded' || $post_status=='wc-failed' ) $post_status = $_GET['post_status'];
    else $post_status = array_keys( wc_get_order_statuses() );
}
if(isset($_GET['search_by']) && isset($_GET['s']) && !empty($_GET['s'])){
    $search_by = $_GET['search_by'];
    $searchterm = trim($_GET['s']);
    $count = 0;
    if($search_by!='order_no')
        $args['meta_query']['relation'] = 'OR';
    if($search_by=='fname'){
        $args['meta_query'][$count++] = array(
    		'key'		=> '_billing_first_name',
    		'value'		=> $searchterm,
    		'compare'	=> 'LIKE'
    	);
        $args['meta_query'][$count++] = array(
    		'key'		=> '_shipping_first_name',
    		'value'		=> $searchterm,
    		'compare'	=> 'LIKE'
    	);
    }
    elseif($search_by=='lname'){
        $args['meta_query'][$count++] = array(
    		'key'		=> '_billing_last_name',
    		'value'		=> $searchterm,
    		'compare'	=> 'LIKE'
    	);
        $args['meta_query'][$count++] = array(
    		'key'		=> '_shipping_last_name',
    		'value'		=> $searchterm,
    		'compare'	=> 'LIKE'
    	);
    }
    elseif($search_by=='order_no'){
        $searchtermx = (int)$searchterm;
        $args['post__in'] = array($searchtermx);
    }
    elseif($search_by=='email'){
        $args['meta_query'][$count++] = array(
    		'key'		=> '_billing_email',
    		'value'		=> $searchterm,
    		'compare'	=> 'LIKE'
    	);
    }
    elseif($search_by=='phone'){
        $args['meta_query'][$count++] = array(
    		'key'		=> '_billing_phone',
    		'value'		=> $searchterm,
    		'compare'	=> 'LIKE'
    	);
    }
    elseif($search_by=='tran_id'){
        $args['meta_query'][$count++] = array(
    		'key'		=> '_transaction_id',
    		'value'		=> $searchterm,
    		'compare'	=> 'LIKE'
    	);
    }
    elseif($search_by=='company'){
        $args['meta_query'][$count++] = array(
    		'key'		=> '_billing_company',
    		'value'		=> $searchterm,
    		'compare'	=> 'LIKE'
    	);
    }
    
}

if(isset($_GET['year']) && !empty($_GET['month'])){
    $year = (int)$_GET['year'];
    $args['year'] = $year;
}

if(isset($_GET['month']) && !empty($_GET['month'])){
    $month = (int)$_GET['month'];
    $args['monthnum'] = $month;
}


$args['post_status'] = $post_status;
//if($post_status=='publish') unset($args['post_status']);
$orders=new WP_Query($args);

$all_orders_count = hlt_reps_get_order_count_by_status(array_keys( wc_get_order_statuses() ));
$trashed_orders_count = hlt_reps_get_order_count_by_status('trash');
$onhold_orders_count = hlt_reps_get_order_count_by_status('wc-on-hold');
$processing_orders_count = hlt_reps_get_order_count_by_status('wc-processing');
$completed_orders_count = hlt_reps_get_order_count_by_status('wc-completed');
$cancelled_orders_count = hlt_reps_get_order_count_by_status('wc-cancelled');
$refunded_orders_count = hlt_reps_get_order_count_by_status('wc-refunded');
$failed_orders_count = hlt_reps_get_order_count_by_status('wc-failed');
$total_records = $orders->found_posts;
?>
<div class="wrap">
<h2>HLT SUPPORT ORDERS <a href="https://healthlivetransform.com/cart/support-rep-orders/" target="_blank" class="add-new-h2">Add New Order</a></h2>
<form id="posts-filter" action="<?php echo admin_url('admin.php?page=hlt-reps') ?>" method="get">
 <input type="hidden" value="hlt-reps" name="page" />
 <input type="hidden" value="<?php if(isset($post_status)) echo $post_status; else echo 'publish' ?>" name="post_status" />
<ul class="subsubsub" style="margin-left: 10px; margin-bottom: 10px;">
	<li class="all"><a href="<?php echo admin_url('admin.php?page=hlt-reps&post_status=publish') ?>" class="publish <?php if(isset($post_status) && $post_status=='publish') echo 'current' ?>">All <span class="count">(<?php echo $all_orders_count ?>)</span></a> |</li>
	<li class="wc-on-hold <?php if(isset($post_status) && $post_status=='wc-processing') echo 'current' ?>"><a href="<?php echo admin_url('admin.php?page=hlt-reps&post_status=wc-processing') ?>">Processing <span class="count">(<?php echo $processing_orders_count ?>)</span></a> |</li>
	<li class="trash <?php if(isset($post_status) && $post_status=='trash') echo 'current' ?>"><a href="<?php echo admin_url('admin.php?page=hlt-reps&post_status=trash') ?>">Trash <span class="count">(<?php echo $trashed_orders_count ?>)</span></a> |</li>
	<li class="wc-on-hold <?php if(isset($post_status) && $post_status=='wc-on-hold') echo 'current' ?>"><a href="<?php echo admin_url('admin.php?page=hlt-reps&post_status=wc-on-hold') ?>">On hold <span class="count">(<?php echo $onhold_orders_count ?>)</span></a> |</li>
	<li class="wc-completed <?php if(isset($post_status) && $post_status=='wc-completed') echo 'current' ?>"><a href="<?php echo admin_url('admin.php?page=hlt-reps&post_status=wc-completed') ?>">Completed <span class="count">(<?php echo $completed_orders_count ?>)</span></a> |</li>
	<li class="wc-cancelled  <?php if(isset($post_status) && $post_status=='wc-cancelled') echo 'current' ?>"><a href="<?php echo admin_url('admin.php?page=hlt-reps&post_status=wc-cancelled') ?>">Cancelled <span class="count">(<?php echo $cancelled_orders_count ?>)</span></a> |</li>
	<li class="wc-refunded <?php if(isset($post_status) && $post_status=='wc-refunded') echo 'current' ?>"><a href="<?php echo admin_url('admin.php?page=hlt-reps&post_status=wc-refunded') ?>">Refunded <span class="count">(<?php echo $refunded_orders_count ?>)</span></a> |</li>
	<li class="wc-failed <?php if(isset($post_status) && $post_status=='wc-failed') echo 'current' ?>"><a href="<?php echo admin_url('admin.php?page=hlt-reps&post_status=wc-failed') ?>">Failed <span class="count">(<?php echo $failed_orders_count ?>)</span></a></li>
</ul>
 <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<table class="widefat s_tbl">
    <tr class="form-field">
        <td>Year</td>
        <td><select name="year"><option value="">Year</option><?php echo hlt_reps_get_years(isset($year)?$year:'') ?></select></td>
        <td>Month</td>
        <td><select name="month"><option value="">Month</option><?php echo hlt_reps_get_months(isset($month)?$month:0) ?></select></td>
        <td>Search By</td>
        <td>
            <select name="search_by">
                <option value="order_no" <?php if(isset($search_by) && $search_by=='order_no') echo ' selected="selected" ' ?>>Order No.</option>
                <option value="email" <?php if(isset($search_by) && $search_by=='email') echo ' selected="selected" ' ?>>Email Address</option>
                <option value="fname" <?php if(isset($search_by) && $search_by=='fname') echo ' selected="selected" ' ?>>First Name</option>
                <option value="lname" <?php if(isset($search_by) && $search_by=='lname') echo ' selected="selected" ' ?>>Last Name</option>
                <option value="company" <?php if(isset($search_by) && $search_by=='company') echo ' selected="selected" ' ?>>Company Name</option>
                <option value="phone" <?php if(isset($search_by) && $search_by=='phone') echo ' selected="selected" ' ?>>Phone Number</option>
                <option value="tran_id" <?php if(isset($search_by) && $search_by=='tran_id') echo ' selected="selected" ' ?>>Transaction ID</option>
            </select>
        </td>
        <td>Search Value</td>
        <td><input type="text" name="s" value="<?php if(isset($searchterm)) echo $searchterm ?>" /></td>
        <td><input name="search" id="submit" class="button button-primary" value="Search" type="submit"></td>
        
    </tr>
</table>
<table class="wp-list-table widefat fixed posts " style="margin-top: 20px;">
<thead>
	<tr>
		<th scope="col" class="manage-column column-name" width="5%">No.</th>
        
        <th scope="col" class="manage-column column-name" width="20%">Order No.</th>
        <th scope="col" class="manage-column column-name" width="8%">status</th>
        <th scope="col" class="manage-column column-name"  width="15%">Coupons</th>
        <th scope="col" class="manage-column column-name"  width="8%">Purchased</th>
        <th scope="col" class="manage-column column-name"  width="20%">Ship To</th>
        <th scope="col" class="manage-column column-name" width="12%">Date</th>
        <th scope="col" class="manage-column column-name" width="10%">Total</th>
   	</tr>
	</thead>

	<tbody id="the-list">
<?php
$curr = get_woocommerce_currency_symbol();
if($orders->have_posts()):
    $count =$start_from+1;
    while($orders->have_posts()): $orders->the_post();
        $order_id = $orders->post->ID;
        $order = new WC_Order($order_id);
        $status = $order->get_status();
        $items_no = $order->get_item_count();
        $items = $items_no>1?'items':'item';
        $items_no = $items_no.' '.$items;
        $billing_address = $order->get_formatted_billing_address();
        $shipping_address = $order->get_formatted_shipping_address();
        $sale_price = $order->get_subtotal()-$order->get_total_discount();
        $payment_method_title = $order->get_payment_method_title();
        $sale_price = $curr.number_format($sale_price,2);
        $sale_price.=' <br/> Via <br/>'.$payment_method_title;
        $shipping_method_title = $order->get_shipping_method();
        $shipping_address.=' <br/> Via <span class="dim_highlight">'.$shipping_method_title.'</span>';
        $order_date = date('d-m-Y H:i:s',strtotime($order->get_date_created()));
        $order_dets = '<a href="'.admin_url('admin.php?page=hlt-reps&action=edit&id='.$order_id).'" title="'.$billing_address.'">#'.$order_id.'</a> by '.$order->get_billing_first_name().' '.$order->get_billing_last_name().' <br/> <a href="mailto:'.$order->get_billing_email().'">'.$order->get_billing_email().'</a>';
        $coupons = '';
        if( $order->get_used_coupons() ) {
			 foreach( $order->get_used_coupons() as $coupon) {
		        $coupons .= ''.$coupon.'<br/>';
	        }
		}
        ?>
        <tr>
            <td ><?php echo $count++ ?></td>
            <td align="center"><?php echo $order_dets ?></td>
            <td align="center"><?php echo $status ?></td>
            <td align="center"><?php echo $coupons ?></td>
            <td><?php echo $items_no; ?></td>
            <td><?php echo $shipping_address ?></td>
            <td align="center"><?php echo $order_date ?></td>
            <td><?php echo $sale_price ?></td>
        </tr>
        
        <?php
    endwhile;
else:
        ?>
        <tr id="post-5796" class="post-5796 hentry alternate iedit author-self level-0">

			<td class="name column-name" colspan="7">No records found</td>
		</tr>
        <?php
endif;
 ?>
	</tbody>
</table>
<?php 
hlt_reps_pagination(admin_url('admin.php?'.$qstr),$total_records,$page);
 ?>

</form>

<div id="ajax-response"></div>
<br class="clear">
</div>
<style>
.wp-list-table tr td{
    vertical-align: middle;
}
</style>