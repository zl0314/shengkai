<?php
/**
 * Description of GroupModel
 *
 * @author Kevin
 */
__load("Model", "model");

class ComboModel extends Model {

    //put your code here
    public function add_Combo($data) {
        return $this->db->autoExecute($this->ecs->table("combo"), $data, 'INSERT');            
    }

    public function update($data) {
        $id = $data['id'];
        return $this->db->autoExecute($this->ecs->table("combo"), $data, 'UPDATE', " combo_id=$id");
    }

    public function get_All_Combo() {
        /* 记录总数 */
        $sql = "SELECT COUNT(*) FROM  " . $GLOBALS['ecs']->table('combo');
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        /* 分页大小 */
        $filter = page_and_size($filter);

        $sql = "SELECT * FROM  " . $GLOBALS['ecs']->table('combo'). " LIMIT " . $filter['start'] . ",$filter[page_size]";
        $row = $this->db->getAll($sql);
        return array('combo_list' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
    }
     
    public function get_Combo($id) {
        $sql = "SELECT * FROM " . $this->ecs->table("combo") . " WHERE combo_id=$id";
        return $this->db->getRow($sql);
    }
    //hechengbin--获取全部套餐
    public function get_combo_all(){
        $sql = "SELECT * FROM ".$GLOBALS['ecs']->table('combo');
        return $this->db->getAll($sql);
    }

    public function remove($id) {
        $sql = "DELETE FROM" . $this->ecs->table("combo") . " WHERE combo_id=$id";
        return $this->db->query($sql);
    }

    public function remove_batch($data) {
        foreach ($data as $id) {
            $sql = "DELETE  FROM " . $this->ecs->table("combo") . " WHERE combo_id=$id";
            $res = $this->db->query($sql);
        }
        return $res;
    }
    
    public function get_combo_list() {
        $sql = "SELECT combo_id,combo_name FROM " . $this->ecs->table("combo") . " WHERE is_show = 1";
        return $this->db->getAll($sql);
    }
    
    public function get_combo_show($page,$num){
        return $this->db->getAll( "SELECT * FROM  " . $GLOBALS['ecs']->table('combo') . " WHERE is_show = 1 order by sort_order desc limit ".$page.",".$num);
    }
    public function get_combo_info($id){
        return $this->db->getRow( "SELECT * FROM  " . $GLOBALS['ecs']->table('combo') . " WHERE is_show = 1 AND combo_id = $id");
    }
    //查看套餐金额
    public function get_combo_money($id){
        return $this->db->getOne("SELECT combo_price FROM ".$GLOBALS['ecs']->table('combo')." WHERE combo_id = $id");
    }
    //获取套餐名称
    public function get_combo_name($combo_id){
        $sql = "SELECT * FROM ".$GLOBALS['ecs']->table('combo')." WHERE combo_id=$combo_id";
        return $this->db->getRow($sql);
    }
    
    public function get_combo_index($id){
        $combo_info = $this->db->getRow("SELECT * FROM  " . $GLOBALS['ecs']->table('combo') . " WHERE combo_id = $id");
        //场馆信息
        $combo_pitch_ids = array();
        if(strstr($combo_info['combo_pitchs'],"|")){
            $combo_pitch_array = explode('|',$combo_info['combo_pitchs']);

            for($i=0; $i<count($combo_pitch_array); $i++){
                if(!empty($combo_pitch_array[$i])){
                    $combo_pitch_ids[] = $this->db->getRow("SELECT * FROM  " . $GLOBALS['ecs']->table('combo_pitch') . " WHERE combo_pitch_id = '$combo_pitch_array[$i]'");
                }
            }

        }else{
            if(!empty($combo_info['combo_pitchs'])){
                $combo_pitch_ids[] = $this->db->getRow("SELECT * FROM  " . $GLOBALS['ecs']->table('combo_pitch') . " WHERE combo_pitch_id = '".$combo_info['combo_pitchs'] ."'");
            }
        }
        $combo_info['combo_pitchs'] = $combo_pitch_ids;
        //行程信息
        $combo_travel_ids = array();
        if(strstr($combo_info['combo_travels'],"|")){
            $combo_travel_array = explode('|',$combo_info['combo_travels']);
            for($j=0; $j<count($combo_travel_array); $j++){
                $combo_travel = $this->db->getRow("SELECT combo_travel_day,combo_travel_date,combo_travel_title,combo_travel_content,combo_travel_img FROM  " . $GLOBALS['ecs']->table('combo_travel') . " WHERE combo_travel_id = $combo_travel_array[$j]");
//                @$combo_travel['combo_travel_date'] = date("m月d日", $combo_travel['combo_travel_date']);
                $combo_travel_ids[] = $combo_travel;
                foreach($combo_travel_ids as $k=>$v){
                    if(!$v){
                        unset($combo_travel_ids[$k]);
                    }
                }
                array_multisort($combo_travel_ids);
            }
        }else{
            //$combo_travel = $this->db->getRow("SELECT * FROM  " . $GLOBALS['ecs']->table('combo_travel') . " WHERE combo_travel_id = ".$combo_info['combo_travels']);
            $combo_travel = $this->db->getAll("SELECT * FROM  " . $GLOBALS['ecs']->table('combo_travel') . " WHERE combo_travel_type_id = ".$combo_info['combo_travel_type_id'] . " ORDER BY combo_travel_day ASC");
//            @$combo_travel['combo_travel_date'] = date("m月d日", $combo_travel['combo_travel_date']);
            $combo_travel_ids = $combo_travel;
        }
        $combo_info['combo_travels'] = $combo_travel_ids;
            
        return $combo_info;
    }
}