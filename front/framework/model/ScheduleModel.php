<?php

/**
 * 作者：鞠嵩
 * 时间：11:38:56
 * 
 */
/**
 * Description of ScheduleModel
 *
 * @author jusong
 */
__load("Model", "model");

class ScheduleModel extends Model {

    private $schedule_key = array('id', 'sche_name', 'sche_intro', 'sche_img', 'game_id', 'sche_start', 'sche_end'), $schedule;

    public function __construct() {
        parent::__construct();
    }

    function get_all() {      
        $sql = "SELECT * FROM " . $this->ecs->table("schedule") ;
        return $this->db->getAll($sql);
    }
    
    function get_alls() {
       /* 查询条件 */
       $filter = array();
       $filter['id'] = empty($_REQUEST['id']) ? 0 : intval($_REQUEST['id']);
       $where = (!empty($filter['id'])) ? " WHERE game_id = '$filter[id]' " : '';
        /* 记录总数 */
        $sql = "SELECT COUNT(*) FROM " .$GLOBALS['ecs']->table('schedule') .$where;
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        /* 分页大小 */
        $filter = page_and_size($filter);
        $sql = "SELECT * FROM " . $this->ecs->table("schedule") .$where." LIMIT " . $filter['start'] . ",$filter[page_size]";
        $row = $this->db->getAll($sql);
        return array('schedule' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
    }

    function remove($id) {
        //查看当前赛程下是否存在场次
        $num_exist = $this->db->getOne("SELECT * FROM " . $this->ecs->table("number") . " WHERE sche_id=$id");
        if (empty($num_exist)) {
            $sql = "DELETE FROM" . $this->ecs->table("schedule") . "WHERE id=$id";
            return $this->db->query($sql);
        } else {
            return false;
        }
    }

    public function save($data) {
        $this->schedule['sche_start'] = strtotime($this->schedule['sche_start']);
        $this->schedule['sche_end'] = strtotime($this->schedule['sche_end']);
        $res = $this->db->autoExecute($this->ecs->table("schedule"), $data, 'INSERT');
        if ($res) {
            return $this->db->insert_id();
        } else {
            return 0;
        }
    }

    public function get_gname($id) {
        $sql = "SELECT game_name FROM " . $this->ecs->table("game") . "WHERE id = $id";
        return $this->db->getOne($sql);
    }

    public function update($data) {
        $this->schedule['sche_start'] = strtotime($this->schedule['sche_start']);
        $this->schedule['sche_end'] = strtotime($this->schedule['sche_end']);
        $res = $this->db->autoExecute($this->ecs->table("schedule"), $data, 'UPDATE', "id=" . $this->schedule['id']);
        return $res;
    }

    public function set(Array $array) {
        foreach ($this->schedule_key as $value) {
            if (isset($array[$value])) {
                $this->schedule[$value] = $array[$value];
            }
        }
    }

    public function toString() {
        var_dump(strtotime($this->schedule['sche_end']));
        var_dump(strtotime($this->schedule['sche_start']));
    }

    public function get($id) {
        $sql = "SELECT * FROM " . $this->ecs->table("schedule") . "WHERE id=$id";
        return $this->db->getRow($sql);
    }

    public function getgname($id) {
//        $game_id=$this->db->getOne("SELECT game_id FROM ".$this->ecs->table("schedule")."WHERE id=$id");
//        $sql="SELECT game_name FROM ".$this->ecs->table("game")."WHERE id=$game_id";
        $sql = "SELECT g.game_name,s.sche_name FROM " . $this->ecs->table("game") . " as g, " . $this->ecs->table("schedule") . " as s WHERE g.id=s.game_id AND s.id=$id";
        return $this->db->getRow($sql);
    }

    public function getgid($id) {
        $game_id = $this->db->getOne("SELECT game_id FROM " . $this->ecs->table("schedule") . "WHERE id=$id");
        return $game_id;
    }

    function get_Sche_List() {
        $sql = "SELECT * FROM " . $this->ecs->table("schedule");
        return $this->db->getAll($sql);
    }
    
    function sche_list($game_id) {
        $sql = "SELECT * FROM " . $this->ecs->table("schedule")."WHERE game_id = $game_id";
        return $this->db->getAll($sql);
    }
 
    function get_Sche_Name($id) {
        $game_id = $this->db->getOne("SELECT sche_name FROM " . $this->ecs->table("schedule") . "WHERE id=$id");
        return $game_id;
    }

    public function remove_batch($data) {
        foreach ($data as $key => $id) {
            //查看当前赛程下是否存在场次
            $num_exist = $this->db->getOne("SELECT * FROM " . $this->ecs->table("number") . " WHERE sche_id=$id");
            if (empty($num_exist)) {
                $sql = "DELETE FROM" . $this->ecs->table("schedule") . "WHERE id=$id";
                $res = $this->db->query($sql);
            }
        }
        return $res;
    }
        function sche_list_info($game_id) {
        $sql = "SELECT * FROM " . $this->ecs->table("schedule")."WHERE game_id = $game_id ORDER BY sequence";
        return $this->db->getAll($sql);
    }

}

