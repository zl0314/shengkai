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
class HotelService {
    //put your code here
    private $region,$hotel;
    public function __construct() {
          __load("RegionModel", "model");
          __load("HotelModel", "model");
        $this->region=new RegionModel();
        $this->hotel=new HotelModel();
    }
    
    public function get_AllRegion($type){
        return $this->region->get_all($type);        
    }
    public function add($data){
        return $this->hotel->add($data);        
    }
    public function update($data){
       
        return $this->hotel->update($data);        
    }
    public function get_AllHotel(){
        return $this->hotel->get_AllHotel();        
    }
    public function get_Hotel($id){
        return $this->hotel->get_Hotel($id);        
    }
    public function remove($id){
        return $this->hotel->remove($id);
    }
    public function get_hotel_list($region_id){
        return $this->hotel->get_hotel_list($region_id);
    }
    public function get_room_list($hotel_id){
        return $this->hotel->get_room_list($hotel_id);
    }
    public function get_hotel_id($region_id){
        return $this->hotel->get_hotel_id($region_id);
    }

    public function get_hotel_list_by_region_ids($region_ids){
        return $this->hotel->get_hotel_list_by_region_ids($region_ids);
    }
}
