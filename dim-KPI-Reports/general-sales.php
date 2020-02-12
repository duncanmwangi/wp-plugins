<?php

$startDate1 = '01-'.date('m-Y');
$endDate1 = date('d-m-Y');
$startDate2 = '01-'.date('m-Y');
$endDate2 = date('d-m-Y');
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
    $rpt4 = $_POST['rpt4']=='yes'?true:false;

    if($rpt1){
        //period 1 queries
        $rpt1_sql_p1 ="SELECT coalesce(count(b.order_id),0) as no_of_orders, coalesce(sum(b.amount),0) as total_sales, coalesce((sum(b.amount)/count(b.order_id)),0) as average_sale FROM all_orders as b 
                    LEFT JOIN (SELECT email FROM all_orders as a WHERE date(a.order_date)<date('$fstartDate1') GROUP BY email) as c ON c.email=b.email
                    WHERE date(b.order_date)>=date('$fstartDate1') AND date(b.order_date)<=date('$fendDate1') AND c.email IS NULL AND b.amount>0";
        $rpt1_result_p1 = $wpdb->get_row($rpt1_sql_p1);
        //period 1 queries
        if($has_period2){
            $rpt1_sql_p2 ="SELECT coalesce(count(b.order_id),0) as no_of_orders, coalesce(sum(b.amount),0) as total_sales, coalesce((sum(b.amount)/count(b.order_id)),0) as average_sale FROM all_orders as b 
                    LEFT JOIN (SELECT email FROM all_orders as a WHERE date(a.order_date)<date('$fstartDate2') GROUP BY email) as c ON c.email=b.email
                    WHERE date(b.order_date)>=date('$fstartDate2') AND date(b.order_date)<=date('$fendDate2') AND c.email IS NULL AND b.amount>0";
            $rpt1_result_p2 = $wpdb->get_row($rpt1_sql_p2);
        }
    }
    if($rpt2){
        //period 1 queries
        $rpt2_sql_p1 ="SELECT coalesce(count(b.order_id),0) as no_of_orders, coalesce(sum(b.amount),0) as total_sales, coalesce((sum(b.amount)/count(b.order_id)),0) as average_sale FROM all_orders as b 
                    LEFT JOIN (SELECT email FROM all_orders as a WHERE date(a.order_date)<date('$fstartDate1') GROUP BY email) as c ON c.email=b.email
                    WHERE date(b.order_date)>=date('$fstartDate1') AND date(b.order_date)<=date('$fendDate1') AND c.email IS NOT NULL AND b.amount>0";
        $rpt2_result_p1 = $wpdb->get_row($rpt2_sql_p1);
        //period 1 queries
        if($has_period2){
            $rpt2_sql_p2 ="SELECT coalesce(count(b.order_id),0) as no_of_orders, coalesce(sum(b.amount),0) as total_sales, coalesce((sum(b.amount)/count(b.order_id)),0) as average_sale FROM all_orders as b 
                    LEFT JOIN (SELECT email FROM all_orders as a WHERE date(a.order_date)<date('$fstartDate2') GROUP BY email) as c ON c.email=b.email
                    WHERE date(b.order_date)>=date('$fstartDate2') AND date(b.order_date)<=date('$fendDate2') AND c.email IS NOT NULL AND b.amount>0";
            $rpt2_result_p2 = $wpdb->get_row($rpt2_sql_p2);
        }
    }

    if($rpt4){
        //period 1 queries
        $rpt4_never_ordered_again_sql = "SELECT count(*) as num, sum(amount) as total_sales, sum(amount)/count(*) as average_sale FROM (SELECT count(id) as num,email,amount FROM all_orders as a WHERE amount>0 AND a.order_date>=date('$fstartDate1') AND date(a.order_date)<=date('$fendDate1') GROUP BY email HAVING num=1 ) as t
        LEFT JOIN (SELECT email FROM all_orders as a WHERE amount>0 AND a.order_date>date('$fendDate1') GROUP BY email ) as b ON t.email = b.email
        WHERE b.email IS NULL
        ";
        $rpt4_never_ordered_again_results = $wpdb->get_row($rpt4_never_ordered_again_sql);

        if($has_period2){
            $rpt4_never_ordered_again_sql2 = "SELECT count(*) as num, sum(amount) as total_sales, sum(amount)/count(*) as average_sale FROM (SELECT count(id) as num,email,amount FROM all_orders as a WHERE amount>0 AND a.order_date>=date('$fstartDate2') AND date(a.order_date)<=date('$fendDate2') GROUP BY email HAVING num=1 ) as t
                LEFT JOIN (SELECT email FROM all_orders as a WHERE amount>0 AND a.order_date>date('$fendDate2') GROUP BY email ) as b ON t.email = b.email
                WHERE b.email IS NULL
                ";
                $rpt4_never_ordered_again_results2 = $wpdb->get_row($rpt4_never_ordered_again_sql2);
        }
        
    }


    if($rpt3){
        //period 1 queries
        $last_30_days_clients_sql = "SELECT email FROM all_orders as a WHERE a.order_date >= date(DATE_SUB(date('$fstartDate1'),INTERVAL 30 DAY)) AND a.amount>0 GROUP BY a.email";
        $rpt3_last_30_days_clients ="SELECT count(*) as num FROM  ($last_30_days_clients_sql) as b";
        $rpt3_last_30_days_clients_results = $wpdb->get_row($rpt3_last_30_days_clients);
        $rpt3_sql_no_of_clients_sale = "SELECT sum(amount) as total_sales, sum(amount)/count(id)as average_sale FROM all_orders as a WHERE a.order_date >= date(DATE_SUB(date('$fstartDate1'),INTERVAL 30 DAY)) AND a.amount>0 ";
        $rpt3_sql_no_of_clients_sales_results = $wpdb->get_row($rpt3_sql_no_of_clients_sale);

        if($has_period2){

            $last_30_days_clients_sql2 = "SELECT email FROM all_orders as a WHERE a.order_date >= date(DATE_SUB(date('$fstartDate2'),INTERVAL 30 DAY)) AND a.amount>0 GROUP BY a.email";
            $rpt3_last_30_days_clients2 ="SELECT count(*) as num FROM  ($last_30_days_clients_sql2) as b";
            $rpt3_last_30_days_clients_results2 = $wpdb->get_row($rpt3_last_30_days_clients2);
            $rpt3_sql_no_of_clients_sale2 = "SELECT sum(amount) as total_sales, sum(amount)/count(id)as average_sale FROM all_orders as a WHERE a.order_date >= date(DATE_SUB(date('$fstartDate2'),INTERVAL 30 DAY)) AND a.amount>0 ";
            $rpt3_sql_no_of_clients_sales_results2 = $wpdb->get_row($rpt3_sql_no_of_clients_sale2);
        }
        

        $rpt3_30_days_clients_60_sql = "SELECT count(*) as num, sum(sales) as total_sales,sum(sales)/count(*) as average_sale FROM (SELECT a.email,count(id) as no,sum(a.amount) as sales FROM all_orders as a 
        LEFT JOIN 
        ($last_30_days_clients_sql)
        AS b on b.email=a.email
        WHERE a.order_date >= date(DATE_SUB(date('$fstartDate1'),INTERVAL 60 DAY))  AND a.amount>0 AND b.email IS NOT NULL   GROUP BY a.email HAVING no>1) as c WHERE 1 ";
        $rpt3_30_days_clients_60_results = $wpdb->get_row($rpt3_30_days_clients_60_sql);

        if($has_period2){
            $rpt3_30_days_clients_60_sql2 = "SELECT count(*) as num, sum(sales) as total_sales,sum(sales)/count(*) as average_sale FROM (SELECT a.email,count(id) as no,sum(a.amount) as sales FROM all_orders as a 
            LEFT JOIN 
            ($last_30_days_clients_sql2)
            AS b on b.email=a.email
            WHERE a.order_date >= date(DATE_SUB(date('$fstartDate2'),INTERVAL 60 DAY))  AND a.amount>0 AND b.email IS NOT NULL   GROUP BY a.email HAVING no>1) as c WHERE 1 ";
            $rpt3_30_days_clients_60_results2 = $wpdb->get_row($rpt3_30_days_clients_60_sql2);
        }

        

        $rpt3_30_days_clients_90_sql = "SELECT count(*) as num, sum(sales) as total_sales,sum(sales)/count(*) as average_sale FROM (SELECT a.email,count(id) as no,sum(a.amount) as sales FROM all_orders as a 
        LEFT JOIN 
        ($last_30_days_clients_sql)
        AS b on b.email=a.email
        WHERE a.order_date >= date(DATE_SUB(date('$fstartDate1'),INTERVAL 90 DAY))  AND a.amount>0 AND b.email IS NOT NULL   GROUP BY a.email HAVING no>1) as c WHERE 1 ";
        $rpt3_30_days_clients_90_results = $wpdb->get_row($rpt3_30_days_clients_90_sql);


        if($has_period2){
            $rpt3_30_days_clients_90_sql2 = "SELECT count(*) as num, sum(sales) as total_sales,sum(sales)/count(*) as average_sale FROM (SELECT a.email,count(id) as no,sum(a.amount) as sales FROM all_orders as a 
            LEFT JOIN 
            ($last_30_days_clients_sql2)
            AS b on b.email=a.email
            WHERE a.order_date >= date(DATE_SUB(date('$fstartDate2'),INTERVAL 90 DAY))  AND a.amount>0 AND b.email IS NOT NULL   GROUP BY a.email HAVING no>1) as c WHERE 1 ";
            $rpt3_30_days_clients_90_results2 = $wpdb->get_row($rpt3_30_days_clients_90_sql2);
        }

        $rpt3_30_days_clients_120_sql = "SELECT count(*) as num, sum(sales) as total_sales,sum(sales)/count(*) as average_sale FROM (SELECT a.email,count(id) as no,sum(a.amount) as sales FROM all_orders as a 
        LEFT JOIN 
        ($last_30_days_clients_sql)
        AS b on b.email=a.email
        WHERE a.order_date >= date(DATE_SUB(date('$fstartDate1'),INTERVAL 120 DAY))  AND a.amount>0 AND b.email IS NOT NULL   GROUP BY a.email HAVING no>1) as c WHERE 1 ";
        $rpt3_30_days_clients_120_results = $wpdb->get_row($rpt3_30_days_clients_120_sql);

        if($has_period2){
            $rpt3_30_days_clients_120_sql2 = "SELECT count(*) as num, sum(sales) as total_sales,sum(sales)/count(*) as average_sale FROM (SELECT a.email,count(id) as no,sum(a.amount) as sales FROM all_orders as a 
            LEFT JOIN 
            ($last_30_days_clients_sql2)
            AS b on b.email=a.email
            WHERE a.order_date >= date(DATE_SUB(date('$fstartDate2'),INTERVAL 120 DAY))  AND a.amount>0 AND b.email IS NOT NULL   GROUP BY a.email HAVING no>1) as c WHERE 1 ";
            $rpt3_30_days_clients_120_results2 = $wpdb->get_row($rpt3_30_days_clients_120_sql2);
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
 <h2>General Sales</h2>
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
        <td colspan="3">Number of new orders with total and average sales</td>
    </tr>
    <tr class="row-tr">
        <td align="right"><input type="checkbox" id="rpt2" name="rpt2" value="yes" <?=$rpt2?'checked':''?>></td>
        <td colspan="3">Number of repeat orders with total and average sales</td>
    </tr>
    <tr class="row-tr">
        <td align="right"><input type="checkbox" id="rpt3" name="rpt3" value="yes" <?=$rpt3?'checked':''?>></td>
        <td colspan="3">Number of clients who’ve ordered at least once in the last 30 days and then separate numbers for those same clients who’ve ordered a 2nd time in the last 60,90,120 days and with total and average sales</td>
    </tr>
    <tr class="row-tr">
        <td align="right"><input type="checkbox" id="rpt4" name="rpt4" value="yes" <?=$rpt4?'checked':''?>></td>
        <td colspan="3">Number of clients who’ve ordered once and never ordered again and with total and average sales</td>
    </tr>
    <tr>
        <td colspan="4" align="right"><input name="search" id="submit" class="button button-primary" value="Generate Report" type="submit"></td>
    </tr>
