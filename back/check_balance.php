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
require_once(ROOT_PATH . 'includes/modules/payment/func/log.php');
require_once(ROOT_PATH . 'languages/' . $_CFG['lang'] . '/user.php');
require_once(ROOT_PATH . 'languages/' . $_CFG['lang'] . '/shopping_flow.php');
include_once('includes/cls_json.php');


//初始化日志
$logHandler= new CLogFileHandler(ROOT_PATH."/logs/".date('Y-m-d').'.log');
$log = Logger::Init($logHandler, 15);

__load("CartService");
$cart_obj = new CartService();
//获取赛事信息

$act = empty($_REQUEST['act']) ? "index" : trim($_REQUEST['act']);
/*
 * 检查用户是否已经登录
 * 如果用户已经登录了则检查是否有默认的收货地址
 * 如果没有登录则跳转到登录和注册页面
 */
if ($_SESSION['user_id'] == 0) {
    /* 用户没有登录且没有选定匿名购物，转向到登录页面 */
    ecs_header("Location: user.php");
    exit;
}

if ($act == "check_order") {
    $smarty->assign('lang', $_LANG);
    $cart_info = $cart_obj->get_cart_info();
//    echo "<pre>";
//    print_r($cart_info);die;
    $cart_sum = 0;
    if (empty($_SESSION['user_id'])) {
        show_message("尚未登录，请登录再结算！", '', '', 'warning');
    }
    /* 检查购物车中是否有商品 */
    $sql = "SELECT COUNT(*) FROM " . $ecs->table('cart') .
        " WHERE session_id = '" . SESS_ID . "'";
    if ($db->getOne($sql) == 0) {
        show_message($_LANG['no_goods_in_cart'], '', '', 'warning');
    }
    $res = check_cart_goods_number();
    if (!empty($res)) {
        show_message( "库存不足,请重新选择商品", '', '', 'warning');
    }
    foreach ($cart_info as $value) {
        $cart_sum += count($value);
    }

    if ($cart_sum == 0) {
        show_message("购物车是空的", '', '', 'warning');
    }
    //查询当前用户属性是否为代理商
    $user_id = $_SESSION['user_id'];
    $type_info = get_user($user_id);
    $game_id = !empty($_REQUEST['game_id']) ? $_REQUEST['game_id'] : '';

    /* 获取运动类别列表 start */
    __load("SportcatService");
    $sportcat_obj = new SportcatService();
    $sportcat_list = $sportcat_obj->get_sportcat_list();
    /* 获取运动类别列表 end */
    $number=get_cart_num(SESS_ID);
    $num = 0;
    foreach ($number as $value){
        $num += $value['goods_number'];
    }
    //    echo $num;die;
    $smarty->assign('num',$num);
    $smarty->assign('yuanquan',1);
    //判断购物车是否有普通球票
    if($cart_info['ticket']['t']) {
        foreach ($cart_info['ticket']['t'] as $v) {
            $game_id = $v['game_id'];
            $goods_id = $v['goods_id'];
            if ($type_info == 0 && $game_id == 38) {
                $one_ticket_number = check_ticket_number(2,$goods_id);
                if (!empty($one_ticket_number)) {
                    show_message("每种球票最多预订2张", '', '', 'warning');
                }
                //查询当前用户所下订单中有几张球票
                $user_id = $_SESSION['user_id'];
                //统计订单中属于这个赛事的球票
                $number = count(get_order_info($user_id,$goods_id));
                //查询用户购物车中购买得属于当前赛事的球票数量
                $num = get_cart_info($goods_id, $user_id);
                //统计订单和购物车中属于当前赛事的球票数量
                $res = $number + $num;
                //判断数量是否超过限制（超过限制提示信息）
                if ($res > 2) {
                    show_message("单个用户最多购买两张票", '', '', 'warning');
                    exit;
                }
            } elseif ($type_info == 0 && $game_id != 38) {
                $one_ticket_number = check_ticket_number_pc(4,$goods_id);
                if (!empty($one_ticket_number)) {
                    show_message("每种球票最多预订4张", '', '', 'warning');
                }
                //查询当前用户所下订单中有几张球票
                $user_id = $_SESSION['user_id'];
                //统计订单中属于这个赛事的球票
                $number = count(get_order_info($user_id,$goods_id));
                //查询用户购物车中购买得属于当前赛事的球票数量
                $num = get_cart_info($goods_id, $user_id);
                //统计订单和购物车中属于当前赛事的球票数量
                $res = $number + $num;
                //判断数量是否超过限制（超过限制提示信息）
                if ($res > 4) {
                    show_message("单个用户最多购买四张相同球票", '', '', 'warning');
                    exit;
                }
                if($res < 2){
                    show_message("单个用户最少购买两张相同球票", '', '', 'warning');
                    exit;
                }
                //查询当前赛事共卖了几张票
                $game_ticket = count(get_order_ticket_number($user_id,$game_id));
                //查询购物车中当前赛事的票的数量
                $game_ticket_cart_num = count(get_cart_ticket_num($user_id,$game_id));
                //统计当前赛事球票的数量
                $number_ticket = $game_ticket + $game_ticket_cart_num;
                //判断数量是否超过限制（超过限制提示信息）
                if($number_ticket > 28){
                    show_message("单个赛事用户最多购买二十八张球票", '', '', 'warning');
                    exit;
                }
            }
        }
        __load("GameService");
        $game_obj = new GameService();
        $game_name_info = $game_obj->get_game_name($game_id);
        $smarty->assign('game_name_info', $game_name_info);
    }

    $default_address_id = get_default_address($_SESSION['user_id']);
    $smarty->assign('default_address', $default_address_id);
    $smarty->assign("cart_money", $cart_obj->get_cart_money());
    // 给货到付款的手续费加<span id>，以便改变配送的时候动态显示
    $payment_list = available_payment_list(1, $cod_fee);
    if (isset($payment_list)) {
        foreach ($payment_list as $key => $payment) {
            if ($payment['is_cod'] == '1') {
                $payment_list[$key]['format_pay_fee'] = '<span id="ECS_CODFEE">' . $payment['format_pay_fee'] . '</span>';
            }
            /* 如果有易宝神州行支付 如果订单金额大于300 则不显示 */
            if ($payment['pay_code'] == 'yeepayszx' && $total['amount'] > 300) {
                unset($payment_list[$key]);
            }
            /* 如果有余额支付 */
            if ($payment['pay_code'] == 'balance') {
                /* 如果未登录，不显示 */
                if ($_SESSION['user_id'] == 0) {
                    unset($payment_list[$key]);
                } else {
                    if ($_SESSION['flow_order']['pay_id'] == $payment['pay_id']) {
                        $smarty->assign('disable_surplus', 1);
                    }
                }
            }
        }
    }
    //收货地址
    $user_address = get_address($_SESSION['user_id']);
    $card_type_text = array(0 => '身份证', 1 => '护照');
    foreach ($user_address as $key => $value) {
        $user_address[$key]['country_info'] = get_region_name($value['country']);
        $user_address[$key]['province_info'] = get_region_name($value['province']);
        $user_address[$key]['city_info'] = get_region_name($value['city']);
        $user_address[$key]['district_info'] = get_region_name($value['district']);
        $user_address[$key]['card_type_text'] = $card_type_text[$user_address[$key]['card_type']];

    }
    $smarty->assign('user_address', $user_address);
    $smarty->assign('payment_list', $payment_list);
    $ticket_array = $cart_info['ticket'];
    $cart = $cart_obj->get_cart();
    //获取购物车内的套餐
    $combo_info_cart = $cart_obj->get_cart_combo();
    $smarty->assign('combo_info',$combo_info_cart);
    /* 取得国家列表、商店所在国家、商店所在国家的省列表 */
    $smarty->assign('country_list', get_regions());
    $smarty->assign('shop_province_list', get_regions(1, $_CFG['shop_country']));
    $smarty->assign('ticket_info', $cart['ticket']);
    $smarty->assign('all', $cart['all']);
    $smarty->assign('goods', $cart['goods']);
    $smarty->assign('plane', $cart['plane']);
    $smarty->assign('hotel', $cart['hotel']);
    $smarty->display("check_balance.dwt");
    exit;
} /* 添加收货人地址 */ elseif ($act == 'add_consignee') {

    include_once('includes/cls_json.php');
    include_once(ROOT_PATH . 'includes/lib_transaction.php');
    $json = new JSON;
    $consignee = array(
        'user_id' => $_SESSION['user_id'],
        'consignee' => $_POST['consignee'],
        'email' => $_POST['email'],
        'mobile' => $_POST['mobile'],
        'tel' => $_POST['tel'],
        'card_type' => $_POST['card_type'],
        'card_num' => $_POST['card_num']
    );
    if ($consignee['consignee'] == '' || $consignee['email'] == '' || $consignee['mobile'] == '' || $consignee['card_num'] == '') {
        echo $json->encode(array('code' => 2, 'msg' => '信息不全'));
        exit;
    } else {
        $res = $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table("user_address"), $consignee, 'INSERT');
        $address_id = $GLOBALS['db']->insert_id();
        if ($address_id > 0) {
            save_consignee($address_id);
            echo $json->encode(array('code' => 1, 'msg' => 'success'));
            exit;
        } else {
            echo $json->encode(array('code' => 2, 'msg' => 'error'));
            exit;
        }
    }
}elseif($act == "zhifu_done"){
    $order_sn = $_REQUEST['order_sn'];
    $order_amount = $_REQUEST['order_amount'];
    $qc_data = $_REQUEST['data'];
    if($qc_data){
        $GLOBALS['db']->query("UPDATE ".$GLOBALS['ecs']->table('order_info')." SET qc_data='".$qc_data."' WHERE order_sn=$order_sn");
    }
    $smarty->assign('data',$qc_data);
    $smarty->assign('order_amount',$order_amount);
    $smarty->assign('order_sn',$order_sn);

    $smarty->display("sk_order_zhifu.dwt");
} elseif ($act == "done") {
    include_once('includes/lib_clips.php');
    include_once('includes/lib_payment.php');
    include_once(ROOT_PATH . 'includes/lib_transaction.php');
    /* 获取运动类别列表 start */
    __load("SportcatService");
    $sportcat_obj = new SportcatService();
    $sportcat_list = $sportcat_obj->get_sportcat_list();
    //初始化日志
    $logHandler= new CLogFileHandler(ROOT_PATH ."/logs/".date('Y-m-d').'.log');
    $log = Logger::Init($logHandler, 15);
    //判断购物车内是否有商品
    $user_id = $_SESSION['user_id'];
    $cart = get_cart_info_all($user_id);
    if(!empty($cart)) { 
        /* 如果使用库存，且下订单时减库存，则减少库存 */
        if ($_CFG['use_storage'] == '1') {
            /* 检查商品库存 */
            $res = check_cart_goods_number();
            if (!empty($res)) {
                show_message( "库存不足,请重新选择商品", '', '', 'warning');
                exit;
            }
            __load("CartService");
            $cart_obj = new CartService();
            $cart_info = $cart_obj->get_cart_info();
            $cart_info['combo'] = $cart_obj->get_cart_combo();
//                                echo "<pre>";
//        print_r($combo_info);die("222");
            foreach ($cart_info['combo'] as $combo) {
                $json_res = json_decode($combo['combo_tickets'], true);
                foreach ($json_res['default'] as $value) {
                    $id = explode('|', $value);
                    $goods_id = $id[1];
                    //查询这张票剩余库存
                    $goods_number = $GLOBALS['db']->getOne("SELECT goods_number FROM ".$GLOBALS['ecs']->table('goods')." WHERE goods_id=$goods_id");
                    if(($goods_number-$combo['goods_number'])<0){
                        show_message( "库存不足,请重新选择商品", '', '', 'warning');
                        exit;
                    }else {
                        $sql = "UPDATE " . $GLOBALS['ecs']->table('goods') . "SET goods_number=goods_number-$combo[goods_number] WHERE goods_id=$goods_id";
                        $GLOBALS['db']->query($sql);
                    }
                }
            }
            foreach ($cart_info['ticket']['t'] as $ticker) {
                //查询这张票剩余库存
                $goods_number = $GLOBALS['db']->getOne("SELECT goods_number FROM ".$GLOBALS['ecs']->table('goods')." WHERE goods_id=$ticker[goods_id]");
                if(($goods_number-$ticker['goods_number'])<0){
                    show_message( "库存不足,请重新选择商品", '', '', 'warning');
                    exit;
                }else {
                    $sql = "UPDATE " . $GLOBALS['ecs']->table('goods') . "SET goods_number=goods_number-$ticker[goods_number] WHERE goods_id=$ticker[goods_id]";
                    $GLOBALS['db']->query($sql);
                }
            }
             foreach ($cart_info['goods'] as $ticker) {
                //查询这张票剩余库存
                $goods_number = $GLOBALS['db']->getOne("SELECT goods_number FROM ".$GLOBALS['ecs']->table('goods')." WHERE goods_id=$ticker[goods_id]");
                if(($goods_number-$ticker['goods_number'])<0){
                    show_message( "库存不足,请重新选择商品", '', '', 'warning');
                    exit;
                }else {
                    $sql = "UPDATE " . $GLOBALS['ecs']->table('goods') . "SET goods_number=goods_number-$ticker[goods_number] WHERE goods_id=$ticker[goods_id]";
                    $GLOBALS['db']->query($sql);
                }
            }
            foreach ($cart_info['goods'] as $goods) {
                $sql = "UPDATE " . $GLOBALS['ecs']->table('products') . "SET product_number=product_number-$goods[goods_number] WHERE goods_id=$goods[goods_id] AND goods_attr=$goods[goods_attr_id]";
                $GLOBALS['db']->query($sql);
            }
        }
        $user_id = $_SESSION['user_id'];
        $order = array(
            'user_id' => $_SESSION['user_id'],
            'get_ticket_type' => $_POST['get_ticket_type'],
            'pay_id' => $_POST['pay_id'],
            'order_status' => OS_UNCONFIRMED,
            'shipping_status' => SS_UNSHIPPED,
            'pay_status' => PS_UNPAYED
        );

        $address_info = get_consignee_info($_POST['address_id']);
        save_consignee($_POST['address_id']);//添加默认地址
        $order['consignee'] = $address_info['consignee'];
        $order['country'] = $address_info['country'];
        $order['province'] = $address_info['province'];
        $order['city'] = $address_info['city'];
        $order['district'] = $address_info['district'];
        $order['card_type'] = $address_info['card_type'];
        $order['card_num'] = $address_info['card_num'];
        $order['email'] = $address_info['email'];
        $order['mobile'] = $address_info['mobile'];
        $order['order_sn'] = get_order_sn(); //获取新订单号
        $_SESSION['order_sn'] = $order['order_sn'];
        if ($user_id > 0) {
            $user_info = user_info($user_id);
            $order['surplus'] = min($order['surplus'], $user_info['user_money'] + $user_info['credit_line']);
            if ($order['surplus'] < 0) {
                $order['surplus'] = 0;
            }
        } else {
            $order['surplus'] = 0;
            $order['integral'] = 0;
        }

        /* 订单中的总额 */
        $total = order_fee($order, $cart_goods, $consignee);
        $order['bonus'] = $total['bonus'];
        $order['goods_amount'] = $total['goods_price'];
        $order['discount'] = $total['discount'];
        $order['surplus'] = $total['surplus'];
        $order['tax'] = $total['tax'];
        /* 支付方式 */
        if ($order['pay_id'] > 0 && $order['pay_id'] != 99) {
            $payment = payment_info($order['pay_id']);
            $order['pay_name'] = addslashes($payment['pay_name']);
        }else{
            $order['pay_name'] = '微信';
        }
        $order['pay_fee'] = $total['pay_fee'];
        $order['cod_fee'] = $total['cod_fee'];
        $cart_money = $cart_obj->get_cart_money();
        $order['goods_amount'] = $cart_money['all'];
        $order['order_amount'] = $cart_money['all'];
        $order['address_id'] = $_POST['address_id'];
        $order['inv_type'] = $_POST['inv_type'];
        $order['inv_payee'] = $_POST['inv_payee'];
        $order['inv_content'] = $_POST['inv_content'];
//    $order['consignee'] = $_POST['consignee'];
        $order['add_time'] = gmtime();
        $order['confirm_time'] = gmtime();
        $order['address'] = $_POST['address'];
        $order['zipcode'] = $_POST['zipcode'];
        $order['tel'] = $_POST['tel'];
        $order['is_pc_mobile'] = 1;
        __load("OrderService");
        $order_obj = new OrderService();
        $order_obj->set_order_info($order);
        /* 取得支付信息，生成支付代码 */
        if ($order['order_amount'] > 0) {
            if($order['pay_id'] == 99 || $order['pay_id'] == 6) {
                $smarty->assign('pay_id', $order['pay_id']);
            }else{
                $payment = payment_info($order['pay_id']);
                include_once('includes/modules/payment/' . $payment['pay_code'] . '.php');
                $pay_obj = new $payment['pay_code'];
                if ($payment['pay_code'] == "bank") {
                    $pay_online = $pay_obj->get_code($order, $payment);
                } else {
                    $pay_online = $pay_obj->get_code($order, unserialize_config($payment['pay_config']));
                }

                $order['pay_desc'] = $payment['pay_desc'];
                $smarty->assign('pay_online', $pay_online);
            }
        }
        $smarty->assign('order_info', $order);
        $order_id = $order_obj->get_order_id($order['order_sn']);
        /* 购物车中的商品 */
        $cart_info = $cart_obj->get_cart_info();

        $ticket_array = $cart_info['ticket']["t"];
        //存储门票
        $order_ticket = array();

        foreach ($ticket_array as $key => $value) {

            $order_ticket['order_id'] = $order_id;
            $order_ticket['goods_id'] = $value['goods_id'];
            $order_ticket['goods_name'] = $value['goods_name'];
            $order_ticket['goods_price'] = $value['goods_price'];
            $order_ticket['goods_number'] = 1;
            $order_ticket['type'] = 'ticket';
            Logger::DEBUG("order_ticket:" . json_encode($order_ticket));
            for ($i = 1; $i <= $value['goods_number']; $i++) {
                $order_ticket['code'] = $order['order_sn'] . $key . $i;
                $order_obj->set_order_ticket($order_ticket);
            }
        }
        //存储商品信息。
        $order_obj->set_order_goods($order_id);
        //存储机票信息。
        $plane_array = $cart_info['plane'];
        foreach ($plane_array as $key => $value) {
            $order_obj->set_order_plane(array(
                "from_city" => $value['from_city'],
                "to_city" => $value['to_city'],
                "fly_date" => $value['fly_date'],
                "air_id" => $value['air_id'],
                "space_id" => $value['space_id'],
                "goods_number" => $value['goods_number'],
                "goods_price" => $value['goods_price'],
                "order_id" => $order_id
            ));
        }
        //存储酒店
        $hotel_array = $cart_info['hotel'];
        foreach ($hotel_array as $key => $value) {
            $order_obj->set_order_hotel(array(
                "hotel_id" => $value['hotel_id'],
                "room_id" => $value['room_id'],
                "room_num" => $value['room_num'],
                "people" => $value['people'],
                "goods_number" => $value['goods_number'],
                "room_type" => $value['room_type'],
                "goods_price" => $value['goods_price'],
                "hotel_start_date" => $value['hotel_start_date'],
                "hotel_end_date" => $value['hotel_end_date'],
                "order_id" => $order_id
            ));
        }
        //存储套餐门票信息
        $combo_info = $cart_obj->get_cart_combo();
//        echo "<pre>";
//        print_r($combo_info);die;
        foreach ($combo_info as $key => $value) {
            $combo_info_id = $value['combo_id'];
            $combo['order_id'] = $order_id;
            $combo['combo_id'] = $combo_info_id;
            $combo['order_combo_number'] = $value['goods_number'];
            $combo['order_combo_price'] = $value['goods_price'];
            $combo['order_combo_money'] = number_format($value['goods_price']*$value['goods_number'],2,".","");
            $order_obj->set_order_combo_info($combo);
            if($value['goods_number'] > 0){
                for($i = 0;$i<$value['goods_number'];$i++){
                    $josn_res = objarray_to_array(json_decode($value['combo_tickets']));
                    foreach ($josn_res['default'] as $key => $val) {
                        $id = explode('|', $val);
                        $goods_id = $id[1];
                        $sql = "SELECT * FROM " . $GLOBALS['ecs']->table('goods') . "WHERE goods_id = '" . $goods_id . "'";
                        $res = $GLOBALS['db']->getAll($sql);
                        $order = array();
                        $order_combo['order_id'] = $order_id;
                        $order_combo['combo_id'] = $combo_info_id;
                        $order_combo['goods_id'] = $res[0]['goods_id'];
                        $order_combo['goods_name'] = $res[0]['goods_name'];
                        $order_combo['goods_price'] = $res[0]['shop_price'];
                        $order_combo['goods_number'] = 1;
                        $order_combo['type'] = 'combo';
                        $order_combo['game_id'] = $res[0]['game_id'];
                        $order_obj->set_order_combo($order_combo);
                    }
                }
            }
        }
        /*发送短信*/
        //----hechengbin --- start---
        require_once(ROOT_PATH . 'includes/lib_sms.php');
        if(substr($order['mobile'],0,2) == '86'){//hechengbin 9.28 修改
            $ret = sendsms($order['mobile'], '您的订单已提交成功！请您在72小时内付款，逾期我们将不再为您保留，需重新预定。');
        }else{
            $ret = sendsms($order['mobile'], 'You have succesfully submitted the order! Please proceed the payment in 72 hours. The tickets can not be reserved for the deferred payment and please re-order.');
        }
        /*发送短信end*/
        /*发送邮件*/
        __load("BearerService");
        $bearer = new BearerService();
        $bearer->send_mail('order_done', array("to" => array($order['email'])));
        /*发送邮件ok*/

        /* 清空购物车 */
        clear_cart($flow_type);
        /* 清除缓存，否则买了商品，但是前台页面读取缓存，商品数量不减少 */
        clear_all_files();
        /* 插入支付日志 */
        $order['log_id'] = insert_pay_log($new_order_id, $order['order_amount'], PAY_ORDER);
        unset($_SESSION['flow_consignee']); // 清除session中保存的收货人信息
        unset($_SESSION['flow_order']);
        unset($_SESSION['direct_shopping']);
        $smarty->assign('order', $order);
        $smarty->display("sk_order_done.dwt");
        exit;
    }else{
        show_message('您还没有选择商品~','','','warning');
    }
} elseif ($act == "pay_done") {
    $pay_done = $_POST;
    Logger::DEBUG("pay_done:". json_encode($pay_done));
    if ($pay_done['respCode'] == 00) {
        $pay_done['txnAmt'] = number_format($pay_done['txnAmt'] / 100, 2, ".", "");
        $smarty->assign('pay_done', $pay_done);
        $smarty->display("sk_pay_done.dwt");
        exit;

    } else {


    }
}elseif($act == 'info_order'){
    $order_sn = $_REQUEST['order_sn'];
    //根据订单号查询订单信息
    $order_info = get_order_jine($order_sn);
    $json = new JSON();
    exit($json->encode(array('order_sn'=>$order_info['order_sn'],'order_amount'=>$order_info['order_amount'])));
}elseif($act == 'info_order_detail'){
    $order_sn = $_REQUEST['order_sn'];
    //根据订单号查询订单信息
    $order_info = get_order_jine($order_sn);
    $json = new JSON();
    exit($json->encode(array('qc_data'=>$order_info['qc_data'],'order_amount'=>$order_info['order_amount'])));
}

