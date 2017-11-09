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
//获取openid 判断是否存在
$openid = !empty($_SESSION['wx_openid']) ? $_SESSION['wx_openid'] : '';
//$remember = !empty($_POST['remember']) ? $_POST['remember'] : '';
if($openid){
    //查询数据库是否有记录
    $user_info = get_users_openid($openid);
    if($user_info){
        $GLOBALS['user']->set_session($user_info['user_name']);
        $GLOBALS['user']->set_cookie($user_info['user_name'], null);
        //更新session
        $_SESSION['last_time']   = $user_info['last_login'];
        $_SESSION['last_ip']     = $user_info['last_ip'];
        $_SESSION['login_fail']  = 0;
        $_SESSION['email']       = $user_info['email'];

        //* 更新登录时间，登录次数及登录ip */
        $sql = "UPDATE " .$GLOBALS['ecs']->table('users'). " SET".
            " visit_count = visit_count + 1, ".
            " last_ip = '" .real_ip(). "',".
            " last_login = '" .gmtime(). "'".
            " WHERE wx_open_id = '" . $_SESSION['wx_openid'] . "'";
        $GLOBALS['db']->query($sql);
    }
}
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
$combo= $GLOBALS['db']->getALL('select * from sk_combo where is_show = 1');
$sportcat_list = $GLOBALS['db']->getAll('SELECT id,name FROM sk_sportcat where is_display=1');
//根据运动类别查询赛事
//foreach($sportcat_list as $k=>$v){
//    $sportcat_list[$k]['scat'] = get_game($v['id']);
//}
//echo "<pre>";
//print_r($sportcat_list);die;
$smarty->assign('sportcat_list', $sportcat_list);
/* banner图 start */
__load("BannerService");
$banner_obj = new BannerService();
$top_banner = $banner_obj->get_banner_list("index_top");
$smarty->assign('top_banner', $top_banner);
$number=get_cart_num(SESS_ID);
$num = 0;
foreach ($number as $value){
    $num += $value['goods_number'];
}
$smarty->assign('num',$num);
$smarty->assign('yuanquan',1);
$smarty->assign("combo",$combo);
//$smarty->assign('sportcat_list', $sportcat_list);
$namel="<img src='images/logo.png'>";
$smarty->assign( 'name1',$namel);
$smarty->display("index.html");
//判断购物车是否有商品
function get_cart_num($sess){
    $sql="SELECT c.* FROM sk_cart AS c WHERE c.session_id = '$sess' AND (c.goods_type = 'ticket' OR c.goods_type='goods' OR c.goods_type='plane' OR c.goods_type = 'combo')";
    return $GLOBALS['db']->getAll($sql);
}
////查询赛事
//function get_game($scat_id){
//    $sql = "SELECT id,game_name FROM " .$GLOBALS['ecs']->table('game')." WHERE scat_id = $scat_id";
//    return $GLOBALS['db']->getAll($sql);
//}
?>
