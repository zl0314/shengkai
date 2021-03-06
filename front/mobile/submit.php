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
require(dirname(__FILE__) . '/includes/init.php');
$act = empty($_REQUEST['act']) ? "index" : trim($_REQUEST['act']);
/* 获取赛事列表 start */
__load("GameService");
$game_obj = new GameService();
$game_list = $game_obj->get_list();
if (!empty($game_id)) {
    $game = $game_obj->get_game($game_id);

    $smarty->assign('game', $game);

    //根据赛事自动查询城市
    __load("RegionService");
    $RegionService = new RegionService();
    $schedule_id = $RegionService->get_schedule($game_id);

    $smarty->assign('schedule_id', $schedule_id);
}
/* 获取赛事列表 end */
$combo = $GLOBALS['db']->getALL('select * from sk_combo');
$sportcat_list = $GLOBALS['db']->getAll('SELECT id,name FROM sk_sportcat where is_display=1');
$smarty->assign('sportcat_list', $sportcat_list);
if ($act == "done") {
    include_once('includes/lib_order.php');
    include_once('includes/lib_clips.php');
    include_once('includes/lib_payment.php');
    include_once(ROOT_PATH . 'includes/lib_transaction.php');

    //判断购物车内是否有商品
    $user_id = $_SESSION['user_id'];
    $cart = get_cart_info_all($user_id);
    if (!empty($cart)) {
        /* 如果使用库存，且下订单时减库存，则减少库存 */
        if ($_CFG['use_storage'] == '1') {
            /* 检查商品库存 */
            $res = check_cart_goods_number();
            if (!empty($res)) {
                mobile_show_message("库存不足,请重新选择商品", '', '', 'warning');
                exit;
            }
            __load("CartService");
            $cart_obj = new CartService();
            $cart_info = $cart_obj->get_cart_info();
            $cart_info['combo'] = $cart_obj->get_cart_combo();
            foreach ($cart_info['combo'] as $combo) {
                $json_res = json_decode($combo['combo_tickets'], true);
                foreach ($json_res['default'] as $value) {
                    $id = explode('|', $value);
                    $goods_id = $id[1];
                    //查询球票数量
                    $sql = "SELECT goods_number FROM " . $GLOBALS['ecs']->table('goods') . " WHERE goods_id=$goods_id";
                    $res = $GLOBALS['db']->getOne($sql);
                    $num = $res - $combo['goods_number'];
                    if ($num >= 0) {
                        $sql = "UPDATE " . $GLOBALS['ecs']->table('goods') . "SET goods_number=goods_number-$combo[goods_number] WHERE goods_id=$goods_id";
                        $GLOBALS['db']->query($sql);
                    } else {
                        mobile_show_message("库存不足,请重新选择套餐", '', '', 'warning');
                        exit;
                    }
                }
            }
            foreach ($cart_info['ticket']['t'] as $ticker) {
                //查询球票数量
                $sql = "SELECT goods_number FROM " . $GLOBALS['ecs']->table('goods') . " WHERE goods_id=$ticker[goods_id]";
                $res = $GLOBALS['db']->getOne($sql);
                $num = $res - $ticker['goods_number'];
                if ($num >= 0) {
                    $sql = "UPDATE " . $GLOBALS['ecs']->table('goods') . "SET goods_number=goods_number-$ticker[goods_number] WHERE goods_id=$ticker[goods_id]";
                    $GLOBALS['db']->query($sql);
                } else {
                    mobile_show_message("库存不足,请重新选择商品", '', '', 'warning');
                    exit;
                }
            }
            foreach ($cart_info['goods'] as $goods) {
                $sql = "UPDATE " . $GLOBALS['ecs']->table('products') . "SET product_number=product_number-$goods[goods_number] WHERE goods_id=$goods[goods_id] AND goods_attr=$goods[goods_attr_id]";
                $GLOBALS['db']->query($sql);
            }
        }
        $user_id = $_SESSION['user_id'];
        $order = array(
            'user_id' => $user_id,
            'get_ticket_type' => $_POST['get_ticket_type'],
            'pay_id' => $_POST['pay_id'],
            'order_status' => OS_UNCONFIRMED,
            'shipping_status' => SS_UNSHIPPED,
            'pay_status' => PS_UNPAYED
        );

        //如果来自第三方来源的订单
        if (isset($_SESSION['spm'])) {
            $order['spm'] = $_SESSION['spm'];
        }

        $address_id = $_POST['address_id'];
        $address_info = get_consignee_info($address_id);
        save_consignee($address_id);//添加默认地址
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

        if ($order['pay_id'] > 0) {
            $payment = payment_info($order['pay_id']);

            $order['pay_name'] = addslashes($payment['pay_name']);
            if ($order['pay_id'] == '99') {
                $order['pay_name'] = '微信';
                $smarty->assign('is_wx_pay', true);
            } elseif ($order['pay_id'] == '6') {
                $order['pay_name'] = '支付宝';
                $smarty->assign('is_wx_pay', false);
            } elseif ($order['pay_id'] == '9') {
                $order['pay_name'] = '掌上生活';
                $smarty->assign('is_cmbuat_pay', false);
            }

            if ($order['pay_name'] == '线下支付') {
                $smarty->assign('is_pay', $order['pay_name']);
            }
        }
        __load("CartService");
        $cart_obj = new CartService();
        $cart_money = $cart_obj->get_cart_money();
        $order['goods_amount'] = $cart_money['all'];
        $_SESSION['order_amout'] = $order['order_amount'] = $cart_money['all'];
        if (empty($_POST['address_id'])) {
            mobile_show_message("您还没有添加联系人，请添加联系人", '', '', 'warning');
            exit;
        } else {
            $order['address_id'] = $_POST['address_id'];
        }
        $order['inv_type'] = $_POST['inv_type'];
        $order['inv_payee'] = $_POST['inv_payee'];
        $order['inv_content'] = $_POST['inv_content'];
        $order['add_time'] = gmtime();
        $order['confirm_time'] = gmtime();
        $order['is_pc_mobile'] = 2;
        __load("OrderService");
        $order_obj = new OrderService();
        $order_obj->set_order_info($order);
        /*发送短信*/

        /* 取得支付信息，生成支付代码 */
        if ($_SESSION['order_amout'] > 0) {
            if ($order['pay_id'] != 99 && $order['pay_id'] != 6 && $order['pay_id'] != 9) {
                $payment = payment_info($order['pay_id']);
                include_once('includes/modules/payment/' . $payment['pay_code'] . '.php');
                $pay_obj = new $payment['pay_code'];
                if ($payment['pay_code'] == "bank") {
                    $pay_online = $pay_obj->get_code($order, $payment);
                    $smarty->assign('pay_online', $pay_online);
                }
            }

        }
        //----hechengbin --- start---
        require_once(ROOT_PATH . 'includes/lib_sms.php');
        if (substr($order['mobile'], 0, 2) == '86') {
            $ret = sendsms($order['mobile'], '您的订单已提交成功！请您在72小时内付款，逾期我们将不再为您保留，需重新预定。');
        } else {
            $ret = sendsms($order['mobile'], 'You have succesfully submitted the order! Please proceed the payment in 72 hours. The tickets can not be reserved for the deferred payment and please re-order.');
        }
        /*发送短信end*/
        $smarty->assign('order', $order);
        /*发送邮件*/
        __load("BearerService");
        $bearer = new BearerService();
        $bearer->send_mail('order_done', array("to" => array($order['email']), "sub" => array('%order_sn%' => array($order['order_sn']))));
        $order_id = $order_obj->get_order_id($order['order_sn']);
        /* 购物车中的商品 */
        $cart_info = $cart_obj->get_cart_info();
        if ($cart_info['ticket']) {
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
                for ($i = 1; $i <= $value['goods_number']; $i++) {
                    $order_ticket['code'] = $order['order_sn'] . $key . $i;
                    $order_obj->set_order_ticket($order_ticket);
                }
            }
        }
        if ($cart_info['goods']) {
            //存储商品信息。
            $order_obj->set_order_goods($order_id);
        }
        //存储套餐门票信息
        $combo_info = $cart_obj->get_cart_combo();
        if ($combo_info) {
            foreach ($combo_info as $key => $value) {
                $combo_info_id = $value['combo_id'];
                $combo['order_id'] = $order_id;
                $combo['combo_id'] = $combo_info_id;
                $combo['order_combo_number'] = $value['goods_number'];
                $combo['order_combo_price'] = $value['goods_price'];
                $combo['order_combo_money'] = number_format($value['goods_price'] * $value['goods_number'], 2, ".", "");
                $order_obj->set_order_combo_info($combo);
                if ($value['goods_number'] > 0) {
                    for ($i = 0; $i < $value['goods_number']; $i++) {
                        $json_res = objarray_to_array(json_decode($value['combo_tickets']));
                        foreach ($json_res['default'] as $k => $val) {
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
        }

        /* 如果使用库存，且下订单时减库存，则减少库存 */
//    if ($_CFG['use_storage'] == '1' && SDT_PLACE) {
//        mobile_change_order_goods_storage($order_id, true, SDT_PLACE);
//    }

        /* 清空购物车 */
        $flow_type = isset($_SESSION['flow_type']) ? intval($_SESSION['flow_type']) : CART_GENERAL_GOODS;
        clear_cart($flow_type);
        unset($_SESSION['order_amout']);
        /* 清除缓存，否则买了商品，但是前台页面读取缓存，商品数量不减少 */
        clear_all_files();

        //如果是招行掌上支付,则显示招行支付信息
        if ($order['pay_id'] == 9) {
//
//            require '../payment/cmbuat/cmbuatpay.class.php';
//
//            //新增支付记录
//            $order_pay_log_sn = $order['order_sn'] . date('His');
//            $params = array(
//                'order_sn' => $order_pay_log_sn,
//                'order_name' => '门票',
//                'amount' => $order_info['order_amount'] * 100,
//            );
//            //招行支付如果超过了5w,那么此次支付设置最大金额为5w
//            if ($order['order_amount'] > 50000) {
//                $params['amount'] = 50000 * 10;
//            }
//
//            $user_id = $_SESSION['user_id'];
//            if ($user_id == 323) {
//                $order['amount'] = 10;
//            }
//
//            $cmbuat = new cmbuatpay();
//            $cmbuatPayProtocol = $cmbuat->getAppPay($params);
//
//            //写入支付记录
//            $res = insert_order_pay_log($order['order_id'], $order_pay_log_sn, $order['pay_id'], $params['amount'], $user_id);
//
//            $smarty->assign('cmbuat_protocol', $cmbuatPayProtocol);
            $smarty->assign('cmbuat_protocol', "submit.php?act=cmbuat_pay_status&order_sn={$order['order_sn']}");
        }

        $namel = "<img src='images/logo.png'>";
        $smarty->assign('name1', $namel);
        $smarty->display("submit.html");
        exit;
    } else {
        mobile_show_message('您还没有选择商品~', '', '', 'warning');
    }
} elseif ($act == 'tijiao') {
    $order_id = $_REQUEST['order_id'];
    if (isset($_REQUEST['ticket'])) {
        $ticket = $_REQUEST['ticket'];
        $bearer = $_REQUEST['bearer_id'];
        for ($index = 0; $index < count($ticket); $index++) {
            $ticket_id = $ticket[$index];
            $bearer_id = $bearer[$index];
            $sql = "UPDATE sk_order_ticket SET bearer_id='" . $bearer_id . "' WHERE order_id='" . $order_id . "' AND rec_id='" . $ticket_id . "'";
            $GLOBALS['db']->query($sql);
        }
    }
    //根据订单号获取订单联系人
    __load('OrderService');
    $order_obj = new OrderService();
    $order_lianxiren = $order_obj->get_order_info($order_id);
    $smarty->assign('order_detail', $order_lianxiren);
    $namel = "<img src='images/logo.png'>";
    $smarty->assign('name1', $namel);
    $smarty->display("success.html");
} elseif ($act == 'info_order_detail') {
    $order_sn = $_REQUEST['order_sn'];
    //根据订单号查询订单信息
    $order_info = get_order_jine($order_sn);
    print_r($order_info);
    exit;
} elseif ($act == 'refresh_cmbuat_protocol') {
    $order_sn = $_REQUEST['order_sn'];
    if (!is_numeric($order_sn)) {
        echo 'ACCESS DENIED!';
        exit;
    }

    //查询订单信息
    $order_detail = get_order_info_by_order_sn($order_sn);
    if (empty($order_detail)) {
        echo 'ACCESS DENIED!';
        exit;
    }

    //检查用户是否一致
    $user_id = $_SESSION['user_id'];
    if ($order_detail['user_id'] != $user_id) {
        echo 'ACCESS DENIED!';
        exit;
    }

    $curr_path = dirname(__FILE__);
    require_once $curr_path .'/../payment/cmbuat/cmbuatpay.class.php';
    $cmbuatpay = new cmbuatpay();

    $amount = number_format($order_detail['order_amount'] - $order_detail['money_paid'], 2, '.', '');

    //招行支付如果超过了5w,那么此次支付设置最大金额为5w
    if ($amount > 50000) {
        $amount = 50000;
    }

    if ($user_id == 323) {
//        $order['amount'] = 10;
    }

    //新增支付记录
    $order_pay_log_sn = $order_sn . date('His');
    $params = array(
        'order_sn' => $order_pay_log_sn,
        'order_name' => '门票',
        'amount' => $amount * 100,
    );

    $cmbuat = new cmbuatpay();
    $cmbuatPayProtocol = $cmbuat->getAppPay($params);

    //写入支付记录
    $res = insert_order_pay_log($order_detail['order_id'], $order_pay_log_sn, $order_detail['pay_id'], $order['amount'], $order_detail['user_id']);
    echo json_encode(array('protocol' => $cmbuatPayProtocol));
    exit;
} elseif ($act == 'cmbuat_pay_status') {
    $order_sn = $_REQUEST['order_sn'];
    if (!is_numeric($order_sn)) {
        echo 'ACCESS DENIED!';
        exit;
    }

    //查询订单信息
    $order_detail = get_order_info_by_order_sn($order_sn);
    if (empty($order_detail)) {
        echo 'ACCESS DENIED!';
        exit;
    }

    //检查用户是否一致
    $user_id = $_SESSION['user_id'];
    if ($order_detail['user_id'] != $user_id) {
        echo 'ACCESS DENIED!';
        exit;
    }

    $amount = $order_detail['order_amount'] - $order_detail['money_paid'];

    //招行支付如果超过了5w,那么此次支付设置最大金额为5w
    if ($amount > 50000) {
        $amount = 50000;
    }

    $smarty->assign('order_detail', $order_detail);
    $smarty->assign('amount', $amount);
    $smarty->display("cmbuat_pay_status.html");
    exit;
}
//将对象数组转换成普通数组
function objarray_to_array($obj)
{
    $ret = array();
    foreach ($obj as $key => $value) {
        if (gettype($value) == "array" || gettype($value) == "object") {
            $ret[$key] = objarray_to_array($value);
        } else {
            $ret[$key] = $value;
        }
    }
    return $ret;
}

//获取用户购物车内全部商品
function get_cart_info_all($user_id)
{
    $sql = "SELECT * FROM " . $GLOBALS['ecs']->table('cart') . "WHERE user_id = '" . $user_id . "' ";
    return $GLOBALS['db']->getAll($sql);
}

//获得订单金额及订单号
function get_order_jine($order_sn)
{
    return $GLOBALS['db']->getOne("SELECT order_amount FROM " . $GLOBALS['ecs']->table('order_info') . " WHERE order_sn=$order_sn");
}

/**
 * 新增一条支付记录
 *
 * @param $order_id
 * @param $order_sn
 * @param $pay_type
 * @param $pay_fee
 * @param $user_id
 * @return mixed
 */
function insert_order_pay_log($order_id, $order_sn, $pay_type, $pay_fee, $user_id)
{
    $sql = "INSERT INTO " . $GLOBALS['ecs']->table('order_pay_log') . " SET order_id = '{$order_id}', trade_no = '{$order_sn}', pay_type = '{$pay_type}', pay_fee = '{$pay_fee}', user_id = '{$user_id}'";
    return $GLOBALS['db']->query($sql);
}

/**
 * 通过order_sn获取订单
 *
 * @param $order_sn
 * @return array
 */
function get_order_info_by_order_sn($order_sn)
{
    $sql = "SELECT * FROM " . $GLOBALS['ecs']->table('order_info') . " WHERE order_sn = {$order_sn}";
    $res = $GLOBALS['db']->getRow($sql);
    return empty($res) ? array() : $res;
}