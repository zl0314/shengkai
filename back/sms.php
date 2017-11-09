<?php

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
include_once('includes/cls_json.php');
require(ROOT_PATH . 'includes/lib_sms.php');

require_once(ROOT_PATH . 'languages/' . $_CFG['lang'] . '/sms.php');

require_once(ROOT_PATH . 'includes/modules/payment/func/log.php');
//初始化日志
$logHandler= new CLogFileHandler(ROOT_PATH."/logs/".date('Y-m-d').'.log');
$log = Logger::Init($logHandler, 15);

if (!isset($_REQUEST['step'])) {
    $_REQUEST['step'] = "";
}

$result = array('error' => 0, 'message' => '');
$json = new JSON;

$mobile = $_REQUEST['mobile'];
$captcha = isset($_REQUEST['captcha']) ? trim($_REQUEST['captcha']) : '';//图形验证码



$denied_log = '';
if (file_exists("denied.log")) {
    $denied_log = file_get_contents("denied.log");
}

$ip_array = explode(",", $denied_log);

if ($_REQUEST['step'] == 'getverifycode1') {
    /* 是否开启手机短信验证注册 */
    if ($_CFG['ecsdxt_mobile_reg'] == '0') {
        Logger::DEBUG("the sms verify switch is off !");
        $result['error'] = 1;
        $result['message'] = $_LANG['ecsdxt_mobile_reg_closed'];
        die($json->encode($result));
    }


    /* 提交的手机号是否已经注册帐号 */
    $sql = "SELECT COUNT(user_id) FROM " . $ecs->table('users') . " WHERE mobile_phone = '$mobile'";

    if ($db->getOne($sql) > 0) {
        $result['error'] = 3;
        Logger::DEBUG("the mobile already registed!");
        $result['message'] = $_LANG['mobile_phone_registered'];
        die($json->encode($result));
    }
    /*验证图形验证码*/
    if (gd_version() > 0) {
        if (empty($captcha)) {
            $result['error'] = 5;
            $result['message'] = sprintf($_LANG['verifycode_picture'], $_CFG['ecsdxt_smsgap']);
            die($json->encode($result));
        }

        /* 检查验证码 */
        include_once('includes/cls_captcha.php');

        $validator = new captcha();
        $validator->session_word = 'captcha_word';
        if (!$validator->check_word($captcha)) {
            $result['error'] = 6;
            $result['message'] = sprintf($_LANG['verifycode_picture_notmatch'], $_CFG['ecsdxt_smsgap']);
            die($json->encode($result));
        }
    }

    /* 获取验证码请求是否获取过 */
    $sql = "SELECT COUNT(id) FROM " . $ecs->table('verifycode') . " WHERE status=1 AND getip='" . real_ip() . "' AND dateline>'" . gmtime() . "'-" . $_CFG['ecsdxt_smsgap'];
    $IP = real_ip();
    Logger::DEBUG("获得ip :" . $IP);
    if ($db->getOne($sql) > 0) {
        $result['error'] = 4;
        Logger::DEBUG("mobile:duo shao miao !");
        $result['message'] = sprintf($_LANG['get_verifycode_excessived'], $_CFG['ecsdxt_smsgap']);
        die($json->encode($result));
    }

    $verifycode = getverifycode();
    Logger::DEBUG("获得验证码 :" .json_encode($verifycode));
    $smarty->assign('shop_name', $_CFG['shop_name']);
    $smarty->assign('user_mobile', $mobile);
    $smarty->assign('verify_code', $verifycode);

    $content = $smarty->fetch('str:' . $_CFG['ecsdxt_mobile_reg_value_en']);
    Logger::DEBUG("111111111111111111 :" .$content);
    /* 发送注册手机短信验证 */
    if(substr($mobile,0,2) == '86'){//hechengbin 9.28 修改
        $content = $smarty->fetch('str:' . $_CFG['ecsdxt_mobile_reg_value']);
        $ret = sendsms($mobile, $content);
    }else{
        $content = $smarty->fetch('str:' . $_CFG['ecsdxt_mobile_reg_value_en']);
        $ret = sendsms($mobile, $content);
    }

    Logger::DEBUG("短息已经发送，网关返回：" .$ret);
    if ($ret === true) {
        //插入获取验证码数据记录
        $sql = "INSERT INTO " . $ecs->table('verifycode') . "(mobile, getip, verifycode, dateline) VALUES ('" . trim($_REQUEST['mobile']) . "', '" . real_ip() . "', '$verifycode', '" . gmtime() . "')";
        $db->query($sql);
        Logger::DEBUG("短息验证码已经插入数据库：" .$sql);

        $result['error'] = 0;
        $result['message'] = $_LANG['send_mobile_verifycode_successed'];
        Logger::DEBUG("mobile fa song yan zheng duianxin:" .json_encode($result));
        die($json->encode($result));
    } else {
        $statusStr = array(
            "0" => "短信发送成功",
            "-1" => "参数不全",
            "-2" => "服务器空间不支持,请确认支持curl或者fsocket，联系您的空间商解决或者更换空间！",
            "30" => "密码错误",
            "40" => "账号不存在",
            "41" => "余额不足",
            "42" => "帐户已过期",
            "43" => "IP地址限制",
            "50" => "内容含有敏感词"
        );

        $result['error'] = 5;
        $result['message'] = $_LANG['send_mobile_verifycode_failured'] . $statusStr[$ret];
        die($json->encode($result));
    }
} elseif ($_REQUEST['step'] == 'getverifycode2') {
    /* 是否开启手机绑定 */
    if ($_CFG['ecsdxt_mobile_bind'] == '0') {
        $result['error'] = 1;
        $result['message'] = $_LANG['ecsdxt_mobile_bind_closed'];
        die($json->encode($result));
    }



    /* 提交的手机号是否已经绑定帐号 */
    $sql = "SELECT COUNT(user_id) FROM " . $ecs->table('users') . " WHERE mobile_phone = '$mobile'";

    if ($db->getOne($sql) > 0) {
        $result['error'] = 3;
        $result['message'] = $_LANG['mobile_phone_binded'];
        die($json->encode($result));
    }
    /* 获取验证码请求是否获取过 */
    $sql = "SELECT COUNT(id) FROM " . $ecs->table('verifycode') . " WHERE status=1 AND getip='" . real_ip() . "' AND dateline>'" . gmtime() . "'-" . $_CFG['ecsdxt_smsgap'];
    $IP = real_ip();
    Logger::DEBUG("获得ip :" . $IP);
    if ($db->getOne($sql) > 0) {
        $result['error'] = 4;
        Logger::DEBUG("mobile:duo shao miao !");
        $result['message'] = sprintf($_LANG['get_verifycode_excessived'], $_CFG['ecsdxt_smsgap']);
        die($json->encode($result));
    }
    /* 获取验证码请求是否获取过 */
    $sql = "SELECT COUNT(id) FROM " . $ecs->table('verifycode') . " WHERE (status=4 or status=5) AND getip='" . real_ip() . "' AND dateline>'" . gmtime() . "'-" . $_CFG['ecsdxt_smsgap'];

    if ($db->getOne($sql) > 0) {
        $result['error'] = 4;
        $result['message'] = sprintf($_LANG['get_verifycode_excessived'], $_CFG['ecsdxt_smsgap']);
        die($json->encode($result));
    }

    $verifycode = getverifycode();

    $smarty->assign('shop_name', $_CFG['shop_name']);
    $smarty->assign('user_mobile', $mobile);
    $smarty->assign('verify_code', $verifycode);

//    $content = $smarty->fetch('str:' . $_CFG['ecsdxt_mobile_reg_value']);

    /* 发送注册手机短信验证 */
    if(substr($mobile,0,2) == '86'){//hechengbin 9.28 修改
        $content = $smarty->fetch('str:' . $_CFG['ecsdxt_mobile_pwd_value']);
        Logger::DEBUG("mobile is chinese num:" .$mobile);
        $ret = sendsms($mobile, $content);
    }else{
        $content = $smarty->fetch('str:' . $_CFG['ecsdxt_mobile_pwd_value_en']);
        Logger::DEBUG("Korean mobile sms sending:" .$mobile);
        $ret = sendsms($mobile, $content);
    }


    if ($ret === true) {
        //插入获取验证码数据记录
        $sql = "INSERT INTO " . $ecs->table('verifycode') . "(mobile, getip, verifycode, dateline, status) VALUES ('" . $mobile . "', '" . real_ip() . "', '$verifycode', '" . gmtime() . "', 4)";
        $db->query($sql);
        $result['error'] = 0;
        $result['message'] = $_LANG['bind_mobile_verifycode_successed'];
        die($json->encode($result));
    } else {
        $result['error'] = 5;
        $result['message'] = $_LANG['bind_mobile_verifycode_failured'] . $ret;
        die($json->encode($result));
    }
}

