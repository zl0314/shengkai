<?php
/**
 * Created by PhpStorm.
 * User: stepreal
 * Date: 17/10/9
 * Time: 上午1:46
 */

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

/* 载入语言文件 */
require_once(ROOT_PATH . 'languages/' . $_CFG['lang'] . '/user.php');
require_once(ROOT_PATH . 'includes/modules/payment/func/log.php');
include_once('includes/cls_json.php');
//初始化日志

$logHandler = new CLogFileHandler(ROOT_PATH . "/logs/" . date('Y-m-d') . '.log');
$log = Logger::Init($logHandler, 15);
$user_id = $_SESSION['user_id'];
$act = isset($_REQUEST['act']) ? trim($_REQUEST['act']) : 'default';
$affiliate = unserialize($GLOBALS['_CFG']['affiliate']);
$smarty->assign('affiliate', $affiliate);
$back_act = '';
$smarty->assign('_CFG', $_CFG);//附加系统配置
// 不需要登录的操作或自己验证是否登录（如ajax处理）的act

/* 未登录处理 */
if (empty($user_id)) {
    show_message("请登录");
}

$act = empty($_REQUEST['act']) ? "index" : trim($_REQUEST['act']);

//用户中心欢迎页
if ($act == 'index') {
    __load("UserCouponService");
    $user_coupon_service = new UserCouponService();
    $user_coupon_list = $user_coupon_service->get_all_user_coupon_list($user_id);
    $smarty->assign('user_coupon_list', $user_coupon_list);
    $smarty->display('user_coupon_list.dwt');
} elseif ($act == 'exchange') {
    $res = array(
        'code' => 200,
        'message' => '',
        'data' => array(),
    );
    __load("CouponClusterService");
    $coupon_cluster_service = new CouponClusterService();

    $coupon_key = isset($_REQUEST['coupon_key']) ? trim($_REQUEST['coupon_key']) : '';
    $coupon_cluster = $coupon_cluster_service->get_by_coupon_key($coupon_key);
    if (empty($coupon_cluster) || $coupon_cluster['deleted'] == 1) {
        $res['code'] = -1;
        $res['message'] = "优惠券码错误";
        echo json_encode($res);
        exit;
    }
    if (time() >= strtotime($coupon_cluster['end_time'])) {
        $res['code'] = -1;
        $res['message'] = "优惠券已过期,不能继续兑换";
        echo json_encode($res);
        exit;
    }
    if ($coupon_cluster['exchange_count'] >= $coupon_cluster['demand_count']) {
        $res['code'] = -1;
        $res['message'] = "优惠券码错误";
        echo json_encode($res);
        exit;
    }

    __load("CouponService");
    $coupon_service = new CouponService();

    $ret = $coupon_service->exchange($coupon_cluster['coupon_cluster_id'], $user_id);
    if ($ret < 1) {
        $res['code'] = -2;
        $res['message'] = '兑换失败[' . $ret .  ']';
    }
    echo json_encode($res);
    exit;
}