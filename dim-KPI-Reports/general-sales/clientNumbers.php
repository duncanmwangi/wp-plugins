<?php
	global $wpdb;




 if(isset($msg)) echo $msg; ?>
<form method="post" action="">

<table class="widefat s_tbl reportstbl" style="width: 60%; margin: 20px auto;">

    <tr class="form-field">
        <td>Number of clients ordered at least once in the last 30 days</td>
        <td class="nums">222</td>
    </tr>
    <tr>
        <td>Number of clients ordered a second time in the last 60 days</td>
        <td class="nums">46</td>
    </tr>
    <tr>
        <td>Number of clients ordered a second time in the last 90 days</td>
        <td class="nums">46</td>
    </tr>
    <tr>
        <td>Number of clients ordered a second time in the last 120 days</td>
        <td class="nums">46</td>
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