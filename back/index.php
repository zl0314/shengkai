<?php

/**
 * ECSHOP 首页文件
 * ============================================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: index.php 17217 2011-01-19 06:29:08Z liubo $
 */
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');


require(ROOT_PATH . 'includes/modules/payment/func/log.php');
//初始化日志
$logHandler= new CLogFileHandler(ROOT_PATH."/logs/".date('Y-m-d').'.log');
$log = Logger::Init($logHandler, 15);

/* 载入语言文件 */
require_once(ROOT_PATH . 'languages/' . $_CFG['lang'] . '/user.php');
if ((DEBUG_MODE & 2) != 2) {
    $smarty->caching = true;
}
/*判断是否设置自动登录*/
if (!empty($_COOKIE['username']) && !empty($_COOKIE['password'])) {
    $username = $_COOKIE['username'];
    $password = $_COOKIE['password'];
    $user->login($username, $password);

    $smarty->assign('username', $username);
}

/*判断是否已经登陆*/
else if(!empty($_SESSION['user_name'])){
    $smarty->assign('username', $_SESSION['user_name']);
}


$ua = strtolower($_SERVER['HTTP_USER_AGENT']);

$uachar = "/(nokia|sony|ericsson|mot|samsung|sgh|lg|philips|panasonic|alcatel|lenovo|cldc|midp|mobile)/i";

if(($ua == '' || preg_match($uachar, $ua))&& !strpos(strtolower($_SERVER['REQUEST_URI']),'wap'))
{
    ecs_header("Location:mobile/");
    exit;
}

if($_REQUEST['act']=='mobile'){
        show_message("不支持手机端购买,请在电脑端进行购买<br/> http://webshop.shankaisports.com", '', '', 'warning');
}
$position = assign_ur_here();
$smarty->assign('page_title', $position['title']);    // 页面标题
$smarty->assign('ur_here', $position['ur_here']);  // 当前位置

/* meta information */
$smarty->assign('keywords', htmlspecialchars($_CFG['shop_keywords']));
$smarty->assign('description', htmlspecialchars($_CFG['shop_desc']));

/* 获取赛事列表 start */
$game_id=$_GET['game_id'];
__load("GameService");

$game_obj = new GameService();
$game_list = $game_obj->get_list();
$smarty->assign('game_list',$game_list);
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

/* 获取运动类别列表 start */
__load("SportcatService");
$sportcat_obj = new SportcatService();
$sportcat_list = $sportcat_obj->get_sportcat_list();
$number=get_cart_num(SESS_ID);
$num = 0;
foreach ($number as $value){
    $num += $value['goods_number'];
}
//    echo $num;die;
$smarty->assign('num',$num);
$smarty->assign('yuanquan',1);

/* banner图 start */
__load("BannerService");
$banner_obj = new BannerService();
$top_banner = $banner_obj->get_banner_list("index_top");
$index_video = $banner_obj->get_banner_list("index_video");
$smarty->assign('index_video', $index_video[0]);
$smarty->assign('top_banner', $top_banner);
/* banner图 end */
$bill_video = get_bill_list();
$smarty->assign('video', $bill_video);

/* 套餐 */
__load("Set_mealService");
$set_meal_obj = new Set_mealService();
$set_meal_list=$set_meal_obj->get_set_meal_show();
$smarty->assign('set_meal_list', $set_meal_list);

/* 套餐-新版 */
__load("ComboService");
$combo_obj = new ComboService();
$page = !empty($_REQUEST['page']) ? $_REQUEST['page'] : 0;
$num = 8;
$combo_list = $combo_obj->get_combo_show($page,$num);
/* 统计套餐数量*/
$count = count(get_combo_num());
if($count > 8){
    $smarty->assign('combo_num',$count);
}
$smarty->assign('page',$page);
$smarty->assign('combo_list', $combo_list);

/* 热门赛事 start */
__load("NumberService");
$number_obj = new NumberService();
$hot_number = $number_obj->get_hot_number(4);
$smarty->assign('hot_number', $hot_number);


$smarty->display('index.dwt', $cache_id);

//判断购物车是否有商品
function get_cart_num($sess){
    $sql="SELECT c.* FROM sk_cart AS c WHERE c.session_id = '$sess' AND (c.goods_type = 'ticket' OR c.goods_type='goods' OR c.goods_type='plane' OR c.goods_type = 'combo')";
    return $GLOBALS['db']->getAll($sql);
}

//查询首页广告视频
function get_bill_list(){
    $sql = "SELECT * FROM ".$GLOBALS['ecs']->table('bill')." WHERE media_type = 2";
    return $GLOBALS['db']->getRow($sql);
}

//统计套餐数量
function get_combo_num($p,$num){
    $sql = "SELECT * FROM ".$GLOBALS['ecs']->table('combo')." WHERE is_show = 1 ";
    return $GLOBALS['db']->getAll($sql);
}

?>
