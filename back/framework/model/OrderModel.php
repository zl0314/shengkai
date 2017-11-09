<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BatchModel
 *
 * @author qs
 */
__load("Model", "model");

class OrderModel extends Model {  
    public function set_order_info($order){
       return $this->db->autoExecute($this->ecs->table("order_info"),$order, 'INSERT');
    }
    public function get_order_id($order_sn){   
        return $this->db->getOne("SELECT order_id FROM ".$this->ecs->table("order_info")."WHERE order_sn=$order_sn");
    }
    public function set_order_ticket($order_ticket){
        return $this->db->autoExecute($this->ecs->table("order_ticket"),$order_ticket, 'INSERT');
    }
    public function set_order_hotel($hotel_data){
        return $this->db->autoExecute($this->ecs->table("order_hotel"),$hotel_data, 'INSERT');
    }

    public function set_order_plane($plane_data){
        return $this->db->autoExecute($this->ecs->table("order_plane"),$plane_data, 'INSERT');
    }

    public function get_order($where){
        $sql = "SELECT * FROM " . $this->ecs->table("order_info") . " WHERE " . $where;
        $res = $this->db->getRow($sql);
        return $res;
    }
//    public function get_wx_order($order_id){
//        $sql = "SELECT * FROM " . $this->ecs->table("order_info") . " WHERE order_id='".$order_id."'";
//        $res = $this->db->getRow($sql);
//        return $res;
//    }
    public function get_order_info($order_id){
        $sql = "SELECT * FROM " . $this->ecs->table('order_info') . " WHERE order_id='".$order_id."'";
        return $this->db->getRow($sql);
    }
    public function get_order_user($order_sn){
        $sql = "SELECT * FROM " .$this->ecs->table('order_info')." WHERE order_sn='".$order_sn."'";
        return $this->db->getRow($sql);
    }
    public function get_order_ticket($where,$fields){
        $sql = "SELECT ".$fields." FROM " . $this->ecs->table("order_ticket") . " WHERE ".$where;
        $res = $this->db->getRow($sql);
        return $res;
    }
//    public function get_order_ticket_info($order_id){
//        $sql = "SELECT * FROM " . $this->ecs->table('order_ticket') . " WHERE order_id=$order_id";
//        return $this->db->getAll($sql);
//    }
    public function get_ticket_bearer_info($order_id,$num, $start){
        $sql = "SELECT ot.*,g.*,bi.cn_customer_name,bi.passport_number,bi.date_birth,bi.mobile FROM ".$this->ecs->table('order_ticket')."AS ot,".$this->ecs->table('goods')."AS g,".$this->ecs->table('bearer_info')."AS bi WHERE ot.order_id='".$order_id."' AND ot.goods_id=g.goods_id AND ot.bearer_id=bi.id";
        return $this->db->getAll($sql);
    }
    public function get_combo_bearer_info($order_id){
        $sql = "SELECT oc.*,g.*,bi.cn_customer_name,bi.passport_number,bi.date_birth,bi.mobile FROM ".$this->ecs->table('order_combo')."AS oc,".$this->ecs->table('goods')."AS g,".$this->ecs->table('bearer_info')."AS bi WHERE oc.order_id='".$order_id."' AND oc.goods_id=g.goods_id AND oc.bearer_id=bi.id";
        return $this->db->getAll($sql);
    }
    public function get_bearer_info_ticket($id){
        $sql = "SELECT * FROM ".$this->ecs->table('bearer_info')."WHERE id='".$id."'";
        return $this->db->getAll($sql);
    }
    public function get_bearer_info_combo($id){
        $sql = "SELECT * FROM ".$this->ecs->table('bearer_info')."WHERE id='".$id."'";
        return $this->db->getAll($sql);
    }
//    public function get_order_combo_info($order_id){
//        $sql = "SELECT * FROM " . $this->ecs->table('order_combo') . " WHERE order_id=$order_id";
//        return $this->db->getAll($sql);
//    }
//    public function get_combo_bearer_info($order_id){
//        $sql = "SELECT oc.*,g.* FROM ".$this->ecs->table('order_combo')."AS oc,".$this->ecs->table('goods')."AS g WHERE oc.order_id='".$order_id."' AND oc.goods_id=g.goods_id";
//        $res = $this->db->getAll($sql);
//        foreach($res as $key=>$value){
//            if($value['bearer_id'] == 0)
//        }
//        $sql = "SELECT oc.*,g.*,bi.cn_customer_name,bi.passport_number,bi.date_birth,bi.mobile FROM ".$this->ecs->table('order_combo')."AS oc,".$this->ecs->table('goods')."AS g,".$this->ecs->table('bearer_info')."AS bi WHERE oc.order_id='".$order_id."' AND oc.goods_id=g.goods_id AND oc.bearer_id=bi.id";
//        return $this->db->getAll($sql);
//    }
    public function set_order_goods($order_id){
        $sql = "INSERT INTO " . $this->ecs->table('order_goods') . "( " .
            "order_id, goods_id, goods_name, goods_sn, product_id, goods_number, market_price, ".
            "goods_price, goods_attr, is_real, extension_code, parent_id, is_gift, goods_attr_id) ".
            " SELECT '$order_id', goods_id, goods_name, goods_sn, product_id, goods_number, market_price, ".
            "goods_price, goods_attr, is_real, extension_code, parent_id, is_gift, goods_attr_id".
            " FROM " .$this->ecs->table('cart') .
            " WHERE session_id = '".SESS_ID."' AND goods_type = 'goods'";
        $this->db->query($sql);
    }
    public function set_order_combo($order_combo){
        return $this->db->autoExecute($this->ecs->table("order_combo"),$order_combo, 'INSERT');
    }
    //--hechengbin-- 保存套餐信息
    public function set_order_combo_info($combo){
        return $this->db->autoExecute($this->ecs->table('order_combo_info'),$combo,'INSERT');
    }
    public function pay_done($order_sn,$queryId,$txnAmt){
        $a="3333333333";
        $path = ROOT_PATH.'temp/log/pay_log/1.txt';
//                    error_log("2222222222",3,$path);
        file_put_contents($path,$a);
        $sql = "UPDATE " . $this->ecs->table("order_info") . "SET pay_status= 2,order_amount=0,queryid='{$queryId}',txnAmt={$txnAmt},pay_time=".gmtime()." WHERE order_sn='{$order_sn}'";
        __log("pay_sql_log: " .$sql,"pay_log");
        $this->db->query($sql);
    }
    public function pay_loading($order_sn,$money){
        $money=$money/100;
        $sql = "UPDATE " . $this->ecs->table("order_info") . "SET pay_status= 1,order_amount=order_amount-{$money} WHERE order_sn='{$order_sn}'";
        __log("pay_sql_log: " .$sql,"pay_log");
        $this->db->query($sql);
    }
    //因为翻页的功能在此页面无法实现，所以这段代码已经转到了lib_transaction.php文件中了
//    public function get_goods_sn($user_id, $num = 10, $start = 0){
//        $arr    = array();
//        $sql="SELECT order_sn,money_paid,pay_time FROM 
//                 sk_order_info  WHERE user_id=$user_id AND pay_time!=0";
//            $res = $GLOBALS['db']->SelectLimit($sql, $num, $start);
//             while ($row = $GLOBALS['db']->fetchRow($res)){
//                 $arr[] = array(
//                       'order_sn'       => $row['order_sn'],
//                       'money_paid'     => $row['money_paid'],
//                       'pay_time'       => $row['pay_time'],
//                );
//             }
//             return $arr;
//    }
     public function get_order_sn($order_id){
        return $this->db->getRow("SELECT * FROM ".$this->ecs->table("order_info")."WHERE order_id=$order_id");
    }
//    public function get_order_tickets($order_id){
//        return $this->db->getRow("SELECT ot.*,g.*,bi.* FROM ".$this->ecs->table("order_ticket")." AS ot,".$this->ecs->table("goods")."AS g,".$this->ecs->table("bearer_info")."AS bi WHERE ot.order_id=$order_id AND ot.goods_id=g.goods_id AND ot.bearer_id=bi.id");
//    }
    public function get_order_goods_info($order_id){
        return $this->db->getRow("SELECT * FROM ".$this->ecs->table("order_goods")."WHERE order_id=$order_id");
    }
    public function get_order_tickets($order_id){
        $sql = "SELECT ot.*,g.* FROM ".$this->ecs->table("order_ticket")." AS ot,".$this->ecs->table("goods")."AS g WHERE ot.order_id=$order_id AND ot.goods_id=g.goods_id";
        $res = $this->db->getAll($sql);
        $bearer = array();
        foreach($res as $key=>$value){
            if($value['bearer_id'] == '0'){
                $sql = "SELECT ot.*,g.*,ge.*,c.color_value,n.numbers FROM ".$this->ecs->table("order_ticket")." AS ot,".$this->ecs->table('color_manage')."AS c,".$this->ecs->table('number')."AS n,".$this->ecs->table('game')."AS ge,".$this->ecs->table("goods")."AS g WHERE ot.order_id='".$value['order_id']."' AND ot.goods_id=g.goods_id AND g.number_id=n.id AND n.color_id=c.color_id AND ot.bearer_id='0' AND g.game_id=ge.id";
                $bearer['key']  = $this->db->getAll($sql);
            }
            if(!($value['bearer_id'] == '0')){
                $sql = "SELECT ot.*,g.*,bi.*,ge.*,c.color_value,n.numbers FROM ".$this->ecs->table("order_ticket")." AS ot,".$this->ecs->table('color_manage')."AS c,".$this->ecs->table('number')."AS n,".$this->ecs->table('game')."AS ge,".$this->ecs->table("goods")."AS g,".$this->ecs->table("bearer_info")."AS bi WHERE ot.order_id='".$value['order_id']."' AND ot.goods_id=g.goods_id AND g.number_id=n.id AND n.color_id=c.color_id AND  ot.bearer_id=bi.id AND g.game_id=ge.id";
                $bearer['info'] = $this->db->getAll($sql);
            }
        }
        return $bearer;
    }
    public function get_order_combo($order_id){
        $sql = "SELECT oc.*,g.* FROM " . $this->ecs->table("order_combo") . " AS oc," . $this->ecs->table("goods") . "AS g WHERE oc.order_id='".$order_id."' AND oc.goods_id=g.goods_id";
        $res = $this->db->getAll($sql);
        $bearer_info = array();
        foreach($res as $key=>$value){
            if($value['bearer_id'] == '0'){
                $sql = "SELECT oc.*,g.*,co.*,n.numbers FROM " . $this->ecs->table("order_combo") . " AS oc,".$this->ecs->table('combo')."AS co," . $this->ecs->table("goods") . "AS g,".$GLOBALS['ecs']->table('number')." AS n WHERE oc.order_id='".$value['order_id']."' AND oc.goods_id=g.goods_id AND g.number_id=n.id AND oc.combo_id=co.combo_id AND oc.bearer_id='0'";
                $bearer_info['key'] = $this->db->getAll($sql);
            }
            if(!($value['bearer_id'] == '0')){
                $sql = "SELECT oc.*,g.*,bi.cn_customer_name,bi.passport_number,bi.date_birth,bi.mobile,co.*,n.numbers FROM ".$this->ecs->table("order_combo")." AS oc," . $this->ecs->table('combo')."AS co,".$GLOBALS['ecs']->table('number')." AS n,".$this->ecs->table("goods")."AS g,".$this->ecs->table("bearer_info")."AS bi WHERE oc.order_id='".$value['order_id']."' AND oc.goods_id=g.goods_id AND g.number_id=n.id AND oc.bearer_id=bi.id AND oc.combo_id=co.combo_id";
                $bearer_info['info'] = $this->db->getAll($sql);
            }
        }
        return $bearer_info;
    }
//    public function get_combo_bearer_info($order_id){
//        $sql = "SELECT oc.*,g.* FROM " . $this->ecs->table("order_combo") . " AS oc," . $this->ecs->table("goods") . "AS g WHERE oc.order_id='".$order_id."' AND oc.goods_id=g.goods_id";
//        $res = $this->db->getAll($sql);
//        foreach($res as $key=>$value){
//            if($value['bearer_id'] == '0'){
//                $sql = "SELECT oc.*,g.*,co.* FROM " . $this->ecs->table("order_combo") . " AS oc,".$this->ecs->table('combo')."AS co," . $this->ecs->table("goods") . "AS g WHERE oc.order_id='".$value['order_id']."' AND oc.goods_id=g.goods_id AND oc.combo_id=co.combo_id AND oc.bearer_id='0'";
//                $bearer_info_ticket = $this->db->getAll($sql);
//            }else{
//                $sql = "SELECT oc.*,g.*,bi.cn_customer_name,bi.passport_number,bi.date_birth,bi.mobile,co.* FROM ".$this->ecs->table("order_combo")." AS oc," . $this->ecs->table('combo')."AS co,".$this->ecs->table("goods")."AS g,".$this->ecs->table("bearer_info")."AS bi WHERE oc.order_id='".$value['order_id']."' AND oc.goods_id=g.goods_id AND oc.bearer_id=bi.id AND oc.combo_id=co.combo_id";
//                $bearer_info_combo = $this->db->getAll($sql);
//            }
//        }
//        $bearer_info = array_merge($bearer_info_ticket,$bearer_info_combo);
//        return $bearer_info;
//    }