//获得订单金额及订单号
function get_order_jine($order_sn){
    return $GLOBALS['db']->getRow("SELECT order_amount,qc_data FROM ".$GLOBALS['ecs']->table('order_info')." WHERE order_sn=$order_sn");
}


function get_address($user_id)
{
    return $GLOBALS['db']->getAll("SELECT u.user_name,ud.* FROM " . $GLOBALS['ecs']->table("user_address") . "AS ud," . $GLOBALS['ecs']->table("users") . "AS u WHERE ud.user_id = $user_id AND u.user_id = ud.user_id");
}
function get_default_address($user_id)
{
    return $GLOBALS['db']->getOne("SELECT address_id FROM " . $GLOBALS['ecs']->table("users") . " WHERE user_id = $user_id");
}


/**
 *
 * @param $number 单场次最少可以买几张票
 */
function check_ticket_number($number,$game_id)
{
    $sql = "SELECT * FROM " . $GLOBALS['ecs']->table('cart') .
        " WHERE session_id = '" . SESS_ID . "' AND goods_type='ticket' AND goods_number>$number AND game_id=$game_id";
    return $GLOBALS['db']->getAll($sql);
}
function check_ticket_number_pc($number,$game_id)
{
    $sql = "SELECT * FROM " . $GLOBALS['ecs']->table('cart') .
        " WHERE session_id = '" . SESS_ID . "' AND goods_type='ticket' AND goods_number>$number AND game_id=$game_id ";
    return $GLOBALS['db']->getAll($sql);
}
//将对象数组转换成普通数组
function objarray_to_array($obj) {
    $ret = array();
    foreach ($obj as $key => $value) {
        if (gettype($value) == "array" || gettype($value) == "object"){
            $ret[$key] =  objarray_to_array($value);
        }else{
            $ret[$key] = $value;
        }
    }
    return $ret;
}
//获取用户购买的球票
/**
 * @param $game_id
 * @param $user_id
 * @return mixed
 */
