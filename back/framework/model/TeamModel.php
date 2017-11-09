<?php

/*
 * 作者：鞠嵩
 * 时间：10:21:51
 * 
 */

/**
 * Description of TeamModel
 *
 * @author jusong
 */
__load("Model", "model");

class TeamModel extends Model {

    //put your code here
    private $team_key = array(
                'id', 'team_name', "team_intro", 'team_img'
                    ), $team;

    public function __construct() {
        parent::__construct();
    }

    public function get($id) {
        $sql = "SELECT * FROM " . $this->ecs->table("teams") . "WHERE id=$id";
        return $this->db->getRow($sql);
    }

    public function get_team_info($teams) {
        $where = implode(",", $teams);
        $sql = "SELECT * FROM " . $this->ecs->table("teams") . "WHERE id in(" . $where . ")";
        return $this->db->getAll($sql);
    }

    public function get_all() {
        $sql = "SELECT * FROM " . $this->ecs->table("teams");
        return $this->db->getAll($sql);
    }
  
    public function get_alls() {
       
          /* 记录总数 */
        $sql = "SELECT COUNT(*) FROM " .$GLOBALS['ecs']->table('teams');
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        /* 分页大小 */
        $filter = page_and_size($filter);
        
        $sql = "SELECT * FROM " . $this->ecs->table("teams")." LIMIT " . $filter['start'] . ",$filter[page_size]";
        $teams =  $this->db->getAll($sql);
        return array('teams' => $teams, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
    }
    
    public function set(Array $array) {
        foreach ($this->team_key as $value) {
            if (isset($array[$value])) {
                $this->team[$value] = $array[$value];
            }
        }
    }

    public function save() {
        $res = $this->db->autoExecute($this->ecs->table("teams"), $this->team, 'INSERT');
        if ($res) {
            return $this->db->insert_id();
        } else {
            return 0;
        }
    }

    public function update() {
        $res = $this->db->autoExecute($this->ecs->table("teams"), $this->team, 'UPDATE', "id=" . $this->team['id']);
        return $res;
    }

    public function remove($id) {
        $sql = "DELETE  FROM " . $this->ecs->table("teams") . " WHERE id=$id";
        return $this->db->query($sql);
    }
    
     public function remove_batch($data) {
        foreach ($data as $key => $id) {
        $sql = "DELETE  FROM " . $this->ecs->table("teams") . " WHERE id=$id";
        $res=$this->db->query($sql);
        }
        return $res;
    }
    
}