//此处为获取找回密码的验证码
else if($_REQUEST['step'] == 'getverifycode3') {



    /* 获取验证码请求是否获取过 */
    $sql = "SELECT COUNT(id) FROM " . $ecs->table('verifycode') . " WHERE status=1 AND getip='" . real_ip() . "' AND dateline>'" . gmtime() . "'-" . $_CFG['ecsdxt_smsgap'];

    if ($db->getOne($sql) > 0) {
        $result['error'] = 4;
        $result['message'] = sprintf($_LANG['get_verifycode_excessived'], $_CFG['ecsdxt_smsgap']);
        die($json->encode($result));
    }
    /* 获取验证码请求是否获取过 */
    $sql = "SELECT COUNT(id) FROM " . $ecs->table('verifycode') . " WHERE status=1 AND getip='" . real_ip() . "' AND dateline>'" . gmtime() . "'-" . $_CFG['ecsdxt_smsgap'];
    $IP = real_ip();
    Logger::DEBUG("获得ip :" . $IP);
    if ($db->getOne($sql) > 0) {
        $result['error'] = 4;
        Logger::DEBUG("mobile:duo shao miao !");
        $result['message'] = sprintf($_LANG['get_verifycode_excessived'], $_CFG['ecsdxt_smsgap']);
        die($json->encode($result));
    }
    $verifycode = getverifycode();

    $smarty->assign('shop_name', $_CFG['shop_name']);
    $smarty->assign('verify_code', $verifycode);

    /* 发送手机短信验证 */
    if(substr($mobile,0,2) == '86'){//hechengbin 9.28 修改
        $content = $smarty->fetch('str:' . $_CFG['ecsdxt_mobile_pwd_value']);
        Logger::DEBUG("mobile is chinese num:" .$mobile);
        $ret = sendsms($mobile, $content);
    }else{
        $content = $smarty->fetch('str:' . $_CFG['ecsdxt_mobile_pwd_value_en']);
        Logger::DEBUG("Korean mobile sms sending:" .$mobile);
        $ret = sendsms($mobile, $content);
    }


    if ($ret === true) {
        //插入获取验证码数据记录
        $sql = "INSERT INTO " . $ecs->table('verifycode') . "(mobile, getip, verifycode, dateline) VALUES ('" . $_REQUEST['mobile'] . "', '" . real_ip() . "', '$verifycode', '" . gmtime() . "')";
        $db->query($sql);

        $result['error'] = 0;
        $result['message'] = $_LANG['send_mobile_verifycode_successed'];
        die($json->encode($result));
    } else {
        $statusStr = array(
            "0" => "短信发送成功",
            "-1" => "参数不全",
            "-2" => "服务器空间不支持,请确认支持curl或者fsocket，联系您的空间商解决或者更换空间！",
            "30" => "密码错误",
            "40" => "账号不存在",
            "41" => "余额不足",
            "42" => "帐户已过期",
            "43" => "IP地址限制",
            "50" => "内容含有敏感词"
        );

        $result['error'] = 5;
        $result['message'] = $_LANG['send_mobile_verifycode_failured'] . $statusStr[$ret];
        die($json->encode($result));
    }
}

?>