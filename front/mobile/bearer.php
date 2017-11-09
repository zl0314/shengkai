<?php

/**
 * 订单检查和确认订单
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
require(ROOT_PATH . 'includes/lib_order.php');
require_once(ROOT_PATH . 'languages/' . $_CFG['lang'] . '/user.php');
require_once(ROOT_PATH . 'languages/' . $_CFG['lang'] . '/shopping_flow.php');
require_once(ROOT_PATH . 'includes/lib_sms.php');
include_once(dirname(__FILE__).'/includes/cls_json.php');
include_once(dirname(__FILE__).'/includes/lib_main.php');
__load("BearerService");
$bearer = new BearerService();

$act=!empty($_REQUEST['act'])?$_REQUEST['act']:'';
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

if ($act == "index") {
    $user_id = $_SESSION['user_id'];

    $order_sn = empty($_GET['order_sn']) ? null : $_GET['order_sn'];
    if (empty($order_sn)) {
        show_message("未检测到订单,请稍后再试！", '', '', 'warning');
    }
    $order_id = $bearer->check_order_user($order_sn, $user_id); //通过订单号查询订单id
    if (empty($order_id)) {
        show_message("订单不存在，请稍后再试！", '', '', 'warning');
    }

    $pay_status = $bearer->get_pay_status($order_id, $user_id);

    if ($pay_status == 2) {
        //向客户发送短信通知和邮件通知
        //获取当前用户的手机和邮箱
        $query_list = $bearer->query_list($user_id);
        $smarty->assign('shop_name', $_CFG['shop_name']);
        $content = $smarty->fetch('str:' . $_CFG['ecsdxt_customer_payed_value']);
        $content = $smarty->fetch('str:' . $_CFG['ecsdxt_order_picking_value']);
        $bearer_info = $bearer->bearer_info_list($order_id); //查询持票人购物信息
        $arr = Array();
        $arr = $bearer_info;
        foreach ($bearer_info AS $key => $value) {
            if ($arr[$key]['keywords']) {
                $arr[$key]['keywords'] = explode(' ', $arr[$key]['keywords']);
            }
            $arr[$key]['num_start'] = date('Y-m-d H:i', strtotime($arr[$key]['num_start']));
            if ($bearer_info[$key]['bearer_id'] != 0) {
                $this_bearer=$bearer->bearer_info($bearer_info[$key]['bearer_id']);
                $arr[$key]['passport_number'] = $this_bearer['passport_number'];
                $arr[$key]['mobile'] = $this_bearer['mobile'];

            } else {
                $arr[$key]['passport_number'] = '';
            }
        }
        $bearer_name = $bearer->bearer_name($user_id); //查询持票人名字

        $smarty->assign('bearer_info', $arr);
        $smarty->assign('order_id', $order_id);
        $smarty->assign('order_sn', $order_sn);
        $smarty->assign('bearer_name', $bearer_name);
    } else {
        show_message("非法入口", '', '', 'warning');
    }


    $smarty->display("bearer_list.html");
}
if ($_GET['act'] == "add") {
    if ($_GET['step'] == "center_add") {
        if($_REQUEST['order_sn']){
            $order_sn = $_REQUEST['order_sn'];
            $smarty->assign('order_sn', $order_sn);
        }
        $smarty->assign('temp', "center_add");
        $smarty->assign('post_url', "bearer.php?act=edit");
    }
    $smarty->assign('post_url', "bearer.php?act=edit");
    $smarty->assign('rec_id',$_REQUEST['rec_id']);
    $smarty->assign('act', "add");
    $smarty->display("bearer_info.html");

}

if ($_GET['act'] == "add_cpr") {
    if ($_GET['step'] == "order_add") {
        $cpr_order_sn = $_REQUEST['order_sn'];
    }
    $smarty->assign('post_url', "bearer.php?act=cpr_edit&cpr_order_sn={$cpr_order_sn}");
    $smarty->assign('rec_id',$_REQUEST['rec_id']);
    $smarty->assign('act', "add");
    $namel="<img src='images/logo.png'>";
    $smarty->assign( 'name1',$namel);
    $smarty->display("bearer_info.html");

}
if ($_GET['act'] == "add_cpr_combo") {
    if ($_GET['step'] == "order_add") {
        $cpr_order_sn = $_REQUEST['order_sn'];
    }
    $smarty->assign('post_url', "bearer.php?act=cpr_combo_edit&cpr_order_sn={$cpr_order_sn}");
    $smarty->assign('rec_id',$_REQUEST['rec_id']);
    $smarty->assign('act', "update");
    $namel="<img src='images/logo.png'>";
    $smarty->assign( 'name1',$namel);
    $smarty->display("bearer_info.html");

}
if ($_GET['act'] == "edit") {
    if ($_REQUEST['act'] == 'add') {
        $_POST['user_id'] = $_SESSION['user_id'];
        $res = $bearer->save_bearer_infos($_POST, "INSERT", '');
        if($res){
            ecs_header("Location:personal_center.php");
        }
    }
    if ($_REQUEST['act'] == 'update') {
        $_POST['user_id'] = $_SESSION['user_id'];
        $bearer_id = $_POST['bearer_id'];
        $bearer->save_bearer_infos($_POST, "UPDATE", "id=$bearer_id");
     $smarty->assign('message','修改持票人信息成功');
     $smarty->display("message.html");
    }
}
//持票人跳转页面
if ($_GET['act'] == "cpr_edit") {
    $cpr_order_sn = $_REQUEST['cpr_order_sn'];
    $_POST['user_id'] = $_SESSION['user_id'];
    $bearer->save_bearer_infos($_POST, "INSERT", '');
    $new_id = $bearer->insert_id();
    if(!empty($_POST['rec_id'])){
        $arr['bearer_id'] = $new_id;
        $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table("order_ticket"), $arr, 'UPDATE', 'rec_id=' . $_POST['rec_id']);
    }
    header("location:bearer.php?act=add_bearer_info&order_sn=$cpr_order_sn");
}
//持票人跳转页面
if ($_GET['act'] == "cpr_combo_edit") {
    $cpr_order_sn = $_REQUEST['cpr_order_sn'];
    $_POST['user_id'] = $_SESSION['user_id'];
    $bearer->save_bearer_infos($_POST, "INSERT", '');
    $new_id = $bearer->insert_id();
    if(!empty($_POST['rec_id'])){
        $arr['bearer_id'] = $new_id;
        $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table("order_combo"), $arr, 'UPDATE', 'rec_id=' . $_POST['rec_id']);
    }
    header("location:bearer.php?act=add_bearer_info&order_sn=$cpr_order_sn");
}

if ($_GET['act'] == "cpr_edit_new") {
    $cpr_order_sn = $_REQUEST['cpr_order_sn'];
    $_POST['user_id'] = $_SESSION['user_id'];
    $bearer_id = $_POST['bearer_id'];
    $bearer->save_bearer_infos($_POST, "UPDATE", "id=$bearer_id");
    header("location:bearer.php?act=index&order_sn=$cpr_order_sn");
}


if ($_GET['act'] == 'update') {
    //根据持票人id查询持票人信息
    $bearer_info = $bearer->bearer_info($_GET['id']);
    if(!empty($_GET['order_sn'])){
        $order_sn=$_GET['order_sn'];
        $smarty->assign('post_url', "bearer.php?act=cpr_edit_new&cpr_order_sn=$order_sn");
    }else{
        $smarty->assign('post_url', "bearer.php?act=edit");
    }
    $smarty->assign('act', "update");
    $smarty->assign('bearer_id', $_GET['id']);
    $smarty->assign('bearer_info', $bearer_info);
    $name="添加持票人";
    $smarty->assign( 'name',$name);
    $smarty->display("bearer_info.html");
}
if ($act == 'bearer_name') {
    $json = new JSON;
    $arr = Array();
    $arr['bearer_id'] = $_POST['bearer_id'];
    $rec_id = $_POST['order_ticket_id'];
    $count=get_bearer_id($rec_id, $arr['bearer_id']);
    if($count>=1){
        die($json->encode(array('res'=>false,'rec_id'=>$rec_id)));
    }else{
        $time = date("Y-m-d H:i:s",gmtime());
    $bearer_info= $bearer->bearer_info($_POST['bearer_id']);
        die($json->encode(array("bearer_id"=>$bearer_info['id'],"cn_customer_name"=>$bearer_info['cn_customer_name'],"passport_number" =>$bearer_info['passport_number'],"mobile"=>$bearer_info['mobile'],"add_time"=>$time,"order_ticket_id" => $_POST['order_ticket_id'],"res"=>true)));
    }
}
if($act == 'bearer_info_name'){
    $json = new JSON;
    $arr = Array();
    $arr['bearer_id'] = $_POST['bearer_id'];
    $rec_id = $_POST['order_combo_id'];
    $count=get_bearer_info_id($rec_id, $arr['bearer_id']);
    if($count>=1){
        die($json->encode(array('res'=>false,'rec_id'=>$rec_id)));
    }else{
        $time = date("Y-m-d H:i:s",gmtime());
        $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table("order_combo"), $arr, 'UPDATE', 'rec_id=' . $rec_id);
        $bearer_info= $bearer->bearer_info($_POST['bearer_id']);
        die($json->encode(array("bearer_id"=>$bearer_info['id'],"cn_customer_name"=>$bearer_info['cn_customer_name'],"passport_number" =>$bearer_info['passport_number'],"mobile"=>$bearer_info['mobile'],"add_time"=>$time, "order_combo_id" => $_POST['order_combo_id'],"res"=>true)));
    }
}
if ($act == "remove") {
    //移除持票人信息
    $bearer->remove($_POST['bearer_id']);
    echo "1";
}
if($act == "remove_ticket"){
    //移除持票人信息
    $order_id = $_POST['order_id'];
    $bearer_id = $_POST['bearer_id'];
    $bearer->remove_order_ticket($order_id,$bearer_id);
    echo "1";
}
if($act == "remove_combo"){
    //移除持票人信息
    $order_id = $_POST['order_id'];
    $bearer_id = $_POST['bearer_id'];
    $bearer->remove_order($order_id,$bearer_id);
    echo "1";
}
if ($act == "done") {
    $user_id = $_SESSION['user_id'];
    //填写持票人信息完成后，发送短信通知
    $order_id = intval($_POST['order_id']);
    __load("OrderService");
    $order_obj = new OrderService();
    $res = $order_obj->check_bearer_done($order_id);

    if ($res) {
        $order_info = $order_obj->get_order_info($order_id);
    }

    $query_list = $bearer->query_list($user_id);
    //根据订单号查询球票rec_id
    $rec_id_list = $bearer->get_rec_id($_POST['order_id']);

    //众安保险
    //获取订单ID,根据订单ID查出对应的订单号、购票人证件人号码、购票人证件人类型、购票人姓名、购票人姓名、购票人手机号码
    $order_id_num = $_REQUEST['order_id_num'];
    $rec_order_sn = $bearer->get_order_sn($order_id_num);
    $card_type = $rec_order_sn['card_type'];
    if ($card_type == 0) {
        $card_type_name = "I";
        //身份证
    } else if ($card_type == 1) {
        $card_type_name = "P";
        //护照
    }

    //根据订单号查出包含的所有门票
    $rec_menpiao_list = $bearer->get_order_menpiao_list($order_id_num);
    $game_info = $bearer->get_game_info($order_id_num);
    $rec_menpiao = Array();
    $rec_menpiao = $rec_menpiao_list;
    $smarty->assign('game_info', $game_info);
    foreach ($rec_menpiao AS $key => $value) {
        if ($value['num_start']) {
            $rec_menpiao[$key]['num_start'] = date('Y-m-d H-i', strtotime($value['num_start']));
        }
        if ($value['keywords']) {
            $rec_menpiao[$key]['keywords'] = explode(' ', trim($rec_menpiao[$key]['keywords']));
        }
        $rec_menpiao[$key]['total_item'] = number_format($rec_menpiao[$key]['goods_price'] * $rec_menpiao[$key]['goods_number'], 2, ".", "");
    }

    $arr = Array();
    $smarty->assign('bizOrigin', "shankai");
    $smarty->assign("productId", "5100001");
    $smarty->assign("shankaisportsOrderNo", $rec_order_sn['order_sn']);
    $smarty->assign("order_id", $rec_order_sn['order_id']);
    $smarty->assign("ticketName", $rec_order_sn['consignee']);
    $smarty->assign("ticketCertiType", $card_type_name);
    $smarty->assign("ticketCertiCode", $rec_order_sn['card_num']);
    $smarty->assign("ticketPhone", $rec_order_sn['mobile']);
    $smarty->assign("email", $rec_order_sn['email']);
    $smarty->assign("rec_menpiao_list", $rec_menpiao);
    $smarty->assign("ticket_info", $arr);
    $namel="<img src='images/logo.png'>";
    $smarty->assign( 'name1',$namel);
    $smarty->display("success.html");
}
if($act == 'add_bearer_info'){
    __load("BearerService");
    $bearer = new BearerService();
        //查询持票人信息.
        $order_sn=$_REQUEST['order_sn'];
        $order_id=$bearer->get_order_id($order_sn);
        __load("OrderService");
        $order = new OrderService();
        $order_ticket = $order->get_order_tickets($order_id);
        $order_combo = $order->get_order_combo($order_id);
        $order_goods = $order->get_order_goods_info($order_id);
        $order_bearer_info = $bearer->get_bearer_info($_SESSION['user_id']);
        $smarty->assign('order_goods',$order_goods);
        $smarty->assign('order_combo',$order_combo);
        $smarty->assign('order_ticket',$order_ticket);
        $smarty->assign('order_id',$order_id);
        $smarty->assign('bearer_info_list', $order_bearer_info);
        $smarty->assign('order_sn', $order_sn);
        $smarty->assign('act', "update");
    $namel="<img src='images/logo.png'>";
    $smarty->assign( 'name1',$namel);
    $smarty->display("bearer.html");
}
if($act == 'add_bearer'){
        __load("BearerService");
        $bearer = new BearerService();
        if ($_SESSION['user_id'] != 0) {
            //查询持票人信息.
            $order_sn = $_REQUEST['WIDout_trade_no'];
            $order_id=$bearer->get_order_id($order_sn);
            __load("OrderService");
            $order = new OrderService();
            $order_ticket = $order->get_order_tickets($order_id);
            $order_combo = $order->get_order_combo($order_id);
            $order_goods = $order->get_order_goods_info($order_id);
            $order_bearer_info = $bearer->get_bearer_info($_SESSION['user_id']);
            $smarty->assign('order_goods',$order_goods);
            $smarty->assign('order_combo',$order_combo);
            $smarty->assign('order_ticket',$order_ticket);
            $smarty->assign('order_sn',$order_sn);
            $smarty->assign('order_id',$order_id);
            $smarty->assign('bearer_info_list', $order_bearer_info);
            $smarty->assign('act', "update");
        }
        $namel="<img src='images/logo.png'>";
        $smarty->assign( 'name1',$namel);
        $smarty->display("bearer.html");
}
function get_user_email($user_id)
{
    $sql = "SELECT email From" . $GLOBALS['ecs']->table('users') . "WHERE user_id=$user_id";
    return $GLOBALS['db']->getOne($sql);
}

function get_bearer_id($rec_id,$bearer_id){
    $bearer_info = $GLOBALS['db']->getRow( "SELECT bearer_id,order_id,goods_id FROM " . $GLOBALS['ecs']->table('order_ticket') . "WHERE rec_id=$rec_id");
    $count = $GLOBALS['db']->getOne("SELECT COUNT(*)  FROM " . $GLOBALS['ecs']->table('order_ticket') . "WHERE order_id=$bearer_info[order_id] AND bearer_id =$bearer_id AND goods_id=$bearer_info[goods_id] ");
    return $count;
}
function get_bearer_info_id($rec_id,$bearer_id){
    $bearer_info = $GLOBALS['db']->getRow( "SELECT bearer_id,order_id,goods_id FROM " . $GLOBALS['ecs']->table('order_combo') . "WHERE rec_id=$rec_id");
    $count = $GLOBALS['db']->getOne("SELECT COUNT(*)  FROM " . $GLOBALS['ecs']->table('order_combo') . "WHERE order_id=$bearer_info[order_id] AND bearer_id =$bearer_id AND goods_id=$bearer_info[goods_id] ");
    return $count;
}
?>