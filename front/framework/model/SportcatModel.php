<?php
/**
 * Description of GroupModel
 *
 * @author Kevin
 */
__load("Model", "model");

class SportcatModel extends Model {

    //put your code here
    public function add_Sportcat($data) {
        return $this->db->autoExecute($this->ecs->table("sportcat"), $data, 'INSERT');            
    }

    public function update($data) {
        $id = $data['id'];
        return $this->db->autoExecute($this->ecs->table("sportcat"), $data, 'UPDATE', " id=$id");
    }

    public function get_All_Sportcat() {
        /* 记录总数 */
        $sql = "SELECT COUNT(*) FROM  " . $GLOBALS['ecs']->table('sportcat');
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        /* 分页大小 */
        $filter = page_and_size($filter);

        $sql = "SELECT * FROM  " . $GLOBALS['ecs']->table('sportcat'). " LIMIT " . $filter['start'] . ",$filter[page_size]";
        $row = $this->db->getAll($sql);
        return array('sportcat_list' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
    }
     
    public function get_Sportcat($id) {
        $sql = "SELECT * FROM " . $this->ecs->table("sportcat") . " WHERE id=$id";
        return $this->db->getRow($sql);
    }

    public function remove($id) {
        $sql = "DELETE FROM" . $this->ecs->table("sportcat") . " WHERE id=$id";
        return $this->db->query($sql);
    }

    public function remove_batch($data) {
        foreach ($data as $id) {
            $sql = "DELETE  FROM " . $this->ecs->table("sportcat") . " WHERE id=$id";
            $res = $this->db->query($sql);
        }
        return $res;
    }
    
    public function get_sportcat_list() {
        $sql = "SELECT id,name FROM " . $this->ecs->table("sportcat") . " WHERE is_display=1";
        return $this->db->getAll($sql);
    }

    //获取所有赛事--hechengbin--
    public function get_all_game(){
        return $this->db->getAll("SELECT * FROM " . $this->ecs->table('game'));
    }
}