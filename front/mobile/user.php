<?php

/**
 * ECSHOP 用户中心
 * ============================================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: user.php 16643 2009-09-08 07:02:13Z liubo $
 */

define('IN_ECS', true);
ini_set('display_errors', 1);
require(dirname(__FILE__) . '/includes/init.php');
//require(ROOT_PATH . '/includes/init.php');
//require(ROOT_PATH . '/includes/integrate.php');

/* 载入语言文件 */
require_once(ROOT_PATH . 'languages/' . $_CFG['lang'] . '/user.php');
require_once(ROOT_PATH . 'includes/modules/payment/func/log.php');
//初始化日志
$logHandler = new CLogFileHandler(ROOT_PATH . "/logs/" . date('Y-m-d') . '.log');
$log = Logger::Init($logHandler, 15);
$act = isset($_REQUEST['act']) ? trim($_REQUEST['act']) : '';
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
// 不需要登录的操作或自己验证是否登录（如ajax处理）的act
$not_login_arr = array('login', 'show_pdf_info_agent', 'show_pdf_info', 'act_login', 'register', 'register', 'act_register', 'act_edit_password', 'get_password', 'send_pwd_email', 'send_pwd_mobile', 'password', 'signin', 'add_tag', 'collect', 'return_to_cart', 'logout', 'email_list', 'validate_email', 'send_hash_mail', 'order_query', 'is_registered', 'check_email', 'clear_history', 'qpassword_name', 'mpassword_name', 'get_passwd_question', 'check_answer', 'is_cunzai', 'is_verifycode', 'is_tihuan');
/* 未登录处理 */
if (empty($_SESSION['user_id'])) {
    if (!in_array($action, $not_login_arr)) {
        header("Location: http://www.skstravel.com/mobile/");
    }
}

/* 获取赛事列表 end */
$combo = $GLOBALS['db']->getALL('select * from sk_combo');
$sportcat_list = $GLOBALS['db']->getAll('SELECT id,name FROM sk_sportcat where is_display=1');
$smarty->assign('sportcat_list', $sportcat_list);

