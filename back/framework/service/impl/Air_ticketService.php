<?php

class Air_ticketService
{
    //put your code here
    private $region, $air;

    public function __construct()
    {
        __load("RegionModel", "model");
        __load("Air_ticketModel", "model");
        $this->region = new RegionModel();
        $this->air_ticket_model = new Air_ticketModel();
    }

    public function get_AllRegion($type)
    {
        return $this->region->get_all($type);
    }

    public function add($data)
    {
        return $this->air_ticket_model->add($data);
    }

    public function update($data)
    {
        return $this->air_ticket_model->update($data);
    }

    /**
     * 获取航班机票信息
     *
     * @param $air_ticket_id
     * @return mixed
     */
    public function getById($air_ticket_id)
    {
        return $this->air_ticket_model->getById($air_ticket_id);
    }

    /**
     * 查询列表
     *
     * @param $air_line_id
     * @param int $type 类型: 1:去程 2:返程
     * @return array
     */
    public function get_air_ticket_list($air_line_id, $type = -1)
    {
        return $this->air_ticket_model->get_air_ticket_list($air_line_id, $type);
    }

    /**
     * 不带分页查询航程下的机票
     *
     * @param $air_line_id
     * @param int $type 类型: 1:去程 2:返程
     * @return mixed
     */
    public function get_all_air_ticket_list($air_line_id, $type = -1)
    {
        return $this->air_ticket_model->get_all_air_ticket_list($air_line_id, $type);
    }

    public function get_air_line($id)
    {
        return $this->air_ticket_model->get($id);
    }

    public function remove($id)
    {
        return $this->air_ticket_model->remove($id);
    }

    public function remove_b($data)
    {
        return $this->air_ticket_model->remove_batch($data);
    }

    public function add_air_space($data)
    {
        return $this->air_ticket_model->air_space($data);
    }

    public function update_air_space($data)
    {
        return $this->air_ticket_model->space_air($data);
    }

    public function get_air_line_info()
    {
        return $this->air_ticket_model->air_info();
    }

    public function get_space($id)
    {
        return $this->air_ticket_model->get_space($id);
    }

    public function get_space_info($id)
    {
        return $this->air_ticket_model->get_space_info($id);
    }

    public function get_air_space_info()
    {
        return $this->air_ticket_model->air_space_info();
    }

    public function remove_air_space_info()
    {
        return $this->air_ticket_model->rem_air_space_info();
    }

    public function drop($data)
    {
        return $this->air_ticket_model->drop_air_space_info($data);
    }

    public function get_space_name($space_id)
    {
        $space = $this->get_space($space_id);
        $space_info = $this->get_space_info($space['s_id']);
        return $space_info['space_name'];
    }

    public function select_air_space_info($air_id, $s_id)
    {
        return $this->air_ticket_model->select_air_space_info($air_id, $s_id);
    }

    /**
     * 通过出发城市获取目的城市的航班
     *
     * @param $from_city
     * @return mixed
     */
    public function get_to_region_from_city($from_city)
    {
        return $this->air_ticket_model->get_to_region_from_city($from_city);
    }
}
