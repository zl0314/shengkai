<?php

/*
 * 作者：鞠嵩
 * 时间：10:23:52
 * 
 */

/**
 * Description of TeamService
 *
 * @author jusong
 */
__load("TeamModel", "model");

class TeamService {

    //put your code here
    private $team;

    public function __construct() {

        $this->team = new TeamModel();
    }

    public function get_list() {
        return $this->team->get_all();
    }
    
    public function get_team_list() {
        return $this->team->get_alls();
    }

    public function get_team($id) {
        return $this->team->get($id);
    }

    public function add_team() {
        $this->team->set($_POST);
        return $this->team->save();
    }

    public function update_team() {
        $this->team->set($_POST);
        return $this->team->update();
    }

    public function remove_team($id) {
        $this->team->set($_POST);
        return $this->team->remove($id);
    }
    
    public function remove_b($data) {
        return $this->team->remove_batch($data);
    }
}
