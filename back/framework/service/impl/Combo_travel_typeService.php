<?php

/**
 * Created by PhpStorm.
 * User: stepreal
 * Date: 17/6/22
 * Time: 下午10:43
 */
class Combo_travel_typeService
{
    //put your code here
    private $combo;
    public function __construct() {
        __load("Combo_travel_typeModel", "model");
        $this->combo=new Combo_travel_typeModel();
    }
    public function add_Combo_travel_type($data){
        return $this->combo->add_Combo_travel_type($data);
    }
    public function update($data){
        return $this->combo->update($data);
    }
    public function get_All_Combo_travel_type(){
        return $this->combo->get_All_Combo_travel_type();
    }
    public function get_Combo_travel_type($id){
        return $this->combo->get_Combo_travel_type($id);
    }
    public function remove($id){
        return $this->combo->remove($id);
    }
    public function remove_b($data) {
        return $this->combo->remove_batch($data);
    }
    public function get_combo_list() {
        return $this->combo->get_combo_list();
    }
}