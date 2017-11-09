<?php

/**
 * Created by PhpStorm.
 * User: stepreal
 * Date: 17/8/16
 * Time: 上午12:37
 */
class Air_lineService
{
    public function __construct()
    {
        __load("RegionModel", "model");
        __load("Air_lineModel", "model");
        $this->region = new RegionModel();
        $this->air_line_model = new Air_lineModel();
    }

    /**
     * 获取航线详情
     *
     * @param $air_line_id
     * @return mixed
     */
    public function getById($air_line_id)
    {
        return $this->air_line_model->get($air_line_id);
    }

    public function get_AllRegion($type)
    {
        return $this->region->get_all($type);
    }

    public function add($data)
    {
        return $this->air_line_model->add($data);
    }

    public function update($data)
    {
        return $this->air_line_model->update($data);
    }

    public function get_All_air_line()
    {
        return $this->air_line_model->get_All_Query();
    }

    public function get_airticket_region()
    {
        return $this->air_line_model->airticket_region();
    }

    public function get_All_Airticket()
    {
        return $this->air_line_model->get_All_Query();
    }

    public function remove($id)
    {
        return $this->air_line_model->remove($id);
    }

    public function remove_b($data)
    {
        return $this->air_line_model->remove_batch($data);
    }

    /**
     * 通过出发城市获取目的城市的航班
     *
     * @param $from_city
     * @return mixed
     */
    public function get_air_list_from_city($from_city)
    {
        return $this->air_line_model->get_air_list_from_city($from_city);
    }


    /**
     * 通过出发城市获取目的城市的航班
     *
     * @param $from_city
     * @param $to_city
     * @return mixed
     */
    public function get_air_list_from_city_and_to_city($from_city, $to_city)
    {
        return $this->air_line_model->get_air_list_from_city_and_to_city($from_city, $to_city);
    }

    /**
     * 获取去程和返程的所有记录
     *
     * @return array
     */
    public function get_air_line_region_list()
    {
        return $this->air_line_model->get_air_line_region_list();
    }

    /**
     * 获取去程出发城市
     *
     * @return array
     */
    public function get_air_line_from_region_list()
    {
        return $this->air_line_model->get_air_line_from_region_list();
    }
}
