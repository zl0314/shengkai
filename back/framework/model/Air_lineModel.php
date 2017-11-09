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

class Air_lineModel extends Model
{
    //put your code here
    public function add($data)
    {
        return $this->db->autoExecute($this->ecs->table("air_line"), $data, 'INSERT');
    }

    public function update($data)
    {
        $id = $data['id'];
        unset($data['id']);
        return $this->db->autoExecute($this->ecs->table("air_line"), $data, 'UPDATE', " id=$id");
    }

    public function get_air_space($air_id)
    {
        $sql = "SELECT * FROM " . $this->ecs->table("air_space_info") . " as a ," . $this->ecs->table("ship_space") . " as s WHERE s.s_id=a.s_id AND a.air_id={$air_id}";
        return $this->db->getAll($sql);
    }

    public function get_space($id)
    {
        $sql = "SELECT * FROM " . $this->ecs->table("air_space_info") . " WHERE id={$id}";
        return $this->db->getRow($sql);
    }

    public function get_space_info($id)
    {
        $sql = "SELECT * FROM " . $this->ecs->table("ship_space") . " WHERE s_id={$id}";

        return $this->db->getRow($sql);
    }

    public function get_All()
    {
        $sql = "SELECT * FROM " . $this->ecs->table("air_line");
        return $this->db->getAll($sql);
    }

    public function get_space_list()
    {
        $sql = "SELECT * FROM " . $this->ecs->table("ship_space");
        return $this->db->getAll($sql);
    }

    public function air_name_time()
    {
        $sql = "SELECT * FROM " . $this->ecs->table("air_line") . " ORDER BY id";
        $air_all = $this->db->getAll($sql);
        $space_list = array();
        foreach ($air_all AS $key => $air_info) {
            $sql = "SELECT * FROM " . $this->ecs->table("air_space_info") . " AS a LEFT JOIN " . $this->ecs->table("ship_space") . " AS s ON a.s_id = s.s_id WHERE a.air_id = '$air_info[id]'";
            $space_list[$key]['space'] = $this->db->getAll($sql);
            $space_list[$key]['air_id'] = $air_info[id];
        }

        $sql = "SELECT * FROM " . $this->ecs->table("air_line") . " ORDER BY id LIMIT 1";
        $first_air = $this->db->getRow($sql);
        $sql = "SELECT * FROM " . $this->ecs->table("air_space_info") . " AS a LEFT JOIN " . $this->ecs->table("ship_space") . " AS s ON a.s_id = s.s_id WHERE a.air_id = '$first_air[id]'";
        $first_space = $this->db->getAll($sql);

        return array("air_all" => $air_all, "first_space" => $space_list);
    }

    public function select_air_space_info($air_id, $s_id)
    {
        $sql = "SELECT * FROM " . $this->ecs->table("air_space_info") . " WHERE air_id={$air_id} AND s_id={$s_id} ";
        return $this->db->getRow($sql);
    }


    public function air_line_region()
    {
        //出发城市
        $sql = "SELECT distinct(from_ctiy) FROM " . $this->ecs->table("air_line");
        $from_ctiys = $this->db->getAll($sql);
        foreach ($from_ctiys AS $key => $region_code) {
            $sql = "SELECT * FROM " . $this->ecs->table("region") . " WHERE region_id = '$region_code[from_ctiy]'";
            $from_ctiy[$key] = $this->db->getRow($sql);
        }

        //抵达城市
        $sql = "SELECT distinct(to_ctiy) FROM " . $this->ecs->table("air_line");
        $to_ctiys = $this->db->getAll($sql);
        foreach ($to_ctiys AS $key => $region_code) {
            $sql = "SELECT * FROM " . $this->ecs->table("region") . " WHERE region_id = '$region_code[to_ctiy]'";
            $to_ctiy[$key] = $this->db->getRow($sql);
        }

        return array("from_ctiy" => $from_ctiy, "to_ctiy" => $to_ctiy);
    }

