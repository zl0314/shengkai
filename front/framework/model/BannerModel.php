<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BatchModel
 *
 * @author qs
 */
__load("Model", "model");

class BannerModel extends Model {

    //put your code here

    public function get() {
        /* 查询条件 */
       $filter = array();
       $filter['place_id'] = empty($_REQUEST['place_id']) ? 0 : intval($_REQUEST['place_id']);
       $where = (!empty($filter['place_id'])) ? " WHERE place_id = '$filter[place_id]' " : '';
        /* 记录总数 */
        $sql = "SELECT COUNT(*) FROM " .$GLOBALS['ecs']->table('banner') .$where;
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        /* 分页大小 */
        $filter = page_and_size($filter);
        
        $sql = "SELECT * FROM " . $this->ecs->table('banner') .$where." LIMIT " . $filter['start'] . ",$filter[page_size]";
        $banner = $this->db->getAll($sql);
        return array('banner' => $banner, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
    }
    
    public function getOne($id) {
        $sql = "SELECT * FROM " . $this->ecs->table('banner') . " WHERE id=$id";
        $banner = $this->db->getRow($sql);
        return $banner;
    }

    public function insert($data) {
        $res = $this->db->autoExecute($this->ecs->table("banner"), $data, 'INSERT');
        if ($res) {
            return true;
        } else {
            return false;
        }
    }
      public function update($data) {
        $res = $this->db->autoExecute($this->ecs->table("banner"), $data, 'UPDATE', "id=" . $data['id']);
        if ($res) {
            return true;
        } else {
            return false;
        }
    }
    public  function remove($id){
         $sql = "DELETE  FROM " . $this->ecs->table('banner') . " WHERE id=$id";
         return  $this->db->query($sql);
    }
    
    public function get_for_code($code){
        $sql="SELECT * FROM ". $this->ecs->table('banner') ." AS b , ". $this->ecs->table('banner_place') ." AS p WHERE  p.id=b.place_id And p.place_status=0 AND b.status=0 AND p.place_code='{$code}'  ORDER BY b.sequence";
        return  $this->db->getAll($sql);
    }

    //查询首页广告视频
    public function get_bill_list(){
        $sql = "SELECT * FROM ".$this->ecs->table('bill')." WHERE media_type = 2";
        return $this->db->getRow($sql);
    }

}
