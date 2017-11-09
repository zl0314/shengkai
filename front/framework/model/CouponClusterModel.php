<?php

/**
 * Created by PhpStorm.
 * User: stepreal
 * Date: 17/9/26
 * Time: 下午5:48
 */
__load("Model", "model");

class CouponClusterModel extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_coupon_cluster_list($coupon_cluster_id)
    {
        /* 查询条件 */
        $filter = array();
        $filter['coupon_cluster_id'] = $coupon_cluster_id;
        $where = (!empty($filter['cluster_id'])) ? " AND cluster_id = '{$filter['cluster_id']}' " : '';
        /* 记录总数 */
        $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('coupon_cluster') . " WHERE 1 " . $where;
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        /* 分页大小 */
        $filter = page_and_size($filter);

        $sql = "SELECT * FROM " . $this->ecs->table("coupon_cluster") . "  WHERE 1 " . $where . " AND deleted = 0 ORDER BY coupon_cluster_id ASC LIMIT {$filter['start']}, {$filter['page_size']}";
        $row = $this->db->getAll($sql);
        return array('list' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
    }

    /**
     * @param $coupon_cluster_id
     * @return array
     */
    public function get_all_coupon_cluster_list($coupon_cluster_id)
    {
        /* 查询条件 */
        $filter = array();
        $filter['coupon_cluster_id'] = intval($coupon_cluster_id);
        $where = (!empty($filter['cluster_id'])) ? " AND cluster_id = '{$filter['cluster_id']}' " : '';
        $sql = "SELECT * FROM " . $this->ecs->table("coupon_cluster") . "  WHERE 1 " . $where . " AND deleted = 0 ORDER BY coupon_cluster_id ASC";
        $result = $this->db->getAll($sql);
        return $result;
    }

    /**
     * 新增
     *
     * @param $data
     * @return bool
     */
    public function add($data)
    {
        if (!empty($data['coupon_cluster_id'])) {
            $sql = "SELECT COUNT(*) AS num FROM " . $this->ecs->table("coupon_cluster") . " WHERE coupon_cluster_id = '{$data['coupon_cluster_id']}'";
            $num = $this->db->getOne($sql);
            if ($num > 0) {
                return true;
            }
        }
        $ret = $this->db->autoExecute($this->ecs->table("coupon_cluster"), $data, 'INSERT');
        return $ret;
    }

    public function update($data)
    {
        $coupon_cluster_id = $data['coupon_cluster_id'];
        unset($data['coupon_cluster_id']);
        return $this->db->autoExecute($this->ecs->table("coupon_cluster"), $data, 'UPDATE', "coupon_cluster_id=$coupon_cluster_id");
    }

    public function remove($coupon_cluster_id)
    {
        $sql = "UPDATE " . $this->ecs->table("coupon_cluster") . " SET deleted = 1, coupon_key = concat(coupon_key, '-" . date('ymdHis') . "') WHERE coupon_cluster_id='{$coupon_cluster_id}'";
        return $this->db->query($sql);
    }

    public function get($coupon_cluster_id)
    {
        $where = "AND coupon_cluster_id = '{$coupon_cluster_id}'";
        $sql = "SELECT * FROM " . $this->ecs->table("coupon_cluster") . "  WHERE 1 " . $where;
        return $this->db->getRow($sql);
    }

    /**
     * @param $coupon_key
     * @return mixed
     */
    public function get_by_coupon_key($coupon_key)
    {
        $where = "AND coupon_key = '{$coupon_key}'";
        $sql = "SELECT * FROM " . $this->ecs->table("coupon_cluster") . "  WHERE 1 " . $where;
        return $this->db->getRow($sql);
    }

    public function remove_batch($data)
    {
        $res = false;
        foreach ($data as $coupon_cluster_id) {
            $sql = "DELETE  FROM " . $this->ecs->table("coupon_cluster") . " WHERE coupon_cluster_id= '{$coupon_cluster_id}'";
            $res = $this->db->query($sql);
        }
        return $res;
    }
}