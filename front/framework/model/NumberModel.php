<?php

/*
 * 作者：鞠嵩
 * 时间：15:58:15
 * 
 */

/**
 * Description of PitchModel
 *
 * @author Kevin
 */
__load("Model", "model");

class NumberModel extends Model {

    //put your code here
    private $number_key = array(
                'id', 'num_name', "num_text", 'num_img', 'sche_id', 'pitch_id', 'numstart', 'num_end', 'num_title', 'is_hot'
                    ), $number, $num_team_key = array('id', 'num_id', 'team_ids');

    public function __construct() {
        parent::__construct();
    }

    public function set(Array $array) {
        foreach ($this->number_key as $value) {
            if (isset($array[$value])) {
                $this->number[$value] = $array[$value];
            }
        }
        foreach ($this->num_team_key as $res) {
            if (isset($array[$res])) {
                $this->num_team_key[$res] = $array[$res];
            }
        }
    }

//    public function add($data){
//        return $this->db->autoExecute($this->ecs->table("number"), $data, 'INSERT');
//    }
    public function update($data) {
        $id = $data['id'];
        unset($data['id']);
        return $this->db->autoExecute($this->ecs->table("number"), $data, 'UPDATE', " id=$id");
    }

    public function get_All_Number() {
        $sql = "SELECT n.*,s.sche_name,p.pitch_name FROM sk_number as n,sk_schedule as s,sk_pitch as p WHERE n.sche_id=s.id AND n.pitch_id =p.id ";
        return $this->db->getAll($sql);
    }
    public function getAllNumber(){
        $sql = "SELECT * FROM " . $GLOBALS['ecs']->table('number');
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);
        return $this->db->getAll($sql);
    }
    public function get_List($id) {

        $sql = "SELECT n.*,s.sche_name,p.pitch_name FROM sk_schedule as s,sk_number AS n LEFT JOIN sk_pitch AS p ON n.pitch_id=p.id WHERE n.sche_id=s.id AND  n.sche_id=$id";
        //    $sql = "SELECT n.*,s.sche_name,p.pitch_name FROM sk_number as n,sk_schedule as s,sk_pitch as p WHERE n.sche_id=s.id AND n.pitch_id =p.id AND n.sche_id=$id";       
        // $sql = "SELECT n.*,s.sche_name,p.pitch_name FROM sk_schedule as s,sk_number AS n LEFT JOIN sk_pitch AS p ON n.pitch_id=p.id WHERE n.sche_id=s.id AND  n.sche_id= '$id'";

        return $this->db->getAll($sql);
    }

    public function number_list() {
        /* 查询条件 */
        $filter = array();
        $filter['id'] = empty($_REQUEST['id']) ? 0 : intval($_REQUEST['id']);
        $filter['p_id'] = empty($_REQUEST['p_id']) ? 0 : intval($_REQUEST['p_id']);
        $where = (!empty($filter['id'])) ? "  AND  n.sche_id= '$filter[id]' " : '';
        /* 记录总数 */
        $sql = "SELECT COUNT(*) FROM sk_schedule as s,sk_number AS n LEFT JOIN sk_pitch AS p ON n.pitch_id=p.id WHERE n.sche_id=s.id" . $where;
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        /* 分页大小 */
        $filter = page_and_size($filter);

        $sql = "SELECT n.*,s.sche_name,p.pitch_name FROM sk_schedule as s,sk_number AS n LEFT JOIN sk_pitch AS p ON n.pitch_id=p.id WHERE n.sche_id=s.id" . $where . " LIMIT " . $filter['start'] . ",$filter[page_size]";
        $row = $this->db->getAll($sql);
        return array('number' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
    }

    public function save($data) {
        $this->number['num_start'] = strtotime($this->number['num_start']);
        $this->number['num_end'] = strtotime($this->number['num_end']);

        $res = $this->db->autoExecute($this->ecs->table("number"), $data, 'INSERT');
        if ($res) {
            return $this->db->insert_id();
        } else {
            return 0;
        }
    }

    public function toString() {
        var_dump(strtotime($this->number['num_start']));
        var_dump(strtotime($this->number['num_end']));
    }

//    public function get_Pitch($id) {
//        $sql = "SELECT * FROM " . $this->ecs->table("pitch")." WHERE id=$id";
//        return $this->db->getRow($sql);
//    }
    public function remove($id) {
        $sql = "DELETE FROM" . $this->ecs->table("number") . " WHERE id=$id";
        return $this->db->query($sql);
    }

    public function get_Number($id) {
        $sql = "SELECT n.*,c.color_name,s.sche_name,s.id as sche_id,p.pitch_name,g.game_name FROM " . $this->ecs->table("schedule") . " as s," . $this->ecs->table("color_manage") . " as c," . $this->ecs->table("game") . " as g," . $this->ecs->table("number") . " as n LEFT JOIN " . $this->ecs->table("pitch") . " as p ON n.pitch_id = p.id WHERE n.sche_id=s.id AND s.game_id=g.id AND  n.id=$id";

        return $this->db->getRow($sql);
    }

    public function remove_batch($data) {
        foreach ($data as $key => $id) {
            $sql = "DELETE FROM" . $this->ecs->table("number") . " WHERE id=$id";
            $res = $this->db->query($sql);
        }
        return $res;
    }

    public function get_number_attr($id) {
//        $sql="SELECT g.*, a.*, p.product_number,p.product_id FROM ".$this->ecs->table("goods") ." as g ,".$this->ecs->table("goods_attr") ."as a ,".$this->ecs->table("products")." as p"." WHERE g.number_id={$id} AND a.goods_id=g.goods_id AND p.goods_attr = a.goods_attr_id GROUP BY attr_value";
        $sql = "SELECT * FROM " . $this->ecs->table("goods") . " WHERE number_id={$id} AND is_ticket = 1 AND is_delete=0";
        $res = $this->db->getAll($sql);
        return $res;
    }

    public function get_hot($number) {
        $sql = "SELECT
	r.region_name,n.*,g.game_logo,c.color_value
FROM
	sk_game as g,
	sk_schedule as s,
	sk_number as n
	LEFT JOIN
	sk_pitch as p
	ON
	n.pitch_id=p.id
	LEFT JOIN
	sk_region as r
	ON
	p.region_id=r.region_id
        LEFT JOIN        
        sk_color_manage as c
        ON
        c.color_id=n.color_id
WHERE 
	n.is_hot=1
AND	
	n.sche_id=s.id
AND
	s.game_id=g.id 	
LIMIT $number

	";
        $res = $this->db->getAll($sql);
        return $res;
    }

    public function schel_num($id) {
        $sql = "SELECT n.*,c.color_value FROM " . $this->ecs->table('number') . "AS n,". $this->ecs->table('color_manage') . "AS c" ." WHERE sche_id = {$id} AND c.color_id=n.color_id";
        return $this->db->getAll($sql);
    }

    /**
     * 获取场次信息
     * @param type $id
     */
    public function get($id) {
        $sql = "SELECT * FROM " . $this->ecs->table('number') . " WHERE id = {$id}";
        return $this->db->getAll($sql);
    }

    public function number_color($id) {
        $sql="SELECT c.color_value FROM ". $this->ecs->table('number') . "AS n,". $this->ecs->table('color_manage') . "AS c"." WHERE n.id = {$id} AND c.color_id=n.color_id";
        return $this->db->getOne($sql);
    }

}
