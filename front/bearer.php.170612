<?php

/**
 * 订单检查和确认订单
 * ============================================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo
 * $Id: search.php 17217 2011-01-19 06:29:08Z liubo $
 */
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
require(ROOT_PATH . 'includes/lib_order.php');
require_once(ROOT_PATH . 'languages/' . $_CFG['lang'] . '/user.php');
require_once(ROOT_PATH . 'languages/' . $_CFG['lang'] . '/shopping_flow.php');
require_once(ROOT_PATH . 'includes/lib_sms.php');
include_once('includes/cls_json.php');
$_REQUEST['act'] = empty($_REQUEST['act']) ? "index" : trim($_REQUEST['act']);
__load("BearerService");
$bearer = new BearerService();
if ($_REQUEST['act'] == "index") {
    $user_id = $_SESSION['user_id'];
    if(!empty($_GET['out_trade_no'])) {
        $order_sn = $_GET['out_trade_no'];
    }else {
        $order_sn = empty($_GET['order_sn']) ? null : $_GET['order_sn'];
    }
    if (empty($order_sn)) {
        show_message("未检测到订单,请稍后再试！", '', '', 'warning');
    }
    $order_id = $bearer->check_order_user($order_sn, $user_id); //通过订单号查询订单id
    if (empty($order_id)) {
        show_message("订单不存在，请稍后再试！", '', '', 'warning');
    }

    $pay_status = $bearer->get_pay_status($order_id, $user_id);
    //pay_status 支付状态 2 = 支付成功
    if ($pay_status == 2) {
        //向客户发送短信通知和邮件通知
        //获取当前用户的手机和邮箱
        $query_list = $bearer->query_list($user_id);
        $smarty->assign('shop_name', $_CFG['shop_name']);
        $content = $smarty->fetch('str:' . $_CFG['ecsdxt_customer_payed_value']);
        $content = $smarty->fetch('str:' . $_CFG['ecsdxt_order_picking_value']);
        $bearer_info = $bearer->bearer_info_list($order_id); //查询持票人购物信息
        $combo_info = $bearer->bearer_info_combo($order_id);
        $array = Array();
        $array = $combo_info;
        foreach($combo_info as $key=>$value){
            $array[$key]['num_start'] = date('Y-m-d H:i', strtotime($array[$key]['num_start']));
            if ($combo_info[$key]['bearer_id'] != 0) {
                $this_bearer=$bearer->combo_bearer_info($combo_info[$key]['bearer_id']);
                $array[$key]['passport_number'] = $this_bearer['passport_number'];
                $array[$key]['mobile'] = $this_bearer['mobile'];

            } else {
                $array[$key]['passport_number'] = '';
            }
        }
        $arr = Array();
        $arr = $bearer_info;
        foreach ($bearer_info AS $key => $value) {
            if ($arr[$key]['keywords']) {
                $arr[$key]['keywords'] = explode(' ', $arr[$key]['keywords']);
            }
            $arr[$key]['num_start'] = date('Y-m-d H:i', strtotime($arr[$key]['num_start']));
            if ($bearer_info[$key]['bearer_id'] != 0) {
                $this_bearer=$bearer->bearer_info($bearer_info[$key]['bearer_id']);
                $arr[$key]['passport_number'] = $this_bearer['passport_number'];
                $arr[$key]['mobile'] = $this_bearer['mobile'];

            } else {
                $arr[$key]['passport_number'] = '';
            }
        }
        $bearer_name = $bearer->bearer_name($user_id); //查询持票人名字
        $smarty->assign('combo_info', $array);
        $smarty->assign('bearer_info', $arr);
        $smarty->assign('order_id', $order_id);
        $smarty->assign('order_sn', $order_sn);
        $smarty->assign('bearer_name', $bearer_name);
    } else {
        show_message("非法入口", '', '', 'warning');
    }


    $smarty->display("bearer_list.dwt");
}
if ($_GET['act'] == "add") {
    if ($_GET['step'] == "center_add") {
        $order_sn = $_REQUEST['order_sn'];
        $smarty->assign('act', "add_cop");
        $smarty->assign('temp', "center_add");
        $smarty->assign('order_sn', $order_sn);
        $smarty->assign('rec_id', $_REQUEST['rec_id']);
        $smarty->assign('post_url', "bearer.php?act=edit");
    }else{
        $order_sn = $_REQUEST['order_sn'];
        $smarty->assign('temp', "center_add");
        $smarty->assign('order_sn', $order_sn);
        $smarty->assign('post_url', "bearer.php?act=edit");
        $smarty->assign('rec_id', $_REQUEST['rec_id']);
        $smarty->assign('act', "add_combo");
    }
    $smarty->display("bearer_info.dwt");

}