/* 用户登陆 */
if ($act == 'do_login') {
    $login_faild = 0;
    $user_name = !empty($_POST['username']) ? $_POST['username'] : '';
    $pwd = !empty($_POST['pwd']) ? $_POST['pwd'] : '';
    $remember = !empty($_POST['remember']) ? $_POST['remember'] : '';
    $mobile_phone = !empty($_POST['mobile_phone']) ? $_POST['mobile_phone'] : '';
    $openid = !empty($_SESSION['wx_openid']) ? $_SESSION['wx_openid'] : '';
    Logger::DEBUG("username and pwd id :" . $user_name . " and " . $pwd);
    if (empty($user_name) || empty($pwd)) {
        $login_faild = 1;
    } else {
        if ($GLOBALS['user']->login($user_name, $pwd, $remember) > 0) {
            $GLOBALS['user']->set_session($user_name);
            $GLOBALS['user']->set_cookie($user_name, null);

            update_user_info();
            if ($openid) {
                update_user_info_openid();
                insert_user_info_wx($user_name);
            }
            show_user_center();
        } else {
            $login_faild = 1;
        }
    }
    if ($login_faild) {
        mobile_show_login_message("账号或者密码错误，请重新输入", '', '', 'warning');
    }
} /* 查看订单列表 */
elseif ($act == 'order_list' || $act == "default") {
    include_once(ROOT_PATH . 'includes/lib_transaction.php');
    $user_id = $_SESSION['user_id'];

    $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;

    $record_count = $db->getOne("SELECT COUNT(*) FROM " . $ecs->table('order_info') . " WHERE user_id = '$user_id'");

    $pager = get_pager('user.php', array('act' => $act), $record_count, $page);

    $orders = get_user_orders($user_id, $pager['size'], $pager['start']);
    $orders_info = get_order_all_goods_info($user_id);

    //获取合同编号
    foreach ($orders as $key => $value) {
        $contract_no = $db->getOne("SELECT contract_no FROM " . $ecs->table('contract') . " WHERE user_id = '$user_id' && order_sn= '{$value['order_sn']}'");
        $orders[$key]['contract_no'] = $contract_no;
    }

    $order = count(get_order($user_id));
    if ($order == 0) {
        mobile_show_message("您还没有订单，请购买商品", '', '', 'warning');
    } else {
        $orders_info = get_order_all_goods_info($user_id);
    }
    $number = get_cart_num(SESS_ID);
    $num = 0;
    foreach ($number as $value) {
        $num += $value['goods_number'];
    }
    $smarty->assign('num', $num);
    $smarty->assign('yuanquan', 1);
    $merge = get_user_merge($user_id);
    $smarty->assign('merge', $merge);
    $smarty->assign('pager', $pager);
    $smarty->assign('orders', $orders);
    $name = "我的订单";
    $smarty->assign('name', $name);
    $smarty->display('order_list.html');
} elseif ($act == 'user_address') {
    $user_id = $_SESSION['user_id'];
    include_once(ROOT_PATH . 'includes/lib_transaction.php');
    include_once(ROOT_PATH . 'languages/' . $_CFG['lang'] . '/shopping_flow.php');
    $smarty->assign('lang', $_LANG);
    $number = get_cart_num(SESS_ID);
    $num = 0;
    foreach ($number as $value) {
        $num += $value['goods_number'];
    }
    $smarty->assign('num', $num);
    $smarty->assign('yuanquan', 1);
    /* 取得国家列表、商店所在国家、商店所在国家的省列表 */
    $smarty->assign('country_list', get_regions());
    $smarty->assign('shop_province_list', get_regions(1, $_CFG['shop_country']));

    /* 获得用户所有的收货人信息 */
    $consignee_list = get_consignee_list($user_id);
    $card_type_text = array(0 => '身份证', 1 => '护照');
    //取得国家列表，如果有收货人列表，取得省市区列表
    foreach ($consignee_list AS $region_id => $consignee) {
        $consignee['country'] = isset($consignee['country']) ? intval($consignee['country']) : 0;
        $consignee['province'] = isset($consignee['province']) ? intval($consignee['province']) : 0;
        $consignee['city'] = isset($consignee['city']) ? intval($consignee['city']) : 0;

        $province_list[$region_id] = get_regions(1, $consignee['country']);
        $city_list[$region_id] = get_regions(2, $consignee['province']);
        $district_list[$region_id] = get_regions(3, $consignee['city']);
        $consignee_list[$region_id]['country_info'] = get_region_name($consignee['country']);
        $consignee_list[$region_id]['province_info'] = get_region_name($consignee['province']);
        $consignee_list[$region_id]['city_info'] = get_region_name($consignee['city']);
        $consignee_list[$region_id]['district_info'] = get_region_name($consignee['district']);
        $consignee_list[$region_id]['card_type_text'] = !empty($card_type_text[$consignee_list[$region_id]['card_type']]) ? $card_type_text[$consignee_list[$region_id]['card_type']] : '';
    }
    $smarty->assign('consignee_list', $consignee_list);
    /* 获取默认收货ID */
    $address_id = $db->getOne("SELECT address_id FROM " . $ecs->table('users') . " WHERE user_id='$user_id'");

    //赋值于模板
    $smarty->assign('real_goods_count', 1);
    $smarty->assign('shop_country', $_CFG['shop_country']);
    $smarty->assign('shop_province', get_regions(1, $_CFG['shop_country']));
    // $smarty->assign('province_list', $province_list);
    $smarty->assign('address', $address_id);
    // $smarty->assign('city_list', $city_list);
    // $smarty->assign('district_list', $district_list);
    $smarty->assign('currency_format', $_CFG['currency_format']);
    $smarty->assign('integral_scale', $_CFG['integral_scale']);
    $smarty->assign('name_of_region', array($_CFG['name_of_region_1'], $_CFG['name_of_region_2'], $_CFG['name_of_region_3'], $_CFG['name_of_region_4']));
    $name = "联系地址管理";
    $smarty->assign('name', $name);

    $smarty->display('user_address.html');

}/* 验证用户注册用户名是否可以注册 */
elseif ($act == 'is_registered') {
    include_once(ROOT_PATH . 'includes/lib_passport.php');

    $username = trim($_GET['username']);
    $username = json_str_iconv($username);

    if ($user->check_user($username) || admin_registered($username)) {
        echo 'false';
    } else {
        echo 'true';

    }
} /*支付凭证*/
elseif ($act == 'buy_proof') {
    include_once(ROOT_PATH . 'includes/lib_transaction.php');
    $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
    $record_count = $db->getOne("SELECT COUNT(*) FROM sk_order_info WHERE user_id=" . $_SESSION['user_id'] . " AND pay_time!=0");
    $pager = get_pager('user.php', array('act' => $act), $record_count, $page);
    $goods_sn_list = get_goods_sn($_SESSION['user_id'], $pager['size'], $pager['start']);
    $arr = Array();
    $arr = $goods_sn_list;
    foreach ($goods_sn_list AS $key => $value) {
        $arr[$key]['pay_time'] = date("Y年m月d日H时i分s秒", ($value['pay_time']));

    }
    $number = get_cart_num(SESS_ID);
    $num = 0;
    foreach ($number as $value) {
        $num += $value['goods_number'];
    }
    $smarty->assign('num', $num);
    $smarty->assign('yuanquan', 1);
    $smarty->assign('goods_info', $arr);
    $smarty->assign('pager', $pager);
    $name = "付款凭证";
    $smarty->assign('name', $name);

    $smarty->display('buy_proof.html');
}/* 取消订单 */
elseif ($act == 'cancel_order') {
    include_once(ROOT_PATH . 'includes/lib_transaction.php');
    include_once(ROOT_PATH . 'includes/lib_order.php');

    $order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
//    $order_id = $_REQUEST['order_id'];
//    //获取订单中球票数量
//    $ticket_num = get_order_ticket_info($order_id);
//    echo $ticket;die;
//    //修改库存 goods_number + 订单球票数量
//    save_goods_info($ticket_num);
    if (cancel_order($order_id, $_SESSION['user_id'])) {
        /* 获取用户手机号 */
        $sql = "SELECT user_id, mobile_phone FROM " . $ecs->table('users') . " WHERE user_name='$_SESSION[user_name]' LIMIT 1";
        $row = $db->getRow($sql);
        change_order_goods_storage($order_id, false, 1);
        //查询订单中是否有套餐
        $sql = "SELECT * FROM " . $ecs->table('order_combo') . "WHERE order_id = $order_id";
        $combo = $db->getAll($sql);
        foreach ($combo as $key => $val) {
            $sql = "UPDATE" . $GLOBALS['ecs']->table('goods') . "SET goods_number=goods_number+" . $val['goods_number'] . " WHERE goods_id=" . $val['goods_id'];
            $GLOBALS['db']->query($sql);
        }
        ecs_header("Location: user.php?act=order_list\n");
        exit;
    }
} /* 查看订单详情 */
elseif ($act == 'order_detail') {
    include_once(ROOT_PATH . 'includes/lib_transaction.php');
    include_once(ROOT_PATH . 'includes/lib_payment.php');
    include_once(ROOT_PATH . 'includes/lib_order.php');
    include_once(ROOT_PATH . 'includes/lib_clips.php');

    $order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
    /* 订单详情 */
    $user_id = $_SESSION['user_id'];
    $order = get_order_detail($order_id, $user_id);
    if ($order === false) {
        $err->show($_LANG['back_home_lnk'], './');
        exit;
    }

    //获取合同下载地址
    __load("ContractService");

    $contract_file = $db->getRow("SELECT contract_file,contract_id FROM " . $ecs->table('contract') . " WHERE user_id = '$user_id' && order_sn= '{$order['order_sn']}'");
//    $contract_file = $contract_file[0]['contract_file'];
    $smarty->assign('contract_file', $contract_file['contract_file']);

    $contract_service = new ContractService;
    if($contract_file['contract_id']){
        $contract_info = $contract_service->get_Contract($contract_file['contract_id']);
        $smarty->assign('contract_info', $contract_info);
    }

    /* 是否显示添加到购物车 */
    if ($order['extension_code'] != 'group_buy' && $order['extension_code'] != 'exchange_goods') {
        $smarty->assign('allow_to_cart', 1);
    }
    /* 订单商品 */
//    $goods_list = order_goods($order_id);
    $goods_list = get_order_goods_info($order_id);
    foreach ($goods_list AS $key => $value) {
        $goods_list[$key]['market_price'] = price_format($value['market_price'], false);
        $goods_list[$key]['goods_price'] = price_format($value['goods_price'], false);
        $goods_list[$key]['subtotal'] = price_format($value['subtotal'], false);
    }
    /* 设置能否修改使用余额数 */
//    if ($order['order_amount'] > 0)
//    {
//        if ($order['order_status'] == OS_UNCONFIRMED || $order['order_status'] == OS_CONFIRMED)
//        {
//            $user = user_info($order['user_id']);
//            if ($user['user_money'] + $user['credit_line'] > 0)
//            {
//                $smarty->assign('allow_edit_surplus', 1);
//                $smarty->assign('max_surplus', sprintf($_LANG['max_surplus'], $user['user_money']));
//            }
//        }
//
//    }
    $number = get_cart_num(SESS_ID);
    $num = 0;
    foreach ($number as $value) {
        $num += $value['goods_number'];
    }
    $smarty->assign('num', $num);
    $smarty->assign('yuanquan', 1);
    //取得订单下的门票信息、门票颜色
    $order_menpiao = order_menpiao($order_id);
    $order_combo_info = order_combo_info($order_id);
    //--hechengin --查询订单套餐
    $combo_order = get_order_combo_info($order_id);
    $order_combo_menpiao['key'] = order_combo_menpiao($order_id);
    $order_money = order_combo_money($order_id);
    __load('ComboService');
    $combo_obj = new ComboService;
    foreach ($order_combo_menpiao['key'] as $key => $value) {
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
    $rec_menpiao_list[''] = $bearer->get_order_menpiao_list($order_id);
    $rec_menpiao = Array();
    $rec_menpiao = $rec_menpiao_list;

    //获取联系人
    $order_sn = empty($_GET['order_sn']) ? 0 : $_GET['order_sn'];
    if (empty($order_sn)) {
        echo "订单不存在";
    } else {
        __load("OrderService");
        $order_obj = new OrderService();
        $order_id = $order_obj->get_order_id($order_sn);
//        $order_info = $order_obj->get_order_info($order_id);
        $order_lianxiren = order_lianxiren($order_id);
    }
    /* 订单 支付 配送 状态语言项 */
    $order['order_status'] = $_LANG['os'][$order['order_status']];
    $order['pay_status'] = $_LANG['ps'][$order['pay_status']];
    $order['shipping_status'] = $_LANG['ss'][$order['shipping_status']];
    $openid = !empty($_SESSION['wx_openid']) ? $_SESSION['wx_openid'] : '1';
    if ($order['pay_id'] == '99') {
        $order['pay_name'] = '微信';
        $smarty->assign('is_wx_pay', 1);
        $smarty->assign('openid', $openid);
    } elseif ($order['pay_id'] == '6') {
        $order['pay_name'] = '支付宝';
        $smarty->assign('is_wx_pay', 2);
        $smarty->assign('openid', $openid);
    } elseif ($order['pay_id'] == '9') {
        $smarty->assign('is_wx_pay', 9);
        $order['pay_name'] = '招行掌上生活支付';
        $smarty->assign('is_wx_pay', 9);
    } elseif ($order['pay_id'] != 99 && $order['pay_id'] != 6 && $order['pay_id'] != 2) {
        $smarty->assign('is_wx_pay', 3);
    } elseif ($order['pay_id'] != 99 && $order['pay_id'] != 6 && $order['pay_id'] == 2) {
        $payment = payment_info_bank($order['pay_id']);
        include_once('includes/modules/payment/' . $payment['pay_code'] . '.php');
        $pay_obj = new $payment['pay_code'];
        if ($payment['pay_code'] == "bank") {
            $pay_online = $payment['pay_desc'];
//            $pay_online = $pay_obj->get_code($order, $payment);
            $smarty->assign('pay_online', $pay_online);
        }
    }
    $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;

    $record_count = $db->getOne("SELECT COUNT(*) FROM " . $ecs->table('order_ticket') . " WHERE order_id = '$order_id'");

    $pager = get_pager('user.php', array('act' => 'order_detail'), $record_count, $page);

//    $orders = get_user_orders($user_id, $pager['size'], $pager['start']);
    //根据订单id查询球票信息
    __load("OrderService");
    $order_obj = new OrderService();
    $order_info_list = $order_obj->get_order_ticket_combo_info($order_id, $pager['size'], $pager['start']);
    //时间戳转换
    $order['add_time'] = date('Y-m-d H:m:s', $order['add_time']);
    $smarty->assign('order', $order);
    $smarty->assign('order_money', $order_money);
    $smarty->assign('goods_list', $goods_list);
    $smarty->assign('order_menpiao', $order_menpiao);
    $smarty->assign('order_combo_menpiao', $order_combo_menpiao);
    $smarty->assign('order_info', $order_info_list);
    $smarty->assign('order_menpiao_sum', $order_menpiao_sum);
    $smarty->assign("rec_menpiao_list", $rec_menpiao);
    $smarty->assign("combo_order_info", $combo_order);
    $smarty->assign('game_info', $game_info);
    $smarty->assign('pager', $pager);
    $smarty->assign('order_id', $order_id);
    $smarty->assign('order_sn', $order_sn);
    $smarty->assign('order_lianxiren', $order_lianxiren);
    $name = "订单详情";
    $smarty->assign('name', $name);
    //二维码，pdf
    __load("OrderService");
    $order = new OrderService();
    $order_info = $order->get_order_sn($order_id);
    $smarty->display('order_detail.html');
} elseif ($act == 'voucher_img') {
    $order_sn = empty($_GET['order_sn']) ? 0 : $_GET['order_sn'];
    $value = 'http://webshop.shankaisports.com/mobile/user.php?act=show_pdf_info&order_sn=' . $order_sn; //二维码内容
    echo create_qr($value);
    exit;
} elseif ($act == 'show_pdf_info') {
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

        $order_info['order_status'] = $_LANG['os'][$order_info['order_status']];
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
        $order_bearer = order_bearer($order_id);
        $smarty->assign('lianxiren', $order_lianxiren);
        $smarty->assign('order_bearer', $order_bearer);
        $smarty->assign('info_list', $titckInfo);
        $smarty->display('show_pdf2.html');
        exit;
    }
    exit;
}/* 添加/编辑收货地址的处理 */
elseif ($act == 'act_edit_address') {
    include_once(ROOT_PATH . 'includes/lib_transaction.php');
    include_once(ROOT_PATH . 'languages/' . $_CFG['lang'] . '/shopping_flow.php');

    $smarty->assign('lang', $_LANG);

    $address = array(
        'user_id' => $user_id,
        'address_id' => intval($_POST['address_id']),
        'country' => isset($_POST['country']) ? intval($_POST['country']) : 0,
        'province' => isset($_POST['province']) ? intval($_POST['province']) : 0,
        'city' => isset($_POST['city']) ? intval($_POST['city']) : 0,
        'district' => isset($_POST['district']) ? intval($_POST['district']) : 0,
        'address' => isset($_POST['address']) ? trim($_POST['address']) : '',
        'consignee' => isset($_POST['consignee']) ? trim($_POST['consignee']) : '',
        'email' => isset($_POST['email']) ? trim($_POST['email']) : '',
        'tel' => isset($_POST['tel']) ? make_semiangle(trim($_POST['tel'])) : '',
        'mobile' => isset($_POST['mobile']) ? make_semiangle(trim($_POST['mobile'])) : '',
        'best_time' => isset($_POST['best_time']) ? trim($_POST['best_time']) : '',
        'sign_building' => isset($_POST['sign_building']) ? trim($_POST['sign_building']) : '',
        'zipcode' => isset($_POST['zipcode']) ? make_semiangle(trim($_POST['zipcode'])) : '',
        'card_type' => isset($_POST['card_type']) ? make_semiangle(trim($_POST['card_type'])) : '',
        'card_num' => isset($_POST['card_num']) ? make_semiangle(trim($_POST['card_num'])) : '',
    );
    if (update_address($address)) {
        ecs_header("Location: user.php?act=user_address\n");
    }
} /* 添加/编辑收货地址的处理 */
elseif ($act == 'actl_edit_address') {
    include_once(ROOT_PATH . 'includes/lib_transaction.php');
    include_once(ROOT_PATH . 'languages/' . $_CFG['lang'] . '/shopping_flow.php');
    $smarty->assign('lang', $_LANG);
    $user_id = $_SESSION['user_id'];
    //获取联系人的所有信息
    $name = "编辑联系地址";
    $smarty->assign('name', $name);
    $smarty->display('actl_edit_address.html');

} /* 添加/编辑收货地址的处理 */
elseif ($act == 'edit_address') {
    include_once(ROOT_PATH . 'includes/lib_transaction.php');
    include_once(ROOT_PATH . 'languages/' . $_CFG['lang'] . '/shopping_flow.php');
    $smarty->assign('lang', $_LANG);
    $address_id = $_REQUEST['address_id'];
    $user_id = $_SESSION['user_id'];
    //获取联系人的所有信息
    $consignee_info = get_users_consignee_info($address_id);
    $smarty->assign('consignee_info', $consignee_info);
    $name = "编辑联系地址";
    $smarty->assign('name', $name);
    $smarty->display('edit_address.html');

} elseif ($act == 'drop_consignee') {

    include_once('includes/lib_transaction.php');

    $consignee_id = intval($_GET['id']);
    if (drop_consignee($consignee_id)) {
        ecs_header("Location: user.php?act=user_address\n");
        exit;
    } else {
        $smarty->assign('message', '您的收货地址信息删除失败');
        $smarty->display("message.html");
    }
} elseif ($act == 'bearer_manage') {
    __load("BearerService");
    $bearer = new BearerService();
    if ($_SESSION['user_id'] != 0) {
        //查询持票人信息.
        $bearer_info = $bearer->get_bearer_info($_SESSION['user_id']);
        $smarty->assign('bearer_info', $bearer_info);
        $smarty->assign('act', "update");
    }
    $number = get_cart_num(SESS_ID);
    $num = 0;
    foreach ($number as $value) {
        $num += $value['goods_number'];
    }
    $smarty->assign('num', $num);
    $smarty->assign('yuanquan', 1);
    $name = "持票人信息";
    $smarty->assign('name', $name);
    $smarty->display('bearer_manage.html');
} elseif ($act == 'edit_userInfo') {
    include_once(ROOT_PATH . 'includes/lib_transaction.php');
    $edit_userInfo = edit_userInfo($_POST, $_SESSION['user_id']);
    $user_info = get_profile($_SESSION['user_id']);
    $smarty->assign('user_info', $user_info);
    $number = get_cart_num(SESS_ID);
    $num = 0;
    foreach ($number as $value) {
        $num += $value['goods_number'];
    }
    $smarty->assign('num', $num);
    $smarty->assign('yuanquan', 1);
    $name = "个人资料";
    $smarty->assign('name', $name);
    $smarty->display('edit_userInfo.html');

} elseif ($act == 'edit_password') {
    $number = get_cart_num(SESS_ID);
    $num = 0;
    foreach ($number as $value) {
        $num += $value['goods_number'];
    }
    $smarty->assign('num', $num);
    $smarty->assign('yuanquan', 1);
    $name = "修改密码";
    $smarty->assign('name', $name);
    $smarty->display('edit_password.html');
} /* 修改会员密码 */
elseif ($act == 'act_edit_password') {
    require_once(ROOT_PATH . 'includes/lib_sms.php');
    include_once(ROOT_PATH . 'includes/lib_passport.php');
    $user_id = $_SESSION['user_id'];
    $old_password = isset($_POST['old_password']) ? trim($_POST['old_password']) : null;
    $new_password = isset($_POST['new_password']) ? trim($_POST['new_password']) : '';
    $user_id = isset($_POST['uid']) ? intval($_POST['uid']) : $user_id;
    $code = isset($_POST['code']) ? trim($_POST['code']) : '';
    $user_info = $user->get_profile_by_id($user_id); //论坛记录
    if (($user_info && (!empty($code) && md5($user_info['user_id'] . $_CFG['hash_code'] . $user_info['reg_time']) == $code)) || ($_SESSION['user_id'] > 0 && $_SESSION['user_id'] == $user_id && $user->check_user($_SESSION['user_name'], $old_password))) {
        if ($user->edit_user(array('username' => (empty($code) ? $_SESSION['user_name'] : $user_info['user_name']), 'old_password' => $old_password, 'password' => $new_password), empty($code) ? 0 : 1)) {
            $sql = "UPDATE " . $ecs->table('users') . "SET `ec_salt`='0' WHERE user_id= '" . $user_id . "'";
            $db->query($sql);
            $user->logout();
            $smarty->assign('message', '修改密码成功');
            $smarty->display("message.html");
        } else {
            $smarty->assign('message', '修改密码失败');
            $smarty->display("message.html");
        }
    } else {
        $smarty->assign('message', '修改密码失败');
        $smarty->display("message.html");
    }

} /* 确认收货 */
elseif ($act == 'affirm_received') {
    include_once(ROOT_PATH . 'includes/lib_transaction.php');

    $order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
    $_LANG['buyer'] = '买家';
    if (affirm_received($order_id, $_SESSION['user_id'])) {
        ecs_header("Location: user.php?act=order_list\n");
        exit;
    }

} /* 退出会员中心 */
elseif ($act == 'logout') {
    if (!isset($back_act) && isset($GLOBALS['_SERVER']['HTTP_REFERER'])) {
        $back_act = strpos($GLOBALS['_SERVER']['HTTP_REFERER'], 'user.php') ? './index.php' : $GLOBALS['_SERVER']['HTTP_REFERER'];
    }
    //清空用户表中openid
    $openid = $_SESSION['wx_openid'];
    $user_info = get_users_info_openid($openid);
    update_userinfo_openid($user_info['wx_open_id']);
    $user->logout();
    $Loaction = 'index.php';
    ecs_header("Location: $Loaction\n");

} /* 显示会员注册界面 */
elseif ($act == 'register') {
    if (!isset($back_act) && isset($GLOBALS['_SERVER']['HTTP_REFERER'])) {
        $back_act = strpos($GLOBALS['_SERVER']['HTTP_REFERER'], 'user.php') ? './index.php' : $GLOBALS['_SERVER']['HTTP_REFERER'];
    }

    /* 取出注册扩展字段 */
    $sql = 'SELECT * FROM ' . $ecs->table('reg_fields') . ' WHERE type < 2 AND display = 1 ORDER BY dis_order, id';
    $extend_info_list = $db->getAll($sql);
    $smarty->assign('extend_info_list', $extend_info_list);
    /* 密码找回问题 */
    $_LANG['passwd_questions']['friend_birthday'] = '我最好朋友的生日？';
    $_LANG['passwd_questions']['old_address'] = '我儿时居住地的地址？';
    $_LANG['passwd_questions']['motto'] = '我的座右铭是？';
    $_LANG['passwd_questions']['favorite_movie'] = '我最喜爱的电影？';
    $_LANG['passwd_questions']['favorite_song'] = '我最喜爱的歌曲？';
    $_LANG['passwd_questions']['favorite_food'] = '我最喜爱的食物？';
    $_LANG['passwd_questions']['interest'] = '我最大的爱好？';
    $_LANG['passwd_questions']['favorite_novel'] = '我最喜欢的小说？';
    $_LANG['passwd_questions']['favorite_equipe'] = '我最喜欢的运动队？';
    /* 密码提示问题 */
    $smarty->assign('passwd_questions', $_LANG['passwd_questions']);
    $smarty->assign('footer', get_footer());
    $smarty->display('user_passport.html');
} /* 注册会员的处理 */
elseif ($act == 'act_register') {

    include_once(ROOT_PATH . 'includes/lib_passport.php');

    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $other['mobile_phone'] = isset($_POST['extend_field5']) ? $_POST['extend_field5'] : '';
    $back_act = isset($_POST['back_act']) ? trim($_POST['back_act']) : '';
    $mobile = isset($_POST['extend_field5']) ? trim($_POST['extend_field5']) : '';//手机号
    $captcha = isset($_POST['captcha']) ? trim($_POST['captcha']) : '';//图形验证码
    $verifycode = isset($_POST['extend_field']) ? trim($_POST['extend_field']) : '';//验证码
    if ($_CFG['ecsdxt_mobile_reg'] == '1') {
        require_once(ROOT_PATH . 'includes/lib_sms.php');
        require_once(ROOT_PATH . 'languages/' . $_CFG['lang'] . '/sms.php');
        /* 提交的手机号是否已经注册帐号 */
        $sql = "SELECT COUNT(user_id) FROM " . $ecs->table('users') . " WHERE mobile_phone = '$mobile'";
        if ($db->getOne($sql) > 0) {
            return_json(array('code' => 1, 'msg' => '手机号已注册'));
        }
        /*验证图形验证码*/
        if (gd_version() > 0) {
            if (empty($captcha)) {
                return_json(array('code' => 1, 'msg' => '图形验证码不能为空'));
            }

            /* 检查验证码 */
            include_once('includes/cls_captcha.php');

            $validator = new captcha();
            $validator->session_word = 'captcha_word';
            if (!$validator->check_word($captcha)) {
                return_json(array('code' => 1, 'msg' => '图形验证码不匹配'));
            }
        }
        /* 验证手机号验证码和IP */
        $sql = "SELECT COUNT(id) FROM " . $ecs->table('verifycode') . " WHERE mobile='$mobile' AND verifycode='$verifycode' AND status=1 AND dateline>'" . gmtime() . "'-600";//验证码一天内有效
        Logger::DEBUG("mobile verify code :" . json_decode($sql));
        if ($db->getOne($sql) == 0) {
            return_json(array('code' => 1, 'msg' => '手机号和验证码不匹配'));
        }
    }
    /*验证图形验证码*/
    if (gd_version() > 0) {
        if (empty($captcha)) {
            return_json(array('code' => 1, 'msg' => '图形验证码不能为空'));
        }

        /* 检查验证码 */
        include_once('includes/cls_captcha.php');

        $validator = new captcha();
        $validator->session_word = 'captcha_word';
        if (!$validator->check_word($captcha)) {
            return_json(array('code' => 1, 'msg' => '图形验证码不匹配'));
        }
    }

    $is_third = empty($_GET['step']) ? 0 : 1;
    if ($is_third == 1) {
        $in_urt = $_GET['in_urt'];
    }
    if (register($username, $password, null, $other, $is_third, 0) !== false) {
        if ($_CFG['ecsdxt_customer_registed'] == '1') {
            require_once(ROOT_PATH . 'includes/lib_sms.php');
            require_once(ROOT_PATH . 'languages/' . $_CFG['lang'] . '/sms.php');
            $smarty->assign('shop_name', $_CFG['shop_name']);
            $smarty->assign('user_name', $username);
            $smarty->assign('user_pwd', $password);
            //hechengbin 修改发送国外短信 -- start
            if (substr(trim($other['mobile_phone']), 0, 2) == '86') {
                $content = $smarty->fetch('str:' . $_CFG['ecsdxt_customer_registed_value']);
                $ret = sendsms($other['mobile_phone'], $content);
                Logger::DEBUG("mobile send:" . '----------5' . $other['mobile_phone'] . '1------------' . $content);
            } else {
                $content = $smarty->fetch('str:' . $_CFG['ecsdxt_customer_registed_en']);
                $ret = sendsms($other['mobile_phone'], $content);
                Logger::DEBUG("mobile send:" . '----------6' . $other['mobile_phone'] . '1------------' . $content);
            }
            if ($ret === true) {
                //插入注册成功短信提醒数据记录
                $sql = "INSERT INTO " . $ecs->table('verifycode') . "(mobile, getip, verifycode, dateline, reguid, status) VALUES ('" . $other['mobile_phone'] . "', '" . real_ip() . "', '$verifycode', '" . gmtime() . "', $_SESSION[user_id], 7)";
                $db->query($sql);
            }
            $ua = strtolower($_SERVER['HTTP_USER_AGENT']);
            $uachar = "/(nokia|sony|ericsson|mot|samsung|sgh|lg|philips|panasonic|alcatel|lenovo|cldc|midp|mobile)/i";

            if (($ua == '' || preg_match($uachar, $ua)) && !strpos(strtolower($_SERVER['REQUEST_URI']), 'wap')) {
                $Loaction = 'mobile/';
                if (!empty($Loaction)) {
                    return_json(array('code' => 2, 'msg' => '注册成功'));
                    if ($GLOBALS['user']->check_user($username, $password) > 0) {
                        $GLOBALS['user']->set_session($username);
                        $GLOBALS['user']->set_cookie($username, null);

                        update_user_info();
                        show_user_center();
                    }
                }

            }
            return_json(array('code' => 0, 'msg' => '注册成功'));
        }

        $sql = "UPDATE " . $ecs->table('verifycode') . " SET reguid=" . $_SESSION['user_id'] . ",regdateline='" . gmtime() . "',status=2 WHERE mobile='$mobile' AND verifycode='$verifycode' AND getip='" . real_ip() . "' AND status=1 AND dateline>'" . gmtime() . "'-600";
        $db->query($sql);

        /*把新注册用户的扩展信息插入数据库*/
        $sql = 'SELECT id FROM ' . $ecs->table('reg_fields') . ' WHERE type = 0 AND display = 1 ORDER BY dis_order, id';   //读出所有自定义扩展字段的id
        $fields_arr = $db->getAll($sql);

        $extend_field_str = '';    //生成扩展字段的内容字符串
        foreach ($fields_arr AS $val) {
            $extend_field_index = 'extend_field' . $val['id'];
            if (!empty($_POST[$extend_field_index])) {
                $temp_field_content = strlen($_POST[$extend_field_index]) > 100 ? mb_substr($_POST[$extend_field_index], 0, 99) : $_POST[$extend_field_index];
                $extend_field_str .= " ('" . $_SESSION['user_id'] . "', '" . $val['id'] . "', '" . $temp_field_content . "'),";
            }
        }
        $extend_field_str = substr($extend_field_str, 0, -1);

        if ($extend_field_str)      //插入注册扩展数据
        {
            $sql = 'INSERT INTO ' . $ecs->table('reg_extend_info') . ' (`user_id`, `reg_field_id`, `content`) VALUES' . $extend_field_str;
            $db->query($sql);
        }

        /* 写入密码提示问题和答案 */
        if (!empty($passwd_answer) && !empty($sel_question)) {
            $sql = 'UPDATE ' . $ecs->table('users') . " SET `passwd_question`='$sel_question', `passwd_answer`='$passwd_answer'  WHERE `user_id`='" . $_SESSION['user_id'] . "'";
            $db->query($sql);
        }
        //更新openid 到用户表中--hechengbin--
        $openid = !empty($_SESSION['wx_openid']) ? $_SESSION['wx_openid'] : '';
        if ($openid) {
            $sql = 'UPDATE ' . $ecs->table('users') . " SET `wx_open_id`='$openid' WHERE `user_id`='" . $_SESSION['user_id'] . "'";
            $db->query($sql);
        }
//            //将用户微信信息插入绑定表中
//              $wx_info = json_decode(getWxUserInfo(),true);
//              $data = array(
//                  'user_id' => get_user_id($username),
//                  'openid' => $_SESSION['wx_openid'],
//                  'name' => $wx_info['name'],
//                  'sex' => $wx_info['sex'],
//                  'city' => $wx_info['city'],
//                  'country' => $wx_info['country'],
//                  'province' => $wx_info['province'],
//                  'img_url' => $wx_info['img_url'],
//                  'subscribe_time' => $wx_info['subscribe_time'],
//                  'unionid' => $wx_info['unionid'],
//                  'bind_time' => time()
//              );
        //查询微信用户信息
        $wx_info = getWxUserInfo();
        $data = array(
            'user_id' => get_user_id($username),
            'openid' => $_SESSION['wx_openid'],
            'name' => addslashes($wx_info['nickname']),
            'sex' => $wx_info['sex'],
            'city' => $wx_info['city'],
            'country' => $wx_info['country'],
            'province' => $wx_info['province'],
            'img_url' => $wx_info['headimgurl'],
            'subscribe_time' => $wx_info['subscribe_time'],
            'unionid' => $wx_info['unionid'],
            'bind_time' => time()
        );
        $this->db->autoExecute($this->ecs->table("wx_bind"), $data, 'INSERT');
        /* 判断是否需要自动发送注册邮件 */
        if ($GLOBALS['_CFG']['member_email_validate'] && $GLOBALS['_CFG']['send_verify_email']) {
            send_regiter_hash($_SESSION['user_id']);
        }
        $ucdata = empty($user->ucdata) ? "" : $user->ucdata;
        $GLOBALS['smarty']->display('personal_center.html');

    } else {
        $GLOBALS['smarty']->display('user.php?act=act_register');

    }

} /**
 * 修改用户信息界面
 * @var [type]
 */
