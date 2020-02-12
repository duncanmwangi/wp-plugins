<?php

$startDate1 = '01-'.date('m-Y');
$endDate1 = date('d-m-Y');
$startDate2 = '01-'.date('m-Y');
$endDate2 = date('d-m-Y');
if(isset($_POST['searchp'])){
    //period 1 dates
    $fstartDate1 = $_POST['p1_start_date'];
    $fendDate1 = $_POST['p1_end_date'];
    //check if has 2 periods 
    $has_period2 = false;
    if($_POST['period2checkbox']=='yes'){
        $has_period2 = true;
        //period 2 dates
        $fstartDate2 = $_POST['p2_start_date'];
        $fendDate2 = $_POST['p2_end_date'];
    }

    $rpt1 = $_POST['rpt1']==1?1:2;


    
    if($rpt1==1){
        //period 1 queries

        $rpt1_top_20_coupons_sql = "SELECT a.order_item_name as coupon_code, count(a.order_item_name) as uses, sum(b.meta_value) as total_sales,c.post_date,c.post_status FROM `wp_woocommerce_order_items` as a 
        LEFT JOIN wp_postmeta as b ON b.post_id = a.order_id
        LEFT JOIN wp_posts as c ON c.ID = a.order_id
        WHERE a.order_item_type LIKE 'coupon' AND b.meta_key = '_order_total' AND c.post_status IN('wc-processing','wc-completed') AND c.post_type = 'shop_order' AND date(c.post_date)>=date('$fstartDate1') AND date(c.post_date)<=date('$fendDate1')  GROUP BY a.order_item_name ORDER BY sum(b.meta_value) DESC LIMIT 0,20";
        $rpt1_top_20_coupons = $wpdb->get_results($rpt1_top_20_coupons_sql);

    if($has_period2){
        $rpt1_top_20_coupons_sql2 = "SELECT a.order_item_name as coupon_code, count(a.order_item_name) as uses, sum(b.meta_value) as total_sales,c.post_date,c.post_status FROM `wp_woocommerce_order_items` as a 
        LEFT JOIN wp_postmeta as b ON b.post_id = a.order_id
        LEFT JOIN wp_posts as c ON c.ID = a.order_id
        WHERE a.order_item_type LIKE 'coupon' AND b.meta_key = '_order_total' AND c.post_status IN('wc-processing','wc-completed') AND c.post_type = 'shop_order' AND date(c.post_date)>=date('$fstartDate2') AND date(c.post_date)<=date('$fendDate2')  GROUP BY a.order_item_name ORDER BY sum(b.meta_value) DESC LIMIT 0,20";
        $rpt1_top_20_coupons2 = $wpdb->get_results($rpt1_top_20_coupons_sql2);
    }


        
    }


    if($rpt1==2){
        $coupon_code = $_POST['couponcode'];
        $rpt2_coupon_sql = "SELECT a.order_item_name as coupon_code, count(a.order_item_name) as uses, sum(b.meta_value) as total_sales,c.post_date,c.post_status FROM `wp_woocommerce_order_items` as a 
        LEFT JOIN wp_postmeta as b ON b.post_id = a.order_id
        LEFT JOIN wp_posts as c ON c.ID = a.order_id
        WHERE a.order_item_type LIKE 'coupon' AND b.meta_key = '_order_total' AND c.post_status IN('wc-processing','wc-completed') AND c.post_type = 'shop_order' AND date(c.post_date)>=date('$fstartDate1') AND date(c.post_date)<=date('$fendDate1') AND a.order_item_name LIKE '$coupon_code'  GROUP BY a.order_item_name";
        $rpt2_coupon = $wpdb->get_row($rpt2_coupon_sql);




        $rpt2_coupon_sqlk = "SELECT count(*) as uses FROM (SELECT count(d.post_id) as uses FROM `wp_woocommerce_order_items` as a 
        LEFT JOIN wp_postmeta as b ON b.post_id = a.order_id
        LEFT JOIN wp_postmeta as d ON d.post_id = a.order_id
        LEFT JOIN wp_posts as c ON c.ID = a.order_id
        WHERE a.order_item_type LIKE 'coupon' AND b.meta_key = '_order_total' AND d.meta_key = '_billing_email' AND c.post_status IN('wc-processing','wc-completed') AND c.post_type = 'shop_order' AND date(c.post_date)>=date('$fstartDate1') AND date(c.post_date)<=date('$fendDate1') AND a.order_item_name LIKE '$coupon_code'  GROUP BY d.meta_value HAVING uses";
        $endpart_sql =  " ) as a GROUP BY uses ";
        //echo $rpt2_coupon_sqlk."=1 $endpart_sql";

        if($rpt2_coupon)
        $rpt2_coupon_n_times =(object) [
            'one'=>$wpdb->get_row($rpt2_coupon_sqlk."=1 $endpart_sql")->uses,
            'two'=>$wpdb->get_row($rpt2_coupon_sqlk."=2 $endpart_sql")->uses,
            'three'=>$wpdb->get_row($rpt2_coupon_sqlk."=3 $endpart_sql")->uses,
            'four'=>$wpdb->get_row($rpt2_coupon_sqlk."=4 $endpart_sql")->uses,
            'five'=>$wpdb->get_row($rpt2_coupon_sqlk."=5 $endpart_sql")->uses,
            'above'=>$wpdb->get_row($rpt2_coupon_sqlk.">5 $endpart_sql")->uses
            ];

        if($has_period2){
            $rpt2_coupon_sql2 = "SELECT a.order_item_name as coupon_code, count(a.order_item_name) as uses, sum(b.meta_value) as total_sales,c.post_date,c.post_status FROM `wp_woocommerce_order_items` as a 
            LEFT JOIN wp_postmeta as b ON b.post_id = a.order_id
            LEFT JOIN wp_posts as c ON c.ID = a.order_id
            WHERE a.order_item_type LIKE 'coupon' AND b.meta_key = '_order_total' AND c.post_status IN('wc-processing','wc-completed') AND c.post_type = 'shop_order' AND date(c.post_date)>=date('$fstartDate2') AND date(c.post_date)<=date('$fendDate2') AND a.order_item_name LIKE '$coupon_code'  GROUP BY a.order_item_name";
            $rpt2_coupon2 = $wpdb->get_row($rpt2_coupon_sql2);


            $rpt2_coupon_sqlk2 = "SELECT count(*) as uses FROM (SELECT count(d.post_id) as uses FROM `wp_woocommerce_order_items` as a 
        LEFT JOIN wp_postmeta as b ON b.post_id = a.order_id
        LEFT JOIN wp_postmeta as d ON d.post_id = a.order_id
        LEFT JOIN wp_posts as c ON c.ID = a.order_id
        WHERE a.order_item_type LIKE 'coupon' AND b.meta_key = '_order_total' AND d.meta_key = '_billing_email' AND c.post_status IN('wc-processing','wc-completed') AND c.post_type = 'shop_order' AND date(c.post_date)>=date('$fstartDate2') AND date(c.post_date)<=date('$fendDate2') AND a.order_item_name LIKE '$coupon_code'  GROUP BY d.meta_value HAVING uses";
        $endpart_sql2 =  " ) as a GROUP BY uses ";
        if($rpt2_coupon2)
        $rpt2_coupon_n_times2 =(object) [
            'one'=>$wpdb->get_row($rpt2_coupon_sqlk2."=1 $endpart_sql2")->uses,
            'two'=>$wpdb->get_row($rpt2_coupon_sqlk2."=2 $endpart_sql2")->uses,
            'three'=>$wpdb->get_row($rpt2_coupon_sqlk2."=3 $endpart_sql2")->uses,
            'four'=>$wpdb->get_row($rpt2_coupon_sqlk2."=4 $endpart_sql2")->uses,
            'five'=>$wpdb->get_row($rpt2_coupon_sqlk2."=5 $endpart_sql2")->uses,
            'above'=>$wpdb->get_row($rpt2_coupon_sqlk2.">5 $endpart_sql2")->uses
            ];
        }
    }

    $startDate1 = date('Y-m-d',strtotime($fstartDate1));
    $endDate1 = date('Y-m-d',strtotime($fendDate1));
    if($has_period2){
        $startDate2 = date('Y-m-d',strtotime($fstartDate2));
        $endDate2 = date('Y-m-d',strtotime($fendDate2));
    }

}

 if(isset($msg)) echo $msg; ?>
 <h2>Coupons</h2>
