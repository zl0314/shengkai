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

class Set_meal_orderModel extends Model {

    //put your code here
    public function add_set_meal_order($data) {
        return $this->db->autoExecute($this->ecs->table("set_meal_order"), $data, 'INSERT');            
    }

    public function update_set_meal_order($data) {
        $id = $data['set_meal_order_id'];
        return $this->db->autoExecute($this->ecs->table("set_meal_order"), $data, 'UPDATE', "set_meal_order_id=$id");
    }


    public function get_set_meal_order_list(){
          /* 记录总数 */
        $sql = "SELECT COUNT(*) FROM  " . $GLOBALS['ecs']->table('set_meal_order')."AS so, ". $GLOBALS['ecs']->table('set_meal')."AS s WHERE s.set_meal_id=so.set_meal_id"  ;
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        /* 分页大小 */
        $filter = page_and_size($filter);

        $sql = "SELECT so.*,s.set_meal_name FROM  "  . $GLOBALS['ecs']->table('set_meal_order')."AS so, ". $GLOBALS['ecs']->table('set_meal')."AS s WHERE s.set_meal_id=so.set_meal_id"  . " LIMIT " . $filter['start'] . ",$filter[page_size]";
        $row = $this->db->getAll($sql);
        return array('set_meal_order' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
    }
    public function get_set_meal_order($id) {
        $sql = "SELECT so.*,s.set_meal_name FROM " . $this->ecs->table("set_meal_order") . " AS so, ". $this->ecs->table("set_meal") . " AS s WHERE so.set_meal_order_id=$id AND so.set_meal_id=s.set_meal_id";
        return $this->db->getRow($sql);
    }

    public function remove_set_meal_order($id) {
        $sql = "DELETE FROM" . $this->ecs->table("set_meal_order") . " WHERE set_meal_order_id=$id";
        return $this->db->query($sql);
    }

    public function remove_batch($data) {
        foreach ($data as $key => $id) {
            $sql = "DELETE  FROM " . $this->ecs->table("set_meal_order") . " WHERE set_meal_order_id=$id";
            $res = $this->db->query($sql);
        }
        return $res;
    } 

}