elseif ($act == 'edit_userInfo') {
    include_once(ROOT_PATH . 'includes/lib_transaction.php');
    $edit_userInfo = edit_userInfo($_POST, $user_id);
    $user_info = get_profile($user_id);
    $smarty->assign('user_info', $user_info);
    $smarty->display('edit_userInfo.html');
} //微信免登陆相关
elseif ($act == 'act_wx_register') {
    include_once(ROOT_PATH . 'includes/lib_passport.php');

    if (empty($_SESSION['wx_openid'])) {
        return_json(array('code' => 1, 'msg' => '发生异常错误，请退出重试'));
    }

    $mobile = isset($_POST['mobile_phone']) ? trim($_POST['mobile_phone']) : '';//手机号
    $verifycode = isset($_POST['mobile_code']) ? trim($_POST['mobile_code']) : '';//验证码
    if ($_CFG['ecsdxt_mobile_reg'] == '1') {
        require_once(ROOT_PATH . 'includes/lib_sms.php');
        require_once(ROOT_PATH . 'languages/' . $_CFG['lang'] . '/sms.php');

        /* 验证手机号验证码和IP */
        $sql = "SELECT COUNT(id) FROM " . $ecs->table('verifycode') . " WHERE mobile='$mobile' AND verifycode='$verifycode' AND getip='" . real_ip() . "' AND status=1 AND dateline>'" . gmtime() . "'-600";//验证码一天内有效

        if ($db->getOne($sql) == 0) {
            return_json(array('code' => 1, 'msg' => '手机号和验证码不匹配'));
        } else {
            $sql = "SELECT COUNT(user_id) FROM " . $ecs->table('users') . " WHERE mobile_phone = '$mobile'";
            if ($db->getOne($sql) > 0) {
                $sql1 = "UPDATE sk_users SET wx_open_id='" . $_SESSION['wx_openid'] . "' WHERE mobile_phone='$mobile'";
                $db->query($sql1);

                $sql2 = "SELECT * FROM sk_users WHERE mobile_phone='$mobile'";
                $user_info = $db->getRow($sql2);
                $_SESSION['user_id'] = $user_info['user_id'];
                $_SESSION['user_name'] = $user_info['user_name'];
                update_user_info();
                return_json(array('code' => 0, 'msg' => '绑定成功'));
            } else {
                $user_name1 = "sk_";
                $user_name2 = rand(1, 999999999);
                $user_name_sum = $user_name1 .= $user_name2;
                $sql = "select user_id from sk_users where user_name=" . "'$user_name_sum'";
                $user_info = $db->getOne($sql);
                while (!empty($user_info)) {
                    $user_name1 = "sk_";
                    $user_name2 = rand(1, 999999999);
                    $user_name_sum = $user_name1 .= $user_name2;
                    $sql = "select user_id from sk_users where user_name=" . "'$user_name_sum'";
                    $user_info = $db->getOne($sql);
                }
                $username = $user_name_sum;
                $password = 'sk123456';
                $other['mobile_phone'] = $mobile;
                if (register($username, $password, null, $other) !== false) {
                    $sql = "SELECT * FROM sk_users WHERE mobile_phone='$mobile'";
                    $user_info = $db->getRow($sql);
                    $_SESSION['user_id'] = $user_info['user_id'];
                    $_SESSION['user_name'] = $user_info['user_name'];
                    update_user_info();
                    return_json(array('code' => 0, 'msg' => '新建账号成功'));
                } else {
                    return_json(array('code' => 1, 'msg' => '新建账号失败'));
                }
            }
        }
    }

} /* 用户中心 */
else {
    if ($_SESSION['user_id'] > 0) {
        show_user_center();
    } else {
        $smarty->display('login.html');
    }
}

