<?php

/*
 * 作者：鞠嵩
 * 时间：10:37:56
 * 
 */

/**
 * Description of GroupService
 *
 * @author Kevin
 */
class Set_meal_orderService {
    //put your code here
    private $set_meal_order;
    public function __construct() {
          __load("Set_meal_orderModel", "model");
        $this->set_meal_order=new Set_meal_orderModel();

    }

    public function add_set_meal_order($data){
        return $this->set_meal_order->add_set_meal_order($data);
    }
    public function update_set_meal_order($data){     
        return $this->set_meal_order->update_set_meal_order($data); 
    }
     public function get_set_meal_order_list(){
        return $this->set_meal_order->get_set_meal_order_list();        
    }
     public function get_set_meal_order($id){
        return $this->set_meal_order->get_set_meal_order($id);
     }
    public function remove_set_meal_order($id){
        return $this->set_meal_order->remove_set_meal_order($id);
    }
    public function remove_b($data) {
        return $this->set_meal_order->remove_batch($data);
    }
}
