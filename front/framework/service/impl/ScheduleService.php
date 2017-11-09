<?php

/**
 * 作者：鞠嵩
 * 时间：13:45:50
 * 
 */
/**
 * Description of ScheduleService
 *
 * @author jusong
 */
__load("ScheduleModel", "model");

class ScheduleService {

    private $schedule;

    public function __construct() {
        $this->schedule = new ScheduleModel();
    }

    public function get_list($game_id) {
        return $this->schedule->sche_list($game_id);
    }
    
    public function get_sche_list(){
         return $this->schedule->get_alls();
    }

    public function remove($id) {
        return $this->schedule->remove($id);
    }

    public function add_sche($data) {
        return $this->schedule->save($data);
    }

    public function get_game_name($id) {
        return $this->schedule->get_gname($id);
    }

    public function update_sche($data) {
        $this->schedule->set($_POST);
        return $this->schedule->update($data);
    }

    public function get_schedule($id) {
        return $this->schedule->get($id);
    }

    public function get_scheduleName($id) {
        return $this->schedule->getgname($id);
    }
    
    public function sche_list($game_id) {
        return $this->schedule->sche_list($game_id);
    }
    public function sche_list_info($game_id) {
        return $this->schedule->sche_list_info($game_id);
    }
    public function get_gameid($id) {
        return $this->schedule->getgid($id);
    }

    public function get_ScheList() {
        return $this->schedule->get_Sche_List();
    }

    public function get_ScheName($id) {
        return $this->schedule->get_Sche_Name($id);
    }
    public function remove_batch($data) {
        return $this->schedule->remove_batch($data);
    }
}

?>
