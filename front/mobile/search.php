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
//获取赛事信息


$act = empty($_REQUEST['act']) ? "index" : trim($_REQUEST['act']);


if ($act == "index") {

    //检查当前用户购物车中是否有数据
    $game_id = empty($_REQUEST['game_id']) ? 0 : intval($_REQUEST['game_id']);
    $cart_info = get_cart_info(SESS_ID, $game_id);
    $cart_num = count($cart_info);
    $_SESSION['cart_num'] = $cart_num;

    //获取城市信息
    $region_id = empty($_REQUEST['region_id']) ? 0 : intval($_REQUEST['region_id']);
    __load("GameService");
    __load("RegionService");
    $region_obj = new RegionService();
    $this_region = $region_obj->get_region($region_id);
    if (empty($region_id)) {
        $smarty->assign('region_name', "所有城市");
    } else {
        $smarty->assign('region_name', $this_region['region_name']);
    }
    $game = new GameService();
    $left_info = $game->get_game_info($game_id, $region_id);
    $arr = Array();
    $arr = $left_info;
    foreach ($arr AS $key => $value) {
        $arr[$key]['keywords'] = explode(' ', trim($value['keywords']));
        $arr[$key]['num_start'] = date('Y-m-d H:i', strtotime($arr[$key]['num_start']));
    }
    if ($_GET['sche_id']) {
        $left_info = $game->get_game_sche($game_id, $region_id, $_GET['sche_id']);
        $arr = $left_info;
        foreach ($arr AS $key => $value) {
            $arr[$key]['keywords'] = explode(' ', trim($value['keywords']));
        }
    }
    /* 获取地区列表  */
    $region_list = $game->get_region_list($game_id, $_GET['sche_id']);
    $smarty->assign('region_list', $region_list);
    $smarty->assign("game_id", $game_id);
    $smarty->assign("left_info", $arr);
    $game_info = $game->get_game_name($game_id);
    $smarty->assign("game_info", $game_info);
    if (empty($game_info['template'])) {
        $smarty->assign("game_more", 0);
    } else {
        $smarty->assign("game_more", 1);
    }


    /* 获取赛程列表 start */
    __load("ScheduleService");
    $sche_obj = new ScheduleService();
    $sche_list = $sche_obj->sche_list_info($game_id);
    if (empty($_GET['sche_id'])) {
        $smarty->assign('sche_name', "所有赛段");
    } else {
        $sche_name = $sche_obj->get_ScheName($_GET['sche_id']);
        $smarty->assign('sche_name', $sche_name);
    }

    $smarty->assign('sche_list', $sche_list);
    $schedule_html = '';
    /* 获取赛程列表 end */
    $namel="<img src='images/logo.png'>";
    $smarty->assign( 'name1',$namel);
    $smarty->display("lister.html");
    exit;
} else if ($act == "search") {
    if ($_POST['search'] != null) {

        $game = search_game($_POST['search']);
        $teams = search_teams($_POST['search']);
        $city = search_city($_POST['search']);
        $smarty->assign('like', $_POST['search']);
        if ($game) {
            $smarty->assign('game_info', $game);
            $smarty->assign('game_count', game_count($_POST['search']));
        } else if ($teams) {
            $smarty->assign('game_info', $teams);
            $smarty->assign('game_count', teams_count($_POST['search']));
        } else if ($city) {
            $smarty->assign('game_info', $city);
            $smarty->assign('game_count', city_count($_POST['search']));
        } else {
            $smarty->assign('game_count', "0");
        }
    } else {
        $smarty->assign('game_count', "0");
    }
    $smarty->display("search_info.dwt");
}elseif ($act == "ajax_game") {
    $scat_id = $_GET['id'];
    //根据赛事自动查询城市
    __load("GameService");
    $game_obj = new GameService();
    $game_list = $game_obj->get_by_scat($scat_id);
    $json = new JSON();
    $data = $json->encode($game_list);
    print_r($data);
    exit;
}  elseif ($act == "ajax_game_city") {
    $game_id = $_GET['game_id'];
    //根据赛事自动查询城市
    __load("RegionService");
    $RegionService = new RegionService();
    $schedule_id = $RegionService->get_schedule($game_id);
    $json = new JSON();
    $data = $json->encode($schedule_id);
    print_r($data);
    exit;


} elseif ($act == "remove_cart_goods") {
    $goods_id = empty($_GET['goods_id']) ? 0 : $_GET['goods_id'];
    $game_id = empty($_GET['game_id']) ? 0 : $_GET['game_id'];
    remove_hotel_info($goods_id);
    $GLOBALS['db']->query("delete from " . $GLOBALS['ecs']->table('cart') . " where session_id='" . SESS_ID . "' and goods_id='" . $goods_id . "'");

    $url = 'game_search.php?game_id=' . $game_id;
    ecs_header("Location: $url\n");

    exit;
} elseif ($act == 'update_goods_number') {
    $goods_id = empty($_GET['goods_id']) ? 0 : $_GET['goods_id'];
    $game_id = empty($_GET['game_id']) ? 0 : $_GET['game_id'];
    $goods_number = empty($_GET['goods_number']) ? 0 : intval($_GET['goods_number']);
    if ($goods_number == 0) {
        remove_hotel_info($goods_id);
        $GLOBALS['db']->query("delete from " . $GLOBALS['ecs']->table('cart') . " where session_id='" . SESS_ID . "' and goods_id='" . $goods_id . "'");

    } elseif ($goods_number > 4) {

    } else {
        $GLOBALS['db']->query("update " . $GLOBALS['ecs']->table('cart') . " set goods_number='" . $goods_number . "' where session_id='" . SESS_ID . "' and goods_id='" . $goods_id . "'");
    }

    $url = 'game_search.php?game_id=' . $game_id;
    ecs_header("Location: $url\n");
    exit;
}


