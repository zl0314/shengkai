<?php

/*
 * 作者：戎青松
 * 时间：10:35:56
 * 
 */

/**
 * Description of RegionModel
 *
 * @author Kevin
 */
__load("Model", "model");

class RegionModel extends Model {

    //put your code here
    public function get_region($region_id){
        $sql = "SELECT * FROM " . $this->ecs->table("region") . " WHERE region_id=$region_id";
        return $this->db->getRow($sql);
    }

    public function get_all($type) {
        $sql = "SELECT * FROM " . $this->ecs->table("region") . " WHERE region_type=$type";
        return $this->db->getAll($sql);
    }

    public function get_region_name($id) {
        $sql = "SELECT region_name FROM " . $this->ecs->table("region") . " WHERE region_id=$id";
        return $this->db->getOne($sql);
    }

    public function get_agency_id($id) {
        $sql = "SELECT agency_id FROM " . $this->ecs->table("region") . " WHERE region_id=$id";
        return $this->db->getOne($sql);
    }

    public function get_region_type($id) {
        $sql = "SELECT region_type FROM " . $this->ecs->table("region") . " WHERE region_id=$id";
        return $this->db->getOne($sql);
    }
    
    public function region_list($sche_id){
        $sql="SELECT * FROM " . $this->ecs->table("region") . "  WHERE region_id IN (SELECT region_id FROM". $this->ecs->table("pitch") ." WHERE id IN ( SELECT pitch_id FROM". $this->ecs->table("number") ."  WHERE sche_id =$sche_id))";
        return $this->db->getAll($sql);
        
    }
    
    public function get_schedule($game_id){
        
        $sql="SELECT DISTINCT(r.region_name),r.region_id FROM
	sk_schedule as s,
	sk_number as n,
	sk_pitch as p,
	sk_region as r
WHERE
	r.region_id=p.region_id
	AND
	n.sche_id=s.id
	AND	
	n.pitch_id=p.id
	AND
	s.game_id=".$game_id;

        $city_id=$this->db->getAll($sql);
        return $city_id;
    }
}
