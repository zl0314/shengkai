<?php
/**
 * Created by PhpStorm.
 * User: shuwang
 * Date: 2016/7/29 0029
 * Time: 11:20
 */
define('IN_ECS', true);
define('ECS_ADMIN', true);
require(dirname(__FILE__) . '/includes/init.php');
$act = empty($_REQUEST['act']) ? "index" : trim($_REQUEST['act']);
/* 获取赛事列表 start */
__load("GameService");
$smarty->assign('game_list', $game_list);
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
if($act == 'index') {
    include_once(ROOT_PATH . 'includes/lib_order.php');
    $order_sn = empty($_GET['order_sn']) ? 0 : $_GET['order_sn'];
    if (empty($order_sn)) {
        echo "订单不存在";
    } else {
        __load("OrderService");
        $order_obj = new OrderService();
        $order_id = $order_obj->get_order_id($order_sn);
        $order_info = $order_obj->get_order_info($order_id);
        $order_lianxiren = order_lianxiren($order_id);
        $order_info['add_time'] = date("Y年m月d日", $order_info['add_time']);
        $smarty->assign('order_info', $order_info);
        if (empty($order_id)) {
            echo "订单不存在";
            exit;
        }
        $titckInfo = order_menpiao($order_id);
        foreach ($titckInfo as $key => $info) {
            $titckInfo[$key]['num_start'] = date('Y-m-d', strtotime($info['num_start']));
        }
        //取得订单下的持票人信息
        $order_bearer = get_order_bearer($order_id);
//        echo "<pre>";
//        print_r($order_bearer);die;
        $smarty->assign('lianxiren', $order_lianxiren);
        $smarty->assign('order_bearer', $order_bearer);
        $smarty->assign('info_list', $titckInfo);
        $namel = "<img src='images/logo.png'>";
        $smarty->assign('name1', $namel);
        $smarty->display("voucher.html");
    }
}
//取得订单下的持票人信息
function get_order_bearer($order_id){
    $sql = "SELECT bi.* FROM ".$GLOBALS['ecs']->table('order_ticket')."AS ot,".$GLOBALS['ecs']->table('bearer_info')."AS bi WHERE ot.order_id='".$order_id."' AND ot.bearer_id=bi.id";
    return $GLOBALS['db']->getAll($sql);
}
