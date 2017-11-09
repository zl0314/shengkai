<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BatchModel
 *
 * @author qs
 */
__load("Model", "model");

class CartModel extends Model
{

    public function get_cart_combo_info($type)
    {
        $sql = "SELECT c.*,co.* FROM " . $this->ecs->table("cart") . "AS c," . $this->ecs->table('combo') . "AS co WHERE c.goods_type = '{$type}' AND  session_id='" . SESS_ID . "' AND c.combo_id=co.combo_id";
        return $this->db->getAll($sql);
    }

    public function get_cart($type)
    {
        $sql = "SELECT * FROM " . $this->ecs->table("cart") . " WHERE goods_type = '{$type}' AND session_id='" . SESS_ID . "'";
        return $this->db->getAll($sql);
    }

    public function get_one_ticket_number($id)
    {
        $sql = "SELECT goods_number FROM " . $this->ecs->table("cart") . " WHERE goods_type = 'ticket' AND  session_id='" . SESS_ID . "' AND goods_id={$id}";
        return $this->db->getOne($sql);
    }

    public function get_cart_goods($goods_id, $goods_attr_id)
    {
        $sql = "SELECT * FROM " . $this->ecs->table("cart") . " WHERE goods_type = 'goods' AND session_id='" . SESS_ID . "' AND goods_id={$goods_id} AND goods_attr_id={$goods_attr_id} ";
        return $this->db->getRow($sql);
    }

    public function get_cart_ticket($goods_id)
    {

        $sql = "SELECT * FROM " . $this->ecs->table("cart") . " WHERE goods_type = 'ticket' AND session_id='" . SESS_ID . "' AND goods_id={$goods_id} ";

        return $this->db->getRow($sql);
    }

    public function get_cart_combo($combo_id)
    {
        $sql = "SELECT * FROM " . $this->ecs->table("cart") . " WHERE goods_type = 'combo' AND session_id='" . SESS_ID . "' AND combo_id={$combo_id} ";
        return $this->db->getRow($sql);
    }

    public function update_cart($rec_id, $goods_number, $goods_price)
    {
        $sql = "UPDATE " . $this->ecs->table("cart") . " SET goods_number=goods_number+{$goods_number} ,goods_price={$goods_price} WHERE rec_id ={$rec_id} ";
        return $this->db->query($sql);
    }

    public function get_ticket_total($session_id)
    {
        return $this->db->getOne("SELECT SUM(goods_price) FROM" . $this->ecs->table("cart") . "WHERE session_id='$session_id'");
    }

    public function get_money($type)
    {
        if ($type == "all") {
            $sql = "SELECT * FROM " . $this->ecs->table("cart") . " WHERE session_id='" . SESS_ID . "'";
        } else {
            $sql = "SELECT * FROM " . $this->ecs->table("cart") . " WHERE goods_type = '{$type}' AND session_id='" . SESS_ID . "'";
        }
        $result = $this->db->getAll($sql);
        $money = 0;
        foreach ($result as $value) {
            $day = 1;
            if ($value['goods_type'] == 'hotel') {
                $datetime1 = date_create($value['hotel_start_date']);
                $datetime2 = date_create($value['hotel_end_date']);
                $day = round(($datetime2->format('U') - $datetime1->format('U')) / (60*60*24));
                if ($day > 0) {
                    $day = abs($day);
                } else {
                    continue;
                }
            }

            $money += $value['goods_price'] * $value['goods_number'] * $day;
        }
        return $money;
    }

    public function add_cart($data)
    {
        $data['user_id'] = $_SESSION['user_id'];
        $data['session_id'] = SESS_ID;
        $this->db->autoExecute($this->ecs->table("cart"), $data, 'INSERT');
    }

    public function remove_hotel($rec_id)
    {
        $sql = "DELETE FROM " . $this->ecs->table("cart") . " WHERE session_id='" . SESS_ID . "' AND goods_type='hotel' AND  parent_id = {$rec_id}";
        $this->db->query($sql);
    }

    public function remove_cart($type, $id)
    {
        if ($type == "all") {
            $sql = "DELETE FROM " . $this->ecs->table("cart") . " WHERE session_id='" . SESS_ID . "'";
        } else {
            if (empty($id)) {
                $sql = "DELETE FROM " . $this->ecs->table("cart") . " WHERE session_id='" . SESS_ID . "' AND goods_type='{$type}'";
            } else {
                $sql = "DELETE FROM " . $this->ecs->table("cart") . " WHERE session_id='" . SESS_ID . "' AND goods_type='{$type}' AND rec_id={$id}";
            }
        }
        $this->db->query($sql);
    }

    public function get_goods_group()
    {
        $sql = "SELECT goods_id FROM " . $this->ecs->table("cart") . "WHERE session_id='" . SESS_ID . "' AND goods_type='goods' GROUP BY goods_id";
        return $this->db->getAll($sql);
    }

    public function get_goods_attr_list($goods_id)
    {
        $sql = "SELECT c.*,gt.attr_value,gt.attr_price FROM " . $this->ecs->table("cart") . " as c ," . $this->ecs->table("goods_attr") . " as gt WHERE c.session_id='" . SESS_ID . "' AND c.goods_type='goods'AND c.goods_attr_id=gt.goods_attr_id AND c.goods_id={$goods_id}";
        return $this->db->getAll($sql);
    }

    public function ticket_hotel_list($rec_id)
    {
        $sql = "SELECT * FROM " . $this->ecs->table("cart") . "WHERE session_id='" . SESS_ID . "' AND goods_type='hotel' AND parent_id={$rec_id} ";
        return $this->db->getAll($sql);
    }


}
