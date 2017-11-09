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
    $user_id = $_SESSION['user_id'];
    __load("AirticketService");
    $air_obj = new AirticketService();
    $ambitus = $air_obj->get_ambitus_goods();
    $smarty->assign("ambitus", $ambitus);
    $namel="<img src='images/logo.png'>";
    $smarty->assign( 'name1',$namel);
    $smarty->display("souvenir.html");
}
function get_cart_info(){
    $sql = "SELECT c.*,n.*,g.*,p.pitch_name,r.region_name,r.region_id,cm.color_value  FROM " . $GLOBALS['ecs']->table('cart') . "AS c," . $GLOBALS['ecs']->table('goods') . "AS g," . $GLOBALS['ecs']->table('pitch') . "AS p," . $GLOBALS['ecs']->table('region') . "AS r," . $GLOBALS['ecs']->table('number')
        . "AS n," . $GLOBALS['ecs']->table('color_manage') . "AS cm " .  "WHERE c.session_id='".SESS_ID."' AND c.goods_id=g.goods_id AND g.number_id=n.id AND n.pitch_id=p.id AND p.region_id=r.region_id  AND c.goods_type='ticket'  AND n.color_id=cm.color_id" ;
    return $GLOBALS['db']->getAll($sql);
}