</table>

<?php if(isset($_POST['search'])): ?>
    <div id="to_print">
        <h2 style="text-align: center;">KPI REPORT</h2>
        <h3 style="text-align: center;">General Sales</h3>
<table class="widefat s_tbl reportstbl" style="width: 90%; margin: 20px auto;">

    <tr class="form-field">
        <th></th>
        <th>Period 1 <br/><span>From: <?php if(isset($startDate1)) echo date('m-d-Y',strtotime($startDate1)) ?> <br/>To: <?php if(isset($endDate1)) echo date('m-d-Y',strtotime($endDate1)) ?></span></th>
        <?php if($has_period2): ?>
        <th>Period 2 <br/><span>From: <?php if(isset($startDate2)) echo date('m-d-Y',strtotime($startDate2)) ?> <br/>To: <?php if(isset($endDate2)) echo date('m-d-Y',strtotime($endDate2)) ?></span></th>
        <?php endif; ?>
    </tr>
    <?php if($rpt1): ?>
        <tr>
            <td>Number of new orders</td>
            <td class="nums"><?=number_format($rpt1_result_p1->no_of_orders,0)?></td>
            <?php if($has_period2): ?>
            <td class="nums"><?=number_format($rpt1_result_p2->no_of_orders,0)?></td>
            <?php endif; ?>
        </tr>
        <tr>
            <td>Total sales of new orders</td>
            <td class="nums"><?=number_format($rpt1_result_p1->total_sales,2)?></td>
            <?php if($has_period2): ?>
            <td class="nums"><?=number_format($rpt1_result_p2->total_sales,2)?></td>
            <?php endif; ?>
        </tr>
        <tr>
            <td>Average sale of new orders</td>
            <td class="nums"><?=number_format($rpt1_result_p1->average_sale,2)?></td>
            <?php if($has_period2): ?>
            <td class="nums"><?=number_format($rpt1_result_p2->average_sale,2)?></td>
            <?php endif; ?>
        </tr>
    <?php endif; ?>
    <?php if($rpt2): ?>
        <tr>
            <td>Number of repeat orders</td>
            <td class="nums"><?=number_format($rpt2_result_p1->no_of_orders,0)?></td>
            <?php if($has_period2): ?>
            <td class="nums"><?=number_format($rpt2_result_p2->no_of_orders,0)?></td>
            <?php endif; ?>
        </tr>
        <tr>
            <td>Total sales of repeat orders</td>
            <td class="nums"><?=number_format($rpt2_result_p1->total_sales,2)?></td>
            <?php if($has_period2): ?>
            <td class="nums"><?=number_format($rpt2_result_p2->total_sales,2)?></td>
            <?php endif; ?>
        </tr>
        <tr>
            <td>Average sale of repeat orders</td>
            <td class="nums"><?=number_format($rpt2_result_p1->average_sale,2)?></td>
            <?php if($has_period2): ?>
            <td class="nums"><?=number_format($rpt2_result_p2->average_sale,2)?></td>
            <?php endif; ?>
        </tr>
    <?php endif; ?>
    <?php endif; ?><?php if($rpt4): ?>
        <tr>
            <td>Number of clients who’ve ordered once and never ordered again</td>
            <td class="nums"><?=number_format($rpt4_never_ordered_again_results->num,0)?></td>
            <?php if($has_period2): ?>
            <td class="nums"><?=number_format($rpt4_never_ordered_again_results2->num,0)?></td>
            <?php endif; ?>
            
        </tr>
        <tr>
            <td>Total sales to clients who’ve ordered once and never ordered again</td>
            <td class="nums"><?=number_format($rpt4_never_ordered_again_results->total_sales,2)?></td>
            <?php if($has_period2): ?>
            <td class="nums"><?=number_format($rpt4_never_ordered_again_results2->total_sales,2)?></td>
            <?php endif; ?>
            
        </tr>
        <tr>
            <td>Average sale to clients who’ve ordered once and never ordered again</td>
            <td class="nums"><?=number_format($rpt4_never_ordered_again_results->average_sale,2)?></td>
            <?php if($has_period2): ?>
            <td class="nums"><?=number_format($rpt4_never_ordered_again_results2->average_sale,2)?></td>
            <?php endif; ?>
            
        </tr>




    <?php endif; ?>
