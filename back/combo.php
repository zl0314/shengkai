<?php

/**
 * ECSHOP 套餐文件
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
include_once('includes/cls_json.php');
$act = empty($_REQUEST['act']) ? "index" : trim($_REQUEST['act']);
if ($act == "index") {
    $combo_id = !empty($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
    if(!empty($combo_id)){
        /* 套餐-新版 */
        __load("ComboService");
        $combo_obj = new ComboService();
        $combo_info = $combo_obj->get_Combo_index($combo_id);
        if(empty($combo_info)){
           return false; 
        }else{
            /* 获取运动类别列表 start */
            __load("SportcatService");
            $sportcat_obj = new SportcatService();
            $sportcat_list = $sportcat_obj->get_sportcat_list();
            /* 获取运动类别列表 end */
            $number=get_cart_num(SESS_ID);
            $num = 0;
            foreach ($number as $value){
                $num += $value['goods_number'];
            }
            //    echo $num;die;
            $smarty->assign('num',$num);
            $smarty->assign('yuanquan',1);
//             $GLOBALS['smarty']->assign('yuanquan','');
             $GLOBALS['smarty']->assign('combo_id', $combo_id);
             $GLOBALS['smarty']->assign('combo_info', $combo_info);
             $GLOBALS['smarty']->display('combo.dwt');
        }
    }else{
        return false;
    }
}elseif($act == "reserve"){
    $combo_id = $_REQUEST['combo_id'];
    __load("ComboService");
    $combo_obj = new ComboService();
    $combo_list = $combo_obj->get_combo_info($combo_id);
    //获取套餐球票信息
    $combo = json_decode($combo_list['combo_tickets'],true);
    //遍历球票信息
    foreach($combo['default'] as $key=>$value) {
        $id = explode('|', $value);
        $goods_id = $id[1];
        $sql = "SELECT * FROM " . $GLOBALS['ecs']->table('goods') . "WHERE goods_id = '" . $goods_id . "'";
        $res[$key] = $GLOBALS['db']->getRow($sql);
    }
    foreach ($res as $k=>$val){
        if ($val['goods_number'] == '0') {
            $json = new JSON();
            $data = $json->encode(false);
            print_r($data);
            exit;
        }
    }
    __load("CartService");
    $cart_obj = new CartService();
    $cart_list=array(
        "combo_id"=>$combo_id,
        "goods_name"=>$combo_list['combo_name'],
        "goods_price"=>$combo_list['combo_price'],
        "goods_type"=> "combo",
        "goods_number"=>1,
    );
    $res = $cart_obj->add_combo_to_cart($cart_list);
    $json = new JSON();
    $data = $json->encode(true);
    print_r($data);
    exit;
}elseif ($act == "ajax") {
    __load("Combo_orderService");
    $combo_order_obj = new Combo_orderService();
    $res = $combo_order_obj->add_combo_order($_POST);
    $json = new JSON();
    $data = $json->encode($res);
    print_r($data);
    exit;
}elseif($act == "chakan"){
    $p = $_REQUEST['page'];
    $num = $_REQUEST['num'];
    $page = $p*$num;
    __load("ComboService");
    $combo_obj = new ComboService();
    $combo_list = $combo_obj->get_combo_show($page,$num);
    $json = new JSON();
    $data = $json->encode($combo_list);
    print_r($data);
    exit;
}else{
    return false;
}

//判断购物车是否有商品
function get_cart_num($sess){
    $sql="SELECT c.* FROM sk_cart AS c WHERE c.session_id = '$sess' AND (c.goods_type = 'ticket' OR c.goods_type='goods' OR c.goods_type='plane' OR c.goods_type = 'combo')";
    return $GLOBALS['db']->getAll($sql);
}

?>