<?php

/**
 * ECSHOP 搜索程序
 * ============================================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo 
 * $Id: search.php 17217 2011-01-19 06:29:08Z liubo $
 */
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
include_once('includes/cls_json.php');
//获取赛事信息
$act = empty($_REQUEST['act']) ? "index" : trim($_REQUEST['act']);

if ($act == "index") {  
    __load("AdvertService");
    $advert_obj = new AdvertService();
    if($_GET['id']){
    $advert_template=$advert_obj->get_advert_template($_GET['id']);
    if(empty($advert_template)){
       ecs_header("Location: index.php\n");
    }
    }else{
        ecs_header("Location: index.php\n");
    }
    __load("Set_mealService");
    $set_meal_obj = new Set_mealService();
	$ua = strtolower($_SERVER['HTTP_USER_AGENT']);
	
	$uachar = "/(nokia|sony|ericsson|mot|samsung|sgh|lg|philips|panasonic|alcatel|lenovo|cldc|midp|coolpad|k-touch|tcl|oppo|doov|amoi|bbk|cect|amoi|zte|huawei|iphone|ipad|android|smartphone)/i";
	if(($ua == '' || preg_match($uachar, $ua))&& !strpos(strtolower($_SERVER['REQUEST_URI']),'wap'))
	{
		$smarty->assign('platform','mobile');
		
    }else{
		$smarty->assign('platform','pc');
	}
	
	
    $smarty->assign('set_meal_id', $set_meal_obj->get_set_meal_id($_GET['id']));
    $smarty->display("gametpl/" . $advert_template.".dwt");
    exit;
}else if ($act == "ajax") {  
    __load("Set_meal_orderService");
    $set_meal_order_obj = new Set_meal_orderService();
    $res=$set_meal_order_obj->add_set_meal_order($_POST);
    $json = new JSON();
    $data = $json->encode($_POST);
    print_r($data);
    exit;
}

?>