function get_relateds($type, $grand, $parent)
{
    $arr = array();
    if ($type == 1) {


        foreach ($sche_list as $key => $val) {
            $arr[$key]['related_id'] = $val['id'];
            $arr[$key]['related_name'] = $val['sche_name'];
        }
    }
    if ($type == 2) {
        /* 获取地区列表  */
        __load("RegionService");
        $region_obj = new RegionService();
        $region_list = $region_obj->region_list($parent);
        foreach ($res as $key => $val) {
            $arr[$key]['related_id'] = $val['id'];
            $arr[$key]['related_name'] = $val['num_name'] . "--" . $val['pitch_name'];
            __log($arr[$key]['related_id']);
        }
    }
    return $arr;
}



function get_cart_info($session_id, $game_id)
{
    $sql = "SELECT c.*,n.*,g.*,cm.color_value,c.goods_number as cart_goods_number FROM " . $GLOBALS['ecs']->table('cart') . "AS c," . $GLOBALS['ecs']->table('color_manage') . "AS cm," . $GLOBALS['ecs']->table('goods') . "AS g," . $GLOBALS['ecs']->table('number') . "AS n WHERE c.session_id= '$session_id' AND g.game_id={$game_id} AND c.goods_id=g.goods_id AND g.number_id=n.id AND n.color_id=cm.color_id ";
    return $GLOBALS['db']->getAll($sql);
}

function search_game($condition)
{
    return $GLOBALS['db']->getAll("SELECT * FROM " . $GLOBALS['ecs']->table('game') . "WHERE game_name LIKE\"%$condition%\"");
}

function game_count($condition)
{
    return $GLOBALS['db']->getOne("SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('game') . "WHERE game_name LIKE\"%$condition%\"");
}

function search_teams($condition)
{
    return $GLOBALS['db']->getAll("SELECT g.* FROM " . $GLOBALS['ecs']->table('game') . "AS g," . $GLOBALS['ecs']->table('schedule') . "AS s," . $GLOBALS['ecs']->table('number') . "AS n," . $GLOBALS['ecs']->table('num_team') . "AS nt," . $GLOBALS['ecs']->table('teams') . "AS t " . "WHERE g.id=s.game_id AND s.id=n.sche_id  AND n.id=nt.num_id AND nt.team_id=t.id AND t.team_name LIKE\"%$condition%\" GROUP BY g.id");
}

function teams_count($condition)
{
    $sql = "SELECT COUNT(*) FROM (SELECT g.* FROM " . $GLOBALS['ecs']->table('game') . "AS g," . $GLOBALS['ecs']->table('schedule') . "AS s," . $GLOBALS['ecs']->table('number') . "AS n," . $GLOBALS['ecs']->table('num_team') . "AS nt," . $GLOBALS['ecs']->table('teams') . "AS t " . "WHERE g.id=s.game_id AND s.id=n.sche_id  AND n.id=nt.num_id AND nt.team_id=t.id AND t.team_name LIKE\"%$condition%\" GROUP BY g.id) aa";
    return $GLOBALS['db']->getOne($sql);
}

function search_city($condition)
{
    $sql = "SELECT g.* FROM " . $GLOBALS['ecs']->table('game') . "AS g," . $GLOBALS['ecs']->table('schedule') . "AS s," . $GLOBALS['ecs']->table('number') . "AS n," . $GLOBALS['ecs']->table('pitch') . "AS p," . $GLOBALS['ecs']->table('region') . "AS r " . "WHERE g.id=s.game_id AND s.id=n.sche_id  AND n.pitch_id=p.id AND p.region_id=r.region_id AND r.region_name LIKE\"%$condition%\"  AND r.region_type=2 GROUP BY g.id";
    return $GLOBALS['db']->getAll($sql);
}

function city_count($condition)
{
    $sql = "SELECT COUNT(*) FROM (SELECT g.* FROM " . $GLOBALS['ecs']->table('game') . "AS g," . $GLOBALS['ecs']->table('schedule') . "AS s," . $GLOBALS['ecs']->table('number') . "AS n," . $GLOBALS['ecs']->table('pitch') . "AS p," . $GLOBALS['ecs']->table('region') . "AS r " . "WHERE g.id=s.game_id AND s.id=n.sche_id  AND n.pitch_id=p.id AND p.region_id=r.region_id AND r.region_name LIKE\"%$condition%\"  AND r.region_type=2 GROUP BY g.id) aa";
    return $GLOBALS['db']->getOne($sql);
}

function remove_hotel_info($goods_id)
{
    $sql = "select rec_id from " . $GLOBALS['ecs']->table('cart') . " where goods_id = '" . $goods_id . "' and session_id='" . SESS_ID . "'";

    $result = $GLOBALS['db']->getAll($sql);
    foreach ($result as $key => $value) {
        $sql = "delete from " . $GLOBALS['ecs']->table('cart') . " where parent_id = " . $value['rec_id'] . " and goods_type='hotel' ";

        $GLOBALS['db']->query($sql);
    }

}
//判断购物车是否有商品
function get_cart_num($sess){
    $sql="SELECT c.* FROM sk_cart AS c WHERE c.session_id = '$sess' AND (c.goods_type = 'ticket' OR c.goods_type='goods' OR c.goods_type='plane' OR c.goods_type = 'combo')";
    return $GLOBALS['db']->getAll($sql);
}
?>


