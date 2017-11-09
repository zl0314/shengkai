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
class Set_mealService {
    //put your code here
    private $set_meal;
    public function __construct() {
          __load("Set_mealModel", "model");
        $this->set_meal=new Set_mealModel();

    }

    public function add_set_meal($data){
        return $this->set_meal->add_set_meal($data);
    }
    public function update_set_meal($data){     
        return $this->set_meal->update_set_meal($data); 
    }
     public function get_set_meal_list(){
        return $this->set_meal->get_set_meal_list();        
    }
     public function get_set_meal($id){
        return $this->set_meal->get_set_meal($id);
     }
    public function remove_set_meal($id){
        return $this->set_meal->remove_set_meal($id);
    }
    public function remove_b($data) {
        return $this->set_meal->remove_batch($data);
    }
    public function get_set_meal_show(){
        return $this->set_meal->get_set_meal_show();        
    }
    public function get_set_meal_id($id){
        return $this->set_meal->get_set_meal_id($id);
    }
}
