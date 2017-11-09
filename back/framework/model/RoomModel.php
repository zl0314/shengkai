<?php

/*
 * 作者：戎青松
 * 时间：11:06:57
 * 
 */

/**
 * Description of GameModel
 *
 * @author Kevin
 */
__load("Model", "model");

class RoomModel extends Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_room() {
        /* 查询条件 */
       $filter = array();
       $filter['hotel_id'] = empty($_REQUEST['hotel_id']) ? 0 : intval($_REQUEST['hotel_id']);
       $where = (!empty($filter['hotel_id'])) ? " AND hotel_id = '$filter[hotel_id]' " : '';
        /* 记录总数 */
        $sql = "SELECT COUNT(*) FROM " .$GLOBALS['ecs']->table('room') ." WHERE 1 ".$where;
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        /* 分页大小 */
        $filter = page_and_size($filter);

        $sql = "SELECT r.*,h.hotel_name,t.type_name FROM " . $this->ecs->table("room") . " as r ," . $this->ecs->table("hotel") . "  as h ," . $this->ecs->table("room_type") . " as t WHERE r.hotel_id=h.id AND r.room_type=t.id ".$where."LIMIT " . $filter['start'] . ",$filter[page_size]";
       $row = $this->db->getAll($sql);
       return array('rooms' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
    }

    public function save($data) {
        return $this->db->autoExecute($this->ecs->table("room"), $data, 'INSERT');
    }
    public function update($data) {
        $id=$data['id'];
        unset($data['id']);
        return $this->db->autoExecute($this->ecs->table("room"), $data, 'UPDATE',"id=$id");
    }

    public function remove($id) {
        $sql = "DELETE FROM " . $this->ecs->table("room") . " WHERE id=$id";
        return $this->db->query($sql);
    }
    public function get($id) {
        $sql = "SELECT r.*,h.hotel_name,t.type_name FROM " . $this->ecs->table("room") . " as r ," . $this->ecs->table("hotel") . "  as h ," . $this->ecs->table("room_type") . " as t WHERE r.hotel_id=h.id AND r.room_type=t.id AND r.id=$id";
        return $this->db->getRow($sql);
    }

}
