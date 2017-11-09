<?php

/*
 * 作者：戎青松
 * 时间：9:58:15
 * 
 */

/**
 * Description of PitchModel
 *
 * @author Kevin
 */
__load("Model", "model");

class PitchModel extends Model {

    //put your code here
    public function add($data) {
        return $this->db->autoExecute($this->ecs->table("pitch"), $data, 'INSERT');
    }

    public function update($data) {
        $id = $data['id'];
        unset($data['id']);
        return $this->db->autoExecute($this->ecs->table("pitch"), $data, 'UPDATE', " id=$id");
    }

    public function get_AllPitch() {
         $sql = "SELECT * FROM " . $this->ecs->table("pitch")."ORDER BY pitch_sequence";
        return $this->db->getAll($sql);
    }
    
    public function get_pitch_list($game_id){
        $sql = "SELECT DISTINCT p.* FROM " . $this->ecs->table("pitch")."AS p,". $this->ecs->table("number")."AS n,". $this->ecs->table("game")."AS g,". $this->ecs->table("schedule")."AS s "." WHERE p.id=n.pitch_id AND n.sche_id=s.id AND s.game_id=g.id AND g.id=$game_id  ORDER BY p.pitch_sequence";
        return $this->db->getAll($sql);
    }
    //获取所有球场
    public function get_pitch_name_list(){
        $sql = "SELECT * FROM ".$GLOBALS['ecs']->table('pitch');
        return $this->db->getAll($sql);
    }
    
    public function get_All_Pitch() {      
      
         /* 记录总数 */
        $sql = "SELECT COUNT(*) FROM " .$GLOBALS['ecs']->table('pitch');
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        /* 分页大小 */
        $filter = page_and_size($filter);
        
        $sql = "SELECT * FROM " . $this->ecs->table("pitch")." LIMIT " . $filter['start'] . ",$filter[page_size]";
        $row = $this->db->getAll($sql);
        return array('pitchs' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
    }
      public function get_All_Goods(){
        $sql = "SELECT * FROM ".$GLOBALS['ecs']->table('goods');
        return $this->db->getAll($sql);
    }

    public function get_Pitch($id) {
        $sql = "SELECT * FROM " . $this->ecs->table("pitch") . " WHERE id=$id";
        return $this->db->getRow($sql);
    }

    public function remove($id) {
        $sql = "DELETE FROM" . $this->ecs->table("pitch") . " WHERE id=$id";
        return $this->db->query($sql);
    }

    public function remove_batch($data) {
        foreach ($data as $key => $id) {
            $sql = "DELETE  FROM " . $this->ecs->table("pitch") . " WHERE id=$id";
            $res = $this->db->query($sql);
        }
        return $res;
    }

}
