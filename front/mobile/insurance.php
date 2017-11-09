<?php
/**
 * Created by PhpStorm.
 * User: hechengbin
 * Date: 2016/7/14 0014
 * Time: 16:41
 */
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
require(ROOT_PATH . 'includes/lib_order.php');
$act = empty($_REQUEST['act']) ? "index" : trim($_REQUEST['act']);
if($act == 'index'){
    $order_id = $_REQUEST['order_id'];
    //取得订单下的门票信息、门票颜色
    $order_menpiao = order_menpiao($order_id);
    $order_combo_menpiao['key'] = order_combo_menpiao($order_id);
    __load('ComboService');
    $combo_obj = new ComboService;
    foreach($order_combo_menpiao['key'] as $key=>$value){
        $order_combo_menpiao['combo_info'] = $combo_obj->get_combo_info($value['combo_id']);
    }
    foreach ($order_menpiao AS $key => $value) {
        $order_menpiao[$key]['keywords'] = explode(' ', trim($order_menpiao[$key]['keywords']));
    }
    __load("GameService");
    $game_obj = new GameService();
    $game_id_list = Array();

    foreach ($order_menpiao as $key => $value) {
        if (!in_array($order_menpiao[$key]['gameid'], $game_id_list)) {
            Array_push($game_id_list, $order_menpiao[$key]['gameid']);
        }
    }
    $game_info = Array();
    foreach ($game_id_list AS $key => $value) {
        $game_info[$key] = $game_obj->get_game_name($value);
    }

    //取得订单下的商品总价格
    $order_menpiao_sum = order_menpiao_sum($order_id);

    //此处是为保险取得所有门票信息，并且将门票分开显示
    __load("BearerService");
    $bearer = new BearerService();
    $rec_menpiao_list = $bearer->get_order_menpiao_list($order_id);
    $rec_menpiao = Array();
    $rec_menpiao = $rec_menpiao_list;
    //分配变量
    $smarty->assign('game_info', $game_info);
    $smarty->assign('order_id', $order_id);
    $smarty->assign("rec_menpiao_list", $rec_menpiao);
    $smarty->assign('order_combo_menpiao', $order_combo_menpiao);
    $game_list=$GLOBALS['db']->getAll('SELECT id,game_name FROM sk_game');
    $smarty->assign('game_list', $game_list);
    $namel="<img src='images/logo.png'>";
    $smarty->assign( 'name1',$namel);
    $smarty->display('insurance.html');
}