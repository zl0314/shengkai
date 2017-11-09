<?php
/**
 * Description of GroupService
 *
 * @author Kevin
 */
class Combo_travelService {
    //put your code here
    private $combo;
    public function __construct() {
        __load("Combo_travelModel", "model");
        $this->combo=new Combo_travelModel();
    }
    public function add_Combo_travel($data){
        return $this->combo->add_Combo_travel($data);
    }
    public function update($data){     
        return $this->combo->update($data); 
    }
    public function get_All_Combo_travel(){
        return $this->combo->get_All_Combo_travel();
    }
    public function get_Combo_travel($id){
        return $this->combo->get_Combo_travel($id);
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
