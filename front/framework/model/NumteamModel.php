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

class NumteamModel extends Model {

    //put your code here
    public function add($data) {
        return $this->db->autoExecute($this->ecs->table("hotel"), $data, 'INSERT');
    }

    public function update($numbe_id, $teams) {
        $sql = "DELETE FROM" . $this->ecs->table("num_team") . " WHERE num_id=$numbe_id";
        $this->db->query($sql);
        if (!empty($teams)) {
            foreach ($teams as $team) {
                $res = $this->db->autoExecute($this->ecs->table("num_team"), array("num_id" => $numbe_id, "team_id" => $team), 'INSERT');
            }
        }
    }

    public function save($numbe_id, $teams) {
        if (!empty($teams)) {
            foreach ($teams as $team) {
                $res = $this->db->autoExecute($this->ecs->table("num_team"), array("num_id" => $numbe_id, "team_id" => $team), 'INSERT');
            }
        }
    }

    public function get_team($num_id) {
        $sql = "SELECT team_id FROM " . $this->ecs->table("num_team") . " WHERE num_id=$num_id";
        return $this->db->getCol($sql);
    }
    
}
