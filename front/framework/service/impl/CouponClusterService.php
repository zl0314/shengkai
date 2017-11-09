<?php

/**
 * Created by PhpStorm.
 * User: stepreal
 * Date: 17/9/26
 * Time: 下午5:44
 */
class CouponClusterService
{
    public function __construct()
    {
        __load("CouponClusterModel", "model");
        $this->coupon_cluster_model = new CouponClusterModel();
    }

    /**
     * 查询库存,带分页
     *
     * @param $coupon_cluster_id
     * @return array
     */
    public function get_coupon_cluster_list($coupon_cluster_id = null)
    {
        return $this->coupon_cluster_model->get_coupon_cluster_list($coupon_cluster_id);
    }

    /**
     * 获取所有的库存
     *
     * @param $coupon_cluster_id
     * @return array
     */
    public function get_all_coupon_cluster_list($coupon_cluster_id = null)
    {
        return $this->coupon_cluster_model->get_all_coupon_cluster_list($coupon_cluster_id);
    }

    public function get_coupon_cluster_by_id($coupon_cluster_id)
    {
        return $this->coupon_cluster_model->get($coupon_cluster_id);
    }

    public function add($data)
    {
        return $this->coupon_cluster_model->add($data);
    }

    /**
     * @param $coupon_key
     * @return mixed
     */
    public function get_by_coupon_key($coupon_key)
    {
        return $this->coupon_cluster_model->get_by_coupon_key($coupon_key);
    }

    public function update($data)
    {
        return $this->coupon_cluster_model->update($data);
    }

    public function remove($id)
    {
        return $this->coupon_cluster_model->remove($id);
    }

    public function remove_b($data)
    {
        return $this->coupon_cluster_model->remove_batch($data);
    }
}