if ($_GET['act'] == "") {
    if ($_GET['step'] == "order_add") {
        $cpr_order_sn = $_REQUEST['order_sn'];
    }
    $smarty->assign('post_url', "bearer.php?act=cpr_edit&cpr_order_sn={$cpr_order_sn}");
    $smarty->assign('rec_id',$_REQUEST['rec_id']);
    $smarty->assign('act', "add");
    $smarty->display("bearer_info.dwt");

}

if ($_GET['act'] == "edit") {
    if ($_POST['act'] == 'add') {
//        $order_id=$bearer->get_order_id($_SESSION['order_sn']);
        $order_sn = $_REQUEST['order_sn'];
        $smarty->assign('order_sn',$order_sn);
        $_POST['user_id'] = $_SESSION['user_id'];
        $res = $bearer->save_bearer_infos($_POST, "INSERT", '');
        $_POST['user_id'] = $_SESSION['user_id'];
        $new_id = $bearer->insert_id();
        if ($_POST['temp'] == 'center_add') {
            show_message("添加持票人信息成功", array("返回个人中心"), array('user.php?act=sk_bearer_manage'), 'info');
        }
        show_message("添加持票人信息成功", array($_LANG['back_up_page']), array('bearer.php?act=index'), 'info');
    }
    if($_POST['act'] == "add_cop"){
        $order_sn = $_REQUEST['order_sn'];
        $smarty->assign('order_sn',$order_sn);
        $_POST['user_id'] = $_SESSION['user_id'];
        $res = $bearer->save_bearer_infos($_POST, "INSERT", '');
        $_POST['user_id'] = $_SESSION['user_id'];
        $new_id = $bearer->insert_id();
        if(!empty($_POST['rec_id'])){
            $arr['bearer_id'] = $new_id;
            $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table("order_ticket"), $arr, 'UPDATE', 'rec_id=' . $_POST['rec_id']);
        }
        header("location:bearer.php?act=index&order_sn=$order_sn");
    }
    if($_POST['act'] == "add_combo"){
        $order_sn = $_REQUEST['order_sn'];
        $smarty->assign('order_sn',$order_sn);
        $_POST['user_id'] = $_SESSION['user_id'];
        $res = $bearer->save_bearer_infos($_POST, "INSERT", '');
        $_POST['user_id'] = $_SESSION['user_id'];
        $new_id = $bearer->insert_id();
        if(!empty($_POST['rec_id'])){
            $arr['bearer_id'] = $new_id;
            $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table("order_combo"), $arr, 'UPDATE', 'rec_id=' . $_POST['rec_id']);
        }
        header("location:bearer.php?act=index&order_sn=$order_sn");
    }
    if ($_POST['act'] == 'update') {
        $_POST['user_id'] = $_SESSION['user_id'];
        $bearer_id = $_POST['bearer_id'];
        $bearer->save_bearer_infos($_POST, "UPDATE", "id=$bearer_id");
        show_message("修改持票人信息成功", array("返回个人中心"), array('user.php?act=sk_bearer_manage'), 'info');
    }
}
//持票人跳转页面
if ($_GET['act'] == "cpr_edit") {
    $cpr_order_sn = $_REQUEST['cpr_order_sn'];
    $_POST['user_id'] = $_SESSION['user_id'];
    $bearer->save_bearer_infos($_POST, "INSERT", '');
    $new_id = $bearer->insert_id();
    if(!empty($_POST['rec_id'])){
        $arr['bearer_id'] = $new_id;
        $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table("order_ticket"), $arr, 'UPDATE', 'rec_id=' . $_POST['rec_id']);
    }
    header("location:bearer.php?act=index&order_sn=$cpr_order_sn");
}

