<?php

/**
 * Description of OrderService
 *
 * @author qs
 */
class OrderService {
    //put your code here
    private $order;
    public function __construct() {
        __load("OrderModel", "model");
        $this->order = new OrderModel();
    }
    public function set_order_info($order) {
        $this->order->set_order_info($order);
    }
    public function get_order_tickets($order_id){
        return $this->order->get_order_tickets($order_id);
    }
    public function get_order_combo($order_id){
        return $this->order->get_order_combo($order_id);
    }
    public function get_order_goods_info($order_id){
        return $this->order->get_order_goods_info($order_id);
    }
    public function get_order_id($order_sn){
        return $this->order->get_order_id($order_sn);
    }
    public function set_order_ticket($order_ticket){
        $this->order->set_order_ticket($order_ticket);
    }
    public function set_order_goods($order_id){
        $this->order->set_order_goods($order_id);
    }
    public function set_order_combo($order_combo){
        $this->order->set_order_combo($order_combo);
    }
    //--hechengbin-保存套餐信息
    public function set_order_combo_info($combo){
        $this->order->set_order_combo_info($combo);
    }
    public function get_order_info_sn($order_sn){
      return  $this->order->get_order("order_sn= '{$order_sn}'");
    }
    public function get_order_user($order_id){
        return $this->order->get_order_user($order_id);
    }
    public function set_order_plane($plane_data){
        $this->order->set_order_plane($plane_data);
    }
    public function set_order_hotel($hotel_data){
        $this->order->set_order_hotel($hotel_data);
    }
    public function pay_done($order_sn,$queryId,$txnAmt){
        return  $this->order->pay_done($order_sn,$queryId,$txnAmt);
    }

    public function pay_loading($order_sn,$money){
        return  $this->order->pay_loading($order_sn,$money);
    }
    public function check_bearer_done($order_id){
        $res=$this->order->get_order_ticket(" order_id=".$order_id." AND bearer_id>0","count(*) as c");
        $done_count=$res['c'];

        $res=$this->order->get_order_ticket(" order_id=".$order_id,"count(*) as c");
        $count=$res['c'];
        if($count==$done_count){
            return true;
        }else{
            return false;
        }
    }
    public function get_order_ticket_combo_info($order_id,$num,$start){
        //查询订单信息
        $order_bearer_info = $this->order->get_order_info($order_id);
        //查询普通球票信息
        $order_bearer_info['tickets'] = $this->order->get_ticket_bearer_info($order_id,$num, $start);
        //查询套餐门票信息
        $order_bearer_info['combo'] = $this->order->get_combo_bearer_info($order_id);
        $order_bearer_info['info'] = array_merge($order_bearer_info['tickets'],$order_bearer_info['combo']);
        $order_bearer_info['type'] = $this->order->get_order_combo_bearer_info($order_id);
        return $order_bearer_info;
    }
    function get_order_info($order_id){
        return $this->order->get_order("order_id = ".$order_id);
    }
    public function get_goods_sn($user_id){
        return $this->order->get_goods_sn($user_id);
    }
    public function get_order_sn($order_id){
         return $this->order->get_order_sn($order_id);
    }
}
