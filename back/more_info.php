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

if ($act == "index"){  
     __load("GameService");
    $game_obj = new GameService();
    $game_info=$game_obj->get_game($_GET['game_id']);
    $number=get_cart_num(SESS_ID);
    $num = 0;
    foreach ($number as $value){
        $num += $value['goods_number'];
    }
    /* 获取运动类别列表 start */
    __load("SportcatService");
    $sportcat_obj = new SportcatService();
    $sportcat_list = $sportcat_obj->get_sportcat_list();
    /* 获取运动类别列表 end */
    $smarty->assign('num',$num);
    $smarty->assign('yuanquan',1);
    $smarty->assign('game_info', $game_info);
    $smarty->display("more_info.dwt");
    exit;
}
//判断购物车是否有商品
function get_cart_num($sess){
    $sql="SELECT c.* FROM sk_cart AS c WHERE c.session_id = '$sess' AND (c.goods_type = 'ticket' OR c.goods_type='goods' OR c.goods_type='plane' OR c.goods_type = 'combo')";
    return $GLOBALS['db']->getAll($sql);
}
?>