<form method="post" action="">
<script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap/3/css/bootstrap.css" />
 
<!-- Include Date Range Picker -->
<script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />

<table class="widefat s_tbl search_tbl" style="">
    <tr class="tbl_periods">
        <td width="15%">Report Period 1</td>
        <td width="25%">
            <input type="text" name="period1" class="datepicker1" value="<?php if(isset($period1)) echo $period1 ?>" />
            <input type="hidden" name="p1_start_date" id="p1_start_date" value="<?=date('Y-m-d',strtotime($startDate1))?>">
            <input type="hidden" name="p1_end_date" id="p1_end_date" value="<?=date('Y-m-d',strtotime($endDate1))?>">
        </td>
        <td width="25%"><input type="checkbox" id="period2checkbox" name="period2checkbox" value="yes" <?=$has_period2?'checked="checked"':''?> >&nbsp;Report Period 2</td>
        <td width="25%">
            &nbsp;<input type="text" name="period2" id="period2" class="datepicker2" value="<?php if(isset($period2)) echo $period2 ?>" />
            <input type="hidden" name="p2_start_date" id="p2_start_date" value="<?=date('Y-m-d',strtotime($startDate2))?>">
            <input type="hidden" name="p2_end_date" id="p2_end_date" value="<?=date('Y-m-d',strtotime($endDate2))?>">
        </td>
    </tr>
    <tr>
        <th colspan="4"><span style="font-weight: bold;">Check the reports to be included below</span></th>
    </tr>


     <tr class="row-tr">
        <td align="right"><input type="radio" id="rpt1" name="rpt1" value="1" <?=$rpt1==1?'checked':''?>></td>
        <td colspan="3">Top 20 performing coupon codes based on sales amount and number of sales along with average sales amount per order</td>
    </tr>
    <tr class="row-tr">
        <td align="right"><input type="radio" id="couponcheckbox" name="rpt1" value="2" <?=$rpt1==2?'checked':''?>></td>
        <td colspan="3">Coupon code lookup feature: ability to input any coupon code and get the above stats
            <span id="couponfld"><br/>coupon Code: <input type="text" name="couponcode" value="<?=isset($coupon_code) && !empty($coupon_code)?$coupon_code:''?>" placeholder="Coupon Code"></span>
        </td>
    </tr>
    <tr>
        <td colspan="4" align="right"><input name="searchp" id="submit" class="button button-primary" value="Generate Report" type="submit"></td>
    </tr>
