<?php

define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');

__load("BearerService");
$bearer = new BearerService();
   if ($_REQUEST['act'] == "pay") {
//       echo "<pre>";
//       print_r($_REQUEST);die;
    //众安保险
    //获取订单ID,根据订单ID查出对应的订单号、购票人证件人号码、购票人证件人类型、购票人姓名、购票人姓名、购票人手机号码
   $order_id_num=$_REQUEST['order_id_num'];
   $tk_code=$_REQUEST['tk_code'];
   $rec_order_sn=$bearer->get_order_sn($order_id_num);
   $card_type=$rec_order_sn['card_type'];
   if($card_type==0){
       $card_type_name="I";
       //I为身份证
   }else if($card_type==1){
       $card_type_name="P";
       //P为护照
   }
   
   //根据订单号查出包含的所有门票
    $rec_menpiao_list=$bearer->get_order_menpiao_list($order_id_num);
    $rec_combo_menpiao_list=$bearer->get_order_combo_menpiao($order_id_num);

    //根据门票code更新数据库的is_pay_bx字段，is_pay_bx=1 表示需要购买保险
    if($tk_code){
        $bearer->set_pay_bx($tk_code);
    }
    
    
    //查出需要购买保险的门票
    $menpiao_messages_ticket=$bearer->get_menpiao_messages_ticket($order_id_num);
//       echo "<pre>";
//       print_r($menpiao_messages_ticket);die;
    //查出需要购买保险的套餐门票
    $menpiao_messages_combo=$bearer->get_menpiao_messages_combo($order_id_num);
       echo "<pre>";
       print_r($menpiao_messages_combo);die;
    $rec_menpiao=Array();
    $rec_menpiao=$rec_menpiao_list;
    $rec_combo_menpiao=Array();
    $rec_combo_menpiao=$rec_combo_menpiao_list;
    
    //众安约定私钥
    $c= "Z56.qlife.LZK98e2Password.shankai";
    
    //获取当前日期
    $time=date("Y-m-d",time());
    $currentDate=$time;
    $b=$currentDate;
    
    //获取盛开订单号
    $a=$rec_order_sn['order_sn'];
    
    //MD5加密
    $sign=md5($a.=$b.=$c);
    for($i=0;$i<count($menpiao_messages);$i++){
       $menpiao_messages[$i]['num_start']= substr($menpiao_messages[$i]['num_start'],0,10);
    }
    $smarty->assign('bizOrigin',"shankai");
    $smarty->assign("productId","5100001");
    $smarty->assign("shankaisportsOrderNo",$rec_order_sn['order_sn']);
    $smarty->assign("ticketName",$rec_order_sn['consignee']);
    $smarty->assign("ticketCertiType",$card_type_name);
    $smarty->assign("ticketCertiCode",$rec_order_sn['card_num']);
    $smarty->assign("ticketPhone",$rec_order_sn['mobile']);
    $smarty->assign("currentDate",$currentDate);
    $smarty->assign("sign",$sign);
    $smarty->assign("menpiao_messages",$menpiao_messages);
    $smarty->display("demo.dwt");
    
   }
?>