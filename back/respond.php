<?php

/**
 * ECSHOP 支付响应页面
 * ============================================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: respond.php 17217 2011-01-19 06:29:08Z liubo $
 */

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require(ROOT_PATH . 'includes/lib_payment.php');
require(ROOT_PATH . 'includes/lib_order.php');
require(ROOT_PATH . 'includes/modules/payment/func/log.php');

//初始化日志
$logHandler= new CLogFileHandler(ROOT_PATH."/logs/".date('Y-m-d').'.log');
$log = Logger::Init($logHandler, 15);

Logger::DEBUG("union pay call back us");
/* 支付方式代码 */
$pay_code = !empty($_REQUEST['code']) ? trim($_REQUEST['code']) : '';

//获取首信支付方式
if (empty($pay_code) && !empty($_REQUEST['v_pmode']) && !empty($_REQUEST['v_pstring']))
{
    $pay_code = 'cappay';
}

if(isset($_POST['MerRemark'])  && $_POST['MerRemark']=='epay')
{
    $pay_code ='epay';
}

//获取快钱神州行支付方式
if (empty($pay_code) && ($_REQUEST['ext1'] == 'shenzhou') && ($_REQUEST['ext2'] == 'ecshop'))
{
    $pay_code = 'shenzhou';
}

//获取新版银联支付方式
if (empty($pay_code) && ($_REQUEST['reqReserved'] == 'unionpay'))
{
    $pay_code = 'unionpay';
}
if (empty($pay_code) && ($_REQUEST['reqReserved'] == 'unionpay_Online'))
{
    $pay_code = 'unionpay_Online';
}

Logger::DEBUG("pay code is:".$pay_code);

/* 参数是否为空 */
if (empty($pay_code))
{
    Logger::DEBUG("no pay node");
    $msg = $_LANG['pay_not_exist'];
}
else
{
    Logger::DEBUG("pay code is not null");
    /* 检查code里面有没有问号 */
    if (strpos($pay_code, '?') !== false)
    {
        $arr1 = explode('?', $pay_code);
        $arr2 = explode('=', $arr1[1]);
        $_REQUEST['code']   = $arr1[0];
        $_REQUEST[$arr2[0]] = $arr2[1];
        $_GET['code']       = $arr1[0];
        $_GET[$arr2[0]]     = $arr2[1];
        $pay_code           = $arr1[0];
    }
    Logger::DEBUG("strip the ?");
    /* 判断是否启用 */
    $sql = "SELECT COUNT(*) FROM " . $ecs->table('payment') . " WHERE pay_code = '$pay_code' AND enabled = 1";
    if ($db->getOne($sql) == 0)
    {
        Logger::DEBUG("the paycode ".$pay_code." is disabled.");
        $msg = $_LANG['pay_disabled'];
    }
    else
    {
        $plugin_file = 'includes/modules/payment/' . $pay_code . '.php';

        Logger::DEBUG("plugin file:".$plugin_file);
        /* 检查插件文件是否存在，如果存在则验证支付是否成功，否则则返回失败信息 */
        if (file_exists($plugin_file))
        {
            Logger::DEBUG("try to include the file");
            /* 根据支付方式代码创建支付类的对象并调用其响应操作方法 */
            include_once($plugin_file);
            Logger::DEBUG("try to call @payment->respond()");
            $payment = new $pay_code();
            $msg     = (@$payment->respond()) ? $_LANG['pay_success'] : $_LANG['pay_fail'];
            Logger::DEBUG("@paytment return the message is:".$msg);
        }
        else
        {
            Logger::DEBUG("the payment plugin file does not exist: ".$plugin_file);
            $msg = $_LANG['pay_not_exist'];
        }
    }
}

Logger::DEBUG("repsond over");
assign_template();
$position = assign_ur_here();
$smarty->assign('page_title', $position['title']);   // 页面标题
$smarty->assign('ur_here',    $position['ur_here']); // 当前位置
$smarty->assign('page_title', $position['title']);   // 页面标题
$smarty->assign('ur_here',    $position['ur_here']); // 当前位置
$smarty->assign('helps',      get_shop_help());      // 网店帮助

$smarty->assign('message',    $msg);
$smarty->assign('shop_url',   $ecs->url());

$smarty->display('respond.dwt');

?>