</table>

<?php if(isset($_POST['searchp'])): 
?>
<div id="to_print">
        <h2 style="text-align: center;">KPI REPORT</h2>
        <h3 style="text-align: center;">Coupons</h3>
<?php
        if($rpt1==1):
?>
<table class="widefat s_tbl reportstbl" style="width: 90%; margin: 20px auto;">
    <tr>
        <th colspan="5">Top 20 performing coupon codes<br/> Period 1 <br/><span>From: <?php if(isset($startDate1)) echo date('m-d-Y',strtotime($startDate1)) ?> To: <?php if(isset($endDate1)) echo date('m-d-Y',strtotime($endDate1)) ?></span></th>
    </tr>
    <tr class="form-field">
        <th>No</th>
        <th>Coupon Code</th>
        <th>No of Uses</th>
        <th>Total Sales</th>
        <th>Average Sale</th>
        
    </tr>
    <?php  
    $count = 1;
            if($rpt1_top_20_coupons)
                foreach ($rpt1_top_20_coupons as $coupon) {
                    
    ?>
        <tr>
            <td><?=$count?></td>
            <td><?=$coupon->coupon_code?></td>
            <td class="nums"><?=number_format($coupon->uses,0)?></td>
            <td class="nums"><?=number_format($coupon->total_sales,0)?></td>
            <td class="nums"><?=number_format($coupon->total_sales/$coupon->uses,2)?></td>
        </tr>
    <?php $count++; }  ?>
    
    
</table>

<?php if($has_period2): ?>
<table class="widefat s_tbl reportstbl" style="width: 90%; margin: 20px auto;">
    <tr>
        <th colspan="5">Top 20 performing coupon codes<br/> Period 2 <br/><span>From: <?php if(isset($startDate2)) echo date('m-d-Y',strtotime($startDate2)) ?> To: <?php if(isset($endDate2)) echo date('m-d-Y',strtotime($endDate2)) ?></span></th>
    </tr>
    <tr class="form-field">
        <th>No</th>
        <th>Coupon Code</th>
        <th>No of Uses</th>
        <th>Total Sales</th>
        <th>Average Sale</th>
        
    </tr>
    <?php 
    $count = 1;
            if($rpt1_top_20_coupons2)
                foreach ($rpt1_top_20_coupons2 as $coupon) {
                    
    ?>
        <tr>
            <td><?=$count?></td>
            <td><?=$coupon->coupon_code?></td>
            <td class="nums"><?=number_format($coupon->uses,0)?></td>
            <td class="nums"><?=number_format($coupon->total_sales,0)?></td>
            <td class="nums"><?=number_format($coupon->total_sales/$coupon->uses,2)?></td>
        </tr>
    <?php $count++; }  ?>
    
    
