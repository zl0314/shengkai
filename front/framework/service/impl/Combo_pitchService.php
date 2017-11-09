<?php
/**
 * Description of GroupService
 *
 * @author Kevin
 */
class Combo_pitchService {
    //put your code here
    private $combo;
    public function __construct() {
        __load("Combo_pitchModel", "model");
        $this->combo=new Combo_pitchModel();
    }
    public function add_Combo_pitch($data){
        return $this->combo->add_Combo_pitch($data);
    }
    public function update($data){     
        return $this->combo->update($data); 
    }
    public function get_All_Combo_pitch(){
        return $this->combo->get_All_Combo_pitch();        
    }
    public function get_Combo_pitch($id){
        return $this->combo->get_Combo_pitch($id);
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
