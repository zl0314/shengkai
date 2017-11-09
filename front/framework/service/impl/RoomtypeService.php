<?php

/*
 * 作者：戎青松
 * 时间：14:45:52
 * 
 */

/**
 * Description of GameService
 *
 * @author Kevin
 */
class RoomtypeService {

    //put your code here
    private $type;

    public function __construct() {
        __load("RoomtypeModel", "model");
        $this->type = new RoomtypeModel();
    }
    
    public function save($data){
         return $this->type->save($data);
    }
    public function update($data){
         return $this->type->update($data);
    }
    public function get_list(){
         return $this->type->getAll();
    }
    
    public function get_room_type_list(){
         return $this->type->get_All();
    }
    
    public function get_type($id){
        return $this->type->get($id);
    }
    public function remove($id){
        return $this->type->remove($id);
    }

 
}
