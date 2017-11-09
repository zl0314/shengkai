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

class AdvertModel extends Model {

    //put your code here
    public function add_advert($data) {
        return $this->db->autoExecute($this->ecs->table("advert"), $data, 'INSERT');            
    }

    public function update_advert($data) {
        $id = $data['advert_id'];
        return $this->db->autoExecute($this->ecs->table("advert"), $data, 'UPDATE', "advert_id=$id");
    }


    public function get_advert_list(){
          /* 记录总数 */
        $sql = "SELECT COUNT(*) FROM  " . $GLOBALS['ecs']->table('advert');
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        /* 分页大小 */
        $filter = page_and_size($filter);

        $sql = "SELECT * FROM  " . $GLOBALS['ecs']->table('advert') . " LIMIT " . $filter['start'] . ",$filter[page_size]";
        $row = $this->db->getAll($sql);
        return array('advert' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
    }
    public function get_advert($id) {
        $sql = "SELECT * FROM " . $this->ecs->table("advert") . " WHERE advert_id=$id";
        return $this->db->getRow($sql);
    }

    public function remove_advert($id) {
        $sql = "DELETE FROM" . $this->ecs->table("advert") . " WHERE advert_id=$id";
        return $this->db->query($sql);
    }

    public function remove_batch($data) {
        foreach ($data as $key => $id) {
            $sql = "DELETE  FROM " . $this->ecs->table("advert") . " WHERE advert_id=$id";
            $res = $this->db->query($sql);
        }
        return $res;
    }
    
    public function get_advert_template($id){
        return $this->db->getOne( "SELECT advert_template FROM  " . $GLOBALS['ecs']->table('advert') . " WHERE advert_id=$id");
    }

}