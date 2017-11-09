<?php

define('IN_ECS', true);
define('ECS_ADMIN', true);

require(dirname(__FILE__) . '/includes/init.php');
__load("HotelService");
__load("RoomService");
__load("CartService");
$act = empty($_REQUEST['act']) ? "index" : trim($_REQUEST['act']);
$cart_obj = new CartService();
if ($act == 'index') {
    $utf8_str = array(
        "Explain" => ''
    );

    __load("CartService");
    $cart_obj = new CartService();
    $cart_info = $cart_obj->get_cart_info();

    $region_ids = array();
    foreach ($cart_info['ticket']['t'] as $value) {
        $region_ids[] = $value['ticket_info']['region_id'];
    }
    $region_ids = array_unique($region_ids);

    $hotel_obj = new HotelService();
    $hotel_list = $hotel_obj->get_hotel_list_by_region_ids($region_ids);
    $smarty->assign('hotel_list', $hotel_list);
    if (empty($hotel_list)) {
        $utf8_str['Explain'] = '<b style="color: red; font-weight: 700;">没有可预定的酒店</b>';
    }
    $smarty->assign('utf8_str', $utf8_str);
    $smarty->display("hotel.html");
} elseif ($act == "ajax_hotel_html") {
    $utf8_str = array(
        "Explain" => ''
    );
    $hotel_obj = new HotelService();
    $hotel_list = $hotel_obj->get_hotel_list($_POST['region_id']);
    $smarty->assign('hotel_list', $hotel_list);
    if (empty($hotel_list)) {
        $utf8_str['Explain'] = '<b style="color: red; font-weight: 700;">没有可预定的酒店</b>';
    }
    $smarty->assign('utf8_str', $utf8_str);
    print_r($smarty->fetch("tpl/hotel_list.tpl", null, null, false));
    exit;

} elseif ($act == "ajax_room_html") {
    $utf8_str = array(
        "Mark" => '￥',
        "Company" => '天',
        "home_t" => "间数:",
        "people_t" => "人数:",
        "start_date" => "入住日期:",
        "end_date" => "退房日期:"
    );
    $hotel_obj = new HotelService();
    //获取当前时间
    $date = date('Y-m-d', time());
    $next_date = date("Y-m-d", strtotime("+1 day"));
    $smarty->assign('date', $date);
    $smarty->assign('next_date', $next_date);
    $smarty->assign('room_list', $hotel_obj->get_room_list($_POST['hotel_id']));
    $smarty->assign('utf8_str', $utf8_str);
    print_r($smarty->fetch("tpl/room_list.tpl", null, null, false));
    exit;

} elseif ($act == "save_hotel") {
    $room_obj = new RoomService();
    $hotel_cart = array();
    foreach ($_POST['room_id'] as $key => $room) {
        if (!empty($_POST['room_number'][$key])) {
            $start_date_time = strtotime($_POST['hotel_start_date'][$key]);
            $end_date_time = strtotime($_POST['hotel_end_date'][$key]);
            if ($end_date_time - $start_date_time <= 0) {
                mobile_show_login_message('酒店至少需要居住一天时间,请重新选择', '', '', 'cart.php');
            }
            $room_info = $room_obj->get_room($room);
            array_push($hotel_cart, array(
                "hotel_id" => $_POST['hotel_id'],
                "parent_id" => $_POST['rec_id'],
                "room_id" => $room,
                "people" => $_POST['people_number'][$key],
                "room_num" => $_POST['room_number'][$key],
                "goods_number" => $_POST['room_number'][$key],
                "room_type" => $room_info['type_name'],
                "goods_price" => $room_info['room_money'],
                "goods_type" => "hotel",
                "hotel_start_date" => $_POST['hotel_start_date'][$key],
                "hotel_end_date" => $_POST['hotel_end_date'][$key],
            ));
        }
    }

    $cart_obj = new CartService();
    foreach ($hotel_cart as $hotel) {
        $cart_obj->add_cart($hotel);
    }
    ecs_header("Location: cart.php");
} elseif ($act == "remove_hotel") {
    $cart_obj = new CartService();
    $cart_obj->remove_hotel($_GET['rec_id']);
    echo "success";
    exit;
} elseif ($act == "ajax_room_num") {
    __load("Room_numService");
    $room_id = isset($_REQUEST['room_id']) ? intval($_REQUEST['room_id']) : 0;
    $room_num_service = new Room_numService();
    $room_num_list = $room_num_service->get_all_room_num_list($room_id);
    header("content-type: application/json; charset=utf-8");
    echo json_encode($room_num_list);
    exit;
}