//获取用户购买的球票
function get_order_info($user_id,$goods_id){
    $sql = "SELECT ot.* FROM ".$GLOBALS['ecs']->table('order_info')."AS oi,".$GLOBALS['ecs']->table('order_ticket')."AS ot WHERE oi.user_id='".$user_id."' AND oi.order_id=ot.order_id AND oi.order_status != 2 AND ot.goods_id=$goods_id";
    return $GLOBALS['db']->getAll($sql);
}
//获取用户购物车中的球票
function get_cart_info($goods_id,$user_id){
    $sql = "SELECT goods_number FROM ".$GLOBALS['ecs']->table('cart')." WHERE user_id='".$user_id."' AND  goods_id=$goods_id";
    return $GLOBALS['db']->getOne($sql);
}

//获取用户购物车内全部商品
function get_cart_info_all($user_id){
    $sql = "SELECT * FROM " . $GLOBALS['ecs']->table('cart') . "WHERE user_id = '".$user_id."' ";
    return $GLOBALS['db']->getAll($sql);
}
//获取用户的属性是否为代理商
function get_user($user_id){
    $sql = "SELECT type FROM sk_users WHERE user_id=$user_id";
    return $GLOBALS['db']->getOne($sql);
}

//获取赛事下赛程的场次
function get_game_number($game_id){
    $sql = "SELECT * FROM ".$GLOBALS['ecs']->table('schedule')." WHERE game_id=".$game_id;
    return $GLOBALS['db']->getAll($sql);
}

//判断购物车是否有商品
function get_cart_num($sess){
    $sql="SELECT c.* FROM sk_cart AS c WHERE c.session_id = '$sess' AND (c.goods_type = 'ticket' OR c.goods_type='goods' OR c.goods_type='plane' OR c.goods_type = 'combo')";
    return $GLOBALS['db']->getAll($sql);
}
//查询当前赛事卖了几张票
function get_order_ticket_number($user_id,$game_id){
    $sql = "SELECT * FROM " .$GLOBALS['ecs']->table('order_info'). "AS oi," .$GLOBALS['ecs']->table('order_ticket')."AS ot,".$GLOBALS['ecs']->table('goods')."AS g WHERE oi.user_id=$user_id AND oi.order_id=ot.order_id AND oi.order_status != 2 AND ot.goods_id=g.goods_id AND g.game_id=$game_id";
    return $GLOBALS['db']->getAll($sql);
}
//查询购物车中当前赛事有几张票
function get_cart_ticket_num($user_id,$game_id){
    $sql = "SELECT * FROM ".$GLOBALS['ecs']->table('cart'). " WHERE user_id=$user_id AND game_id=$game_id";
    return $GLOBALS['db']->getAll($sql);
}

?>