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
class BillService {
    //put your code here
    private $bill;
    public function __construct() {
          __load("BillModel", "model");
        $this->bill=new BillModel();

    }

    public function add_bill($data){
        return $this->bill->add_bill($data);
    }
    public function update_bill($data){     
        return $this->bill->update_bill($data); 
    }
     public function get_bill_list(){
        return $this->bill->get_bill_list();        
    }
     public function get_bill($id){
        return $this->bill->get_bill($id);
     }
    public function remove_bill($id){
        return $this->bill->remove_bill($id);
    }
    public function remove_b($data) {
        return $this->bill->remove_batch($data);
    }
    public function get_bill_show(){
        return $this->bill->get_bill_show();        
    }
    public function get_bill_id($id){
        return $this->bill->get_bill_id($id);
    }
}