</table>
<table class="widefat s_tbl reportstbl" style="width: 90%; margin: 20px auto;">
    <?php if($rpt3): ?>
        <tr class="form-field">
            <th></th>
            <th>Period 1 <br/><span>Ref Date: <?php if(isset($startDate1)) echo date('m-d-Y',strtotime($startDate1)) ?></span></th>
            <?php if($has_period2): ?>
            <th>Period 2 <br/><span>Ref Date: <?php if(isset($startDate2)) echo date('m-d-Y',strtotime($startDate2)) ?></span></th>
            <?php endif; ?>
        </tr>
        <tr>
            <td>Number of clients who’ve ordered at least once in the last 30 days</td>
            <td class="nums"><?=number_format($rpt3_last_30_days_clients_results->num,0)?></td>
            <?php if($has_period2): ?>
            <td class="nums"><?=number_format($rpt3_last_30_days_clients_results2->num,0)?></td>
            <?php endif; ?>
        </tr>
        <tr>
            <td>Total sales to clients who’ve ordered at least once in the last 30 days</td>
            <td class="nums"><?=number_format($rpt3_sql_no_of_clients_sales_results->total_sales,2)?></td>
            <?php if($has_period2): ?>
            <td class="nums"><?=number_format($rpt3_sql_no_of_clients_sales_results2->total_sales,2)?></td>
            <?php endif; ?>
        </tr>
        <tr>
            <td>Average sale to clients who’ve ordered at least once in the last 30 days</td>
            <td class="nums"><?=number_format($rpt3_sql_no_of_clients_sales_results->average_sale,2)?></td>
            <?php if($has_period2): ?>
            <td class="nums"><?=number_format($rpt3_sql_no_of_clients_sales_results2->average_sale,2)?></td>
            <?php endif; ?>
        </tr>


        <tr>
            <td>Number of clients who’ve ordered at least once in the last 30 days and a second time in the last 60 days</td>
            <td class="nums"><?=number_format($rpt3_30_days_clients_60_results->num,0)?></td>
            <?php if($has_period2): ?>
            <td class="nums"><?=number_format($rpt3_30_days_clients_60_results->num,0)?></td>
            <?php endif; ?>
        </tr>
        <tr>
            <td>Total sales to clients who’ve ordered at least once in the last 30 days and a second time in the last 60 days</td>
            <td class="nums"><?=number_format($rpt3_30_days_clients_60_results->total_sales,2)?></td>
            <?php if($has_period2): ?>
            <td class="nums"><?=number_format($rpt3_30_days_clients_60_results2->total_sales,2)?></td>
            <?php endif; ?>
        </tr>
        <tr>
            <td>Average sale to clients who’ve ordered at least once in the last 30 days and a second time in the last 60 days</td>
            <td class="nums"><?=number_format($rpt3_30_days_clients_60_results->average_sale,2)?></td>
            <?php if($has_period2): ?>
            <td class="nums"><?=number_format($rpt3_30_days_clients_60_results2->average_sale,2)?></td>
            <?php endif; ?>
        </tr>


        <tr>
            <td>Number of clients who’ve ordered at least once in the last 30 days and a second time in the last 90 days</td>
            <td class="nums"><?=number_format($rpt3_30_days_clients_90_results->num,0)?></td>
            <?php if($has_period2): ?>
            <td class="nums"><?=number_format($rpt3_30_days_clients_90_results2->num,0)?></td>
            <?php endif; ?>
        </tr>
        <tr>
            <td>Total sales to clients who’ve ordered at least once in the last 30 days and a second time in the last 90 days</td>
            <td class="nums"><?=number_format($rpt3_30_days_clients_90_results->total_sales,2)?></td>
            <?php if($has_period2): ?>
            <td class="nums"><?=number_format($rpt3_30_days_clients_90_results2->total_sales,2)?></td>
            <?php endif; ?>
        </tr>
        <tr>
            <td>Average sale to clients who’ve ordered at least once in the last 30 days and a second time in the last 90 days</td>
            <td class="nums"><?=number_format($rpt3_30_days_clients_90_results->average_sale,2)?></td>
            <?php if($has_period2): ?>
            <td class="nums"><?=number_format($rpt3_30_days_clients_90_results2->average_sale,2)?></td>
            <?php endif; ?>
        </tr>


        <tr>
            <td>Number of clients who’ve ordered at least once in the last 30 days and a second time in the last 120 days</td>
            <td class="nums"><?=number_format($rpt3_30_days_clients_120_results->num,0)?></td>
            <?php if($has_period2): ?>
            <td class="nums"><?=number_format($rpt3_30_days_clients_120_results2->num,0)?></td>
            <?php endif; ?>
        </tr>
        <tr>
            <td>Total sales to clients who’ve ordered at least once in the last 30 days and a second time in the last 120 days</td>
            <td class="nums"><?=number_format($rpt3_30_days_clients_120_results->total_sales,2)?></td>
            <?php if($has_period2): ?>
            <td class="nums"><?=number_format($rpt3_30_days_clients_120_results2->total_sales,2)?></td>
            <?php endif; ?>
        </tr>
        <tr>
            <td>Average sale to clients who’ve ordered at least once in the last 30 days and a second time in the last 120 days</td>
            <td class="nums"><?=number_format($rpt3_30_days_clients_120_results->average_sale,2)?></td>
            <?php if($has_period2): ?>
            <td class="nums"><?=number_format($rpt3_30_days_clients_120_results2->average_sale,2)?></td>
            <?php endif; ?>
        </tr>



    
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