</table>

<?php endif; ?>
<?php else: 
        if(empty($coupon_code)){
            echo '<h4>Coupon Code field must be filled</h4>';
        }else{
?>

<table class="widefat s_tbl reportstbl" style="width: 90%; margin: 20px auto;">
    <tr>
        <th colspan="9">Coupon code:<?=$coupon_code?> statistics for the period <span>From: <?php if(isset($startDate1)) echo date('m-d-Y',strtotime($startDate1)) ?> To: <?php if(isset($endDate1)) echo date('m-d-Y',strtotime($endDate1)) ?></span></th>
    </tr>
    <tr class="form-fieldxp">
        <th>No of Uses</th>
        <th>Total Sales</th>
        <th>Average Sale</th>
        <th>1 time uses</th>
        <th>2 time uses</th>
        <th>3 time uses</th>
        <th>4 time uses</th>
        <th>5 time uses</th>
        <th>Over 5 time uses</th>        
    </tr>
    <tr>
        <td class="nums"><?=number_format($rpt2_coupon->uses,0)?></td>
        <td class="nums"><?=number_format($rpt2_coupon->total_sales,2)?></td>
        <td class="nums"><?=number_format($rpt2_coupon->uses>0?$rpt2_coupon->total_sales/$rpt2_coupon->uses:0,2)?></td>
        <td class="nums"><?=isset($rpt2_coupon_n_times)? number_format($rpt2_coupon_n_times->one,0) : 0?></td>   
        <td class="nums"><?=isset($rpt2_coupon_n_times)? number_format($rpt2_coupon_n_times->two,0) : 0?></td>   
        <td class="nums"><?=isset($rpt2_coupon_n_times)? number_format($rpt2_coupon_n_times->three,0) : 0?></td>   
        <td class="nums"><?=isset($rpt2_coupon_n_times)? number_format($rpt2_coupon_n_times->four,0) : 0?></td>   
        <td class="nums"><?=isset($rpt2_coupon_n_times)? number_format($rpt2_coupon_n_times->five,0) : 0?></td>   
        <td class="nums"><?=isset($rpt2_coupon_n_times)? number_format($rpt2_coupon_n_times->above,0) : 0?></td>   
    </tr>
</table>
<?php if($has_period2): ?>
 <table class="widefat s_tbl reportstbl" style="width: 90%; margin: 20px auto;">
    <tr>
        <th colspan="9">Coupon code:<?=$coupon_code?> statistics for the period <span>From: <?php if(isset($startDate2)) echo date('m-d-Y',strtotime($startDate2)) ?> To: <?php if(isset($endDate2)) echo date('m-d-Y',strtotime($endDate2)) ?></span></th>
    </tr>
    <tr class="form-fieldxp">
        <th>No of Uses</th>
        <th>Total Sales</th>
        <th>Average Sale</th>
        <th>1 time uses</th>
        <th>2 time uses</th>
        <th>3 time uses</th>
        <th>4 time uses</th>
        <th>5 time uses</th>
        <th>Over 5 time uses</th>        
    </tr>
    <tr>
        <td class="nums"><?=number_format($rpt2_coupon2->uses,0)?></td>
        <td class="nums"><?=number_format($rpt2_coupon2->total_sales,2)?></td>
        <td class="nums"><?=number_format($rpt2_coupon2->uses>0?$rpt2_coupon2->total_sales/$rpt2_coupon2->uses:0,2)?></td>
        <td class="nums"><?=isset($rpt2_coupon_n_times2)? number_format($rpt2_coupon_n_times2->one,0) : 0?></td>   
        <td class="nums"><?=isset($rpt2_coupon_n_times2)? number_format($rpt2_coupon_n_times2->two,0) : 0?></td>   
        <td class="nums"><?=isset($rpt2_coupon_n_times2)? number_format($rpt2_coupon_n_times2->three,0) : 0?></td>   
        <td class="nums"><?=isset($rpt2_coupon_n_times2)? number_format($rpt2_coupon_n_times2->four,0) : 0?></td>   
        <td class="nums"><?=isset($rpt2_coupon_n_times2)? number_format($rpt2_coupon_n_times2->five,0) : 0?></td>   
        <td class="nums"><?=isset($rpt2_coupon_n_times2)? number_format($rpt2_coupon_n_times2->above,0) : 0?></td>        
    </tr>
</table>   
<?php endif; ?>
<?php } endif; ?>
</div>
<button type="button" class="button button-primary" style="float: right;" onclick="print_this('to_print')">Print Report</button>
<?php endif; ?>

