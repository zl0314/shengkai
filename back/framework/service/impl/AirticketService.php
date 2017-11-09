<?php

/*
 * 作者：戎青松
 * 时间：10:37:56
 * 
 */

/**
 * Description of HotelService
 *
 * @author Kevin
 */
class AirticketService {
    //put your code here
    private $region,$air;
    public function __construct() {
          __load("RegionModel", "model");
          __load("AirticketModel", "model");
        $this->region = new RegionModel();
        $this->air = new AirticketModel();
    }
    
    public function get_AllRegion($type){
        return $this->region->get_all($type);        
    }
    public function add($data){
        return $this->air->add($data);        
    }
    public function update($data){
        return $this->air->update($data);        
    }
    public function get_AllAirticket(){
        return $this->air->get_All();        
    }
    
    public function get_all_airName_time(){
        return $this->air->air_name_time();
    }

    public function get_space_list(){
        return $this->air->get_space_list();
    }
    
    public function get_airticket_region(){
        return $this->air->airticket_region();    
    }
    
    public function get_All_Airticket(){
        return $this->air->get_All_Query();        
    }
    
    public function get_air($id){
        return $this->air->get($id);        
    }
    public function remove($id){
        return $this->air->remove($id);
    }
    public function remove_b($data) {
        return $this->hotel->remove_batch($data);
    }
    
    public function get_space_all(){
        return $this->air->space_all();
    }
    
    public function get_flight_num($air_id){
        return $this->air->flight_num($air_id);
    }
    
    public function add_air_space($data){
        return $this->air->air_space($data);
    }
    
    public function update_air_space($data){
        return $this->air->space_air($data);
    }

    public function get_air_info(){
         return $this->air->air_info();
    }

    public function get_air_space($air_id){
        return $this->air->get_air_space($air_id);
    }

    public function get_space($id){
        return $this->air->get_space($id);
    }
    public function get_space_info($id){
        return $this->air->get_space_info($id);
    }
    
    public function get_air_space_info(){
        return $this->air->air_space_info();
    }    
    
    public function remove_air_space_info(){
        return $this->air->rem_air_space_info();
    }
    
    public function drop($data){
         return $this->air->drop_air_space_info($data);
    }
    
    public function get_ambitus_goods(){
        return $this->air->ambitus_goods_info();
    }
    public function get_airticket($from_city,$to_city){
        return $this->air->get_airticket($from_city,$to_city);
    }
    public function get_air_info2($air_id){
        $air= $this->air->get($air_id);

        $air['to_city']=$this->region->get_region_name($air['to_ctiy']);
        $air['from_city']=$this->region->get_region_name($air['from_ctiy']);
        return $air;
    }
    public function get_space_name($space_id){
        $space=$this->get_space($space_id);
        $space_info=$this->get_space_info($space['s_id']);
        return $space_info['space_name'];
    }
    public function  select_air_space_info($air_id,$s_id){
        return  $this->air->select_air_space_info($air_id,$s_id);
    }


}
