<?php

/**
 * Created by PhpStorm.
 * User: stepreal
 * Date: 17/9/26
 * Time: 下午5:49
 */
__load("Model", "model");

class UserCouponModel extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_user_coupon_list($data)
    {
        /* 查询条件 */
        $filter = array();
        if (isset($data['coupon_cluster_id'])) {
            $filter['coupon_cluster_id'] = $data['coupon_cluster_id'];
        }
        if (isset($data['user_id'])) {
            $filter['user_id'] = $data['user_id'];
        }
        if (isset($data['status'])) {
            $filter['status'] = intval($data['status']);
        }

        if (isset($data['order_amount'])) {
            $filter['order_amount'] = intval($data['order_amount']);
        }
        $where = (!empty($filter['coupon_cluster_id'])) ? " AND c.coupon_cluster_id = '{$filter['coupon_cluster_id']}' " : '';
        $where .= (!empty($filter['user_id'])) ? " AND c.user_id = '{$filter['user_id']}' " : '';
        $where .= (isset($filter['status'])) ? " AND c.status = '{$filter['status']}'" : '';
        $where .= (isset($filter['order_amount'])) ? " AND c.min_amount <= '{$filter['order_amount']}'" : '';
        /* 记录总数 */
        $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('user_coupon') . " as c WHERE 1 " . $where;

        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        /* 分页大小 */
        $filter = page_and_size($filter);

        $sql = "SELECT uc.*, u.user_name, c.title, c.min_amount, c.amount FROM " . $this->ecs->table("user_coupon") . " uc LEFT JOIN " . $this->ecs->table("coupon_cluster") . " c ON uc.coupon_cluster_id = c.coupon_cluster_id LEFT JOIN " . $this->ecs->table("users") . " u ON uc.user_id = u.user_id WHERE 1 " . $where . " ORDER BY user_coupon_id ASC LIMIT {$filter['start']}, {$filter['page_size']}";
        $row = $this->db->getAll($sql);
        return array('list' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
    }

    /**
     * @param array $data
     * @return array
     */
    public function get_all_user_coupon_list($data)
    {
        /* 查询条件 */
        $filter = array();
        $filter['user_id'] = intval($data['user_id']);
        if (isset($data['coupon_cluster_id'])) {
            $filter['coupon_cluster_id'] = $data['coupon_cluster_id'];
        }
        if (isset($data['status'])) {
            $filter['status'] = intval($data['status']);
        }
        if (isset($data['order_amount'])) {
            $filter['order_amount'] = intval($data['order_amount']);
        }
        $where = (!empty($filter['user_id'])) ? " AND user_id = '{$filter['user_id']}' " : '';
        $where .= (isset($filter['status'])) ? " AND uc.status = '{$filter['status']}'" : '';
        $where .= (isset($filter['order_amount'])) ? " AND c.min_amount <= '{$filter['order_amount']}'" : '';
        $sql = "SELECT uc.*, c.title, c.min_amount, c.amount FROM " . $this->ecs->table("user_coupon") . " uc LEFT JOIN " . $this->ecs->table("coupon_cluster") . " c ON uc.coupon_cluster_id = c.coupon_cluster_id WHERE 1 " . $where;
        $result = $this->db->getAll($sql);
        return $result;
    }

    /**
     * 新增
     *
     * @param $data
     * @return bool
     */
    public function save($data)
    {
        if (isset($data['user_coupon_id'])) {
            $sql = "SELECT COUNT(*) AS num FROM " . $this->ecs->table("user_coupon") . " WHERE user_coupon_id = '{$data['user_coupon_id']}'";
            $num = $this->db->getOne($sql);
            if ($num > 0) {
                return true;
            }
        }
        $ret = $this->db->autoExecute($this->ecs->table("user_coupon"), $data, 'INSERT');
        return $ret;
    }

    public function update($data)
    {
        $user_coupon_id = $data['user_coupon_id'];
        unset($data['user_coupon_id']);
        return $this->db->autoExecute($this->ecs->table("user_coupon"), $data, 'UPDATE', "user_coupon_id='{$user_coupon_id}'");
    }

    public function remove($user_coupon_id)
    {
        $sql = "DELETE FROM " . $this->ecs->table("user_coupon") . " WHERE user_coupon_id='{$user_coupon_id}'";
        return $this->db->query($sql);
    }

    public function get_by_user_coupon_id($user_coupon_id)
    {
        $sql = "SELECT * FROM " . $this->ecs->table("user_coupon") . "  WHERE user_coupon_id = '{$user_coupon_id}'";
        return $this->db->getRow($sql);
    }

    /**
     * 通过优惠券的id获取用户领取的信息
     *
     * @param $coupon_id
     * @return mixed
     */
    public function get_by_coupon_id($coupon_id)
    {
        $sql = "SELECT * FROM " . $this->ecs->table("user_coupon") . "  WHERE coupon_id = '{$coupon_id}'";
        return $this->db->getRow($sql);
    }

    public function remove_batch($data)
    {
        $res = false;
        foreach ($data as $user_coupon_id) {
            $sql = "DELETE FROM " . $this->ecs->table("user_coupon") . " WHERE user_coupon_id= '{$user_coupon_id}'";
            $res = $this->db->query($sql);
        }
        return $res;
    }

    /**
     * @param $coupon_cluster_id
     * @return mixed
     */
    public function get_user_coupon_num($coupon_cluster_id, $user_id)
    {
        $where = "coupon_cluster_id = '{$coupon_cluster_id}' AND user_id = '{$user_id}'";
        $sql = "SELECT count(*) num FROM " . $this->ecs->table("user_coupon") . "  WHERE " . $where;
        return $this->db->getOne($sql);
    }


    /**
     * 设置优惠券已使用
     *
     * @param $arrInput
     * @return bool
     */
    public function set_coupon_used($arrInput)
    {
        $sql = "UPDATE " . $this->ecs->table("coupon_cluster") . " SET used_count = used_count + 1 WHERE coupon_cluster_id = '{$arrInput['coupon_cluster_id']}'";
        $ret1 = $this->db->query($sql);

        $create_time = date('Y-m-d H:i:s');
        $sql = "UPDATE " . $this->ecs->table("coupon") . " SET order_id = '{$arrInput['order_id']}', use_time = '{$create_time}', status = 2 WHERE coupon_id = '{$arrInput['coupon_id']}'";
        $ret2 = $this->db->query($sql);

        $sql = "UPDATE " . $this->ecs->table("user_coupon") . " SET status = '1' WHERE coupon_id = '{$arrInput['coupon_id']}'";
        $ret3 = $this->db->query($sql);

        return $ret1 && $ret2 && $ret3;
    }

    /**
     * 设置优惠券未使用
     *
     * @param $arrInput
     * @return bool
     */
    public function set_coupon_unused($arrInput)
    {
        $sql = "UPDATE " . $this->ecs->table("coupon_cluster") . " SET used_count = used_count - 1 WHERE coupon_cluster_id = '{$arrInput['coupon_cluster_id']}'";
        $ret1 = $this->db->query($sql);

        //仅仅设置状态为已领取未使用
        //其他的字段用来标识历史记录
        $sql = "UPDATE " . $this->ecs->table("coupon") . " SET status = 1 WHERE coupon_id = '{$arrInput['coupon_id']}'";
        $ret2 = $this->db->query($sql);

        $sql = "UPDATE " . $this->ecs->table("user_coupon") . " SET status = '0' WHERE coupon_id = '{$arrInput['coupon_id']}'";
        $ret3 = $this->db->query($sql);

        return $ret1 && $ret2 && $ret3;
    }
}