/**
 * 用户中心显示
 */
function show_user_center()
{
    $best_goods = get_recommend_goods('best');
    if (count($best_goods) > 0) {
        foreach ($best_goods as $key => $best_data) {
            $best_goods[$key]['shop_price'] = encode_output($best_data['shop_price']);
            $best_goods[$key]['name'] = encode_output($best_data['name']);
        }
    }
    $GLOBALS['smarty']->assign('best_goods', $best_goods);
    $name1 = "<img src='images/logo.png'>";
    $GLOBALS['smarty']->assign('name1', $name1);
    $GLOBALS['smarty']->display('personal_center.html');
}

/**
 * 手机注册
 */
function m_register($username, $password, $email, $other = array())
{
    /* 检查username */
    if (empty($username)) {
        echo '用户名不能为空';
        $Loaction = 'user.php?act=register';
        ecs_header("Location: $Loaction\n");
        return false;
    }
    if (preg_match('/\'\/^\\s*$|^c:\\\\con\\\\con$|[%,\\*\\"\\s\\t\\<\\>\\&\'\\\\]/', $username)) {
        echo '用户名错误';
        $Loaction = 'user.php?act=register';
        ecs_header("Location: $Loaction\n");
        return false;
    }

    /* 检查email */
    if (empty($email)) {
        echo 'email不能为空';
        $Loaction = 'user.php?act=register';
        ecs_header("Location: $Loaction\n");
        return false;
    }
    if (!is_email($email)) {
        echo 'email错误';
        $Loaction = 'user.php?act=register';
        ecs_header("Location: $Loaction\n");
        return false;
    }

    /* 检查是否和管理员重名 */
    if (admin_registered($username)) {
        echo '此用户已存在！';
        $Loaction = 'user.php?act=register';
        ecs_header("Location: $Loaction\n");
        return false;
    }

    if (!$GLOBALS['user']->add_user($username, $password, $email)) {
        echo '注册失败！';
        $Loaction = 'user.php?act=register';
        ecs_header("Location: $Loaction\n");
        //注册失败
        return false;
    } else {
        //注册成功

        /* 设置成登录状态 */
        $GLOBALS['user']->set_session($username);
        $GLOBALS['user']->set_cookie($username);

    }

    //定义other合法的变量数组
    $other_key_array = array('msn', 'qq', 'office_phone', 'home_phone', 'mobile_phone');
    $update_data['reg_time'] = local_strtotime(local_date('Y-m-d H:i:s'));
    if ($other) {
        foreach ($other as $key => $val) {
            //删除非法key值
            if (!in_array($key, $other_key_array)) {
                unset($other[$key]);
            } else {
                $other[$key] = htmlspecialchars(trim($val)); //防止用户输入javascript代码
            }
        }
        $update_data = array_merge($update_data, $other);
    }
    $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('users'), $update_data, 'UPDATE', 'user_id = ' . $_SESSION['user_id']);

    update_user_info();      // 更新用户信息

    return true;

}

