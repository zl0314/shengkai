<?php

/**
 * ECSHOP mobile首页
 * ============================================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liuhui $
 * $Id: index.php 15013 2010-03-25 09:31:42Z liuhui $
*/

define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
include_once('includes/cls_json.php');
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
if ($act == "index") {
    $combo_id = !empty($_REQUEST['combo_id']) ? intval($_REQUEST['combo_id']) : 0;
    if(!empty($combo_id)){
        /* 套餐-新版 */
        __load("ComboService");
        $combo_obj = new ComboService();
        $combo_info = $combo_obj->get_Combo_index($combo_id);
        $number=get_cart_num(SESS_ID);
        $num = 0;
        foreach ($number as $value){
            $num += $value['goods_number'];
        }
        $smarty->assign('num',$num);
        $smarty->assign('yuanquan',1);
        if(empty($combo_info)){
            return false;
        }else{
            $GLOBALS['smarty']->assign('combo_id', $combo_id);
            $GLOBALS['smarty']->assign('combo_info', $combo_info);
            $namel="<img src='images/logo.png'>";
            $smarty->assign( 'name1',$namel);
            $GLOBALS['smarty']->display('secial.html');
        }
    }else{
        return false;
    }
}elseif($act == "reserve"){
    $combo_id = $_REQUEST['combo_id'];
    __load("ComboService");
    $combo_obj = new ComboService();
    //获取套餐信息
    $combo_list = $combo_obj->get_combo_info($combo_id);
    //获取套餐球票信息
    $combo = json_decode($combo_list['combo_tickets'],true);
    //遍历球票信息
    foreach($combo['default'] as $key=>$value) {
        $id = explode('|', $value);
        $goods_id = $id[1];
        $sql = "SELECT * FROM " . $GLOBALS['ecs']->table('goods') . "WHERE goods_id = '" . $goods_id . "'";
        $res[$key] = $GLOBALS['db']->getRow($sql);
    }
    foreach ($res as $k=>$val){
        if ($val['goods_number'] == '0') {
            $json = new JSON();
            $data = $json->encode(false);
            print_r($data);
            exit;
        }
    }
    __load("CartService");
    $cart_obj = new CartService();
    $cart_list = array(
        "combo_id" => $combo_id,
        "goods_name" => $combo_list['combo_name'],
        "goods_price" => $combo_list['combo_price'],
        "goods_type" => "combo",
        "goods_number" => 1,
    );
    $result = $cart_obj->add_combo_to_cart($cart_list);
    $json = new JSON();
    $data = $json->encode(true);
    print_r($data);
    exit;

    $namel="<img src='images/logo.png'>";
    $smarty->assign( 'name1',$namel);
}elseif($act == "chakan"){
    $p = $_REQUEST['page'];
    $num = $_REQUEST['num'];
    $page = $p*$num;
    __load("ComboService");
    $combo_obj = new ComboService();
    $combo_list = $combo_obj->get_combo_show($page,$num);
    $json = new JSON();
    $data = $json->encode($combo_list);
    print_r($data);
    exit;
}
function goods_attr_id($goods_id){
    $sql="SELECT goods_attr_id FROM ". $GLOBALS['ecs']->table('goods_attr'). "WHERE goods_id =$goods_id";
    return $GLOBALS['db']->getOne($sql);
}
//判断购物车是否有商品
function get_cart_num($sess){
    $sql="SELECT c.* FROM sk_cart AS c WHERE c.session_id = '$sess' AND (c.goods_type = 'ticket' OR c.goods_type='goods' OR c.goods_type='plane' OR c.goods_type = 'combo')";
    return $GLOBALS['db']->getAll($sql);
}
?>