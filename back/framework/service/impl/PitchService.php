<?php

/*
 * 作者：戎青松
 * 时间：10:37:56
 * 
 */

/**
 * Description of PitchService
 *
 * @author Kevin
 */
class PitchService {
    //put your code here
    private $region,$pitch;
    public function __construct() {
          __load("RegionModel", "model");
          __load("PitchModel", "model");
        $this->region=new RegionModel();
        $this->pitch=new PitchModel();
    }
    public function get_AllRegion($type){
        return $this->region->get_all($type);        
    }
    public function add($data){
        return $this->pitch->add($data);        
    }
    public function update($data){
       
        return $this->pitch->update($data);        
    }
    public function get_AllPitch(){
        return $this->pitch->get_AllPitch();        
    }
    public function get_pitch_list($game_id){
        return $this->pitch->get_pitch_list($game_id);
    }
     public function get_All_Pitch(){
        return $this->pitch->get_All_Pitch();        
    }
    //获取所有球场
    public function get_pitch_name_list(){
        return $this->pitch->get_pitch_name_list();
    }
     public function get_All_Goods(){
        return $this->pitch->get_All_Goods();        
    }
    public function get_Pitch($id){
        return $this->pitch->get_Pitch($id);        
    }
    public function remove($id){
        return $this->pitch->remove($id);
    }
    public function remove_b($data) {
        return $this->pitch->remove_batch($data);
    }
}
