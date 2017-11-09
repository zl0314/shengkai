<?php
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
$act = empty($_REQUEST['act']) ? 'default' : $_REQUEST['act'];
if($act == 'default'){
    
    $goods_id=$_REQUEST['goods_ID'];
//$sql = "SELECT goods_desc FROM " . $ecs->table('goods') . " WHERE goods_id = '$goods_ID'";
//        $goods = $db->getOne($sql);
__load("GoodsService");
    $goods_obj = new GoodsService();
$goods = $goods_obj->get_goods_info($goods_id);    

$smarty->assign('goods', $goods); 
$smarty->display('goods_xiangqing.dwt');
exit;
}elseif($act == 'add_to_cart'){
    include_once('includes/cls_json.php');
    $json = new JSON();
    $goods_id = $_GET['goods_id'];
    $goods_number = $_GET['goods_number'];
    __load("GoodsService");
    $goods_obj = new GoodsService();
    $data[0] = $goods_obj->get_goods_info($goods_id);
    $data[0]['goods_price'] = $data[0]['shop_price'];
    __load("CartService");
    $data[0]['goods_number'] = $goods_number;
    
    $cart_obj = new CartService();
    
    $cart_obj->add_ticket_to_cart($data);
    echo "加入购物车成功";
    die;
}elseif($act == 'number'){
    
    $id=$_REQUEST['id'];
__load("NumberService");
    $number_obj = new NumberService();
    $number = $number_obj->get_Number($id);
    $goods = array();
    $goods['goods_desc'] = $number['num_text'];
$smarty->assign('goods', $goods); 
$smarty->display('goods_xiangqing.dwt');
exit;
}
?>