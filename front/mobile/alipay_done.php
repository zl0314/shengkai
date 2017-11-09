<?php
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
//require_once("alipay.config.php");
//require_once("lib/alipay_notify.class.php");
//
//$alipayNotify = new AlipayNotify($alipay_config);
//$verify_result = $alipayNotify->verifyReturn();
if(!empty($_GET['out_trade_no'])) {
    $order_sn=$_GET['out_trade_no'];
    $order=$GLOBALS['db']->getRow('SELECT * FROM sk_order_info where order_sn='."'$order_sn'");
    /*发送短信*/
    //----hechengbin --- start ----
    include_once(ROOT_PATH . 'includes/lib_sms.php');
    if(substr($order['mobile'],0,2) == '86'){
        sendsms($order['mobile'], "【盛开国际旅行社】订单：{$order['order_sn']}支付成功！请您在72小时之内前往个人中心填写持票人信息。");
    }else{
        sendsms($order['mobile'], "Order: {$order['order_sn']} Payment successful! Please fill out the holder information in 72 hours in the personal center. ");

    }
//    if(substr($order['mobile'],0,2) == '82'){
//        sendsms($order['mobile'], "Order: {$order['order_sn']} Payment successful! Please fill out the holder's information in 72 hours in the personal center. ");
//    }else{
//        sendsms($order['mobile'], "【盛开国际旅行社】订单：{$order['order_sn']}支付成功！请您在72小时之内前往个人中心填写持票人信息。");
//    }
    //----hechengbin --- end ----
    /*发送短信end*/
    /*发送邮件*/
    __load("BearerService");
    $bearer = new BearerService();
    $bearer->send_mail('pay_ok', array("to" => array($order['email']),"sub" => array('%order_sn%' => array($order['order_sn']))));
    $smarty->assign('order', $order);
    $namel="<img src='images/logo.png'>";
    $smarty->assign( 'name1',$namel);
    $smarty->display("alipay_done.html");
}else if(!empty($_GET['order_sn'])){
    $order_sn=$_GET['order_sn'];
    $order=$GLOBALS['db']->getRow('SELECT * FROM sk_order_info where order_sn='."'$order_sn'");
    /*发送短信*/
    //----hechengbin --- start ----
    include_once(ROOT_PATH . 'includes/lib_sms.php');
    if(substr($order['mobile'],0,2) == '86'){
        sendsms($order['mobile'], "【盛开国际旅行社】订单：{$order['order_sn']}支付成功！请您在72小时之内前往个人中心填写持票人信息。");
    }else{
        sendsms($order['mobile'], "Order: {$order['order_sn']} Payment successful! Please fill out the holder information in 72 hours in the personal center. ");
    }
//    if(substr($order['mobile'],0,2) == '82'){
//        sendsms($order['mobile'], "Order: {$order['order_sn']} Payment successful! Please fill out the holder's information in 72 hours in the personal center. ");
//    }else{
//        sendsms($order['mobile'], "【盛开国际旅行社】订单：{$order['order_sn']}支付成功！请您在72小时之内前往个人中心填写持票人信息。");
//    }
    //----hechengbin --- end ----
    /*发送短信end*/
    /*发送邮件*/
    __load("BearerService");
    $bearer = new BearerService();
    $bearer->send_mail('pay_ok', array("to" => array($order['email']),"sub" => array('%order_sn%' => array($order['order_sn']))));
    $smarty->assign('order', $order);
    $namel="<img src='images/logo.png'>";
    $smarty->assign( 'name1',$namel);
    $smarty->display("alipay_done.html");
}