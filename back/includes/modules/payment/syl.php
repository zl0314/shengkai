<?php

/**
 * ECSHOP 上海银联在线支付
 * ============================================================================
 * 版权所有 2005-2010 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: douqinghua $
 * $Id: upop.php 17063 2010-03-25 06:35:46Z douqinghua $
 */

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

// 包含配置文件
$payment_lang = ROOT_PATH . 'languages/' .$GLOBALS['_CFG']['lang']. '/payment/syl.php';

if (file_exists($payment_lang))
{
    global $_LANG;

    include_once($payment_lang);
}

/* 模块的基本信息 */
if (isset($set_modules) && $set_modules == TRUE)
{
    $i = isset($modules) ? count($modules) : 0;

    /* 代码 */
    $modules[$i]['code']    = basename(__FILE__, '.php');

    /* 描述对应的语言项 */
    $modules[$i]['desc']    = 'syl_desc';

    /* 是否支持货到付款 */
    $modules[$i]['is_cod']  = '0';

    /* 是否支持在线支付 */
    $modules[$i]['is_online']  = '1';

    /* 作者 */
    $modules[$i]['author']  = 'ECSHOP TEAM';

    /* 网址 */
    $modules[$i]['website'] = 'http://www.ecshop.com';

    /* 版本号 */
    $modules[$i]['version'] = '1.0.0';

    /* 配置信息 */
    $modules[$i]['config'] = array(
        array('name' => 'syl_merAbbr', 'type' => 'text', 'value' => ''),
        //array('name' => 'upop_account', 'type' => 'text', 'value' => ''),
        //array('name' => 'upop_security_key', 'type' => 'text', 'value' => ''),
    );

    return;
}

/**
 * 类
 */
class syl
{
    function getSiteUrl(){
        $host = $_SERVER[SERVER_NAME];
        $port = ($_SERVER[SERVER_PORT]=="80")?"":":$_SERVER[SERVER_PORT]";
        return "http://" . $host . $port . $this->getcwdOL();
    }

    function getcwdOL()
    {
        $total = $_SERVER[PHP_SELF];
        $file = explode("/", $total);
        $file = $file[sizeof($file)-1];
        return substr($total, 0, strlen($total)-strlen($file)-1);
    }





    function get_code($order, $payment)
    {
      

    }

    function create_html($merid , $ordid , $transamt , $curyid ,$transdate , $transtype , $order_log_id,$act_url,$frontEndUrl)
    {
        $html = <<<eot
<form action={$act_url} method="post" target="_blank">
<input type="hidden" name="MerId" value="{$merid}"  />
<input type="hidden" name="Version" value="20070129" />
<input type="hidden" name="OrdId" value="{$ordid}" />
<input type="hidden" name="TransAmt" value="{$transamt}" />
<input type="hidden" name="CuryId" value="{$curyid}" />
<input type="hidden" name="TransDate" value="{$transdate}" />
<input type="hidden" name="TransType" value="{$transtype}" />
<input type="hidden" name="BgRetUrl" value="{$frontEndUrl}"/>
<input type="hidden" name="PageRetUrl" value="{$frontEndUrl}"/>
<input type="hidden" name="GateId" value=""/>
<input type="hidden" name="Priv1" value="{$order_log_id}" />
<input type="hidden" name="ChkValue" value="{$this->chkvalue}" />
<input type="submit" value="支付">
</form>
eot;

        return $html;
    }


    /**
     * 响应操作
     $_REQUEST["Priv1"] 为 log_id
     */
    function respond()
    {
        $payment  = get_payment($_GET['code']);
        $merid=$this->config($payment);
        $flag = buildKey(PUB_KEY);
        //获取交易应答的各项值
        $merid = $_REQUEST["merid"];
        $orderno = $_REQUEST["orderno"];
        $transdate = $_REQUEST["transdate"];
        $amount = $_REQUEST["amount"];
        $currencycode = $_REQUEST["currencycode"];
        $transtype = $_REQUEST["transtype"];
        $status = $_REQUEST["status"];
        $checkvalue = $_REQUEST["checkvalue"];
        $gateId = $_REQUEST["GateId"];
        $priv1 = $_REQUEST["Priv1"];
        $flag = verifyTransResponse($merid, $orderno, $amount, $currencycode, $transdate, $transtype, $status, $checkvalue);
        if( flag && $status == '1001')
        {
            // 检查价格是否一致
            if (!check_money($priv1, $amount/100))
            {
               return false;
            }
            order_paid($priv1, 2);
            return true;
        }
        else
        {
            return false;
        }
    }
    /**
    * 格式订单号
    */
    function _formatSN($sn)
    {
        return str_repeat('0', 9 - strlen($sn)) . $sn;
    }

    function sign($params,$security_key,$sign_method)
    {
        if (strtolower($sign_method) == "md5") 
        {
            ksort($params);
            $sign_str = "";
            $sign_ignore_params=array('bank','signMethod','signature');
            foreach ($params as $key => $val)
            {
                if (in_array($key,$sign_ignore_params)) 
                {
                    continue;
                }
                $sign_str .= sprintf("%s=%s&", $key, $val);
            }
            return md5($sign_str . md5($security_key));
        }
        else 
        {
            exit("Unknown sign_method set in quickpay_conf");
        }
    }

}
?>