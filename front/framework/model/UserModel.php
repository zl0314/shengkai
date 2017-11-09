<?php

/**
 * Created by PhpStorm.
 * User: stepreal
 * Date: 17/10/8
 * Time: 下午11:52
 */
__load("Model", "model");

class UserModel extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_by_user_id($user_id)
    {
        $sql = "SELECT * FROM " . $this->ecs->table("users") . "  WHERE user_id = '{$user_id}'";
        return $this->db->getRow($sql);
    }

    public function search_by_mobile_phone($mobile_phone)
    {
        $sql = "SELECT user_id, user_name, mobile_phone, reg_time FROM " . $this->ecs->table("users") . "  WHERE mobile_phone like '%{$mobile_phone}%'";
        return $this->db->getAll($sql);
    }
}