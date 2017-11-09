<?php

/*
 * 作者：鞠嵩
 * 时间：09:45:52
 * 
 */

/**
 * Description of GroupModel
 *
 * @author Kevin
 */
__load("Model", "model");

class ColorModel extends Model {

    //put your code here
    public function add_Color($data) {
        return $this->db->autoExecute($this->ecs->table("color_manage"), $data, 'INSERT');            
    }

    public function update($data) {
        $id = $data['color_id'];
        return $this->db->autoExecute($this->ecs->table("color_manage"), $data, 'UPDATE', " color_id=$id");
    }

    public function get_All_Color() {
     
//        /* 记录总数 */
        $sql = "SELECT COUNT(*) FROM  " . $GLOBALS['ecs']->table('color_manage')."AS c,". $GLOBALS['ecs']->table('game'). "AS g" .
            " WHERE g.id=c.game_id ";
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        /* 分页大小 */
        $filter = page_and_size($filter);

        $sql = "SELECT c.*,g.game_name FROM  " . $GLOBALS['ecs']->table('color_manage')."AS c,". $GLOBALS['ecs']->table('game'). "AS g" .
            " WHERE g.id=c.game_id " . " LIMIT " . $filter['start'] . ",$filter[page_size]";
        $row = $this->db->getAll($sql);
        return array('colors' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
    }
     
    public function get_Color($id) {
        $sql = "SELECT * FROM " . $this->ecs->table("color_manage") . " WHERE color_id=$id";
        return $this->db->getRow($sql);
    }

    public function remove($id) {
        $sql = "DELETE FROM" . $this->ecs->table("color_manage") . " WHERE color_id=$id";
        return $this->db->query($sql);
    }

    public function remove_batch($data) {
        foreach ($data as $key => $id) {
            $sql = "DELETE  FROM " . $this->ecs->table("color_manage") . " WHERE color_id=$id";
            $res = $this->db->query($sql);
        }
        return $res;
    }
    public function get_Color_info($game_id){
        return $this->db->getAll("SELECT * FROM ". $this->ecs->table("color_manage") . " WHERE game_id=$game_id");
    }
     public function color_List($game_id){
        return $this->db->getAll("SELECT * FROM ". $this->ecs->table("color_manage") . " WHERE game_id=$game_id AND is_color=1");
    }
}