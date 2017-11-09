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
//var_dump($_SESSION);die();
/*判断是否设置自动登录*/
if (!empty($_COOKIE['username']) && !empty($_COOKIE['password'])) {
    $username = $_COOKIE['username'];
    $password = $_COOKIE['password'];
    $user->login($username, $password);

    $smarty->assign('username', $username);
//    $ucdata = isset($user->ucdata)? $user->ucdata : '';
//    show_message($_LANG['login_success'] . $ucdata , array($_LANG['back_up_page'], $_LANG['profile_lnk']), array('user.php','user.php'), 'info');
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
/* ------------------------------------------------------ */
//-- Shopex系统地址转换
/* ------------------------------------------------------ */
//if (!empty($_GET['gOo']))
//{
//    if (!empty($_GET['gcat']))
//    {
//        /* 商品分类。*/
//        $Loaction = 'category.php?id=' . $_GET['gcat'];
//    }
//    elseif (!empty($_GET['acat']))
//    {
//        /* 文章分类。*/
//        $Loaction = 'article_cat.php?id=' . $_GET['acat'];
//    }
//    elseif (!empty($_GET['goodsid']))
//    {
//        /* 商品详情。*/
//        $Loaction = 'goods.php?id=' . $_GET['goodsid'];
//    }
//    elseif (!empty($_GET['articleid']))
//    {
//        /* 文章详情。*/
//        $Loaction = 'article.php?id=' . $_GET['articleid'];
//    }
//
//    if (!empty($Loaction))
//    {
//        ecs_header("Location: $Loaction\n");
//
//        exit;
//    }
//}
////判断是否有ajax请求
//$act = !empty($_GET['act']) ? $_GET['act'] : '';
//if ($act == 'cat_rec')
//{
//    $rec_array = array(1 => 'best', 2 => 'new', 3 => 'hot');
//    $rec_type = !empty($_REQUEST['rec_type']) ? intval($_REQUEST['rec_type']) : '1';
//    $cat_id = !empty($_REQUEST['cid']) ? intval($_REQUEST['cid']) : '0';
//    include_once('includes/cls_json.php');
//    $json = new JSON;
//    $result   = array('error' => 0, 'content' => '', 'type' => $rec_type, 'cat_id' => $cat_id);
//
//    $children = get_children($cat_id);
//    $smarty->assign($rec_array[$rec_type] . '_goods',      get_category_recommend_goods($rec_array[$rec_type], $children));    // 推荐商品
//    $smarty->assign('cat_rec_sign', 1);
//    $result['content'] = $smarty->fetch('library/recommend_' . $rec_array[$rec_type] . '.lbi');
//    die($json->encode($result));
//}

/* ------------------------------------------------------ */
//-- 判断是否存在缓存，如果存在则调用缓存，反之读取相应内容
/* ------------------------------------------------------ */
/* 缓存编号 */
//$cache_id = sprintf('%X', crc32($_SESSION['user_rank'] . '-' . $_CFG['lang']));
//    assign_template();
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
//根据运动类别查询赛事
//foreach($sportcat_list as $k=>$v){
//    $sportcat_list[$k]['scat'] = get_game($v['id']);
//}
//$smarty->assign('sportcat_list', $sportcat_list);
/* 获取运动类别列表 end */
//

//print_r(SESS_ID);die;
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
$combo_list = $combo_obj->get_combo_show();
$smarty->assign('combo_list', $combo_list);

/* 热门赛事 start */
__load("NumberService");
$number_obj = new NumberService();
$hot_number = $number_obj->get_hot_number(4);
$smarty->assign('hot_number', $hot_number);




/* 热门赛事 End    */

//    $smarty->assign('flash_theme',     $_CFG['flash_theme']);  // Flash轮播图片模板
//
//    $smarty->assign('feed_url',        ($_CFG['rewrite'] == 1) ? 'feed.xml' : 'feed.php'); // RSS URL
//    $smarty->assign('categories',      get_categories_tree()); // 分类树
//    $smarty->assign('helps',           get_shop_help());       // 网店帮助
//    $smarty->assign('top_goods',       get_top10());           // 销售排行
//
//    $smarty->assign('best_goods',      get_recommend_goods('best'));    // 推荐商品
//    $smarty->assign('new_goods',       get_recommend_goods('new'));     // 最新商品
//    $smarty->assign('hot_goods',       get_recommend_goods('hot'));     // 热点文章
//    $smarty->assign('promotion_goods', get_promote_goods()); // 特价商品
//    $smarty->assign('brand_list',      get_brands());
//    $smarty->assign('promotion_info',  get_promotion_info()); // 增加一个动态显示所有促销信息的标签栏
//
//    $smarty->assign('invoice_list',    index_get_invoice_query());  // 发货查询
//    $smarty->assign('new_articles',    index_get_new_articles());   // 最新文章
//    $smarty->assign('group_buy_goods', index_get_group_buy());      // 团购商品
//    $smarty->assign('auction_list',    index_get_auction());        // 拍卖活动
//    $smarty->assign('shop_notice',     $_CFG['shop_notice']);       // 商店公告

/* 首页主广告设置 */
//    $smarty->assign('index_ad',     $_CFG['index_ad']);
//    if ($_CFG['index_ad'] == 'cus')
//    {
//        $sql = 'SE LECT ad_type, content, url FROM ' . $ecs->table("ad_custom") . ' WHERE ad_status = 1';
//        $ad = $db->getRow($sql, true);
//        $smarty->assign('ad', $ad);
//    }
//    /* links */
//    $links = index_get_links();
//    $smarty->assign('img_links',       $links['img']);
//    $smarty->assign('txt_links',       $links['txt']);
//    $smarty->assign('data_dir',        DATA_DIR);       // 数据目录
//
//    /* 首页推荐分类 */
//    $cat_recommend_res = $db->getAll("SELECT c.cat_id, c.cat_name, cr.recommend_type FROM " . $ecs->table("cat_recommend") . " AS cr INNER JOIN " . $ecs->table("category") . " AS c ON cr.cat_id=c.cat_id");
//    if (!empty($cat_recommend_res))
//    {
//        $cat_rec_array = array();
//        foreach($cat_recommend_res as $cat_recommend_data)
//        {
//            $cat_rec[$cat_recommend_data['recommend_type']][] = array('cat_id' => $cat_recommend_data['cat_id'], 'cat_name' => $cat_recommend_data['cat_name']);
//        }
//        $smarty->assign('cat_rec', $cat_rec);
//    }
//    /* 页面中的动态内容 */
//    assign_dynamic('index');

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

//查询赛事
//function get_game($scat_id){
//    $sql = "SELECT id,game_name FROM " .$GLOBALS['ecs']->table('game')." WHERE scat_id = $scat_id";
//    return $GLOBALS['db']->getAll($sql);
//}
?>