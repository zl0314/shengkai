<?php
/**
 * Description of GroupService
 *
 * @author Kevin
 */
class SportcatService {
    //put your code here
    private $sportcat;
    public function __construct() {
        __load("SportcatModel", "model");
        $this->sportcat=new SportcatModel();
    }
    public function add_Sportcat($data){
        return $this->sportcat->add_Sportcat($data);
    }
    public function update($data){     
        return $this->sportcat->update($data); 
    }
    public function get_All_Sportcat(){
        return $this->sportcat->get_All_Sportcat();        
    }
    public function get_Sportcat($id){
        return $this->sportcat->get_Sportcat($id);
    }
    public function remove($id){
        return $this->sportcat->remove($id);
    }
    public function remove_b($data) {
        return $this->sportcat->remove_batch($data);
    }
    public function get_sportcat_list() {
        return $this->sportcat->get_sportcat_list();
    }
    //获取所有赛事列表
    public function get_all_game(){
        return $this->sportcat->get_all_game();
    }
}
