<?php
/*

Plugin Name: DIM Rebate Programme
Plugin URI: http://gmarkwebsolutions.com/
Description:  Integrating Rebate Programme
Version: 1.0.0
Author: Duncan I. Mwangi
Author URI: http://gmarkwebsolutions.com/
Text Domain: wp-topbar

*/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
set_time_limit(0);
require_once('Rebate.php');
$rebate = new DRP_Rebate();
?>