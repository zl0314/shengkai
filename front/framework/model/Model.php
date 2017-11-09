<?php

/*
 * 作者：戎青松
 * 时间：11:18:36
 * 
 */

/**
 * Description of Model
 *
 * @author Kevin
 */
class Model {
    //put your code here
    protected $db,$ecs;
    public function __construct() {
        $this->db=$GLOBALS['db'];
        $this->ecs=$GLOBALS['ecs'];
    }

    public function insert_id(){
       return  $this->db->insert_id();
    }
    
}
