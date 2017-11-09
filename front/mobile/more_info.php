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
/* 获取赛事列表 start */
__load("GameService");

$game_obj = new GameService();
$game_list = $game_obj->get_list();
if(!empty($game_id)){
    $game = $game_obj->get_game($game_id);

    $smarty->assign('game', $game);

    //根据赛事自动查询城市
    __load("RegionService");
    $RegionService = new RegionService();
    $schedule_id = $RegionService->get_schedule($game_id);

    $smarty->assign('schedule_id', $schedule_id);
}
/* 获取赛事列表 end */
$combo= $GLOBALS['db']->getALL('select * from sk_combo');
$sportcat_list = $GLOBALS['db']->getAll('SELECT id,name FROM sk_sportcat where is_display=1');
$smarty->assign('sportcat_list', $sportcat_list);
if ($act == "index"){  
     __load("GameService");
    $game_obj = new GameService();
    $game_info=$game_obj->get_game($_GET['game_id']);

    $smarty->assign('game_info', $game_info);
    $smarty->display("more_info.dwt");
    exit;
}elseif($act == "tpl"){
    __load("GameService");
    $game_obj = new GameService();
    if($_GET['game_id']&&$_GET['city_id']){
    $city_id=$game_obj->get_game($_GET['game_id'],$_GET['city_id']);
    $smarty->assign('city_id', $city_id);
    }else if($_GET['game_id']){
        $game_info=$game_obj->get_game($_GET['game_id']);
    $smarty->assign('game_info', $game_info);
    }
    if(empty($game_info['template'])){
        ecs_header("Location:game_search.php?game_id=".$_GET['game_id']);
        exit;
    }else{
        $smarty->display("gametpl/" . $game_info['template']);
    }

    exit;
}

?>