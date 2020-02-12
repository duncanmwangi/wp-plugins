<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' ); ?><?php
	global $wpdb;

    if(!isset($_POST['fdate1']) || empty($_POST['fdate1']) ){
        $fdate1 = date('01-m-Y');
    }
    if(!isset($_POST['sdate1']) || empty($_POST['sdate1']) ){
        $sdate1 = date('d-m-Y');
    }
    if(isset($_POST['search'])){
        $fdate1 = $_POST['fdate1'];
        $sdate1 = $_POST['sdate1'];
        if(empty($fdate1)){
            $msg = kpi_display_msg('Error: Date field must be filled.',2);
        }
    }
    $date_arr = explode('-',$fdate1);
    $from_search_date1 = $date_arr[2].'-'.$date_arr[1].'-'.$date_arr[0];
    $date_arrs = explode('-',$sdate1);
    $to_search_date1 = $date_arrs[2].'-'.$date_arrs[1].'-'.$date_arrs[0];

    if(!isset($_POST['fdate2']) || empty($_POST['fdate2']) ){
        $fdate2 = date('01-m-Y');
    }
    if(!isset($_POST['sdate2']) || empty($_POST['sdate2']) ){
        $sdate2 = date('d-m-Y');
    }
    if(isset($_POST['search'])){
        $fdate2 = $_POST['fdate2'];
        $sdate2 = $_POST['sdate2'];
        if(empty($fdate2)){
            $msg = kpi_display_msg('Error: Date field must be filled.',2);
        }
    }
    $date_arr = explode('-',$fdate2);
    $from_search_date2 = $date_arr[2].'-'.$date_arr[1].'-'.$date_arr[0];
    $date_arrs = explode('-',$sdate2);
    $to_search_date2 = $date_arrs[2].'-'.$date_arrs[1].'-'.$date_arrs[0];

    if(isset($_POST['search'])):

	    $no_of_repeat_customers_sql = "SELECT count(*) as total FROM ( SELECT count(b.meta_value) as number FROM wp_posts as a LEFT JOIN wp_postmeta as b on a.ID=b.post_id WHERE post_type='shop_order' AND post_status IN('wc-processing','wc-completed') AND b.meta_key='_billing_email' AND  date(a.post_date)  >= '$from_search_date' AND  date(a.post_date)  <= '$to_search_date' AND b.meta_value!='' GROUP BY b.meta_value) as a WHERE number>1";
	    $no_of_repeat_customers = $wpdb->get_row($no_of_repeat_customers_sql)->total;

	endif;

 if(isset($msg)) echo $msg; ?>
<form method="post" action="">
        <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<table class="widefat s_tbl" style="width: 70%; margin: 0 auto;">
    <tr>
    	<td colspan="4">
    		<h3 style="margin: 5px;">Period 1</h3>
    	</td>
    </tr>
    <tr class="form-field">
        <td>From Date</td>
        <td><input type="text" name="fdate1" class="datepicker" value="<?php if(isset($fdate1)) echo $fdate1 ?>" /></td>
        <td>To Date</td>
        <td><input type="text" name="sdate1" class="datepicker" value="<?php if(isset($sdate1)) echo $sdate1 ?>" /></td>
    </tr>
    <tr>
    	<td colspan="4">
    		<h3 style="margin: 5px;">Period 2</h3>
    	</td>
    </tr>
    <tr class="form-field">
        <td>From Date</td>
        <td><input type="text" name="fdate2" class="datepicker" value="<?php if(isset($fdate2)) echo $fdate2 ?>" /></td>
        <td>To Date</td>
        <td><input type="text" name="sdate2" class="datepicker" value="<?php if(isset($sdate2)) echo $sdate2 ?>" /></td>
    </tr>
    <tr>
        <td colspan="4"><input name="search" id="submit" class="button button-primary" value="Search" type="submit"></td>
    </tr>
</table>

<?php if(isset($_POST['search'])): ?>
<table class="widefat s_tbl reportstbl" style="width: 80%; margin: 20px auto;">

    <tr class="form-field">
        <th></th>
        <th>Period 1 <br/><span>From: <?php if(isset($sdate1)) echo $sdate1 ?> To: <?php if(isset($sdate1)) echo $sdate1 ?></span></th>
        <th>Period 2 <br/><span>From: <?php if(isset($sdate2)) echo $sdate2 ?> To: <?php if(isset($sdate2)) echo $sdate2 ?></span></th>
    </tr>
    <tr>
        <td>Number of clients</td>
        <td class="nums">Period 1</td>
        <td class="nums">Period 3</td>
    </tr>
   
    
</table>

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

</style>
<?php endif; ?>