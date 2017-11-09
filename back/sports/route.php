<?php

/*
 * 作者：戎青松
 * 时间：17:02:45
 * 路由控制
 */
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
//$constr = trim($_GET['con']);
$constr = empty($_GET['con']) ? "batch" : trim($_GET['con']);
if (empty($constr)) {
    $smarty->display("error/404.html");
    exit;
}
$con = ucfirst($constr) . "Controller";
__load($con, "admin_controller");
$con = new $con();
$act = empty($_GET['act']) ? "index" : trim($_GET['act']);
if (method_exists($con, $act)) {
    include_once('includes/inc_unpriv.php');
    $str=ucfirst($constr) . "/" . $act;
    if(empty($un_purview[$str])){
         admin_priv($str);
    }else{
        if(!$un_purview[$str]){
             admin_priv($str);
        }
    }
   
    $con->$act();
} else {
    $smarty->display("error/404.html");
    exit;
}

