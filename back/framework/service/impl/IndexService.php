<?php

/*
 * 作者：戎青松
 * 时间：10:37:56
 * 
 */

/**
 * Description of HotelService
 *
 * @author Kevin
 */
class IndexService {

    //put your code here
    private $agent;

    public function __construct() {
        __load("IndexModel", "model");
        $this->agent = new IndexModel();
    }
    
    public function is_freeze(){
        return $this->agent->freeze();
    }

    public function get_agent_user_pwd($id){
        return $this->agent->agent_user_pwd($id);
    }
    
    public function update_pwd($data,$id){
        return $this->agent->update_agent_pwd($data,$id);
    }
    
    public function pitch_info(){
        return $this->agent->get_pitch_list();
    }
    
    public function save_bearer_infos($bearer_info,$way,$where){
       return $this->agent->save_bearer($bearer_info,$way,$where);
    }
    
    public function get_examine_bearer_info(){
         return $this->agent->get_bearer_info();
    }
    
    public function bearer_list(){
       return  $this->agent->get_bearer_list();
    }
    
    public function bearer_infos($bearer_id){
        return $this->agent->get_bearer_infos($bearer_id);
    }
    
    public function get_ticket_bearer($order_info_id){
        return $this->agent->ticket_bearer($order_info_id);
    }
    
    public function update_submit($data){
        return $this->agent->submit_state($data);
    }
    
    public function single_submit_state($bearer_info,$bearer_id){
        return $this->agent->submit_state_update($bearer_info,$bearer_id);
    }
    
    public function get_game_list(){
        return $this->agent->get_game();
    }
    
    public function get_search($data){
        return $this->agent->get_search_list($data);
    }
}
