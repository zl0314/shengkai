<?php

/**
 * ECSHO"submit" mobile首页
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
define('ECS_ADMIN', true);

require(dirname(__FILE__) . '/includes/init.php');
$act = empty($_REQUEST['act']) ? "index" : trim($_REQUEST['act']);
/* 获取赛事列表 start */
__load("GameService");
$game_obj = new GameService();
$game_list = $game_obj->get_list();
if(!empty($game_id)){
    $game = $game_obj->get_game($game_id);

    $smarty->assign('game', $game);

    //根据赛事自动查询城市
    __load("RegionService");
    $RegionService = new RegionService();
    $schedule_id = $RegionService->get_schedule($game_id);

    $smarty->assign('schedule_id', $schedule_id);
}
/* 获取赛事列表 end */
$combo= $GLOBALS['db']->getALL('select * from sk_combo');
$sportcat_list = $GLOBALS['db']->getAll('SELECT id,name FROM sk_sportcat where is_display=1');
$smarty->assign('sportcat_list', $sportcat_list);
if($act == 'index'){
    $namel="<img src='images/logo.png'>";
    $smarty->assign( 'name1',$namel);
    $smarty->display("login.html");
}elseif($act == 'forgot_password'){
    $namel="<img src='images/logo.png'>";
    $smarty->assign( 'name1',$namel);
    $smarty->display("forgot_password.html");
}elseif($act == 'set_password'){
    $namel="<img src='images/logo.png'>";
    $smarty->assign( 'name1',$namel);
    $smarty->display("set_password.html");
} //检查验证码是否与手机号对照
elseif ($act == 'is_verifycode') {
    $mobile_phone = trim($_GET['mobile_phone']);
    $zh_verifycode = trim($_GET['zh_verifycode']);
    $_SESSION['mobile_phone'] = $mobile_phone;
    if ($user->check_is_verifycode($zh_verifycode, $mobile_phone)) {
        echo 'true';
    } else {
        echo 'false';
    }
}//替换用户密码
elseif ($act == 'is_tihuan') {
    include_once(ROOT_PATH . 'includes/modules/integrates/integrate.php');
    $mobile_phone = trim($_SESSION['mobile_phone']);
    $new_mm = trim($_REQUEST['new_mm']);
    if ($user->check_is_tihuan($mobile_phone, $new_mm)) {
        echo 'true';
    } else {
        echo 'false';
    }
    exit;
}//修改成功
elseif($act == 'success'){
    $namel="<img src='images/logo.png'>";
    $smarty->assign( 'name1',$namel);
    $smarty->display("login.html");
}
function check_is_verifycode($zh_verifycode, $mobile_phone)
{
    $sql = "SELECT * FROM " . $GLOBALS['ecs']->table('users') . "WHERE mobile_phone = $mobile_phone";
    $res = $GLOBALS['db']->getOne($sql);
    if ($res) {
        $sql = "SELECT * FROM " . $GLOBALS['ecs']->table('verifycode') . "WHERE mobile=$mobile_phone AND verifycode=$zh_verifycode";
        $result = $GLOBALS['db']->getOne($sql);
        if ($result) {
            echo true;
        } else {
            echo false;
        }
    } else {
        echo false;
        exit;
    }
}

?>
