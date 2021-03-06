<?php

/*
 * 作者：戎青松
 * 时间：9:58:15
 * 
 */

/**
 * Description of HotelModel
 *
 * @author Kevin
 */
__load("Model", "model");
class HotelModel extends Model {
    //put your code here
    public function add($data){
        return $this->db->autoExecute($this->ecs->table("hotel"), $data, 'INSERT');
    }
    public function update($data){
        $id=$data['id'];
        unset($data['id']);
        return $this->db->autoExecute($this->ecs->table("hotel"), $data, 'UPDATE'," id=$id");
    }
    public function get_AllHotel() {
        require_once(ROOT_PATH . '/sports/includes/lib_main.php');
        /* 记录总数 */
        $sql = "SELECT COUNT(*) FROM " .$GLOBALS['ecs']->table('hotel');
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);
        /* 分页大小 */
        $filter = page_and_size($filter);
        
        $sql = "SELECT * FROM " . $this->ecs->table("hotel")." LIMIT " . $filter['start'] . ",$filter[page_size]";
        $row = $this->db->getAll($sql);
        return array('hotel' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
    }
    public function get_Hotel($id) {
        $sql = "SELECT h.*,r.region_name FROM " . $this->ecs->table("hotel")." as h LEFT JOIN ".$this->ecs->table("region")." as r ON r.region_id=h.region_id   WHERE h.id=$id";
        return $this->db->getRow($sql);
    }
    public function remove($id){
        $sql = "DELETE FROM" . $this->ecs->table("hotel")." WHERE id=$id";
        return $this->db->query($sql);
    }
    public function remove_batch($data) {
        foreach ($data as $key => $id) {
        $sql = "DELETE FROM" . $this->ecs->table("hotel")." WHERE id=$id";
        $res=$this->db->query($sql);
        }
        return $res;
    }
    public function get_hotel_list($region_id){
        $sql = "SELECT * FROM " . $this->ecs->table("hotel")." WHERE region_id=$region_id";
        return $this->db->getAll($sql);
    }
    public function get_hotel_list_by_region_ids($region_ids){
        if (empty($region_ids)) {
            return array();
        }
        $sql = "SELECT * FROM " . $this->ecs->table("hotel")." WHERE region_id in (" . implode(',', $region_ids) . ")";
        return $this->db->getAll($sql);
    }
    public function  get_room_list($hotel_id){
        $sql = "SELECT r.*,ty.type_name,ty.people_num FROM " . $this->ecs->table("room")." as r LEFT JOIN ". $this->ecs->table("room_type")." as ty ON ty.id=r.room_type WHERE r.hotel_id=$hotel_id";
        return $this->db->getAll($sql);
    }
    public function  get_hotel_id($region_id){
        $sql = "SELECT COUNT(h.id) FROM " . $this->ecs->table("hotel")." as h,". $this->ecs->table("region")." as r  WHERE h.region_id=r.region_id AND r.region_id=$region_id";
        return $this->db->getOne($sql);
    }
}
