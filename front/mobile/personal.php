<?php

/**
 * ECSHOP mobile首页
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
require(dirname(__FILE__) . '/includes/init.php');
$user_id = $_SESSION['user_id'];
$act = empty($_REQUEST['act']) ? "index" : trim($_REQUEST['act']);
if( $act == 'index'){
    $number=get_cart_num(SESS_ID);
    $num = 0;
    foreach ($number as $value){
        $num += $value['goods_number'];
    }
    $smarty->assign('num',$num);
    $smarty->assign('yuanquan',1);
    $namel="<img src='images/logo.png'>";
    $smarty->assign( 'name1',$namel);
    $smarty->display("personal.html");
} elseif ( $act == "add_user") {
    include_once('includes/cls_json.php');
    include_once(ROOT_PATH . 'includes/lib_transaction.php');
    $json = new JSON;
    $consignee = array(
        'user_id' => $user_id,
        'consignee' => $_POST['consignee'],
        'email' => $_POST['email'],
        'mobile' => $_POST['mobile'],
        'card_type' => $_POST['card_type'],
        'card_num' => $_POST['card_num']
    );
    if ($consignee['consignee'] == '' || $consignee['email'] == '' || $consignee['mobile'] == '' || $consignee['card_num'] == '') {
        echo $json->encode(array('code' => 2, 'msg' => '信息不全'));
        exit;
    } else {
        $res = $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table("user_address"), $consignee, 'INSERT');
        $address_id = $GLOBALS['db']->insert_id();
        if ($address_id > 0) {
            save_consignee($address_id);
            echo $json->encode(array('code' => 1, 'msg' => '添加联系人成功'));
            exit;
            $number=get_cart_num(SESS_ID);
            $num = 0;
            foreach ($number as $value){
                $num += $value['goods_number'];
            }
            $smarty->assign('num',$num);
            $smarty->assign('yuanquan',1);
            $name="联系地址管理";
            $smarty->assign( 'name',$name);

            $smarty->display('user_address.html');
        } else {
            echo $json->encode(array('code' => 2, 'msg' => 'error'));
            exit;
        }
    }
}elseif($act = "update_address"){
    $user_id = $_SESSION['user_id'];
    $address_id = $_POST['address_id'];
    $data = array(
        'consignee' => $_POST['consignee'],
        'email' => $_POST['email'],
        'card_type' => $_POST['card_type'],
        'mobile' => $_POST['mobile'],
        'card_num' => $_POST['card_num']
    );
    //获取用户当前信息
    $users = get_users_info($address_id,$user_id);
    if(!empty($users)){
         save_consignee_address($data,$user_id,$address_id);
            include_once(ROOT_PATH . 'includes/lib_transaction.php');
            include_once(ROOT_PATH . 'languages/' . $_CFG['lang'] . '/shopping_flow.php');
            $smarty->assign('lang', $_LANG);

            /* 取得国家列表、商店所在国家、商店所在国家的省列表 */
            $smarty->assign('country_list', get_regions());
            $smarty->assign('shop_province_list', get_regions(1, $_CFG['shop_country']));

            /* 获得用户所有的收货人信息 */
            $consignee_list = get_consignee_list($user_id);
            $card_type_text = array(0 => '身份证', 1 => '护照');
            //取得国家列表，如果有收货人列表，取得省市区列表
            foreach ($consignee_list AS $region_id => $consignee) {
                $consignee['country'] = isset($consignee['country']) ? intval($consignee['country']) : 0;
                $consignee['province'] = isset($consignee['province']) ? intval($consignee['province']) : 0;
                $consignee['city'] = isset($consignee['city']) ? intval($consignee['city']) : 0;

                $province_list[$region_id] = get_regions(1, $consignee['country']);
                $city_list[$region_id] = get_regions(2, $consignee['province']);
                $district_list[$region_id] = get_regions(3, $consignee['city']);
                $consignee_list[$region_id]['country_info'] = get_region_name($consignee['country']);
                $consignee_list[$region_id]['province_info'] = get_region_name($consignee['province']);
                $consignee_list[$region_id]['city_info'] = get_region_name($consignee['city']);
                $consignee_list[$region_id]['district_info'] = get_region_name($consignee['district']);
                $consignee_list[$region_id]['card_type_text'] = !empty($card_type_text[$consignee_list[$region_id]['card_type']]) ? $card_type_text[$consignee_list[$region_id]['card_type']] : '';
            }
            $smarty->assign('consignee_list', $consignee_list);
            /* 获取默认收货ID */
            $address_id = $db->getOne("SELECT address_id FROM " . $ecs->table('users') . " WHERE user_id='$user_id'");

            //赋值于模板
            $smarty->assign('real_goods_count', 1);
            $smarty->assign('shop_country', $_CFG['shop_country']);
            $smarty->assign('shop_province', get_regions(1, $_CFG['shop_country']));
            // $smarty->assign('province_list', $province_list);
            $smarty->assign('address', $address_id);
            // $smarty->assign('city_list', $city_list);
            // $smarty->assign('district_list', $district_list);
            $smarty->assign('currency_format', $_CFG['currency_format']);
            $smarty->assign('integral_scale', $_CFG['integral_scale']);
            $smarty->assign('name_of_region', array($_CFG['name_of_region_1'], $_CFG['name_of_region_2'], $_CFG['name_of_region_3'], $_CFG['name_of_region_4']));
            $name="联系地址管理";
            $smarty->assign( 'name',$name);

            $smarty->display('user_address.html');
        }


}

$namel="<img src='images/logo.png'>";
$smarty->assign( 'name1',$namel);
//获取当前用户的信息
function get_users_info($address_id,$user_id){
    $sql = "SELECT * FROM ".$GLOBALS['ecs']->table('user_address')." WHERE address_id='".$address_id."' AND user_id='".$user_id."'";
    return $GLOBALS['db']->getRow($sql);
}

//更新联系人信息
function save_consignee_address($data,$user_id,$address_id){
    $sql = "UPDATE sk_user_address SET consignee='".$data['consignee']."', email='".$data['email']."',mobile='".$data['mobile']."',card_type='".$data['card_type']."',card_num='".$data['card_num']."' WHERE user_id='".$user_id."' AND address_id='".$address_id."' ";
    return $GLOBALS['db']->query($sql);
}
//判断购物车是否有商品
function get_cart_num($sess){
    $sql="SELECT c.* FROM sk_cart AS c WHERE c.session_id = '$sess' AND (c.goods_type = 'ticket' OR c.goods_type='goods' OR c.goods_type='plane' OR c.goods_type = 'combo')";
    return $GLOBALS['db']->getAll($sql);
}
?>
