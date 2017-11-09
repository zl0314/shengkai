<?php
/**
 * Description of GroupModel
 *
 * @author Kevin
 */
__load("Model", "model");

class Combo_pitchModel extends Model {

    //put your code here
    public function add_Combo_pitch($data) {
        return $this->db->autoExecute($this->ecs->table("combo_pitch"), $data, 'INSERT');            
    }

    public function update($data) {
        $id = $data['id'];
        return $this->db->autoExecute($this->ecs->table("combo_pitch"), $data, 'UPDATE', " combo_pitch_id=$id");
    }

    public function get_All_Combo_pitch() {
        /*
        $sql = "SELECT COUNT(*) FROM  " . $GLOBALS['ecs']->table('combo_pitch');
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);
        $filter = page_and_size($filter);
        $sql = "SELECT * FROM  " . $GLOBALS['ecs']->table('combo_pitch'). " LIMIT " . $filter['start'] . ",$filter[page_size]";
        $row = $this->db->getAll($sql);
        return array('combo_pitch_list' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
        */
        $sql = "SELECT * FROM  " . $GLOBALS['ecs']->table('combo_pitch');
        $row = $this->db->getAll($sql);
        return array('combo_pitch_list' => $row);
    }
     
    public function get_Combo_pitch($id) {
        $sql = "SELECT * FROM " . $this->ecs->table("combo_pitch") . " WHERE combo_pitch_id=$id";
        return $this->db->getRow($sql);
    }

    public function remove($id) {
        $sql = "DELETE FROM" . $this->ecs->table("combo_pitch") . " WHERE combo_pitch_id=$id";
        return $this->db->query($sql);
    }

    public function remove_batch($data) {
        foreach ($data as $id) {
            $sql = "DELETE  FROM " . $this->ecs->table("combo_pitch") . " WHERE combo_pitch_id=$id";
            $res = $this->db->query($sql);
        }
        return $res;
    }
    
    public function get_combo_pitch_list() {
        $sql = "SELECT combo_pitch_id,combo_pitch_name FROM " . $this->ecs->table("combo_pitch");
        return $this->db->getAll($sql);
    }
}