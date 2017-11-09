<?php

__load("Model", "model");
class SpaceModel extends Model {
    //put your code here
    public function add($data){
        return $this->db->autoExecute($this->ecs->table("ship_space"), $data, 'INSERT');
    }
    
    public function update($data){
        $id = $data['id'];
        unset($data['id']);
        return $this->db->autoExecute($this->ecs->table("ship_space"), $data, 'UPDATE'," s_id=$id");
        
    }
    
    public function space_all() {
        /* 记录总数 */
        $sql = "SELECT COUNT(*) FROM " .$GLOBALS['ecs']->table('ship_space');
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        /* 分页大小 */
        $filter = page_and_size($filter);
        $sql = "SELECT * FROM " . $this->ecs->table("ship_space")." LIMIT " . $filter['start'] . ",$filter[page_size]";
        $row = $this->db->getAll($sql);
        return array('space' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
    }
    
    public function space_info($id) {
        $sql = "SELECT * FROM " . $this->ecs->table("ship_space")." WHERE s_id = $id";
        return $this->db->getRow($sql);
    }
    
    public function remove($id){
        $sql = "DELETE FROM" . $this->ecs->table("ship_space")." WHERE s_id = $id";
        return $this->db->query($sql);
    }
    
    public function remove_batch($data) {
        foreach ($data as $key => $id) {
            $sql = "DELETE FROM" . $this->ecs->table("ship_space")." WHERE s_id = $id";
            $res=$this->db->query($sql);
        }
        return $res;
    }
}
