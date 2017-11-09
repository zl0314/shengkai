<?php
/**
 * Created by PhpStorm.
 * User: stepreal
 * Date: 17/9/4
 * Time: 下午3:21
 */

define('IN_ECS', true);
define('ECS_ADMIN', true);
require(dirname(__FILE__) . '/includes/init.php');

$act = empty($_REQUEST['act']) ? "index" : trim($_REQUEST['act']);
if ($act == 'index') {
    $smarty->display("football_list.html");
}