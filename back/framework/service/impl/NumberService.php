<?php

/*
 * 作者：鞠嵩
 * 时间：10:37:56
 * 
 */

/**
 * Description of NumberService
 *
 * @author jusong
 */
class NumberService {

    //put your code here
    private $number, $schedule, $numteam, $team;

    public function __construct() {
        __load("NumberModel", "model");
        __load("ScheduleModel", "model");
        __load("NumteamModel", "model");
        __load("TeamModel", "model");
        $this->number = new NumberModel();
        $this->schedule = new ScheduleModel();
        $this->numteam = new NumteamModel();
        $this->team = new TeamModel();
    }

//    
//    public function get_AllRegion($type){
//        return $this->region->get_all($type);        
//    }

    public function update($data) {
        $teams = json_decode(str_replace("\\\"", "\"", $data['teams']));
        if(empty($data['num_name'])){
            __load('TeamService');
            $obj_team=new TeamService();
            $teams_name=array();
            foreach($teams as $team){
                $team_info=$obj_team->get_team($team);
                array_push($teams_name,$team_info['team_name']);
            }
            $data['num_name']=implode($teams_name,'vs');
        }

        if (!array_key_exists('is_hot',$data)) {
            # code...
            $data['is_hot']=0;
        }
        $res = $this->number->update($data);


//        print_r($teams);
//        exit;
        $number_id = $data['id'];
        $this->numteam->update($number_id, $teams);
        return $res;
    }

    /**
     * 添加场次
     * @param type $data 场次数据
     */
    public function add_Number($data) {

        $teams = json_decode(str_replace("\\\"", "\"", $data['teams']));
        if(empty($data['num_name'])){
            __load('TeamService');
            $obj_team=new TeamService();
            $teams_name=array();
            foreach($teams as $team){
                $team_info=$obj_team->get_team($team);
                array_push($teams_name,$team_info['team_name']);
            }
            $data['num_name']=implode($teams_name,'vs');
        }
        $number_id = $this->number->save($data); //添加场次信
        if (empty($number_id)) {
            //如果添加失败
            return null;
        } else {

//            print_r(str_replace("\\\"","\"",$data['teams']));

            $this->numteam->save($number_id, $teams);
            return true;
        }


//        $this->number->set($data);
//        return $this->number->save($data);       
    }

//    public function update($data){
//       
//        return $this->hotel->update($data);        
//    }
    public function get_AllNumber() {
        return $this->number->get_All_Number();
    }

    public function getAllNumber(){
        return $this->number->getAllNumber();
    }
    public function get_List($id) {
        return $this->number->get_List($id);
    }
    
    public function get_number_list(){
        return $this->number->number_list();
    }

    public function get_ScheName() {
        return $this->schedule->get_Sche_Name();
    }

//    public function get_Hotel($id){
//        return $this->hotel->get_Hotel($id);        
//    }
    public function remove($id) {
        return $this->number->remove($id);
    }

    public function get_Number($id) {
        return $this->number->get_Number($id);
    }

    public function get_team($id) {
        $teams = $this->numteam->get_team($id);
        if (empty($teams)) {
            return null;
        }
        return $this->team->get_team_info($teams);
    }
     public function remove_b($data) {
        return $this->number->remove_batch($data);
    }
    
    public function get_number_attr($id) {
        return $this->number->get_number_attr($id);
    }
    
    public function get_hot_number($number) {
        return $this->number->get_hot($number);
    }
 
    public function get_schel_num($id){
        return $this->number->schel_num($id);
    }
    /**获取场次的颜色**/
    public function get_number_color($id){
        return $this->number->number_color($id);
    }
    public function get_Number_info($number_id){
        return $this->number->get($number_id);
    }
}