function return_json($data)
{
    if (is_array($data)) {
        foreach ($data as $key => $value) {
            $data[$key] = urlencode($value);
        }
    } else {
        $data = urlencode($data);
    }
    echo urldecode(json_encode($data));
    exit;
}

function get_order_goods_info($order_id)
{
    $sql = "SELECT g.*,og.rec_id,og.goods_id,og.goods_name,og.goods_sn,og.market_price,og.goods_number,og.goods_price,og.goods_attr,og.is_real,og.parent_id,og.is_gift,og.goods_price * og.goods_number AS subtotal,og.extension_code FROM " . $GLOBALS['ecs']->table('order_goods') . "AS og," . $GLOBALS['ecs']->table('goods') . "AS g WHERE og.order_id = '" . $order_id . "' AND og.goods_id = g.goods_id";
    return $GLOBALS['db']->getAll($sql);
}

function get_order($user_id)
{
    $sql = "SELECT order_sn FROM " . $GLOBALS['ecs']->table('order_info') . " WHERE user_id='" . $user_id . "' ";
    return $GLOBALS['db']->getAll($sql);
}

function get_order_all_goods_info($user_id)
{
    $info = array();
    $sql = "SELECT order_id FROM " . $GLOBALS['ecs']->table('order_info') . " WHERE user_id='" . $user_id . "'";
    $res = $GLOBALS['db']->getAll($sql);
    foreach ($res as $key => $val) {
        $sql = "select
        distinct
			t3.numbers,
			t4.color_value
		from
        (
            select goods_id from sk_order_ticket where order_id='" . $val['order_id'] . "'
			) t1
			left outer join sk_goods t2
			on t1.goods_id=t2.goods_id
			left outer join sk_number t3
			on t2.number_id=t3.id
			left outer join sk_color_manage t4
			on t3.color_id=t4.color_id";
        $result = $GLOBALS['db']->getAll($sql);
        array_push($info, $result);
    }

    return $info;
}

