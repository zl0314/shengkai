<?php

/**
 * 购物车
 */
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
include_once('includes/cls_json.php');
$act = empty($_REQUEST['act']) ? "index" : trim($_REQUEST['act']);
if ($act == "index") {
    $user_id = $_SESSION['user_id'];
    /* 获取航班起始城市 start */
    __load("AirticketService");
    $air_obj = new AirticketService();
    $air_list = $air_obj->get_airticket_region();
    $smarty->assign('from_city', $air_list['from_ctiy']);
    $smarty->assign('to_city', $air_list['to_ctiy']);
    $smarty->assign('game_id', $_GET['game_id']);
    /* 获取航班起始城市 end */
    /*获取仓位列表*/
    $smarty->assign('space_list', $air_obj->get_space_list());
    /*获取仓位列表*/
    __load("CartService");
    $cart_obj = new CartService();
    $smarty->assign("cart_list", $cart_obj->get_cart_info());

    $smarty->assign("cart_money", $cart_obj->get_cart_money());
    /* 周边商品 start */
    $ambitus = $air_obj->get_ambitus_goods();
    $smarty->assign("ambitus", $ambitus);
    /* 周边商品 end */
    /* 酒店 start*/
    $cart_info = get_cart_info(SESS_ID);
    $cart_num = count($cart_info);
    $smarty->assign('cart_num', $cart_num);
    /* 获取运动类别列表 start */
    __load("SportcatService");
    $sportcat_obj = new SportcatService();
    $sportcat_list = $sportcat_obj->get_sportcat_list();
    /* 获取运动类别列表 end */
    $number = get_cart_num(SESS_ID);
    $num = 0;
    foreach ($number as $value) {
        $num += $value['goods_number'];
    }
    $smarty->assign('num', $num);
    $smarty->assign('yuanquan', 1);
    //判断购物车是否有商品
    $get_cart_num = get_cart_num(SESS_ID);
    if (count($get_cart_num) > 0) {
        __load("GameService");
        $game_obj = new GameService();
        $game_info = $game_obj->get_game(intval($_GET['game_id']));
        $smarty->assign('game_info', $game_info);
    } else {
        show_null_cart("购物车空空的哦~，去看看心仪的商品吧~", '', '', 'cart');
    }
    $server = $_SERVER['HTTP_USER_AGENT'];
    $str = 'Mobile';
    $mobile = strpos($server, $str);
    $smarty->assign('mobile', $mobile);

    //取得所有目的地航程

    __load("Air_lineService");
    $air_line_service = new Air_lineService();
//    $air_line_list = $air_line_service->get_air_line_region_list();
//    $smarty->assign('air_line_list', $air_line_list);
    $air_line_from_list = $air_line_service->get_air_line_from_region_list();
    $smarty->assign('air_line_from_list', $air_line_from_list);

    $cart_info_list = $cart_obj->get_cart_info();

    //获取当前时间
    $next_date = date("Y-m-d", strtotime("+1 day"));
    $return_date = date("Y-m-d", strtotime("+3 day"));
    $smarty->assign('next_date', $next_date);
    $smarty->assign('return_date', $return_date);
    $smarty->assign('cart_html', $cart_html);
    $smarty->assign('user_id', $user_id);
    /* 酒店 end */
    $smarty->display("view.dwt");
} /* 添加机票 */
elseif ($act == "sub_ticket") {

    //机票动态name属性动态码
    $to_ticket_flag = 'to_ticket_num_' . $_POST['to_ticket_flag'];
    $back_ticket_flag = 'back_ticket_num_' . $_POST['back_ticket_flag'];
    if ($_POST[$to_ticket_flag] != $_POST[$back_ticket_flag]) {
        echo 2;
        die();
    }
    //机票信息
    $plane_ticket_info = array(
        'to_from_city' => $_POST['to_from_city'],
        'to_to_city' => $_POST['to_to_city'],
        'to_depart_submit' => $_POST['to_depart_submit'],
        'to_arrive_submit' => $_POST['to_arrive_submit'],
        'to_flight' => $_POST['to_flight'],
        'to_ticket_space' => $_POST['to_ticket_space'],
        'to_ticket_num' => $_POST[$to_ticket_flag],
        'to_ticket_price' => $_POST['to_ticket_price'],
        'back_from_city' => $_POST['back_from_city'],
        'back_to_city' => $_POST['back_to_city'],
        'back_depart_submit' => $_POST['back_depart_submit'],
        'back_arrive_submit' => $_POST['back_arrive_submit'],
        'back_flight' => $_POST['back_flight'],
        'back_ticket_space' => $_POST['back_ticket_space'],
        'back_ticket_num' => $_POST[$back_ticket_flag],
        'back_ticket_price' => $_POST['back_ticket_price'],
        'goods_price' => $_POST['back_ticket_price'],
        'session_id' => SESS_ID,
        'goods_type' => 'plane'
    );
    $is_succ = $db->autoExecute($ecs->table("cart"), $plane_ticket_info, "INSERT");
    if ($is_succ) {
        echo 0;
    } else {
        echo 1;
    }
    die();
} /* 添加周边商品 */
elseif ($act == "sub_goods") {
    $json = new JSON();
    $data = $json->decode($_POST['ambitus'], true);
    __load("CartService");
    $cart_obj = new CartService();
    $cart_obj->add_goods_to_cart($data);
    echo true;
    die;
} elseif ($act == "ajax_get_plane") {
    $json = new JSON();
    __load("AirticketService");

    $air_obj = new AirticketService();
    $airticket_list = $air_obj->get_airticket($_POST['from_ctiy'], $_POST['to_ctiy']);
    exit($json->encode($airticket_list));
} elseif ($act == "ajax_get_air_line_list") {//通过开始城市和目的城市获取航程信息
    __load("Air_lineService");
    $air_line_service = new Air_lineService();
    $air_line_list = $air_line_service->get_air_list_from_city_and_to_city($_REQUEST['from_city'], $_REQUEST['to_city']);
    echo json_encode($air_line_list);
    exit;
} elseif ($act == "ajax_air_line_num") {//获取航班有效库存
    __load("Air_line_numService");
    $air_line_num_service = new Air_line_numService();
    $air_line_id = isset($_REQUEST['air']) ? intval($_REQUEST['air']) : 0;
    $air_line_list = $air_line_num_service->get_air_line_num_by_air_line_id($air_line_id);
    echo json_encode($air_line_list);
    exit;
} elseif ($act == "ajax_get_to_region") {
    $json = new JSON();
    __load("AirticketService");

    $air_obj = new AirticketService();
    $region_list = $air_obj->get_to_region_from_city($_POST['from_ctiy']);
    exit($json->encode($region_list));
} elseif ($act == "ajax_get_air_line_list_from_city") {//获取抵达城市
    __load("Air_lineService");
    $air_line_service = new Air_lineService();
    $air_line_list = $air_line_service->get_air_list_from_city(intval($_REQUEST['from_city']));
    echo json_encode($air_line_list);
    exit;
} elseif ($act == "ajax_set_plane") {
    $json = new JSON();
    __load("AirticketService");
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

} elseif ($act == "ajax_save_air") {


    $air_cart = array();
    /*
     * 整理去程机票 start
     *  */
    __load("AirticketService");
    $air_obj = new AirticketService();
    $from_from_city = empty($_POST['from_from_city']) ? 0 : intval($_POST['from_from_city']);//出发城市
    $from_to_city = empty($_POST['from_to_city']) ? 0 : intval($_POST['from_to_city']);//目的地城市
    $from_air = empty($_POST['from_air']) ? 0 : intval($_POST['from_air']);//去程航班ID
    $from_time = empty($_POST['form_form_time_submit']) ? "" : $_POST['form_form_time_submit'];// 去程日期
    $from_space = $_POST['from_space'];
    foreach ($from_space as $space) {
        if ($space['val'] > 0) {
            $space_info = $air_obj->get_space($space['key']);
            $air = array(
                "from_city" => $from_from_city,
                "to_city" => $from_to_city,
                "fly_date" => $from_time,
                "air_id" => $from_air,
                "space_id" => $space['key'],
                "goods_number" => $space['val'],
                "goods_price" => $space_info['s_price'],
                "goods_type" => "plane"
            );
            array_push($air_cart, $air);
        }
    }
    /*
    * 整理去程机票 end
    *  */
    /*
     *   * 整理返程机票 start
     *  */
    $to_from_city = empty($_POST['to_from_city']) ? 0 : intval($_POST['to_from_city']);//出发城市
    $to_to_city = empty($_POST['to_to_city']) ? 0 : intval($_POST['to_to_city']);//目的地城市
    $to_air = empty($_POST['to_air']) ? 0 : intval($_POST['to_air']);//去程航班ID
    $to_time = empty($_POST['to_form_time_submit']) ? "" : $_POST['to_form_time_submit'];// 去程日期
    $to_space = $_POST['to_space'];
    foreach ($to_space as $space) {
        if ($space['val'] > 0) {
            $space_info = $air_obj->get_space($space['key']);
            $air = array(
                "from_city" => $to_from_city,
                "to_city" => $to_to_city,
                "fly_date" => $to_time,
                "air_id" => $to_air,
                "space_id" => $space['key'],
                "goods_number" => $space['val'],
                "goods_price" => $space_info['s_price'],
                "goods_type" => "plane"
            );
            array_push($air_cart, $air);
        }
    }
    /*
     *   * 整理返程机票 end
     *
     * */
    __load("CartService");
    $cart_obj = new CartService();
    foreach ($air_cart as $card) {
        $cart_obj->add_cart($card);
    }
    exit("success");


} elseif ($act == "air_save") {
    $air_cart = array();
    __load("AirticketService");
    $air_obj = new AirticketService();
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

    foreach ($plane_number as $key => $value) {
        if (!empty($value)) {
            $plane_info = $air_obj->select_air_space_info($from_air, $plane_space[$key]);
            if (!empty($plane_info)) {
                $air = array(
                    "from_city" => $from_from_city,
                    "to_city" => $from_to_city,
                    "fly_date" => $from_time,
                    "air_id" => $from_air,
                    "space_id" => $plane_info['id'],
                    "goods_number" => $value,
                    "goods_price" => $plane_info['s_price'],
                    "goods_type" => "plane"
                );
                array_push($air_cart, $air);
            }

            $plane_info = $air_obj->select_air_space_info($to_air, $plane_space[$key]);
            if (!empty($plane_info)) {
                $air = array(
                    "from_city" => $to_from_city,
                    "to_city" => $to_to_city,
                    "fly_date" => $from_time,
                    "air_id" => $to_air,
                    "space_id" => $plane_info['id'],
                    "goods_number" => $value,
                    "goods_price" => $plane_info['s_price'],
                    "goods_type" => "plane"
                );
                array_push($air_cart, $air);
            }

        }
    }
    __load("CartService");
    $cart_obj = new CartService();
    foreach ($air_cart as $card) {
        $cart_obj->add_cart($card);
    }
    ecs_header("Location: view.php?game_id=" . $_GET['game_id']);
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
    ecs_header("Location: view.php?game_id=" . $_GET['game_id']);
} elseif ($act == "remove_cart") {
    $type = empty($_GET['type']) ? "all" : trim($_GET['type']);
    $cart_id = empty($_GET['cart_id']) ? "0" : intval($_GET['cart_id']);
    __load("CartService");
    $cart_obj = new CartService();
    $cart_obj->remove_cart($type, $cart_id);
    if ($type == 'ticket') {
        remove_hotel_info($cart_id);
    }
    ecs_header("Location: view.php?game_id=" . $_GET['game_id']);
} elseif ($act == "ajax_hotel_html") {
    __load("HotelService");
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

} elseif ($act == "show_hotel") {//查询购物车中所有的地区酒店
    __load("HotelService");
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
    print_r($smarty->fetch("tpl/hotel_list.tpl", null, null, false));
    exit;

} elseif ($act == "ajax_room_html") {
    __load("HotelService");
    $hotel_id = isset($_REQUEST['hotel_id']) ? intval($_REQUEST['hotel_id']) : 0;
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
    $smarty->assign('room_list', $hotel_obj->get_room_list($hotel_id));
    $smarty->assign('utf8_str', $utf8_str);
    print_r($smarty->fetch("tpl/room_list.tpl", null, null, false));
    exit;

} elseif ($act == "ajax_room_num") {
    __load("Room_numService");
    $room_id = isset($_REQUEST['room_id']) ? intval($_REQUEST['room_id']) : 0;
    $room_num_service = new Room_numService();
    $room_num_list = $room_num_service->get_all_room_num_list($room_id);
    header("content-type: application/json; charset=utf-8");
    echo json_encode($room_num_list);
    exit;
} elseif ($act == "save_hotel") {//保存酒店信息
    __load("RoomService");
    $room_obj = new RoomService();
    $hotel_cart = array();
    foreach ($_POST['room_id'] as $key => $room) {
        if (!empty($_POST['room_number'][$key])) {
            $start_date_time = strtotime($_POST['hotel_start_date'][$key]);
            $end_date_time = strtotime($_POST['hotel_end_date'][$key]);
            if ($end_date_time - $start_date_time <= 0) {
                show_message('酒店至少需要居住一天时间,请重新选择', '返回', 'view.php?game_id=' . $_REQUEST['game_id']);
            }
            $room_info = $room_obj->get_room($room);
            array_push($hotel_cart, array(
                "hotel_id" => intval($_POST['hotel_id']),
                "parent_id" => intval($_POST['rec_id']),
                "room_id" => $room,
                "people" => intval($_POST['people_number'][$key]),
                "room_num" => intval($_POST['room_number'][$key]),
                "goods_number" => intval($_POST['room_number'][$key]),
                "room_type" => intval($room_info['type_name']),
                "goods_price" => intval($room_info['room_money']),
                "goods_type" => "hotel",
                "hotel_start_date" => $_POST['hotel_start_date'][$key],
                "hotel_end_date" => $_POST['hotel_end_date'][$key],
            ));
        }
    }
    __load("CartService");
    $cart_obj = new CartService();

    __load("Room_numService");
    $room_num_service = new Room_numService();

    //日期校验
    $day_err_list = array();
    foreach ($hotel_cart as $hotel) {
        $begin_date_time = strtotime($hotel['hotel_start_date']);
        $end_date_time = strtotime($hotel['hotel_end_date']);

        if ($hotel['goods_number'] < 1) {
            continue;
        }

        //for ($i = $begin_date_time; $i <= $end_date_time; $i = $i + 86400) {
        //这里只验证开始预定时间了
        for ($i = $begin_date_time; $i <= $begin_date_time; $i = $i + 86400) {
            $date = date('Y-m-d', $i);
            $num = $room_num_service->get_room_num_by_date($hotel['room_id'], $date);
            if ($hotel['goods_number'] > $num) {
                $day_err_list[] = "{$hotel['room_type']} {$date} 房间只有{$num}间";
            }
        }
    }

    if ($day_err_list) {
        show_message(implode(",\n", $day_err_list) . ', 请重新选择', '返回', 'view.php?game_id=' . $_REQUEST['game_id']);
    }

    foreach ($hotel_cart as $hotel) {
        $cart_obj->add_cart($hotel);
    }

    ecs_header("Location: view.php?game_id=" . $_GET['game_id']);

} elseif ($act == 'show_hotel_room_num') {//查看房间库存数量
    __load("Calendar", "util");
    __load("RoomService");
    $room_service = new RoomService();
    __load("Room_numService");
    $room_num_service = new Room_numService();

    $room_id = !empty($_REQUEST['room_id']) ? intval($_REQUEST['room_id']) : 0;
    $room = $room_service->get_room($room_id);
    if (empty($room)) {
        echo '没有房间信息';
        exit;
    }
    $result = $room_num_service->get_room_num_list($room_id);
    echo '<pre>';print_r($result);exit;
} elseif ($act == "remove_hotel") {
    __load("CartService");
    $cart_obj = new CartService();
    $cart_obj->remove_hotel($_GET['rec_id']);
    echo "success";
    exit;
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
        . "AS n," . $GLOBALS['ecs']->table('color_manage') . "AS cm " . "WHERE c.session_id= '$session_id' AND c.goods_id=g.goods_id AND g.number_id=n.id AND n.pitch_id=p.id AND p.region_id=r.region_id  AND c.goods_type='ticket'  AND n.color_id=cm.color_id";
    return $GLOBALS['db']->getAll($sql);
}

function get_hotel_name($region_id)
{
    $sql = "SELECT  hotel_name,id From" . $GLOBALS['ecs']->table('hotel') . "WHERE region_id=$region_id";
    return $GLOBALS['db']->getAll($sql);
}

function get_room_type($hotel_id)
{
    $sql = "SELECT rt.type_name,rm.id FROM " . $GLOBALS['ecs']->table('room') . "AS rm," . $GLOBALS['ecs']->table('room_type') . "AS rt" . " WHERE rm.room_type=rt.id AND rm.hotel_id=$hotel_id";
    return $GLOBALS['db']->getAll($sql);
}


function remove_hotel_info($rec_id)
{
    $sql = "delete from " . $GLOBALS['ecs']->table('cart') . " where parent_id = " . $rec_id . " and goods_type='hotel'";
    return $GLOBALS['db']->query($sql);
}

?>
