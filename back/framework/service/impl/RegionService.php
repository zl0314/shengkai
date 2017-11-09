<?php

/**
 * 作者：鞠嵩
 * 时间：13:45:50
 * 
 */
/**
 * Description of RegionService
 *
 * @author jusong
 */
__load("RegionModel", "model");

class RegionService {

    private $region;

    public function __construct() {
        $this->region = new RegionModel();
    }

    public function region_list($region_id){
        return $this->region->region_list($region_id);
    }
    public function get_all($type){
        return $this->region->get_all($type) ;
    }

    public function get_region($region_id){
        return $this->region->get_region($region_id);
    }
    //zc
    //根据赛事ID查找对应的赛程ID->场次ID->赛场ID->城市ID
    public function get_schedule($game_id){
         $schedule_id=$this->region->get_schedule($game_id);
         return $schedule_id;
    }
}

?>
