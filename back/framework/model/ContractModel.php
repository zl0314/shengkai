<?php

/*
 * 作者：戎青松
 * 时间：9:58:15
 * 
 */

/**
 * Description of HotelModel
 *
 * @author Kevin
 */
__load("Model", "model");
class ContractModel extends Model {
    //put your code here
    public function add($data){
        return $this->db->autoExecute($this->ecs->table("contract"), $data, 'INSERT');
    }
    public function update($data){
        $id=$data['contract_id'];
        unset($data['contract_id']);
        return $this->db->autoExecute($this->ecs->table("contract"), $data, 'UPDATE'," contract_id=$id");
    }
    public function get_AllContract() {
        /* 记录总数 */
        $sql = "SELECT COUNT(*) FROM " .$GLOBALS['ecs']->table('contract');
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);
        /* 分页大小 */
        $filter = page_and_size($filter);
        
        $sql = $sql = "
         SELECT
        c.`contract_id`,
        c.`contract_name`,
        u.user_id,
        c.contract_no,
        c.order_sn,
        c.addtime,
        c.`contract_file`,
        u.`user_name`,
        u.`mobile_phone`,
        g.`goods_name`,
        co.`combo_name`,
        h.`hotel_name`,
        a.`flight`
    FROM
        " . $this->ecs->table("contract")." AS c
    LEFT JOIN " . $this->ecs->table("hotel")." AS h ON c.hotel_id = h.`id`
    LEFT JOIN " . $this->ecs->table("airticket")." AS a ON c.`airticket_id` = a.`id`
    LEFT JOIN " . $this->ecs->table("users")." AS u ON c.user_id = u.`user_id`
    LEFT JOIN " . $this->ecs->table("goods")." AS g ON c.`goods_id` = g.`goods_id`
    LEFT JOIN " . $this->ecs->table("combo")." AS co ON c.`combo_id` = co.`combo_id`
    WHERE 1 LIMIT " . $filter['start'] . ",$filter[page_size]";
        $row = $this->db->getAll($sql);
        return array('contract' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
    }

    public function get_Contract($id) {
        $sql = "
         SELECT
        c.`contract_id`,
        u.user_id,
        c.contract_no,
        c.order_sn,
        c.`contract_name`,
        c.`contract_file`,
        u.`user_name`,
        u.`mobile_phone`,
        g.`goods_name`,
        c.goods_id,
        c.combo_id,
        c.hotel_id,
        c.airticket_id,
        co.`combo_name`,
        h.`hotel_name`,
        a.`flight`
    FROM
        " . $this->ecs->table("contract")." AS c
    LEFT JOIN " . $this->ecs->table("hotel")." AS h ON c.hotel_id = h.`id`
    LEFT JOIN " . $this->ecs->table("airticket")." AS a ON c.`airticket_id` = a.`id`
    LEFT JOIN " . $this->ecs->table("users")." AS u ON c.user_id = u.`user_id`
    LEFT JOIN " . $this->ecs->table("goods")." AS g ON c.`goods_id` = g.`goods_id`
    LEFT JOIN " . $this->ecs->table("combo")." AS co ON c.`combo_id` = co.`combo_id`
    WHERE
        c.`contract_id` = $id";

        return $this->db->getRow($sql);
    }
    public function remove($id){
        $sql = "DELETE FROM" . $this->ecs->table("contract")." WHERE contract_id=$id";
        return $this->db->query($sql);
    }

    public function remove_batch($data) {
        foreach ($data as $key => $id) {
        $sql = "DELETE FROM" . $this->ecs->table("contract")." WHERE contract_id=$id";
        $res=$this->db->query($sql);
        }
        return $res;
    }
    public function get_contract_list($region_id){
        $sql = "SELECT * FROM " . $this->ecs->table("contract")." WHERE region_id=$region_id";
        return $this->db->getAll($sql);
    }

    public function createOrder($data){
        $sql = "INSERT INTO `sk_order_info` (
                    `order_id`,
                    `order_sn`,
                    `user_id`,
                    `order_status`,
                    `pay_status`,
                     `source`,
                     `add_time`
                )
                VALUES
                    (
                        null,
                        '".$data['contract_no']."',
                        ".$data['user_id'].",
                        1,
                        2,
                        2,
                        '".time()."'
                )";
        $this->db->query($sql);
    }
}
