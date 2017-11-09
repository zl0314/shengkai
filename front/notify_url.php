<?php
/* *
 * 功能：支付宝服务器异步通知页面
 * 版本：3.3
 * 日期：2012-07-23
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 * 该代码仅供学习和研究支付宝接口使用，只是提供一个参考。


 *************************页面功能说明*************************
 * 创建该页面文件时，请留心该页面文件中无任何HTML代码及空格。
 * 该页面不能在本机电脑测试，请到服务器上做测试。请确保外部可以访问该页面。
 * 该页面调试工具请使用写文本函数logResult，该函数已被默认关闭，见alipay_notify_class.php中的函数verifyNotify
 * 如果没有收到该页面返回的 success 信息，支付宝会在24小时内按一定的时间策略重发通知
 */

define('IN_ECS', true);
define('ECS_ADMIN', true);

require(dirname(__FILE__) . '/includes/init.php');
require_once("alipay.config.php");
require_once("lib/alipay_notify.class.php");

logResult("支付宝异步回调数据: " . str_replace("\n", "", var_export($_REQUEST, true)));

//计算得出通知验证结果
$alipayNotify = new AlipayNotify($alipay_config);
$verify_result = $alipayNotify->verifyNotify();

if ($verify_result) {//验证成功
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //请在这里加上商户的业务逻辑程序代

    //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——

    //获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表
    $time = gmtime();
    //商户订单号
    $out_trade_no = $_POST['out_trade_no'];
    //支付宝交易号
    //交易时间

    $trade_no = $_POST['trade_no'];
    //交易状态
    $trade_status = $_POST['trade_status'];
    //卖家支付宝账号
    $seller_id = $_POST['seller_id'];
    //交易金额
    $total_fee = $_POST['total_fee'];

    if ($_POST['trade_status'] == 'TRADE_FINISHED') {
        //判断该笔订单是否在商户网站中已经做过处理
        //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
        //请务必判断请求时的total_fee、seller_id与通知时获取的total_fee、seller_id为一致的
        //如果有做过处理，不执行商户的业务程序

        //注意：
        //退款日期超过可退款期限后（如三个月可退款），支付宝系统发送该交易状态通知

        //调试用，写文本函数记录程序运行情况是否正常
        //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
        $order_detail = $GLOBALS['db']->getRow("SELECT * FROM sk_order_info where order_sn= '{$out_trade_no}'");

        if ($order_detail['goods_amount'] == $total_fee) {
            $GLOBALS['db']->query('UPDATE ' . $ecs->table('order_info') . " SET pay_status=2 , order_amount=0, pay_time='$time' WHERE order_sn = '$out_trade_no'");
            logResult("订单付款成功,此订单号是:" . $out_trade_no);
        } else {
            logResult("订单价格错误:" . $total_fee . ' 此错误信息的订单号是:' . $out_trade_no);
            die;
        }
    } else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
        //判断该笔订单是否在商户网站中已经做过处理
        //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
        //请务必判断请求时的total_fee、seller_id与通知时获取的total_fee、seller_id为一致的
        //如果有做过处理，不执行商户的业务程序

        //注意：
        //付款完成后，支付宝系统发送该交易状态通知

        //调试用，写文本函数记录程序运行情况是否正常
        //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
        $order_detail = $GLOBALS['db']->getRow("SELECT * FROM sk_order_info where order_sn='$out_trade_no'");

        if ($order_detail['goods_amount'] == $total_fee) {

            $GLOBALS['db']->query('UPDATE ' . $ecs->table('order_info') . " SET pay_status=2 , order_amount=0 , pay_time= '{$time}' WHERE order_sn = '$out_trade_no'");
            logResult("订单付款成功,此订单号是:" . $out_trade_no);

            //酒店订单
            try {
                __load("Room_numService");
                $order_id = $order_detail['order_id'];
                //检查是否有酒店信息
                $sql = "SELECT room_id, goods_number, hotel_start_date, hotel_end_date FROM " . $ecs->table('order_hotel') . " WHERE order_id = '{$order_id}'";
                $result = $GLOBALS['db']->getAll($sql);
                if (!empty($result)) {
                    $room_num_service = new Room_numService();
                    foreach ($result as $value) {
                        //$ret = $room_num_service->update_order_room_num_used_from_in_date($value['room_id'], $value['goods_number'], $value['hotel_start_date'], $value['hotel_end_date']);
                        //这里只验证更新开始预定时间的库存了
                        $ret = $room_num_service->update_order_room_num_used_from_in_date($value['room_id'], $value['goods_number'], $value['hotel_start_date'], $value['hotel_start_date']);
                        __log("更新订单中酒店预订锁定数量:{$ret}, ORDER_ID = {$order_detail['order_id']}, ROOM_ID = {$value['room_id']}, NUM: {$value['goods_number']}, START DATE: {$value['hotel_start_date']}, START DATE: {$value['hotel_end_date']}");
                    }
                } else {
                    __log("该订单没有酒店信息");
                }
            } catch (Exception $e) {
                __log("ALIPAY ERROR: {$order_detail['order_id']}, {$e->getCode()}, {$e->getFile()}, {$e->getLine()}, {$e->getMessage()}", "error");
            }

            //机票订单
            try {
                __load("Air_line_numService");
                $order_id = $order_detail['order_id'];
                //检查是否有酒店信息
                $sql = "SELECT air_id, fly_date, return_fly_date, goods_number FROM " . $ecs->table('order_plane') . " WHERE order_id = '{$order_id}'";
                $result = $GLOBALS['db']->getAll($sql);
                if (!empty($result)) {
                    $air_line_num_service = new Air_line_numService();
                    foreach ($result as $value) {
                        $ret = $air_line_num_service->update_order_air_line_num_used_from_in_date($value['air_id'], $value['goods_number'], $value['fly_date'], $value['return_fly_date']);
                        __log("更新订单中酒店预订锁定数量:{$ret}, AIR_ID = {$value['air_id']}, NUM: {$value['goods_number']}, START DATE: {$value['fly_date']}, END DATE: {$value['return_fly_date']}");
                    }
                } else {
                    __log("该订单没有酒店信息");
                }
            } catch (Exception $e) {
                __log("ALIPAY ERROR: {$order_detail['order_id']}, {$e->getCode()}, {$e->getFile()}, {$e->getLine()}, {$e->getMessage()}", "error");
            }
        } else {
            logResult("订单价格错误:" . $total_fee . ' 此错误信息的订单号是:' . $out_trade_no);
            die;
        }
    }

    //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——

    echo "success";        //请不要修改或删除

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
} else {
    //验证失败
    echo "fail";

    //调试用，写文本函数记录程序运行情况是否正常
    //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
}
?>