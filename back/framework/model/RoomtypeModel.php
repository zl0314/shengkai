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

class RoomtypeModel extends Model {

    public function __construct() {
        parent::__construct();
    }

    public function save($data) {
        $res = $this->db->autoExecute($this->ecs->table("room_type"), $data, 'INSERT');
        if ($res) {
            return $this->db->insert_id();
        } else {
            return 0;
        }
    }
    public function update($data) {
        $res = $this->db->autoExecute($this->ecs->table("room_type"), $data, 'UPDATE' ,"id=".$data['id']);
        return $res;
    }

    public function getAll() {       
        $sql = "SELECT * FROM " . $this->ecs->table("room_type");
        return $this->db->getAll($sql);
    }
    
    public function get_All() {
        /* 记录总数 */
        $sql = "SELECT COUNT(*) FROM " .$GLOBALS['ecs']->table('room_type');
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        /* 分页大小 */
        $filter = page_and_size($filter);
        
        $sql = "SELECT * FROM " . $this->ecs->table("room_type")." LIMIT " . $filter['start'] . ",$filter[page_size]";
        $row = $this->db->getAll($sql);
        return array('type_list' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
    }

    public function get($id) {
        $sql = "SELECT * FROM " . $this->ecs->table("room_type") . " WHERE id=$id";
        return $this->db->getRow($sql);
    }
    public function remove($id) {
        $sql = "DELETE  FROM " . $this->ecs->table("room_type") . " WHERE id=$id";
        return $this->db->query($sql);
    }

}
