<?php
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
$namel="<img src='images/logo.png'>";
$smarty->assign( 'name1',$namel);
$smarty->display("wx_login.html");