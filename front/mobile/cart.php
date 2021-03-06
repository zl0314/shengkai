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
define('ECS_ADMIN', true);
require(dirname(__FILE__) . '/includes/init.php');
require(ROOT_PATH . 'includes/lib_order.php');
require(ROOT_PATH . 'includes/lib_payment.php');
include_once('includes/cls_json.php');
include_once('includes/lib_main.php');
$act = empty($_REQUEST['act']) ? "index" : trim($_REQUEST['act']);
/* 获取赛事列表 start */
__load("GameService");
$game_obj = new GameService();
$game_list = $game_obj->get_list();
if (!empty($game_id)) {
    $game = $game_obj->get_game($game_id);

    $smarty->assign('game', $game);

    //根据赛事自动查询城市
    __load("RegionService");
    $RegionService = new RegionService();
    $schedule_id = $RegionService->get_schedule($game_id);

    $smarty->assign('schedule_id', $schedule_id);
}
/* 获取赛事列表 end */
$combo = $GLOBALS['db']->getALL('select * from sk_combo');
$sportcat_list = $GLOBALS['db']->getAll('SELECT id,name FROM sk_sportcat where is_display=1');
$smarty->assign('sportcat_list', $sportcat_list);
if ($act == "index") {
    $user_id = $_SESSION['user_id'];
    __load("AirticketService");
    $air_obj = new AirticketService();
    $air_list = $air_obj->get_airticket_region();
    $smarty->assign('from_city', $air_list['from_ctiy']);
    $smarty->assign('to_city', $air_list['to_ctiy']);
    //获取购物车信息
    __load("CartService");
    $cart_obj = new CartService();
    $cart_list = $cart_obj->get_cart_info();
    $smarty->assign("cart_list", $cart_obj->get_cart_info());
    $smarty->assign("cart_combo", $cart_obj->get_cart_combo());
    $smarty->assign("cart_money", $cart_obj->get_cart_money());
    /* 周边商品 end */
    $cart_info = get_cart_info(SESS_ID);
    $cart_num = count($cart_info);
    $smarty->assign('cart_num', $cart_num);
    //判断购物车是否有商品
    $get_cart_num = get_cart_num(SESS_ID);
    $number = get_cart_num(SESS_ID);
    $num = 0;
    foreach ($number as $value) {
        $num += $value['goods_number'];
    }
    $smarty->assign('num', $num);
    $smarty->assign('yuanquan', 1);
    $game_id = empty($_GET['game_id']) ? '0' : $_GET['game_id'];
    $smarty->assign('game_id', $game_id);
    if (count($get_cart_num) > 0) {
        __load("GameService");
        $game_obj = new GameService();
        $game_info = $game_obj->get_game(intval($game_id));
        $game_info = $game_obj->get_game(intval($game_id));
        $smarty->assign('game_info', $game_info);
    } else {
        $namel = "<img src='images/logo.png'>";
        $smarty->assign('name1', $namel);
        mobile_show_null_cart("购物车空空的哦~，去看看心仪的商品吧~", '', '', 'cart');
    }
    //获取当前时间
    $date = date('Y-m-d', time());
    $next_date = date("Y-m-d", strtotime("+1 day"));
    $smarty->assign('date', $date);
    $smarty->assign('next_date', $next_date);
    $smarty->assign('user_id', $user_id);
    /* 酒店 end */
    $namel = "<img src='images/logo.png'>";
    $smarty->assign('name1', $namel);
    $smarty->display("cart.html");
} elseif ($act == "remove_cart") {
    $type = empty($_GET['type']) ? "all" : trim($_GET['type']);
    $cart_id = empty($_GET['cart_id']) ? "0" : intval($_GET['cart_id']);
    __load("CartService");
    $cart_obj = new CartService();
    $cart_obj->remove_cart($type, $cart_id);
    ecs_header("Location: cart.php"); /* 添加周边商品 */
}

//判断购物车是否有商品
function get_cart_num($sess)
{
    $sql = "SELECT c.* FROM sk_cart AS c WHERE c.session_id = '$sess' AND (c.goods_type = 'ticket' OR c.goods_type='goods' OR c.goods_type='plane' OR c.goods_type = 'combo')";
    return $GLOBALS['db']->getAll($sql);
}

function get_cart_info($session_id)
{
    $sql = "SELECT c.*,n.*,g.*,p.pitch_name,r.region_name,r.region_id,cm.color_value  FROM " . $GLOBALS['ecs']->table('cart') . "AS c," . $GLOBALS['ecs']->table('goods') . "AS g," . $GLOBALS['ecs']->table('pitch') . "AS p," . $GLOBALS['ecs']->table('region') . "AS r," . $GLOBALS['ecs']->table('number')
        . "AS n," . $GLOBALS['ecs']->table('color_manage') . "AS cm " . "WHERE c.session_id= '" . $session_id . "' AND c.goods_id=g.goods_id AND g.number_id=n.id AND n.pitch_id=p.id AND p.region_id=r.region_id  AND c.goods_type='ticket'  AND n.color_id=cm.color_id";
    return $GLOBALS['db']->getAll($sql);
}

/**
 *
 * @param $number 单场次最少可以买几张票
 */
function check_ticket_number($number)
{
    $sql = "SELECT * FROM " . $GLOBALS['ecs']->table('cart') .
        " WHERE session_id = '" . SESS_ID . "' AND goods_type='ticket' AND goods_number>$number ";
    return $GLOBALS['db']->getAll($sql);
}
