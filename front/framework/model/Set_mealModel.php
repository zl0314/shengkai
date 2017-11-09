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

class Set_mealModel extends Model {

    //put your code here
    public function add_set_meal($data) {
        return $this->db->autoExecute($this->ecs->table("set_meal"), $data, 'INSERT');            
    }

    public function update_set_meal($data) {
        $id = $data['set_meal_id'];
        return $this->db->autoExecute($this->ecs->table("set_meal"), $data, 'UPDATE', "set_meal_id=$id");
    }


    public function get_set_meal_list(){
          /* 记录总数 */
        $sql = "SELECT COUNT(*) FROM  " . $GLOBALS['ecs']->table('set_meal');
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        /* 分页大小 */
        $filter = page_and_size($filter);

        $sql = "SELECT * FROM  " . $GLOBALS['ecs']->table('set_meal') . " LIMIT " . $filter['start'] . ",$filter[page_size]";
        $row = $this->db->getAll($sql);
        return array('set_meal' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
    }
    public function get_set_meal($id) {
        $sql = "SELECT * FROM " . $this->ecs->table("set_meal") . " WHERE set_meal_id=$id";
        return $this->db->getRow($sql);
    }

    public function remove_set_meal($id) {
        $sql = "DELETE FROM" . $this->ecs->table("set_meal") . " WHERE set_meal_id=$id";
        return $this->db->query($sql);
    }

    public function remove_batch($data) {
        foreach ($data as $key => $id) {
            $sql = "DELETE  FROM " . $this->ecs->table("set_meal") . " WHERE set_meal_id=$id";
            $res = $this->db->query($sql);
        }
        return $res;
    }
    
    public function get_set_meal_show(){
        return $this->db->getAll( "SELECT * FROM  " . $GLOBALS['ecs']->table('set_meal') . " WHERE is_show = 1 LIMIT 4");
    }
    
    public function get_set_meal_id($id){
        return $this->db->getOne( "SELECT set_meal_id  FROM  " . $GLOBALS['ecs']->table('set_meal') . " WHERE set_meal_url = 'advert.php?id=$id'");
    }

}