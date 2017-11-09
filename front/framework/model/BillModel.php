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

class BillModel extends Model {

    //put your code here
    public function add_bill($data) {
        return $this->db->autoExecute($this->ecs->table("bill"), $data, 'INSERT');            
    }

    public function update_bill($data) {
        $id = $data['bill_id'];
        return $this->db->autoExecute($this->ecs->table("bill"), $data, 'UPDATE', "bill_id=$id");
    }


    public function get_bill_list(){
          /* 记录总数 */
        $sql = "SELECT COUNT(*) FROM  " . $GLOBALS['ecs']->table('bill');
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        /* 分页大小 */
        $filter = page_and_size($filter);

        $sql = "SELECT * FROM  " . $GLOBALS['ecs']->table('bill') . " LIMIT " . $filter['start'] . ",$filter[page_size]";
        $row = $this->db->getAll($sql);
        foreach($row as &$val){
            $val['start_time'] = date("Y-m-d H:i:s", $val['start_time']);
            $val['end_time'] = date("Y-m-d H:i:s", $val['end_time']);
        }
        return array('bill' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
    }
    public function get_bill($id) {
        $sql = "SELECT * FROM " . $this->ecs->table("bill") . " WHERE bill_id=$id";
        $res = $this->db->getRow($sql);
        $res['start_time']  = local_date('Y-m-d', $res['start_time']);
        $res['end_time']    = local_date('Y-m-d', $res['end_time']);
        return $res;
    }

    public function remove_bill($id) {
        $sql = "DELETE FROM" . $this->ecs->table("bill") . " WHERE bill_id=$id";
        return $this->db->query($sql);
    }

    public function remove_batch($data) {
        foreach ($data as $key => $id) {
            $sql = "DELETE  FROM " . $this->ecs->table("bill") . " WHERE bill_id=$id";
            $res = $this->db->query($sql);
        }
        return $res;
    }
    
    public function get_bill_show(){
        return $this->db->getAll( "SELECT * FROM  " . $GLOBALS['ecs']->table('bill') . " WHERE is_show = 1 LIMIT 4");
    }
    
    public function get_bill_id($id){
        return $this->db->getOne( "SELECT bill_id  FROM  " . $GLOBALS['ecs']->table('bill') . " WHERE bill_url = 'advert.php?id=$id'");
    }

}