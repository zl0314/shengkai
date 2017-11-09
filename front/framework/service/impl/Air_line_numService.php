<?php

/**
 * Created by PhpStorm.
 * User: stepreal
 * Date: 17/8/20
 * Time: 下午9:00
 */
class Air_line_numService
{
    public function __construct()
    {
        __load("Air_line_numModel", "model");
        $this->air_line_num_model = new Air_line_numModel();
    }

    /**
     * 查询列表
     *
     * @param $air_line_id
     * @param $type
     * @return array
     */
    public function get_air_line_num_list($air_line_id, $type)
    {
        return $this->air_line_num_model->get_air_line_num_list($air_line_id, $type);
    }

    /**
     * 获取指定航程的库存数量(包含去程和返程两种类型的库存)
     *
     * @param $air_line_id
     * @param string $date
     * @return array
     */
    public function get_air_line_num_by_air_line_id($air_line_id, $date = '')
    {
        return $this->air_line_num_model->get_air_line_num_by_air_line_id($air_line_id, $date);
    }

    /**
     * @param $air_line_num_id
     * @return array
     */
    public function getById($air_line_num_id)
    {
        return $this->air_line_num_model->getById($air_line_num_id);
    }

    /**
     * @param $air_line_id
     * @param $date
     * @return array
     */
    public function get_air_line_num_by_date($air_line_id, $date)
    {
        $result = $this->air_line_num_model->get_air_line_num_by_date($air_line_id, $date);
        if (empty($result)) {
            return 0;
        }
        //锁定的数量是否大于库存数量
        $diff = $result['num'] - $result['locked_num'];
        return $diff > 0 ? $diff : 0;
    }

    /**
     * @param $data
     * @return mixed
     */
    public function add($data)
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
            $ret = $this->air_line_num_model->save($data);
        }
        return $ret;
    }

    /**
     * @param $data
     * @return mixed
     */
    public function update($data)
    {
        return $this->air_line_num_model->update($data);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function remove($id)
    {
        return $this->air_line_num_model->remove($id);
    }

    /**
     * @param $data
     * @return bool
     */
    public function remove_b($data)
    {
        return $this->air_line_num_model->remove_batch($data);
    }

    /**
     * 取消某些锁定
     *
     * @param $air_line_id
     * @param $num
     * @param $fly_date
     * @param $return_fly_date
     * @return mixed
     */
    public function unlock_air_line_num_from_in_date($air_line_id, $num, $fly_date, $return_fly_date)
    {
        $ret = array();

        $air_line_num = $this->air_line_num_model->get_air_line_num_by_date($air_line_id, $fly_date);
        $diff = $air_line_num['locked'] - $num;
        $num = $diff > 0 ? $diff : 0;
        $ret[] = $this->air_line_num_model->update(array(
            'id' => $air_line_num['id'],
            'locked_num' => $num,
        ));

        $air_line_num = $this->air_line_num_model->get_air_line_num_by_date($air_line_id, $return_fly_date);
        $diff = $air_line_num['locked'] - $num;
        $num = $diff > 0 ? $diff : 0;
        $ret[] = $this->air_line_num_model->update(array(
            'id' => $air_line_num['id'],
            'locked_num' => $num,
        ));

        return count($ret);
    }

    /**
     * 设置订单库存已使用
     *
     * @param $air_line_id
     * @param $num
     * @param $fly_date
     * @param $return_fly_date
     * @return int
     */
    public function update_order_air_line_num_used_from_in_date($air_line_id, $num, $fly_date, $return_fly_date)
    {
        $ret = array();
        foreach (array($fly_date, $return_fly_date) as $date) {
            if (empty($date)) {
                continue;
            }

            $air_line_num = $this->air_line_num_model->get_air_line_num_by_date($air_line_id, $date);
            $stock = $air_line_num['num'] - $num;
            $diff = $air_line_num['locked'] - $num;
            $locked_num = $diff > 0 ? $diff : 0;
            $sale_num = $air_line_num['sale_num'] + $num;

            $ret[] = $this->air_line_num_model->update(array(
                'id' => $air_line_num['id'],
                'locked_num' => $locked_num,
                'num' => $stock,
                'sale_num' => $sale_num,
            ));
        }
        return count($ret);
    }
}