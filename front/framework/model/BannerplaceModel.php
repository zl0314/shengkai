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

class BannerplaceModel extends Model {

    //put your code here

    public function get() {
        /* 记录总数 */
        $sql = "SELECT COUNT(*) FROM " .$GLOBALS['ecs']->table('banner_place');
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        /* 分页大小 */
        $filter = page_and_size($filter);
        
        $sql = "SELECT * FROM " . $this->ecs->table('banner_place') . " ORDER BY id DESC LIMIT " . $filter['start'] . ",$filter[page_size]";
        $place_all = $this->db->getAll($sql);
        return array('place_all' => $place_all, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
    }

    public function getOne($id) {
        $sql = "SELECT * FROM " . $this->ecs->table('banner_place') . " WHERE id=$id";
        $place = $this->db->getRow($sql);
        return $place;
    }

    public function update($data) {
        $res = $this->db->autoExecute($this->ecs->table("banner_place"), $data, 'UPDATE', "id=" . $data['id']);
        if ($res) {
            return true;
        } else {
            return false;
        }
    }

    public function insert($data) {
        $res = $this->db->autoExecute($this->ecs->table("banner_place"), $data, 'INSERT');
        if ($res) {
            return true;
        } else {
            return false;
        }
    }
    public function remove($id){
         $sql = "DELETE  FROM " . $this->ecs->table('banner_place') . " WHERE id=$id";
         $this->db->query($sql);
         $sql = "DELETE  FROM " . $this->ecs->table('banner') . " WHERE place_id=$id";
        return $this->db->query($sql);
        
    }

}   