if ($_GET['act'] == "cpr_edit_new") {
    $cpr_order_sn = $_REQUEST['cpr_order_sn'];
    $_POST['user_id'] = $_SESSION['user_id'];
    $bearer_id = $_POST['bearer_id'];
    $bearer->save_bearer_infos($_POST, "UPDATE", "id=$bearer_id");
    header("location:bearer.php?act=index&order_sn=$cpr_order_sn");
}


if ($_GET['act'] == 'update') {
    //根据持票人id查询持票人信息
    $bearer_info = $bearer->bearer_info($_GET['id']);
    if(!empty($_GET['order_sn'])){
        $order_sn=$_GET['order_sn'];
        $smarty->assign('post_url', "bearer.php?act=cpr_edit_new&cpr_order_sn=$order_sn");
    }else{
        $smarty->assign('post_url', "bearer.php?act=edit");
    }
    $smarty->assign('act', "update");
    $smarty->assign('bearer_id', $_GET['id']);
    $smarty->assign('bearer_info', $bearer_info);
    $smarty->display("bearer_info.dwt");
}
if ($_REQUEST['act'] == "bearer_name") {
    $json = new JSON;
    $arr = Array();
    $arr['bearer_id'] = $_POST['bearer_id'];
    $rec_id = $_POST['order_ticket_id'];
    $count=get_bearer_id($rec_id, $arr['bearer_id']);
    if($count>=1){
        die($json->encode(array('res'=>false,'rec_id'=>$rec_id)));
    }else{
    $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table("order_ticket"), $arr, 'UPDATE', 'rec_id=' . $rec_id);
    $bearer_info= $bearer->bearer_info($_POST['bearer_id']);
        die($json->encode(array("passport_number" =>$bearer_info['passport_number'],"mobile"=>$bearer_info['mobile'], "order_ticket_id" => $_POST['order_ticket_id'],"res"=>true)));
    }
   
}
if ($_REQUEST['act'] == "add_bearer_name") {
    $json = new JSON;
    $arr = Array();
    $arr['bearer_id'] = $_POST['bearer_id'];
    $rec_id = $_POST['order_combo_id'];
    $count=get_bearer_combo_id($rec_id, $arr['bearer_id']);
    if($count>=1){
        die($json->encode(array('res'=>false,'rec_id'=>$rec_id)));
    }else{
        $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table("order_combo"), $arr, 'UPDATE', 'rec_id=' . $rec_id);
        $bearer_info= $bearer->bearer_info($_POST['bearer_id']);
        die($json->encode(array("passport_number" =>$bearer_info['passport_number'],"mobile"=>$bearer_info['mobile'], "order_combo_id" => $_POST['order_combo_id'],"res"=>true)));
    }

}
if ($_REQUEST['act'] == "remove") {
    //移除持票人信息
    $bearer->remove($_POST['bearer_id']);
    $json = new JSON;
    die($json->encode());
}
if ($_REQUEST['act'] == "done") {
    $user_id = $_SESSION['user_id'];
    //填写持票人信息完成后，发送短信通知
    $order_id = intval($_POST['order_id']);
    __load("OrderService");
    $order_obj = new OrderService();
    $res = $order_obj->check_bearer_done($order_id);

    if ($res) {
        $order_info = $order_obj->get_order_info($order_id);
    }

    $query_list = $bearer->query_list($user_id);
    //根据订单号查询球票rec_id
    $rec_id_list = $bearer->get_rec_id($_POST['order_id']);

    //众安保险
    //获取订单ID,根据订单ID查出对应的订单号、购票人证件人号码、购票人证件人类型、购票人姓名、购票人姓名、购票人手机号码
    $order_id_num = $_REQUEST['order_id_num'];
    $rec_order_sn = $bearer->get_order_sn($order_id_num);
    $card_type = $rec_order_sn['card_type'];
    if ($card_type == 0) {
        $card_type_name = "I";
        //身份证
    } else if ($card_type == 1) {
        $card_type_name = "P";
        //护照
    }

    //根据订单号查出包含的所有门票
    $rec_menpiao_list = $bearer->get_order_menpiao_list($order_id_num);
    $game_info = $bearer->get_game_info($order_id_num);
    $rec_menpiao = Array();
    $rec_menpiao = $rec_menpiao_list;
    $smarty->assign('game_info', $game_info);
    foreach ($rec_menpiao AS $key => $value) {
        if ($value['num_start']) {
            $rec_menpiao[$key]['num_start'] = date('Y-m-d H-i', strtotime($value['num_start']));
        }
        if ($value['keywords']) {
            $rec_menpiao[$key]['keywords'] = explode(' ', trim($rec_menpiao[$key]['keywords']));
        }
        $rec_menpiao[$key]['total_item'] = number_format($rec_menpiao[$key]['goods_price'] * $rec_menpiao[$key]['goods_number'], 2, ".", "");
    }

    $arr = Array();
    foreach ($rec_id_list AS $key => $res) {
        $id = $res['rec_id'];
        $rec_id = 'bearer_id_' . $res['rec_id'];
        $bearer_id = $_POST[$rec_id];
        $arr_ticket = array();
        $arr_bear = array();
        $arr_ticket['bearer_id'] = $_POST[$rec_id];
        $arr_bear['order_id'] = $_POST['order_id'];
        $bearer->order_ticket($arr_ticket, "UPDATE", "rec_id=$id");
        $bearer->save_bearer_infos($arr_bear, "UPDATE", "id=$bearer_id");
        $arr[$key] = $bearer->get_ticket_info($res['rec_id']);
        $arr[$key]['num_start'] = date('Y-m-d', strtotime($arr[$key]['num_start']));

    }
    $smarty->assign('bizOrigin', "shankai");
    $smarty->assign("productId", "5100001");
    $smarty->assign("shankaisportsOrderNo", $rec_order_sn['order_sn']);
    $smarty->assign("order_id", $rec_order_sn['order_id']);
    $smarty->assign("ticketName", $rec_order_sn['consignee']);
    $smarty->assign("ticketCertiType", $card_type_name);
    $smarty->assign("ticketCertiCode", $rec_order_sn['card_num']);
    $smarty->assign("ticketPhone", $rec_order_sn['mobile']);
    $smarty->assign("email", $rec_order_sn['email']);
    $smarty->assign("rec_menpiao_list", $rec_menpiao);
    $smarty->assign("ticket_info", $arr);
    $smarty->display("sk_done.dwt");
}
if($_REQUEST['act'] == 'rb_qxx')
{
    $status=$_POST['status'];
    if(isset($status)){
        if($status=='0'){
            $where=" AND 1";
        }else if($status=='1'){
            $where=" AND policy_no!='' ";
        }else{
            $where=" AND policy_no='' ";
        }
    }else{
        $status=0;
        $where=" AND 1";
    }
    $sql = "SELECT * FROM t_insurance_policy where attribute_type="."'合同取消险'".$where;
    $row = $GLOBALS['db']->getAll($sql);

    foreach ($row AS $key => $value)
    {
        if(empty($row[$key]['policy_no'])){
            $row[$key]['policy_status']='未受理';
        }else{
            $row[$key]['policy_status']='已受理';
        }
        $row[$key]['dateline'] = date('Y-m-d H:i:s', $value['dateline']);
    }
    $smarty->assign('qxx_list', $row);
    $smarty->assign('status', $status);


}
if($_REQUEST['act'] == 'rb_qxx_excel_read')
{
    if (! empty ( $_FILES ['file_stu'] ['name'] ))
    {
        include_once(ROOT_PATH . 'framework/util/phpexcel/PHPExcel.php');
        $tmp_file = $_FILES ['file_stu'] ['tmp_name'];
        $file_types = explode ( ".", $_FILES ['file_stu'] ['name'] );
        $file_type = $file_types [count ( $file_types ) - 1];
        /*判别是不是.xls文件，判别是不是excel文件*/
        if (strtolower ( $file_type ) != "xls")
        {
            $url='bearer.php?act=rb_qxx';
            show_message("上传的文件不是.xls类型，请重新上传",$url);
        }

        /*设置上传路径*/
        $savePath = ROOT_PATH . 'temp/';
        /*以时间来命名上传的文件*/
        $str = date ( 'Ymdhis' );
        $file_name = $str . "." . $file_type;
        /*是否上传成功*/
        if (! copy ( $tmp_file, $savePath . $file_name ))
        {
            $url='bearer.php?act=rb_qxx';
            show_message("上传文件失败，请重新上传",$url);
        }

        $objReader = PHPExcel_IOFactory::createReader('Excel5');
        $objPHPExcel=$objReader->load($savePath . $file_name);
        $sheet = $objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $data_array=array();
        for($j=2;$j<=$highestRow;$j++)                        //从第二行开始读取数据
        {
            $data_array[]=$objPHPExcel->getActiveSheet()->getCell("A"."$j")->getValue().'  ';
        }
        foreach ( $data_array as $k => $v )
        {
                $passport_number = trim($v);
                $sql = "UPDATE sk_order_info SET is_win=1 WHERE order_id=(SELECT oi.order_id FROM sk_order_info AS oi,sk_order_ticket AS ot,sk_bearer_info AS bi WHERE bi.passport_number='".$passport_number."' AND bi.id=ot.bearer_id AND ot.order_id=oi.order_id AND oi.order_status=1 AND oi.pay_status=2)";
        }
        unlink($savePath . $file_name);
        $url='bearer.php?act=index';
        show_message("导入完成~",$url);
    }else{
        $url='bearer.php?act=index';
        show_message("读取文件出错，请重试",$url);
    }

}
function get_user_email($user_id)
{
    $sql = "SELECT email From" . $GLOBALS['ecs']->table('users') . "WHERE user_id=$user_id";
    return $GLOBALS['db']->getOne($sql);
}

function get_bearer_id($rec_id,$bearer_id){
     $bearer_info = $GLOBALS['db']->getRow( "SELECT bearer_id,order_id,goods_id FROM " . $GLOBALS['ecs']->table('order_ticket') . "WHERE rec_id=$rec_id");
     $count = $GLOBALS['db']->getOne("SELECT COUNT(*)  FROM " . $GLOBALS['ecs']->table('order_ticket') . "WHERE order_id=$bearer_info[order_id] AND bearer_id =$bearer_id AND goods_id=$bearer_info[goods_id] ");
     return $count;
}
function get_bearer_combo_id($rec_id,$bearer_id){
    $bearer_info = $GLOBALS['db']->getRow( "SELECT bearer_id,order_id,goods_id FROM " . $GLOBALS['ecs']->table('order_combo') . "WHERE rec_id=$rec_id");
    $count = $GLOBALS['db']->getOne("SELECT COUNT(*)  FROM " . $GLOBALS['ecs']->table('order_combo') . "WHERE order_id=$bearer_info[order_id] AND bearer_id =$bearer_id AND goods_id=$bearer_info[goods_id] ");
    return $count;
}

?>