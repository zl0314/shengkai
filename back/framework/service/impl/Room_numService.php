<?php

/**
 * Created by PhpStorm.
 * User: stepreal
 * Date: 17/8/12
 * Time: 上午11:33
 */
class Room_numService
{
    //put your code here
    private $room, $hotel, $type;

    public function __construct()
    {
        __load("RoomModel", "model");
        __load("HotelModel", "model");
        __load("RoomtypeModel", "model");
        $this->room = new RoomModel();
        $this->hotel = new HotelModel();
        $this->type = new RoomtypeModel();

        __load("Room_numModel", "model");
        $this->room_num_model = new Room_numModel();
    }

    public function get_room_num_list($room_id)
    {
        return $this->room_num_model->get_room_num_list($room_id);
    }

    public function get_room_num($room_id)
    {
        return $this->room_num_model->get($room_id);
    }

    public function get_hotel_name($hotel_id)
    {
        $hotel = $this->hotel->get_Hotel($hotel_id);
        return $hotel['hotel_name'];
    }

    public function get_type_list()
    {
        return $this->type->getAll();
    }

    public function save($data)
    {
        $arr = array($data['date']);
        if (!empty($data['end_date'])) {
            $start_date_time = strtotime($data['date']);
            $end_date_time = strtotime($data['end_date']);
            if ($end_date_time < $start_date_time) {
                return false;
            }
            $arr = array();
            for ($i = $start_date_time; $i <= $end_date_time; $i = $i + 86400) {
                $arr[] = date('Y-m-d', $i);
            }
        }
        $ret = false;
        foreach ($arr as $value) {
            $data['date'] = $value;
            $ret = $this->room_num_model->save($data);
        }
        return $ret;
    }

    public function update($data)
    {
        return $this->room_num_model->update($data);
    }

    public function remove($id)
    {
        return $this->room_num_model->remove($id);
    }

    public function remove_b($data)
    {
        return $this->room_num_model->remove_batch($data);
    }

    /**
     * 取得某天的库存数量
     *
     * @param $room_id
     * @param $date
     * @return array
     */
    public function get_room_num_by_date($room_id, $date)
    {
        $result = $this->room_num_model->get_room_num_by_date($room_id, $date);
        if (empty($result)) {
            return 0;
        }
        //锁定的数量是否大于库存数量
        $diff = $result['num'] - $result['locked_num'];
        return $diff > 0 ? $diff : 0;
    }

    /**
     * 取得某天的锁定数量
     *
     * @param $room_id
     * @param $date
     * @return int
     */
    public function get_room_num_locked($room_id, $date)
    {
        $result = $this->room_num_model->get_room_num_by_date($room_id, $date);
        if (empty($result)) {
            return 0;
        }
        //锁定的数量是否大于库存数量
        return $result['locked_num'] > 0 ? $result['locked_num'] : 0;
    }

    /**
     * 取消某些锁定
     *
     * @param $room_id
     * @param $num
     * @param $begin_date
     * @param $end_date
     * @return mixed
     */
    public function unlock_room_num_from_in_date($room_id, $num, $begin_date, $end_date)
    {
        $ret = array();
        $begin_date_time = strtotime($begin_date);
        $end_date_time = strtotime($end_date);
        for ($i = $begin_date_time; $i <= $end_date_time; $i = $i + 86400) {
            $date = date('Y-m-d', $i);
            $room_date_info = $this->room_num_model->get_room_num_by_date($room_id, $date);
            $diff = $room_date_info['locked'] - $num;
            $num = $diff > 0 ? $diff : 0;
            $ret[] = $this->room_num_model->update(array(
                'id' => $room_date_info['id'],
                'locked_num' => $num,
            ));
        }
        return count($ret);
    }

    /**
     * 设置订单库存已使用
     *
     * @param $room_id
     * @param $num
     * @param $begin_date
     * @param $end_date
     * @return int
     */
    public function update_order_room_num_used_from_in_date($room_id, $num, $begin_date, $end_date)
    {
        $ret = array();
        $begin_date_time = strtotime($begin_date);
        $end_date_time = strtotime($end_date);
        for ($i = $begin_date_time; $i <= $end_date_time; $i = $i + 86400) {
            $date = date('Y-m-d', $i);
            $room_date_info = $this->room_num_model->get_room_num_by_date($room_id, $date);
            $stock = $room_date_info['num'] - $num;

            $diff = $room_date_info['locked'] - $num;
            $locked_num = $diff > 0 ? $diff : 0;

            $sale_num = $room_date_info['sale_num'] + $num;
            $ret[] = $this->room_num_model->update(array(
                'id' => $room_date_info['id'],
                'locked_num' => $locked_num,
                'sale_num' => $sale_num,
                'num' => $stock,
            ));
        }
        return count($ret);
    }
}
