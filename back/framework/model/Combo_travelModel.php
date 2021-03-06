<?php
/**
 * Description of GroupModel
 *
 * @author Kevin
 */
__load("Model", "model");

class Combo_travelModel extends Model {

    //put your code here
    public function add_Combo_travel($data) {
        return $this->db->autoExecute($this->ecs->table("combo_travel"), $data, 'INSERT');
    }

    public function update($data) {
        $id = $data['id'];
        return $this->db->autoExecute($this->ecs->table("combo_travel"), $data, 'UPDATE', " combo_travel_id=$id");
    }

    public function get_All_Combo_travel() {
//        $sql = "SELECT * FROM ".$GLOBALS['ecs']->table('combo');
//        $res = $GLOBALS['db']->getAll($sql);
//        echo "<pre>";
//        print_r($res);die;
//        foreach($res as $k=>$v){
//            $res[$k]['combo_travels'] = explode('|',$v['combo_travels']);
//            foreach($res[$k]['combo_travels'] as $key=>$val){
//                $res[$k]['combo_travels']['combo_name'] = $res[$k]['combo_name'];
//                $res[$k]['combo_travels'][]
//            }
//        }
        /*
        $sql = "SELECT COUNT(*) FROM  " . $GLOBALS['ecs']->table('combo_travel');
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);
        $filter = page_and_size($filter);
        $sql = "SELECT * FROM  " . $GLOBALS['ecs']->table('combo_travel'). " LIMIT " . $filter['start'] . ",$filter[page_size]";
        $row = $this->db->getAll($sql);
        foreach($row as &$val){
            $val['combo_travel_date'] = date("m月d日",$val['combo_travel_date']);
        }
        return array('combo_travel_list' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
        */
        $sql = "SELECT * FROM  " . $GLOBALS['ecs']->table('combo_travel');
        $row = $this->db->getAll($sql);
//        echo "<pre>";
//        print_r($row);die;
//        foreach($row as &$val){
//            @$val['combo_travel_date'] = date("m月d日",$val['combo_travel_date']);
//        }
        return array('combo_travel_list' => $row);
    }

    public function get_All_Combo_travel_by_type_id($type_id) {
        $sql = "SELECT * FROM  " . $GLOBALS['ecs']->table('combo_travel') . " WHERE combo_travel_type_id = '{$type_id}' order by combo_travel_day asc";
        $row = $this->db->getAll($sql);
        /* 分页大小 */
        $filter = page_and_size(array());
        return array('list' => $row, 'record_count' => count($row), 'page_count' => 1, 'filter' => $filter);
    }
    public function get_Combo_travel($id) {
        $sql = "SELECT * FROM " . $this->ecs->table("combo_travel") . " WHERE combo_travel_id=$id";
        return $this->db->getRow($sql);
    }

    public function remove($id) {
        $sql = "DELETE FROM" . $this->ecs->table("combo_travel") . " WHERE combo_travel_id=$id";
        return $this->db->query($sql);
    }

    public function remove_batch($data) {
        foreach ($data as $id) {
            $sql = "DELETE  FROM " . $this->ecs->table("combo_travel") . " WHERE combo_travel_id=$id";
            $res = $this->db->query($sql);
        }
        return $res;
    }

    public function get_combo_travel_list() {
        $sql = "SELECT combo_travel_id,combo_travel_name FROM " . $this->ecs->table("combo_travel") . " WHERE is_display=1";
        return $this->db->getAll($sql);
    }
}