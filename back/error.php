<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 2015/9/10
 * Time: 15:28
 */

define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');

$error_code=empty($_GET['code'])?500:intval($_GET['code']);

if(404==$error_code){
    echo 404;
//    $smarty->display("404.dwt");
}else{
    echo 500;
}


