<?php

/**
 * Created by PhpStorm.
 * User: stepreal
 * Date: 17/10/8
 * Time: 下午11:53
 */
class UserService
{

    public function __construct()
    {
        __load("UserModel", "model");
        $this->user_model = new UserModel();
    }

    public function get_by_user_id($user_coupon_id)
    {
        return $this->user_model->get_by_user_id($user_coupon_id);
    }

    public function search_by_mobile_phone($mobile_phone)
    {
        return $this->user_model->search_by_mobile_phone($mobile_phone);
    }
}