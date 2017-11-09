<?php

/**
 * Created by PhpStorm.
 * User: stepreal
 * Date: 17/9/26
 * Time: 下午5:45
 */
class UserCouponService
{

    public function __construct()
    {
        __load("UserCouponModel", "model");
        $this->user_coupon_model = new UserCouponModel();
    }

    /**
     * @param array $data
     * @return array
     */
    public function get_user_coupon_list($data = array())
    {
        return $this->user_coupon_model->get_user_coupon_list($data);
    }

    /**
     * 获取所有的库存
     *
     * @param $user_id
     * @return array
     */
    public function get_all_user_coupon_list($user_id = null)
    {
        return $this->user_coupon_model->get_all_user_coupon_list($user_id);
    }

    public function get_by_user_coupon_id($user_coupon_id)
    {
        return $this->user_coupon_model->get_by_user_coupon_id($user_coupon_id);
    }

    public function update($data)
    {
        return $this->user_coupon_model->update($data);
    }

    public function remove($id)
    {
        return $this->user_coupon_model->remove($id);
    }

    public function remove_b($data)
    {
        return $this->user_coupon_model->remove_batch($data);
    }

    /**
     * 查询用户可用的优惠券
     *
     * @param $arrInput
     * @return array
     */
    public function get_user_order_available_coupon_list($arrInput)
    {
        $arrInput['status'] = 0;
        $res = $this->user_coupon_model->get_all_user_coupon_list($arrInput);
        return $res;
    }

    /**
     * 设置优惠券未使用
     *
     * @param $arrInput
     * @return bool
     */
    public function set_coupon_used($arrInput)
    {
        __load("CouponModel", "model");
        $coupon_model = new CouponModel();
        $coupon = $coupon_model->get_coupon_by_id($arrInput['coupon_id']);
        if (empty($coupon)) {
            return false;
        }

        $arrInput['coupon_cluster_id'] = $coupon['coupon_cluster_id'];

        $user_coupon = $this->user_coupon_model->get_by_coupon_id($arrInput['coupon_id']);
        if (empty($user_coupon)) {
            return false;
        }

        $arrInput['user_coupon_id'] = $user_coupon['user_coupon_id'];

        return $this->user_coupon_model->set_coupon_used($arrInput);
    }

    /**
     * 设置优惠券未使用
     *
     * @param $arrInput
     * @return bool
     */
    public function set_coupon_unused($arrInput)
    {
        __load("CouponModel", "model");
        $coupon_model = new CouponModel();
        $coupon = $coupon_model->get_coupon_by_id($arrInput['coupon_id']);
        if (empty($coupon)) {
            return false;
        }

        $arrInput['coupon_cluster_id'] = $coupon['coupon_cluster_id'];

        $user_coupon = $this->user_coupon_model->get_by_coupon_id($arrInput['coupon_id']);
        if (empty($user_coupon)) {
            return false;
        }

        $arrInput['user_coupon_id'] = $user_coupon['user_coupon_id'];

        return $this->user_coupon_model->set_coupon_unused($arrInput);
    }
}