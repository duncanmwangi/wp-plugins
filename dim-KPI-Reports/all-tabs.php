<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' ); ?>
<div class="wrap">

<div id="icon-options-general" class="icon32"></div>
<h2>KPI Reports</h2>
<?php
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'general';
if(isset($_GET['tab'])) $active_tab = $_GET['tab'];
?>

<h2 class="nav-tab-wrapper" style="text-align: center; margin-bottom: 20px;">

<a href="?page=kpi-reports&amp;tab=generals" class="nav-tab <?php echo $active_tab == 'generals' ? 'nav-tab-active' : ''; ?>">General Sales</a>

<a href="?page=kpi-reports&amp;tab=trialPack" class="nav-tab <?php echo $active_tab == 'trialPack' ? 'nav-tab-active' : ''; ?>">Trial Pack</a>

<a href="?page=kpi-reports&amp;tab=coupons" class="nav-tab <?php echo $active_tab == 'coupons' ? 'nav-tab-active' : ''; ?>">Coupons</a>
</a>

</h2>


<?php switch($active_tab) { 
        case 'general': echo '<h3 style="margin-top: 30px;">Choose report from the above tabs.</h3>'; break;
        case 'generals': require_once('general-sales.php'); break;
        case 'trialPack': require_once('trial-pack.php'); break;
        case 'coupons': require_once('coupons.php'); break;
        default: echo '<h3 style="margin-top: 30px;">Choose report from the above tabs.</h3>'; break;
         }
 ?>
</div>