    public function get_All_Query()
    {
        /* 记录总数 */
        $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('air_line');
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        /* 分页大小 */
        $filter = page_and_size($filter);

        $sql = "SELECT * FROM " . $this->ecs->table("air_line") . " LIMIT " . $filter['start'] . ",$filter[page_size]";
        $row = $this->db->getAll($sql);
        return array('airs' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
    }


    public function get($id)
    {
        $sql = "SELECT * FROM " . $this->ecs->table("air_line") . " WHERE id=$id";
        return $this->db->getRow($sql);
    }

    public function remove($id)
    {
        $sql = "DELETE FROM" . $this->ecs->table("air_line") . " WHERE id=$id";
        return $this->db->query($sql);
    }

    public function remove_batch($data)
    {
        foreach ($data as $key => $id) {
            $sql = "DELETE FROM" . $this->ecs->table("hotel") . " WHERE id=$id";
            $res = $this->db->query($sql);
        }
        return $res;
    }

    public function space_all()
    {
        $sql = "SELECT * FROM " . $this->ecs->table("ship_space");
        return $this->db->getAll($sql);
    }

    public function flight_num($air_id)
    {
        $sql = "SELECT * FROM " . $this->ecs->table("air_line") . " WHERE id = $air_id";
        return $this->db->getRow($sql);
    }

    public function air_space($data)
    {
        $air_space['air_id'] = $data['air_id'];
        $air_space['s_id'] = $data['s_id'];
        $air_space['s_price'] = $data['s_price'];
        $air_space['s_num'] = $data['s_num'];
        $air_space['add_time'] = time();
        return $this->db->autoExecute($this->ecs->table("air_space_info"), $air_space, 'INSERT');
    }

    public function space_air($data)
    {
        $air_space['air_id'] = $data['air_id'];
        $air_space['s_id'] = $data['s_id'];
        $air_space['s_price'] = $data['s_price'];
        $air_space['s_num'] = $data['s_num'];
        $air_space['add_time'] = time();
        return $this->db->autoExecute($this->ecs->table("air_space_info"), $air_space, 'UPDATE', " id = $data[id]");
    }

    public function air_info()
    {
        /* 查询条件 */
        $filter = array();
        $filter['air_id'] = empty($_REQUEST['air_id']) ? 0 : intval($_REQUEST['air_id']);
        $where = (!empty($filter['air_id'])) ? " WHERE air_id = '$filter[air_id]' " : '';
        /* 记录总数 */
        $sql = "SELECT COUNT(*) FROM " . $this->ecs->table("air_space_info") . $where;
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);
        /* 分页大小 */
        $filter = page_and_size($filter);

        $sql = "SELECT asi.*,FROM_UNIXTIME(asi.add_time,'%Y-%m-%d %H:%i:%s') AS add_time, a.flight, s.space_name FROM " . $this->ecs->table("air_space_info") . " AS asi LEFT JOIN " . $this->ecs->table('air_line') . " AS a ON asi.air_id = a.id LEFT JOIN " . $this->ecs->table('ship_space') . " AS s ON asi.s_id = s.s_id " . $where . " LIMIT " . $filter['start'] . ",$filter[page_size]";
        $row = $this->db->getAll($sql);
        return array('airs' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
    }

    public function air_space_info()
    {
        $id = empty($_REQUEST['id']) ? 0 : intval($_REQUEST['id']);
        $sql = "SELECT * FROM " . $this->ecs->table("air_space_info") . " WHERE id = '$id'";
        return $this->db->getRow($sql);
    }

    public function rem_air_space_info()
    {
        $id = empty($_REQUEST['id']) ? 0 : intval($_REQUEST['id']);
        $sql = "DELETE FROM" . $this->ecs->table("air_space_info") . " WHERE id = $id";
        return $this->db->query($sql);
    }

    public function drop_air_space_info($data)
    {
        foreach ($data as $key => $id) {
            $sql = "DELETE FROM" . $this->ecs->table("air_space_info") . " WHERE id = $id";
            $res = $this->db->query($sql);
        }
        return $res;
    }

    public function ambitus_goods_info()
    {
        $sql = "SELECT * FROM " . $this->ecs->table("goods") . " WHERE is_goods=1 AND is_delete=0 AND is_on_sale=1 order by goods_id";
        $arr = $this->db->getAll($sql);
        $goods_list = Array();
        foreach ($arr AS $key => $goods) {
            $sql = "SELECT * FROM " . $this->ecs->table("goods_attr") . " WHERE goods_id = '$goods[goods_id]' order by attr_sequence";
            $goods_list[$key]['attribute'] = $this->db->getAll($sql);
            if ($goods_list[$key]['attribute']) {
                foreach ($goods_list[$key]['attribute'] AS $k => $val) {
                    $goods_list[$key]['attribute'][$k]['attr_price'] = number_format($goods_list[$key]['attribute'][$k]['attr_price'] + $arr[$key]['shop_price'], 2, ".", "");
                }
                $goods_list[$key]['goods_name'] = $arr[$key]['goods_name'];
                $goods_list[$key]['goods_img'] = $arr[$key]['goods_img'];
                $goods_list[$key]['goods_id'] = $arr[$key]['goods_id'];
            }
        }
        return $goods_list;
    }

    public function get_air_line($from_city, $to_city)
    {
        $sql = "SELECT * FROM " . $this->ecs->table("air_line") . " WHERE to_ctiy={$to_city} AND from_ctiy={$from_city}";
        $air_line_list = $this->db->getAll($sql);
        return $air_line_list;
    }

    /**
     * 通过出发城市获取目的城市的航班
     *
     * @param $from_city
     * @return mixed
     */
    public function get_air_list_from_city($from_city)
    {
        $sql = "SELECT * FROM " . $this->ecs->table("air_line") . " WHERE from_city='{$from_city}' GROUP BY to_city";
        $air_line_list = $this->db->getAll($sql);

        foreach ($air_line_list AS $key => $region_code) {
            $sql = "SELECT * FROM " . $this->ecs->table("region") . " WHERE region_id = '{$region_code['to_city']}'";
            $air_line_list[$key] = $this->db->getRow($sql);
        }
        return $air_line_list;
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
        $sql = "SELECT * FROM " . $this->ecs->table("air_line") . " WHERE from_city = '{$from_city}' and to_city = '{$to_city}' and type = 0";
        $air_line_list = $this->db->getAll($sql);
        return $air_line_list;
    }

    /**
     * 取得所有去程和返程的数据
     *
     * @return array
     */
    public function get_air_line_from_region_list()
    {
        //出发城市
        $sql = "SELECT DISTINCT(from_city) FROM " . $this->ecs->table("air_line") . " WHERE type = 0";
        $from_cities = $this->db->getAll($sql);
//        $from_cities = array();
//        $to_cities = array();
        foreach ($from_cities AS $key => $region_code) {
//            $sql = "SELECT region_name FROM " . $this->ecs->table("region") . " WHERE region_id = '{$region_code['from_city']}'";
//            $from_cities[$key]['region_name'] = $this->db->getOne($sql);
            $sql = "SELECT * FROM " . $this->ecs->table("region") . " WHERE region_id = '{$region_code['from_city']}'";
            $from_cities[$key] = $this->db->getRow($sql);
        }
//        echo '<pre>';print_r($from_cities);exit;
        return $from_cities;
    }
    /**
     * 取得所有去程和返程的数据
     *
     * @return array
     */
    public function get_air_line_region_list()
    {
        //出发城市
        $sql = "SELECT DISTINCT(from_city) FROM " . $this->ecs->table("air_line") . " WHERE type = 0";
        $result = $this->db->getAll($sql);
        $from_cities = array();
        $to_cities = array();
        foreach ($result AS $key => $region_code) {
            $sql = "SELECT * FROM " . $this->ecs->table("region") . " WHERE region_id = '{$region_code['from_city']}'";
            $from_cities[$key] = $this->db->getRow($sql);

            $sql = "SELECT * FROM " . $this->ecs->table("region") . " WHERE region_id = '{$region_code['to_city']}'";
            $to_cities[$key] = $this->db->getRow($sql);
        }

        return array(
            'from_city_1' => $from_cities,
            'from_city_2' => $to_cities,
        );

//        $sql = "SELECT * FROM " . $this->ecs->table("air_line") . " WHERE 1";
//        $result = $this->db->getAll($sql);
//
//        foreach ($result AS $key => $region_code) {
//            $sql = "SELECT * FROM " . $this->ecs->table("region") . " WHERE region_id = '{$region_code['to_city']}'";
//            $result[$key] = $this->db->getRow($sql);
//        }
//
//        $air_line_list = array(
//            'from_city' => array(),
//            'to_city' => array(),
//        );
//        foreach ($result as $value) {
//            if ($value['type'] == 1) {//去程
//                $air_line_list['from_city'][] = $value;
//            } elseif ($value['type'] == 2) {//返程
//                $air_line_list['to_city'][] = $value;
//            }
//        }
//
//        return $air_line_list;
    }
}
