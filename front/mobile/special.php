<?php
/**
 * Created by PhpStorm.
 * User: shuwang
 * Date: 2016/6/23 0023
 * Time: 16:09
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
if($act == "special"){
    __load("GameService");
    $game_obj = new GameService();
    if($_GET['game_id']){
        $game_info=$game_obj->get_game($_GET['game_id']);
     
        $smarty->assign('game_info', $game_info);
        $number=get_cart_num(SESS_ID);
        $num = 0;
        foreach ($number as $value){
            $num += $value['goods_number'];
        }
        $smarty->assign('num',$num);
        $smarty->assign('yuanquan',1);
        $namel="<img src='images/logo.png'>";
        $smarty->assign( 'name1',$namel);
        $smarty->display("special.html");
    }
    exit;
}
//判断购物车是否有商品
function get_cart_num($sess){
    $sql="SELECT c.* FROM sk_cart AS c WHERE c.session_id = '$sess' AND (c.goods_type = 'ticket' OR c.goods_type='goods' OR c.goods_type='plane' OR c.goods_type = 'combo')";
    return $GLOBALS['db']->getAll($sql);
}