</form>
<style type="text/css">
    .reportstbl tr td{
        font-size: 15px;
        border: 1px solid #ccc;
        font-weight: bold;
    }
    .reportstbl tr th{
        text-align: center;
        font-size: 18px;
        border: 1px solid #ccc;
        font-weight: bold;
    }

    .reportstbl tr th span{
        font-size: 15px;

    }
    .reportstbl tr td.nums{
        text-align: center;
        font-weight: lighter;
    }
    .tbl_periods td{
        font-weight: bold;
    }
    .reportstbl tr.form-fieldxp th{
        font-size: 13px;
    }

</style>
<script type="text/javascript">
    
    jQuery('.datepicker1').daterangepicker(
{
    locale: {
      format: 'MM-DD-YYYY'
    },
    startDate: '<?=date('m-d-Y',strtotime($startDate1));?>',
    endDate: '<?=date('m-d-Y',strtotime($endDate1));?>'
}, 
function(start, end, label) {
    alert("A new Period 1 date range was chosen: " + start.format('MM-DD-YYYY') + ' to ' + end.format('MM-DD-YYYY'));
    jQuery('#p1_start_date').val(start.format('YYYY-MM-DD'));
    jQuery('#p1_end_date').val(end.format('YYYY-MM-DD'));
});    
    jQuery('.datepicker2').daterangepicker(
{
    locale: {
      format: 'MM-DD-YYYY'
    },
    startDate: '<?=date('m-d-Y',strtotime($startDate2));?>',
    endDate: '<?=date('m-d-Y',strtotime($endDate2));?>'
}, 
function(start, end, label) {
    alert("A new Period 2 date range was chosen: " + start.format('MM-DD-YYYY') + ' to ' + end.format('MM-DD-YYYY'));
    jQuery('#p2_start_date').val(start.format('YYYY-MM-DD'));
    jQuery('#p2_end_date').val(end.format('YYYY-MM-DD'));
});
jQuery("#period2checkbox").change(function() {
    if(jQuery(this).is(':checked')) {
        jQuery('#period2').show(100);
    }else{
        jQuery('#period2').hide();
    }
});
jQuery(document).ready(function(){
    if(jQuery("#period2checkbox").is(':checked')) {
        jQuery('#period2').show(100);
    }else{
        jQuery('#period2').hide();
    }
});
jQuery("#couponcheckbox").change(function() {
    if(jQuery(this).is(':checked')) {
        jQuery('#couponfld').show(100);
    }else{
        jQuery('#couponfld').hide();
    }
});

jQuery("#rpt1").change(function() {
    if(jQuery(this).is(':checked')) {
        jQuery('#couponfld').hide();
    }else{
        jQuery('#couponfld').show(100);
    }
});
jQuery(document).ready(function(){
    if(jQuery("#couponcheckbox").is(':checked')) {
        jQuery('#couponfld').show(100);
    }else{
        jQuery('#couponfld').hide();
    }
    if(jQuery("#rpt1").is(':checked')) {
        jQuery('#couponfld').hide();
    }else{
        jQuery('#couponfld').show(100);
    }
});
window.print_this = function(id) {
    var prtContent = document.getElementById(id);
    var WinPrint = window.open('', '', 'left=0,top=0,width=800,height=900,toolbar=0,scrollbars=0,status=0');
    
    WinPrint.document.write('<link rel="stylesheet" type="text/css" href="<?=plugins_url( 'style.css', __FILE__ )?>">');
    WinPrint.document.write('<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/bootstrap/3/css/bootstrap.css">');
    
    // To keep styling
    /*var file = WinPrint.document.createElement("link");
    file.setAttribute("rel", "stylesheet");
    file.setAttribute("type", "text/css");
    file.setAttribute("href", 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css');
    WinPrint.document.head.appendChild(file);*/

    
    WinPrint.document.write(prtContent.innerHTML);
    WinPrint.document.close();
    WinPrint.setTimeout(function(){
      WinPrint.focus();
      WinPrint.print();
      WinPrint.close();
    }, 1000);
}
</script>