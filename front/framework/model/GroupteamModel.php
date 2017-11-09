<?php

/*
 * 作者：鞠嵩
 * 时间：9:58:15
 * 
 */

/**
 * Description of GroupTeamModel
 *
 * @author Kevin
 */
__load("Model", "model");

class GroupteamModel extends Model {

    //put your code here
//    public function add($data) {
//        return $this->db->autoExecute($this->ecs->table("hotel"), $data, 'INSERT');
//    }
//
    public function update($group_id, $teams) {
        $sql = "DELETE FROM" . $this->ecs->table("team_group") . " WHERE group_id=$group_id";
        $this->db->query($sql);
        if (!empty($teams)) {
            foreach ($teams as $team) {
                $res = $this->db->autoExecute($this->ecs->table("team_group"), array("team_id" => $team, "group_id" => $group_id), 'INSERT');
            }
        }
    }

    public function save($group_id, $teams) {
        if (!empty($teams)) {
            foreach ($teams as $team){
                $res = $this->db->autoExecute($this->ecs->table("team_group"), array("team_id" => $team, "group_id" => $group_id), 'INSERT');
            }
        }
    }

    public function get_team($group_id) {
        $sql = "SELECT team_id FROM  " . $this->ecs->table("team_group") . " WHERE group_id=$group_id";
        return $this->db->getCol($sql);
    }
    
}
