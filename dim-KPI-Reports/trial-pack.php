<?php

$startDate1 = '01-'.date('m-Y');
$endDate1 = date('d-m-Y');
$startDate2 = '01-'.date('m-Y');
$endDate2 = date('d-m-Y');
$trial_packs_report_start_date = "2018-01-01";
$tp_db = 'cz_my_cart';
$cart_db = 'cz_cart';
if(isset($_POST['search'])){
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

    $rpt1 = $_POST['rpt1']=='yes'?true:false;
    $rpt2 = $_POST['rpt2']=='yes'?true:false;
    $rpt3 = $_POST['rpt3']=='yes'?true:false;

    if($rpt1){
        $tb_orders_sql_1 ="
            SELECT p.ID as order_id,b.meta_value as amount, d.meta_value as email FROM $tp_db.wp_posts as p
            LEFT JOIN $tp_db.wp_postmeta as b ON b.post_id = p.ID
            LEFT JOIN $tp_db.wp_postmeta as d ON d.post_id = p.ID
            WHERE date(p.post_date)>=date('$fstartDate1') AND date(p.post_date)<=date('$fendDate1') AND p.post_status IN('wc-processing','wc-completed') AND p.post_type = 'shop_order' AND b.meta_key = '_order_total' AND d.meta_key = '_billing_email' AND b.meta_value=0
        ";
        $tp_orders_sql1 = "
            SELECT count(*) as no_of_orders FROM ($tb_orders_sql_1) as a 
        ";
        $tp_orders_results1 = $wpdb->get_row($tp_orders_sql1);
        $tp_clients_sql1 = "
            SELECT count(*) as no_of_clients FROM ($tb_orders_sql_1 GROUP BY d.meta_value) as a 
        ";
        $tp_clients_results1 = $wpdb->get_row($tp_clients_sql1);

        if($has_period2){
            $tb_orders_sql_2 ="
                SELECT p.ID as order_id,b.meta_value as amount, d.meta_value as email FROM $tp_db.wp_posts as p
                LEFT JOIN $tp_db.wp_postmeta as b ON b.post_id = p.ID
                LEFT JOIN $tp_db.wp_postmeta as d ON d.post_id = p.ID
                WHERE date(p.post_date)>=date('$fstartDate2') AND date(p.post_date)<=date('$fendDate2') AND p.post_status IN('wc-processing','wc-completed') AND p.post_type = 'shop_order' AND b.meta_key = '_order_total' AND d.meta_key = '_billing_email' AND b.meta_value=0
            ";
            $tp_orders_sql2 = "
                SELECT count(*) as no_of_orders FROM ($tb_orders_sql_2) as a 
            ";
            $tp_orders_results2 = $wpdb->get_row($tp_orders_sql2);
            $tp_clients_sql2 = "
                SELECT count(*) as no_of_clients FROM ($tb_orders_sql_2 GROUP BY d.meta_value) as a 
            ";
            $tp_clients_results2 = $wpdb->get_row($tp_clients_sql2);
        }
    }

    if($rpt2){
        $cart_clients_with_no_of_orders_sql = "
            SELECT count(p.ID) as no_of_orders,sum(b.meta_value) as sale_amount, d.meta_value as email FROM $cart_db.wp_posts as p
                LEFT JOIN $cart_db.wp_postmeta as b ON b.post_id = p.ID
                LEFT JOIN $cart_db.wp_postmeta as d ON d.post_id = p.ID
                WHERE date(p.post_date)>=date('$fstartDate1') AND p.post_status IN('wc-processing','wc-completed') AND p.post_type = 'shop_order' AND b.meta_key = '_order_total' AND d.meta_key = '_billing_email' AND b.meta_value>0 GROUP BY d.meta_value
        ";
        $tb_orders_sql_1 ="
            SELECT p.ID as order_id,b.meta_value as amount, d.meta_value as email FROM $tp_db.wp_posts as p
            LEFT JOIN $tp_db.wp_postmeta as b ON b.post_id = p.ID
            LEFT JOIN $tp_db.wp_postmeta as d ON d.post_id = p.ID
            WHERE date(p.post_date)>=date('$fstartDate1') AND date(p.post_date)<=date('$fendDate1') AND p.post_status IN('wc-processing','wc-completed') AND p.post_type = 'shop_order' AND b.meta_key = '_order_total' AND d.meta_key = '_billing_email' AND b.meta_value=0
        ";
        $tp_clients_with_paid_orders_sql1 = "
            SELECT count(*) as no_of_clients, sum(sale_amount) as total_sales,(sum(sale_amount)/count(*)) as average_sale FROM ($tb_orders_sql_1 GROUP BY d.meta_value) as a 
            LEFT JOIN ($cart_clients_with_no_of_orders_sql) as b ON b.email = a.email
            WHERE b.email IS NOT NULL
        ";

        $tp_clients_with_paid_orders_results1 = $wpdb->get_row($tp_clients_with_paid_orders_sql1);
        //echo $tp_clients_with_paid_orders_sql1;
        $tp_clients_with_one_paid_orders_results1 = $wpdb->get_row($tp_clients_with_paid_orders_sql1." AND no_of_orders=1 ");
        $tp_clients_with_two_paid_orders_results1 = $wpdb->get_row($tp_clients_with_paid_orders_sql1." AND no_of_orders=2 ");
        $tp_clients_with_three_paid_orders_results1 = $wpdb->get_row($tp_clients_with_paid_orders_sql1." AND no_of_orders=3 ");
        $tp_clients_with_four_paid_orders_results1 = $wpdb->get_row($tp_clients_with_paid_orders_sql1." AND no_of_orders=4 ");
        $tp_clients_with_five_paid_orders_results1 = $wpdb->get_row($tp_clients_with_paid_orders_sql1." AND no_of_orders=5 ");

        if($has_period2){
            $cart_clients_with_no_of_orders_sql2 = "
                SELECT count(p.ID) as no_of_orders,sum(b.meta_value) as sale_amount, d.meta_value as email FROM $cart_db.wp_posts as p
                    LEFT JOIN $cart_db.wp_postmeta as b ON b.post_id = p.ID
                    LEFT JOIN $cart_db.wp_postmeta as d ON d.post_id = p.ID
                    WHERE date(p.post_date)>=date('$fstartDate2') AND p.post_status IN('wc-processing','wc-completed') AND p.post_type = 'shop_order' AND b.meta_key = '_order_total' AND d.meta_key = '_billing_email' AND b.meta_value>0 GROUP BY d.meta_value
            ";
            $tb_orders_sql_2 ="
                SELECT p.ID as order_id,b.meta_value as amount, d.meta_value as email FROM $tp_db.wp_posts as p
                LEFT JOIN $tp_db.wp_postmeta as b ON b.post_id = p.ID
                LEFT JOIN $tp_db.wp_postmeta as d ON d.post_id = p.ID
                WHERE date(p.post_date)>=date('$fstartDate2') AND date(p.post_date)<=date('$fendDate2') AND p.post_status IN('wc-processing','wc-completed') AND p.post_type = 'shop_order' AND b.meta_key = '_order_total' AND d.meta_key = '_billing_email' AND b.meta_value=0
            ";
            $tp_clients_with_paid_orders_sql2 = "
                SELECT count(*) as no_of_clients, sum(sale_amount) as total_sales,(sum(sale_amount)/count(*)) as average_sale FROM ($tb_orders_sql_2 GROUP BY d.meta_value) as a 
                LEFT JOIN ($cart_clients_with_no_of_orders_sql2) as b ON b.email = a.email
                WHERE b.email IS NOT NULL
            ";

            $tp_clients_with_paid_orders_results2 = $wpdb->get_row($tp_clients_with_paid_orders_sql2);

            $tp_clients_with_one_paid_orders_results2 = $wpdb->get_row($tp_clients_with_paid_orders_sql2." AND no_of_orders=1 ");
            $tp_clients_with_two_paid_orders_results2 = $wpdb->get_row($tp_clients_with_paid_orders_sql2." AND no_of_orders=2 ");
            $tp_clients_with_three_paid_orders_results2 = $wpdb->get_row($tp_clients_with_paid_orders_sql2." AND no_of_orders=3 ");
            $tp_clients_with_four_paid_orders_results2 = $wpdb->get_row($tp_clients_with_paid_orders_sql2." AND no_of_orders=4 ");
            $tp_clients_with_five_paid_orders_results2 = $wpdb->get_row($tp_clients_with_paid_orders_sql2." AND no_of_orders=5 ");
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
 <h2>Trial Pack</h2>
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
        <td align="right"><input type="checkbox" id="rpt1" name="rpt1" value="yes" <?=$rpt1?'checked':''?>></td>
        <td colspan="3">Number of trial pack orders and clients</td>
    </tr>
    <tr class="row-tr">
        <td align="right"><input type="checkbox" id="rpt2" name="rpt2" value="yes" <?=$rpt2?'checked':''?>></td>
        <td colspan="3">Number of people who have received trial pack and did a paid order after with sales amounts and average sale amount</td>
    </tr>
    <tr class="row-tr">
        <td align="right"><input type="checkbox" id="rpt3" name="rpt3" value="yes" <?=$rpt3?'checked':''?>></td>
        <td colspan="3">How many who received trial pack ordered 2nd,3rd, 4th and 5th time with sales amounts and average sale amount</td>
    </tr>
    <tr>
        <td colspan="4" align="right"><input name="search" id="submit" class="button button-primary" value="Generate Report" type="submit"></td>
    </tr>
</table>

<?php if(isset($_POST['search'])): ?>
    <div id="to_print">
        <h2 style="text-align: center;">KPI REPORT</h2>
        <h3 style="text-align: center;">Trial Packs</h3>
<table class="widefat s_tbl reportstbl" style="width: 90%; margin: 20px auto;">

    <tr class="form-field">
        <th></th>
        <th>Period 1 <br/><span>From: <?php if(isset($startDate1)) echo date('m-d-Y',strtotime($startDate1)) ?> To: <?php if(isset($endDate1)) echo date('m-d-Y',strtotime($endDate1)) ?></span></th>
        <?php if($has_period2): ?>
        <th>Period 2 <br/><span>From: <?php if(isset($startDate2)) echo date('m-d-Y',strtotime($startDate2)) ?> To: <?php if(isset($endDate2)) echo date('m-d-Y',strtotime($endDate2)) ?></span></th>
        <?php endif; ?>
    </tr>
    <?php if($rpt1): ?>
        <tr>
            <td>Number of trial pack orders</td>
            <td class="nums"><?=number_format($tp_orders_results1->no_of_orders,0)?></td>
            <?php if($has_period2): ?>
            <td class="nums"><?=number_format($tp_orders_results2->no_of_orders,0)?></td>
            <?php endif; ?>
        </tr>
        <tr>
            <td>Number of trial pack clients</td>
            <td class="nums"><?=number_format($tp_clients_results1->no_of_clients,0)?></td>
            <?php if($has_period2): ?>
            <td class="nums"><?=number_format($tp_clients_results2->no_of_clients,0)?></td>
            <?php endif; ?>
        </tr>
    <?php endif; ?>
    <?php if($rpt2): ?>
        <tr>
            <td>Number of people who have received trial pack and did a paid order there after</td>
            <td class="nums"><?=number_format($tp_clients_with_paid_orders_results1->no_of_clients,0)?></td>
            <?php if($has_period2): ?>
            <td class="nums"><?=number_format($tp_clients_with_paid_orders_results2->no_of_clients,0)?></td>
            <?php endif; ?>
        </tr>
        <tr>
            <td>Total sales from trial pack clients</td>
            <td class="nums"><?=number_format($tp_clients_with_paid_orders_results1->total_sales,2)?></td>
            <?php if($has_period2): ?>
            <td class="nums"><?=number_format($tp_clients_with_paid_orders_results2->total_sales,2)?></td>
            <?php endif; ?>
        </tr>
        <tr>
            <td>Average sale from trial pack clients</td>
            <td class="nums"><?=number_format($tp_clients_with_paid_orders_results1->average_sale,2)?></td>
            <?php if($has_period2): ?>
            <td class="nums"><?=number_format($tp_clients_with_paid_orders_results2->average_sale,2)?></td>
            <?php endif; ?>
        </tr>
    <?php endif; ?>
    <?php if($rpt3): ?>
        <tr>
            <td>Number of clients who have received trial pack and did one paid order there after</td>
            <td class="nums"><?=number_format($tp_clients_with_one_paid_orders_results1->no_of_clients,0)?></td>
            <?php if($has_period2): ?>
            <td class="nums"><?=number_format($tp_clients_with_one_paid_orders_results2->no_of_clients,0)?></td>
            <?php endif; ?>
        </tr>
        <tr>
            <td>Total sales from clients who have received trial pack and did one paid order there after</td>
            <td class="nums"><?=number_format($tp_clients_with_one_paid_orders_results1->total_sales,2)?></td>
            <?php if($has_period2): ?>
            <td class="nums"><?=number_format($tp_clients_with_one_paid_orders_results2->total_sales,2)?></td>
            <?php endif; ?>
        </tr>
        <tr>
            <td>Average sale from clients who have received trial pack and did one paid order there after</td>
            <td class="nums"><?=number_format($tp_clients_with_one_paid_orders_results1->average_sale,2)?></td>
            <?php if($has_period2): ?>
            <td class="nums"><?=number_format($tp_clients_with_one_paid_orders_results2->average_sale,2)?></td>
            <?php endif; ?>
        </tr>

        <tr>
            <td>Number of clients who have received trial pack and did two paid orders there after</td>
            <td class="nums"><?=number_format($tp_clients_with_two_paid_orders_results1->no_of_clients,0)?></td>
            <?php if($has_period2): ?>
            <td class="nums"><?=number_format($tp_clients_with_two_paid_orders_results2->no_of_clients,0)?></td>
            <?php endif; ?>
        </tr>
        <tr>
            <td>Total sales from clients who have received trial pack and did two paid orders there after</td>
            <td class="nums"><?=number_format($tp_clients_with_two_paid_orders_results1->total_sales,2)?></td>
            <?php if($has_period2): ?>
            <td class="nums"><?=number_format($tp_clients_with_two_paid_orders_results2->total_sales,2)?></td>
            <?php endif; ?>
        </tr>
        <tr>
            <td>Average sale from clients who have received trial pack and did two paid orders there after</td>
            <td class="nums"><?=number_format($tp_clients_with_two_paid_orders_results1->average_sale,2)?></td>
            <?php if($has_period2): ?>
            <td class="nums"><?=number_format($tp_clients_with_two_paid_orders_results2->average_sale,2)?></td>
            <?php endif; ?>
        </tr>



        <tr>
            <td>Number of clients who have received trial pack and did three paid orders there after</td>
            <td class="nums"><?=number_format($tp_clients_with_three_paid_orders_results1->no_of_clients,0)?></td>
            <?php if($has_period2): ?>
            <td class="nums"><?=number_format($tp_clients_with_three_paid_orders_results2->no_of_clients,0)?></td>
            <?php endif; ?>
        </tr>
        <tr>
            <td>Total sales from clients who have received trial pack and did three paid orders there after</td>
            <td class="nums"><?=number_format($tp_clients_with_three_paid_orders_results1->total_sales,2)?></td>
            <?php if($has_period2): ?>
            <td class="nums"><?=number_format($tp_clients_with_three_paid_orders_results2->total_sales,2)?></td>
            <?php endif; ?>
        </tr>
        <tr>
            <td>Average sale from clients who have received trial pack and did three paid orders there after</td>
            <td class="nums"><?=number_format($tp_clients_with_three_paid_orders_results1->average_sale,2)?></td>
            <?php if($has_period2): ?>
            <td class="nums"><?=number_format($tp_clients_with_three_paid_orders_results2->average_sale,2)?></td>
            <?php endif; ?>
        </tr>


        <tr>
            <td>Number of clients who have received trial pack and did four paid orders there after</td>
            <td class="nums"><?=number_format($tp_clients_with_four_paid_orders_results1->no_of_clients,0)?></td>
            <?php if($has_period2): ?>
            <td class="nums"><?=number_format($tp_clients_with_four_paid_orders_results2->no_of_clients,0)?></td>
            <?php endif; ?>
        </tr>
        <tr>
            <td>Total sales from clients who have received trial pack and did four paid orders there after</td>
            <td class="nums"><?=number_format($tp_clients_with_four_paid_orders_results1->total_sales,2)?></td>
            <?php if($has_period2): ?>
            <td class="nums"><?=number_format($tp_clients_with_four_paid_orders_results2->total_sales,2)?></td>
            <?php endif; ?>
        </tr>
        <tr>
            <td>Average sale from clients who have received trial pack and did four paid orders there after</td>
            <td class="nums"><?=number_format($tp_clients_with_four_paid_orders_results1->average_sale,2)?></td>
            <?php if($has_period2): ?>
            <td class="nums"><?=number_format($tp_clients_with_four_paid_orders_results2->average_sale,2)?></td>
            <?php endif; ?>
        </tr>


        <tr>
            <td>Number of clients who have received trial pack and did five paid orders there after</td>
            <td class="nums"><?=number_format($tp_clients_with_five_paid_orders_results1->no_of_clients,0)?></td>
            <?php if($has_period2): ?>
            <td class="nums"><?=number_format($tp_clients_with_five_paid_orders_results2->no_of_clients,0)?></td>
            <?php endif; ?>
        </tr>
        <tr>
            <td>Total sales from clients who have received trial pack and did five paid orders there after</td>
            <td class="nums"><?=number_format($tp_clients_with_five_paid_orders_results1->total_sales,2)?></td>
            <?php if($has_period2): ?>
            <td class="nums"><?=number_format($tp_clients_with_five_paid_orders_results2->total_sales,2)?></td>
            <?php endif; ?>
        </tr>
        <tr>
            <td>Average sale from clients who have received trial pack and did five paid orders there after</td>
            <td class="nums"><?=number_format($tp_clients_with_five_paid_orders_results1->average_sale,2)?></td>
            <?php if($has_period2): ?>
            <td class="nums"><?=number_format($tp_clients_with_five_paid_orders_results2->average_sale,2)?></td>
            <?php endif; ?>
        </tr>
    <?php endif; ?>
    
</table>

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