    public function get_order_combo_bearer_info($order_id){
        $sql = "SELECT bearer_id FROM ".$this->ecs->table('order_ticket')."WHERE order_id='".$order_id."'";
        $res = $this->db->getAll($sql);
        $sql1 = "SELECT bearer_id FROM ".$this->ecs->table('order_combo')."WHERE order_id='".$order_id."'";
        $result = $this->db->getAll($sql1);
        $arr = array_merge($res,$result);
        $type = '';
        foreach($arr as $key=>$value){
            if($value['bearer_id'] == 0 ){
                $type = 0;
            }
        }
        return $type;
    }


    /**
     * 占用酒店库存
     *
     * @param $order_id
     * @param $day_info
     */
    public function set_order_hotel_room_num($order_id, $day_info)
    {
        foreach ($day_info as $value) {
            $sql = "UPDATE " . $this->ecs->table("room_num") . "SET locked_num = locked_num + {$value['num']} WHERE room_id = '{$value['room_id']}' and date = '{$value['date']}'";
            $ret = $this->db->query($sql);
            __log("占用酒店房间库存: 订单ID: {$order_id}, ROOM_ID = {$value['room_id']}, NUM = {$value['num']}, DATE = {$value['date']}, 状态: " . ($ret ? "成功" : "失败"));
        }
    }
}
