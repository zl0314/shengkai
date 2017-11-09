<?php

/**
 * Created by PhpStorm.
 * User: stepreal
 * Date: 17/8/12
 * Time: 上午11:34
 */
__load("Model", "model");

class Room_numModel extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_room_num_list($room_id)
    {
        /* 查询条件 */
        $filter = array();
        $filter['room_id'] = $room_id;
        $where = (!empty($filter['room_id'])) ? " AND room_id = '{$filter['room_id']}' " : '';
        /* 记录总数 */
        $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('room_num') . " WHERE 1 " . $where;
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        /* 分页大小 */
        $filter = page_and_size($filter);

        $sql = "SELECT rn.*,r.room_money, r.hotel_id,t.type_name FROM " . $this->ecs->table("room_num") . " as rn ," . $this->ecs->table("room") . "  as r ," . $this->ecs->table("room_type") . " as t WHERE rn.room_id = r.id AND r.room_type=t.id " . $where . " ORDER BY date ASC LIMIT {$filter['start']}, {$filter['page_size']}";
        $row = $this->db->getAll($sql);
        return array('list' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
    }

    /**
     * @param $room_id
     * @return array
     */
    public function get_all_room_num_list($room_id)
    {
        /* 查询条件 */
        $filter = array();
        $filter['room_id'] = intval($room_id);
        $filter['date'] = date('Y-m-d', strtotime("-1 day"));
        $where = " AND room_id = '{$filter['room_id']}' AND date > '{$filter['date']}'";
        $sql = "SELECT rn.id, rn.room_id, rn.date, rn.date, rn.num - rn.locked_num as num FROM " . $this->ecs->table("room_num") . " as rn WHERE 1 " . $where . " ORDER BY date ASC";
        $result = $this->db->getAll($sql);
        return $result;
    }

    public function get_room_num()
    {
        /* 查询条件 */
        $filter = array();
        $filter['hotel_id'] = empty($_REQUEST['hotel_id']) ? 0 : intval($_REQUEST['hotel_id']);
        $where = (!empty($filter['hotel_id'])) ? " AND hotel_id = '$filter[hotel_id]' " : '';
        /* 记录总数 */
        $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('room_num') . " WHERE 1 " . $where;
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        /* 分页大小 */
        $filter = page_and_size($filter);

        $sql = "SELECT r.*,h.hotel_name,t.type_name FROM " . $this->ecs->table("room_num") . " as r ," . $this->ecs->table("hotel") . "  as h ," . $this->ecs->table("room_type") . " as t WHERE r.hotel_id=h.id AND r.room_type=t.id " . $where . "LIMIT " . $filter['start'] . ",$filter[page_size]";
        $row = $this->db->getAll($sql);
        return array('rooms' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
    }

    /**
     * 新增
     *
     * @param $data
     * @return bool
     */
    public function save($data)
    {
        $data['create_time'] = time();
        $sql = "SELECT COUNT(*) AS num FROM " . $this->ecs->table("room_num") . " WHERE room_id = '{$data['room_id']}' and date = '{$data['date']}'";
        $num = $this->db->getOne($sql);
        if ($num > 0) {
            return true;
        }
        $ret = $this->db->autoExecute($this->ecs->table("room_num"), $data, 'INSERT');
        return $ret;
    }

    public function update($data)
    {
        $id = $data['id'];
        unset($data['id']);
        return $this->db->autoExecute($this->ecs->table("room_num"), $data, 'UPDATE', "id='{$id}'");
    }

    public function remove($id)
    {
        $sql = "DELETE FROM " . $this->ecs->table("room_num") . " WHERE id=$id";
        return $this->db->query($sql);
    }

    public function get($room_id)
    {
        $where = "AND rn.id = '{$room_id}'";
        $sql = "SELECT rn.*,r.room_money, r.hotel_id,t.type_name FROM " . $this->ecs->table("room_num") . " as rn ," . $this->ecs->table("room") . "  as r ," . $this->ecs->table("room_type") . " as t WHERE rn.room_id = r.id AND r.room_type=t.id " . $where;
        return $this->db->getRow($sql);
    }

    public function remove_batch($data)
    {
        $res = false;
        foreach ($data as $id) {
            $sql = "DELETE  FROM " . $this->ecs->table("room_num") . " WHERE id= '{$id}'";
            $res = $this->db->query($sql);
        }
        return $res;
    }

    /**
     * 取得酒店某天的库存数量
     *
     * @param $room_id
     * @param $date
     * @return mixed
     */
    public function get_room_num_by_date($room_id, $date)
    {
        $where = "rn.room_id = '{$room_id}' and date = '{$date}'";
        $sql = "SELECT rn.* FROM " . $this->ecs->table("room_num") . " as rn WHERE " . $where;
        return $this->db->getRow($sql);
    }
}
