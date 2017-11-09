<?php

define('IN_ECS', true);
define('ECS_ADMIN', true);

require(dirname(__FILE__) . '/includes/init.php');
__load("AirticketService");
__load("CartService");
$act = empty($_REQUEST['act']) ? "index" : trim($_REQUEST['act']);
$cart_obj = new CartService();
if ($act == 'index') {
    __load("Air_lineService");
    $air_line_service = new Air_lineService();
    $air_line_list = $air_line_service->get_air_line_region_list();
    $smarty->assign('air_line_list', $air_line_list);
    $smarty->display("airplane.html");
} elseif ($act == "ajax_get_air_line_list_from_city") {//获取抵达城市
    __load("Air_lineService");
    $air_line_service = new Air_lineService();
    $air_line_list = $air_line_service->get_air_list_from_city($_POST['from_city']);
    echo json_encode($air_line_list);
    exit;
}  elseif ($act == "ajax_get_air_line_list") {//通过开始城市和目的城市获取航程信息
    __load("Air_lineService");
    $air_line_service = new Air_lineService();
    $air_line_list = $air_line_service->get_air_list_from_city_and_to_city($_REQUEST['from_city'], $_REQUEST['to_city']);
    echo json_encode($air_line_list);
    exit;
} elseif ($act == "ajax_get_plane") {
    $air_obj = new AirticketService();
    $airticket_list = $air_obj->get_airticket($_POST['from_ctiy'], $_POST['to_ctiy']);
    exit(json_encode($airticket_list));
} elseif ($act == "ajax_get_to_region") {
    $air_obj = new AirticketService();
    $region_list = $air_obj->get_to_region_from_city($_POST['from_ctiy']);
    exit(json_encode($region_list));
} elseif ($act == "ajax_set_plane") {
    $air_obj = new AirticketService();
    $smarty->assign('zh_number', "数量");
    if ($_GET['direction'] == "to") {
        $smarty->assign('direction', "to");
    } else {
        $smarty->assign('direction', "from");
    }
    $smarty->assign('money', "￥");
    $smarty->assign('space_list', $air_obj->get_air_space($_POST['air']));
    exit($smarty->fetch("tpl_space.dwt", null, null, false));
} elseif ($act == "ajax_air_line_num") {//获取航班有效库存
    __load("Air_line_numService");
    $air_line_num_service = new Air_line_numService();
    $air_line_id = isset($_REQUEST['air']) ? intval($_REQUEST['air']) : 0;
    $air_line_list = $air_line_num_service->get_air_line_num_by_air_line_id($air_line_id);
    echo json_encode($air_line_list);
    exit;
} elseif ($act == "air_line_save") {//保存航程
    $air_cart = array();

    //检查参数
    $from_air_line_id = isset($_REQUEST['from_air']) ? intval($_REQUEST['from_air']) : 0;
    $to_air_line_id = isset($_REQUEST['to_air']) ? intval($_REQUEST['to_air']) : 0;

    if ($from_air_line_id == 0 || $to_air_line_id == 0) {
        show_message('去程航程选择错误', '返回', 'view.php?game_id=' . $_REQUEST['game_id']);
    }
    if ($from_air_line_id != $to_air_line_id) {
        show_message('返程航程必须和去程一致', '返回', 'view.php?game_id=' . $_REQUEST['game_id']);
    }

    __load("Air_lineService");
    $air_line_service = new Air_lineService();

    $from_air_line = $air_line_service->getById($from_air_line_id);
    if (empty($from_air_line)) {
        show_message('去程航程选择错误', '返回', 'view.php?game_id=' . $_REQUEST['game_id']);
    }

    $to_air_line = $air_line_service->getById($to_air_line_id);
    if (empty($to_air_line)) {
        show_message('返程航程选择错误', '返回', 'view.php?game_id=' . $_REQUEST['game_id']);
    }

    __load("Air_ticketService");
    $air_ticket_service = new Air_ticketService();
    //获取去程航程下的航班
    $from_air_ticket_list = $air_ticket_service->get_all_air_ticket_list($from_air_line_id, 1);
    if (empty($from_air_ticket_list)) {
        show_message('去程未设置航班', '返回', 'view.php?game_id=' . $_REQUEST['game_id']);
    }

    //获取返程航程下的航班
    $to_air_ticket_list = $air_ticket_service->get_all_air_ticket_list($to_air_line_id, 2);
    if (empty($to_air_ticket_list)) {
        show_message('返程未设置航班', '返回', 'view.php?game_id=' . $_REQUEST['game_id']);
    }

    $plane_space = array();
    foreach ($air_line_list as $value) {
        $plane_space[] = $value['flight'];
    }

    $plane_number = $_POST['from_number'];
    $plane_space = $_POST['from_space'];
    $from_from_city = empty($_POST['from_from_city']) ? 0 : intval($_POST['from_from_city']);//出发城市
    $from_to_city = empty($_POST['from_to_city']) ? 0 : intval($_POST['from_to_city']);//目的地城市
    $from_air = empty($_POST['from_air']) ? 0 : intval($_POST['from_air']);//去程航班ID
    $from_time = empty($_POST['from_form_time']) ? "" : $_POST['from_form_time'];// 去程日期
    $to_from_city = empty($_POST['to_from_city']) ? 0 : intval($_POST['to_from_city']);//出发城市
    $to_to_city = empty($_POST['to_to_city']) ? 0 : intval($_POST['to_to_city']);//目的地城市
    $to_air = empty($_POST['to_air']) ? 0 : intval($_POST['to_air']);//去程航班ID
    $to_time = empty($_POST['to_form_time']) ? "" : $_POST['to_form_time'];// 去程日期
    $to_space = $_POST['to_space'];

    $air = array(
        "from_city" => $from_from_city,
        "to_city" => $from_to_city,
        "fly_date" => $from_time,//出发日期
        "return_fly_date" => $to_time,//返回日期
        "air_line_type" => 0,//往返
        "air_id" => $from_air,
        "space_id" => 0,
        "goods_number" => $plane_number[0],
        "goods_price" => $from_air_line['price'],
        "goods_type" => "plane",
    );

    array_push($air_cart, $air);

    __load("CartService");
    $cart_obj = new CartService();
    foreach ($air_cart as $card) {
        $cart_obj->add_cart($card);
    }
    ecs_header("Location: cart.php");
}
