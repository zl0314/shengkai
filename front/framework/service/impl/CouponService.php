<?php

/**
 * Created by PhpStorm.
 * User: stepreal
 * Date: 17/9/26
 * Time: 下午5:44
 */
class CouponService
{
    public function __construct()
    {
        __load("CouponModel", "model");
        $this->coupon_model = new CouponModel();
        __load("CouponClusterModel", "model");
        $this->coupon_cluster_model = new CouponClusterModel();
        __load("UserCouponModel", "model");
        $this->user_coupon_model = new UserCouponModel();
    }

    /**
     * 查询库存,带分页
     *
     * @param $coupon_cluster_id
     * @return array
     */
    public function get_coupon_list($coupon_cluster_id = null)
    {
        return $this->coupon_model->get_coupon_list($coupon_cluster_id);
    }

    /**
     * 获取所有的库存
     *
     * @param $coupon_cluster_id
     * @return array
     */
    public function get_all_coupon_list($coupon_cluster_id = null)
    {
        return $this->coupon_model->get_all_coupon_list($coupon_cluster_id);
    }

    public function get_coupon_by_id($coupon_id)
    {
        return $this->coupon_model->get_coupon_by_id($coupon_id);
    }

    /**
     * @param $coupon_key
     * @return mixed
     */
    public function get_coupon_by_key($coupon_key)
    {
        return $this->coupon_model->get_coupon_by_key($coupon_key);
    }

    public function save($data)
    {
        $arr = array($data['date']);
        if (!empty($data['end_date'])) {
            $start_date_time = strtotime($data['date']);
            $end_date_time = strtotime($data['end_date']);
            if ($end_date_time < $start_date_time) {
                return false;
            }
            $arr = array();
            for ($i = $start_date_time; $i <= $end_date_time; $i = $i + 86400) {
                $arr[] = date('Y-m-d', $i);
            }
        }
        $ret = false;
        foreach ($arr as $value) {
            $data['date'] = $value;
            $ret = $this->coupon_model->save($data);
        }
        return $ret;
    }

    public function update($data)
    {
        return $this->coupon_model->update($data);
    }

    public function remove($id)
    {
        return $this->coupon_model->remove($id);
    }

    public function remove_b($data)
    {
        return $this->coupon_model->remove_batch($data);
    }

    /**
     * @param $coupon_cluster_id
     * @return bool
     */
    public function generate($coupon_cluster_id)
    {
        $coupon_cluster = $this->coupon_cluster_model->get($coupon_cluster_id);
        $needGenerate = $coupon_cluster['demand_count'] - $coupon_cluster['generated_count'];
        if ($needGenerate <= 0) {
            return true;
        }
        $counter = 0;
        $create_time = date('Y-m-d H:i:s');
        $dn = date('d');
        for ($i = 0; $i < $needGenerate; $i++) {
            $failed_count = 0;
            $coupon_key = $this->generate_coupon_key();
            $arrFields = array(
                'coupon_cluster_id' => $coupon_cluster_id,
                'coupon_key' => '21' . str_pad($coupon_cluster_id, 5, '0', STR_PAD_LEFT) . $dn . $coupon_key,
                'type_id' => $coupon_cluster['type_id'],
                'min_amount' => $coupon_cluster['min_amount'],
                'amount' => $coupon_cluster['amount'],
                'create_time' => $create_time,
                'end_time' => $coupon_cluster['end_time'],
            );

            $ret = $this->coupon_model->generate($arrFields);
            if ($ret == false) {
                $failed_count++;
            }
            if ($failed_count > 2) {
                return false;
            }
            $counter++;
            $data = array(
                'coupon_cluster_id' => $coupon_cluster_id,
                'generated_count' => $coupon_cluster['generated_count'] + $counter,
            );
            $ret = $this->coupon_cluster_model->update($data);
            if (!$ret) {
                echo '更新生成数量失败';
                exit;
            }
        }

        $coupon_cluster = $this->coupon_cluster_model->get($coupon_cluster_id);
        if ($coupon_cluster['generated_count'] >= $coupon_cluster['demand_count']) {
            $this->coupon_cluster_model->update(array(
                'coupon_cluster_id' => $coupon_cluster_id,
                'is_gen' => 1,
            ));
        }
    }

    /**
     * @return string
     */
    private function generate_coupon_key()
    {
        $key = '';
        $str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        for ($i = 0; $i < 23; $i++) {
            $index = mt_rand(0, 35);
            $key .= $str[$index];
        }
        return $key;
    }

    /**
     * @param $coupon_cluster_id
     * @param $user_id
     * @return bool
     */
    public function exchange($coupon_cluster_id, $user_id)
    {
        $coupon_cluster = $this->coupon_cluster_model->get($coupon_cluster_id);
        //取得一张优惠券
        $coupon = $this->coupon_model->get_one_coupon_by_coupon_cluster_id($coupon_cluster_id);
        if (empty($coupon)) {
            //sys_msg("优惠券已经兑换完了");
            return -100;
        }
        //检查最后使用日期
        $end_time_coupon_cluster = strtotime($coupon_cluster['end_time']);
        //默认30天
        $expire_day = $coupon_cluster['expire_day'] > 0 ? $coupon_cluster['expire_day'] : 30;
        $end_time_expire_date_timestamp = strtotime("+$expire_day days");

        $coupon_use_limit_date = $end_time_expire_date_timestamp;
        if ($end_time_expire_date_timestamp > $end_time_coupon_cluster) {
            $coupon_use_limit_date = $end_time_coupon_cluster;
        }

        //检查用户兑换次数是否已经超过限制次数
        $num = $this->user_coupon_model->get_user_coupon_num($coupon_cluster_id, $user_id);
        if ($num >= $coupon_cluster['limitation']) {
            //sys_msg("已经超过兑换次数");
            return -101;
        }

        $create_time = date('Y-m-d H:i:s');
        $arrFields = array(
            'coupon_cluster_id' => $coupon_cluster_id,
            'user_id' => $user_id,
            'type_id' => $coupon_cluster['type_id'],
            'coupon_id' => $coupon['coupon_id'],
            'end_time' => date('Y-m-d H:i:s', $coupon_use_limit_date),
            'create_time' => $create_time,
        );

        $ret = $this->user_coupon_model->save($arrFields);
        if ($ret) {

            //更新优惠券兑换人数
            $arrFields = array(
                'coupon_cluster_id' => $coupon_cluster_id,
                'exchange_count' => $coupon_cluster['exchange_count'] + 1,
            );
            $this->coupon_cluster_model->update($arrFields);

            //更新券已被使用
            $arrFields = array(
                'coupon_id' => $coupon['coupon_id'],
                'user_id' => $user_id,
                'status' => 1,
                'exchange_time' => $create_time,
            );
            $this->coupon_model->update($arrFields);

//            sys_msg("优惠券兑换成功");
            return 200;
        } else {
//            sys_msg("优惠券兑换失败");
            return -102;
        }
    }
}