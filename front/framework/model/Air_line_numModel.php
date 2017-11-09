<?php

/**
 * Created by PhpStorm.
 * User: stepreal
 * Date: 17/8/20
 * Time: 下午9:00
 */

__load("Model", "model");

class Air_line_numModel extends Model
{
    /**
     * 新增
     *
     * @param $data
     * @return bool
     */
    public function save($data)
    {
        $data['create_time'] = time();
        $sql = "SELECT COUNT(*) AS num FROM " . $this->ecs->table("air_line_num") . " WHERE air_line_id = '{$data['air_line_id']}' and type = '{$data['type']}' and date = '{$data['date']}'";
        $num = $this->db->getOne($sql);
        if ($num > 0) {
            return true;
        }
        $ret = $this->db->autoExecute($this->ecs->table("air_line_num"), $data, 'INSERT');
        return $ret;
    }

    /**
     * 获取库存信息
     *
     * @param $air_line_num_id
     * @return array
     */
    public function getById($air_line_num_id)
    {
        $sql = "SELECT * FROM " . $this->ecs->table("air_line_num") . " WHERE id='{$air_line_num_id}'";
        return $this->db->getRow($sql);
    }

    /**
     * @param $air_line_id
     * @param $date
     * @return mixed
     */
    public function get_air_line_num_by_date($air_line_id, $date)
    {
        $sql = "SELECT * FROM " . $this->ecs->table("air_line_num") . " WHERE air_line_id='{$air_line_id}' AND date = '{$date}'";
        return $this->db->getRow($sql);
    }

    /**
     * 带分页的查询
     *
     * @param $air_line_id
     * @param $type
     * @return array
     */
    public function get_air_line_num_list($air_line_id, $type)
    {
        /* 查询条件 */
        $filter = array();
        $filter['air_line_id'] = $air_line_id;
        if (in_array($type, array(1, 2))) {
            $filter['type'] = $type;
        }
        $where = (!empty($filter['air_line_id'])) ? " AND air_line_id = '{$filter['air_line_id']}' " : '';
        $where .= !empty($filter['type']) ? " AND type = '{$type}'" : '';
        /* 记录总数 */
        $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('air_line_num') . " WHERE 1 " . $where;
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        /* 分页大小 */
        $filter = page_and_size($filter);

        $sql = "SELECT sn.* FROM " . $this->ecs->table("air_line_num") . " as sn WHERE 1 " . $where . " ORDER BY date ASC LIMIT {$filter['start']}, {$filter['page_size']}";
        $row = $this->db->getAll($sql);
        return array('list' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
    }

    /**
     * 获取指定航程的库存数量(包含去程和返程两种类型的库存)
     *
     * @param $air_line_id
     * @param string $date
     * @return mixed
     */
    public function get_air_line_num_by_air_line_id($air_line_id, $date = '')
    {
        /* 查询条件 */
        $filter = array();
        $filter['air_line_id'] = intval($air_line_id);
        $where = (!empty($filter['air_line_id'])) ? " AND air_line_id = '{$filter['air_line_id']}' " : '';
        $where .= " AND date >= '" . date('Y-m-d') . "'";

        $sql = "SELECT sn.id, sn.type, sn.date, sn.num - sn.locked_num as num FROM " . $this->ecs->table("air_line_num") . " as sn WHERE 1 " . $where . " AND sn.num - sn.locked_num > 0 ORDER BY date ASC";
        $result = $this->db->getAll($sql);
        return $result;
    }

    /**
     * 获取指定航程,日期,类型(去程/返程)航程的库存数量
     *
     * @param $air_line_id
     * @param string $date
     * @param int $type
     * @return mixed
     */
    public function get_air_line_num($air_line_id, $date = '', $type = 0)
    {
        //参数校验
        if (!is_numeric($air_line_id) || empty($date) || !in_array($type, array(1, 2))) {
            return 0;
        }

        $where = " AND air_line_id = '{$air_line_id}' AND type = '{$type}' AND date >= '" . date('Y-m-d') . "'";
        $sql = "SELECT sn.* FROM " . $this->ecs->table("air_line_num") . " as sn WHERE 1 " . $where . " AND sn.num - sn.locked_num > 0 ORDER BY date ASC";
        $num = $this->db->getOne($sql);
        return $num;
    }

    /**
     * 更新
     *
     * @param $data
     * @return mixed
     */
    public function update($data)
    {
        $id = $data['id'];
        unset($data['id']);
        return $this->db->autoExecute($this->ecs->table("air_line_num"), $data, 'UPDATE', " id='{$id}'");
    }

    /**
     * 删除
     *
     * @param $id
     * @return mixed
     */
    public function remove($id)
    {
        $sql = "DELETE FROM" . $this->ecs->table("air_line_num") . " WHERE id='{$id}'";
        return $this->db->query($sql);
    }

    /**
     * 删除
     *
     * @param $data
     * @return bool
     */
    public function remove_batch($data)
    {
        $res = false;
        foreach ($data as $key => $id) {
            $sql = "DELETE FROM" . $this->ecs->table("air_line_num") . " WHERE id='{$id}'";
            $res = $this->db->query($sql);
        }
        return $res;
    }

}