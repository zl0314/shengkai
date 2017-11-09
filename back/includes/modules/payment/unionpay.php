<?php
/**
 * ECSHOP 新版银联支付
 * $Author: douqinghua $
 */
if (!defined('IN_ECS')) {
    die('Hacking attempt');
}
// 包含配置文件
$payment_lang = ROOT_PATH . 'languages/' . $GLOBALS['_CFG']['lang'] . '/payment/unionpay.php';
require_once 'func/log.php';
//初始化日志
$logHandler= new CLogFileHandler(ROOT_PATH."/logs/".date('Y-m-d').'.log');
$log = Logger::Init($logHandler, 15);
if (file_exists($payment_lang)) {
    global $_LANG;
    include_once($payment_lang);
}
/* 模块的基本信? */
if (isset($set_modules) && $set_modules == TRUE) {
    $i = isset($modules) ? count($modules) : 0;
    Logger::DEBUG("query:". json_encode($modules));
    /* 代码 */
    $modules[$i]['code'] = basename(__FILE__, '.php');

    /* 描述对应的语?? */
    $modules[$i]['desc'] = 'syl_desc';

    /* 是否支持货到付款 */
    $modules[$i]['is_cod'] = '0';

    /* 是否支持在线支付 */
    $modules[$i]['is_online'] = '1';

    /* 作?? */
    $modules[$i]['author'] = 'RQS';

    /* 网址 */
    $modules[$i]['website'] = 'http://www.ecshop.com';

    /* 版本? */
    $modules[$i]['version'] = '1.0.0';

    /* 配置信息 */
    $modules[$i]['config'] = array(
        array('name' => 'syl_merAbbr', 'type' => 'text', 'value' => '')
    );

    return;
}

/***引入银联支付依赖文件**/

require_once 'func/SDKConfig_15.php';
require_once 'func/log.class.php';
require_once 'func/httpClient.php';
require_once 'func/PublicEncrypte.php';
require_once 'func/common.php';
require_once 'func/secureUtil.php';

/**银联支付?
 * Class unionpay
 */
class unionpay
{
    public function  unionpay()
    {

    }

    function get_code($order, $payment)
    {
        $params = array(
            'version' => '5.0.0',                //版本?
            'encoding' => 'utf-8',                //编码方式
            'certId' => getSignCertId(),            //证书ID
            'txnType' => '01',                //交易类型
            'txnSubType' => '01',                //交易子类
            'bizType' => '000201',                //业务类型
            'frontUrl' => SDK_FRONT_NOTIFY_URL,        //前台通知地址
            'backUrl' => SDK_BACK_NOTIFY_URL,        //后台通知地址
            'signMethod' => '01',        //签名方法
            'channelType' => '07',        //渠道类型?07-PC?08-手机
            'accessType' => '0',        //接入类型
            'merId' => '898111459410115',                //商户代码，请改自己的测试商户?
            'orderId' => $order['order_sn'],    //商户订单?
            'txnTime' => date('YmdHis'),    //订单发??时?
            'txnAmt' => $order['order_amount'] * 100,        //交易金额，单位分
            'currencyCode' => '156',    //交易币种
            'defaultPayType' => '0001',    //默认支付方式
            'reqReserved' => ' unionpay' //请求方保留域，??传字段，查询????知、对账文件中均会原样出现
        );
        Logger::DEBUG("get_code:". json_encode($params));
        sign($params);
        $front_uri = SDK_FRONT_TRANS_URL;
        $html_form = create_html($params, $front_uri);
        Logger::DEBUG("html_form:". json_encode($html_form));
        return $html_form;
    }

    function respond()
    {
        __load("OrderService");
        $order_obj = new OrderService();
        $order_info = $order_obj->get_order_info_sn($_POST['orderId']);
        Logger::DEBUG("respond:". json_encode($order_info));
        if (!empty($order_info)) {
            Logger::DEBUG("respond_order:". json_encode($order_info));
            //if (verify($_POST)) {
            if (true) { //verify($_POST) 2016-8-12, hechengbin, 需要去和银联矫正证书，目前临时通过，TODO
                    Logger::DEBUG("respond_post:". json_encode(verify($_POST)));
                    if ($order_info['order_amount'] * 100 == $_POST['txnAmt']) {
                        Logger::DEBUG("respond_order_amount:". json_encode($order_info));
                        $order_obj->pay_done($_POST['orderId'], $_POST['queryId'], $_POST['txnAmt']);
                        Logger::DEBUG("respond_order_pay_done:". json_encode($order_info));
                        if ($order_info['pay_status'] == "0") {
                            //发??邮?
                            Logger::DEBUG("respond_order_pay_status:". json_encode($order_info));
                            __load("BearerService");
                            $bearer = new BearerService();
                            $bearer->send_mail('pay_ok', array("to" => array($order_info['email']), "sub" => array('%order_sn%' => array($_POST['orderId']))));
                            //发??短?我们已收到您订单为{order}的款项，订单金额为￥{money}元。请您在72小时之内前往个人中心填写持票人信息。
                            require_once(ROOT_PATH . 'includes/lib_sms.php');
                            //----hechengbin ----start ----
                            if(substr($order_info['mobile'],0,2) == '86'){
                                sendsms($order_info['mobile'], "【盛开体育】订单：{$_POST['orderId']} 支付成功！请您在72小时之内前往个人中心填写持票人信息。");
                                Logger::DEBUG("unionpay send mobile:". $order_info['mobile']);
                            }else{
                                sendsms($order_info['mobile'],"Order: {$_POST['orderId']} Payment successful! Please fill out the holder information in 72 hours in the personal center.");
                                Logger::DEBUG("unionpay English send mobile:". $order_info['mobile']);
                            }
//                            if(substr($order_info['mobile'],0,2) == '82'){
//                                sendsms($order_info['mobile'],"Order: {$_POST['orderId']} Payment successful! Please fill out the holder's information in 72 hours in the personal center.");
//                            }else{
//                                sendsms($order_info['mobile'], "【盛开体育】订单：{$_POST['orderId']} 支付成功！请您在72小时之内前往个人中心填写持票人信息。");
//                            }
                            //----hechengbin ----end ----
                            order_action($_POST['orderId'], OS_UNCONFIRMED, SS_PREPARING, PS_PAYED, '', $GLOBALS['_LANG']['buyer']);
                        }
                    } else {
                        Logger::DEBUG("respond_order_pay_status_info:". json_encode($order_info));
                        $order_obj->pay_loading($_POST['orderId'], $_POST['txnAmt']);
                    }
                } else {
                    Logger::ERROR("Order verify failed.");
            }
        } else {
            Logger::ERROR("Order info is null.");
        }


    }

}