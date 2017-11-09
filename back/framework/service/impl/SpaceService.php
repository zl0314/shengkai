<?php

class SpaceService {
    //put your code here
    private $region,$space;
    public function __construct() {
        __load("RegionModel", "model");
        __load("SpaceModel", "model");
        $this->region = new RegionModel();
        $this->space = new SpaceModel();
    }
    
    public function add($data){
        return $this->space->add($data);        
    }
    
    public function update($data){
        return $this->space->update($data);        
    }
    
    public function get_space_all(){
        return $this->space->space_all();        
    }
    
    public function get_All_Airticket(){
        return $this->space->get_All_Query();        
    }
    
    public function get_space_info($id){
        return $this->space->space_info($id);        
    }
    public function remove($id){
        return $this->space->remove($id);
    }
    public function remove_b($data) {
        return $this->space->remove_batch($data);
    }
}
