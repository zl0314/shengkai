<?php
/**
 * Description of GroupService
 *
 * @author Kevin
 */
class ComboService {
    //put your code here
    private $combo;
    public function __construct() {
        __load("ComboModel", "model");
        $this->combo=new ComboModel();
    }
    public function add_Combo($data){
        return $this->combo->add_Combo($data);
    }
    public function update($data){     
        return $this->combo->update($data); 
    }
    public function get_All_Combo(){
        return $this->combo->get_All_Combo();        
    }
    public function get_Combo($id){
        return $this->combo->get_Combo($id);
    }
    //hechengbin--获取全部套餐
    public function get_combo_all(){
        return $this->combo->get_combo_all();
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
    public function get_combo_show($page,$num){
        return $this->combo->get_combo_show($page,$num);   
    }
    public function get_combo_index($id){
        return $this->combo->get_combo_index($id);   
    }
    public function get_combo_info($id){
        return $this->combo->get_combo_info($id);
    }
    //查看套餐金额
    public function get_combo_money($id){
        return $this->combo->get_combo_money($id);
    }
    //获取套餐名称
    public function get_combo_name($combo_id){
        return $this->combo->get_combo_name($combo_id);
    }
}