//获取当前用户的信息
function get_users_consignee_info($address_id)
{
    $sql = "SELECT * FROM " . $GLOBALS['ecs']->table('user_address') . " WHERE address_id='" . $address_id . "'";
    return $GLOBALS['db']->getRow($sql);
}

//查看订单套餐价格
function order_combo_money($order_id)
{
    $sql = "SELECT SUM(c.combo_price*oc.goods_number) FROM " . $GLOBALS['ecs']->table("combo") . "AS c," . $GLOBALS['ecs']->table('order_combo') . "AS oc WHERE oc.order_id=$order_id AND oc.combo_id = c.combo_id";
    return $GLOBALS['db']->getOne($sql);
}

//获取套餐信息
function order_combo_info($order_id)
{
    $sql = "SELECT c.*, SUM(oc.goods_number) as combo_number FROM " . $GLOBALS['ecs']->table('order_combo') . "AS oc," . $GLOBALS['ecs']->table('combo') . "AS c WHERE oc.combo_id=c.combo_id AND oc.order_id=" . $order_id;
    return $GLOBALS['db']->getAll($sql);
}

//--hechengbin--
function get_order_combo_info($order_id)
{
    return $GLOBALS['db']->getAll("SELECT c.*,ci.* FROM sk_order_combo_info AS ci,sk_combo AS c WHERE order_id = $order_id AND ci.combo_id=c.combo_id");
}

function payment_info_bank($pay_id)
{
    $sql = 'SELECT * FROM ' . $GLOBALS['ecs']->table('payment') .
        " WHERE pay_id = '$pay_id' AND enabled = 1";

    return $GLOBALS['db']->getRow($sql);
}

//判断购物车是否有商品
function get_cart_num($sess)
{
    $sql = "SELECT c.* FROM sk_cart AS c WHERE c.session_id = '$sess' AND (c.goods_type = 'ticket' OR c.goods_type='goods' OR c.goods_type='plane' OR c.goods_type = 'combo')";
    return $GLOBALS['db']->getAll($sql);
}

?>