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
class RoomService {

    //put your code here
    private $room, $hotel, $type;

    public function __construct() {
        __load("RoomModel", "model");
        __load("HotelModel", "model");
        __load("RoomtypeModel", "model");
        $this->room = new RoomModel();
        $this->hotel = new HotelModel();
        $this->type = new RoomtypeModel();
    }

    public function get_room_list() {
        return $this->room->get_room();
    }

    public function get_hotel_name($hotel_id) {
        $hotel = $this->hotel->get_Hotel($hotel_id);
        return $hotel['hotel_name'];
    }

    public function get_type_list() {
        return $this->type->getAll();
    }
    public function get_room($id){
        return $this->room->get($id);
    }

    public function save($data) {
        return $this->room->save($data);
    }
    public function update($data) {
        return $this->room->update($data);
    }

    public function remove($id) {
        return $this->room->remove($id);
    }

}
