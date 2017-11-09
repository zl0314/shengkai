<?php

/**
 * Description of BatchService
 *
 * @author qs
 */
class CartService
{

    //put your code here

    private $cart;

    public function __construct()
    {
        __load("CartModel", "model");
        $this->cart = new CartModel();
    }

    public function get_cart()
    {
        $cart_info = $this->get_cart_info();
        $cart_money = $this->get_cart_money();
        return array(
            "ticket" => array(
                "info" => $cart_info['ticket'],
                "money" => $cart_money['ticket'],
            ),
            "plane" => array(
                "info" => $cart_info['plane'],
                "money" => $cart_money['plane'],
            ),
            "hotel" => array(
                "info" => $cart_info['hotel'],
                "money" => $cart_money['hotel'],
            ),
            "goods" => array(
                "info" => $cart_info['goods'],
                "money" => $cart_money['goods'],
            ),
            "combo" => array(
                "info" => $cart_info['combo'],
                "money" => $cart_money['combo'],
            ),
            'all' => array(
                "money" => $cart_money['all'],
            )


        );
    }

    public function get_one_number($id)
    {
        return $this->cart->get_one_ticket_number($id);
    }

    public function get_cart_combo()
    {
        $res = $this->cart->get_cart_combo_info('combo');
        foreach ($res as $key => $value) {
            $res[$key]['cart_combo_money'] = number_format($value['goods_price'] * $value['goods_number'], 2, ".", "");
        }
        return $res;
    }

    public function get_cart_info()
    {
        //获取票
        $ticket = $this->cart->get_cart("ticket");
        //获取机票
        $plane = $this->cart->get_cart("plane");
        //获取酒店
        $hotel = $this->cart->get_cart("hotel");
        //获取周边商品《订单中显示》
        $goods = $this->cart->get_cart("goods");
        //获取套餐
        $combo = $this->cart->get_cart("combo");
        return array(
            "ticket" => $this->get_ticket_info($ticket),
            "plane" => $this->get_plane_info($plane),
            "hotel" => $this->get_hotel_info($hotel),
            "goods" => $this->get_goods_info($goods),
            "combo" => $this->get_cart_combo_info($combo),
            "goods_cart" => $this->get_cart_goods_info(),
        );
    }

    //套餐信息
    public function get_cart_combo_info(Array $combo)
    {
        //获取套餐信息
        __load("ComboService");
        $combo_obj = new ComboService();
        foreach ($combo as $key => $val) {
            $combo[$key]['combo_info'] = $combo_obj->get_combo_info($val['combo_id']);
            $combo[$key]['combo_money'] = number_format($val['goods_price'] * $val['goods_number'], 2, ".", "");;
        }
        return $combo;
    }

    public function get_hotel_info(Array $hotel)
    {
        __load("HotelService");
        $hotel_obj = new HotelService();
        foreach ($hotel as $key => $value) {
            $hotel[$key]['hotel_info'] = $hotel_obj->get_Hotel($value['hotel_id']);
            //$hotel[$key]['money'] = number_format($value['goods_number'] * $value['goods_price'], 2, ".", "");
            $datetime1 = date_create($value['hotel_start_date']);
            $datetime2 = date_create($value['hotel_end_date']);
            $day = round(($datetime2->format('U') - $datetime1->format('U')) / (60 * 60 * 24));
            if ($day > 0) {
                $day = abs($day);
                $hotel[$key]['money'] = $value['goods_price'] * $value['goods_number'] * $day;
            }
            $hotel[$key]['day'] = $day > 0 ? $day : 0;
        }
        return $hotel;

    }

    public function  get_cart_goods_info()
    {
        //先获取商品种类
        __load('GoodsService');
        $goods_obj = new GoodsService();
        $goods_list = $this->cart->get_goods_group();
        foreach ($goods_list as $key => $value) {
            $goods_list[$key]['goods_info'] = $goods_obj->get_goods_info($value['goods_id']);
            $goods_list[$key]['info'] = $this->cart->get_goods_attr_list($value['goods_id']);
        }

        return $goods_list;
    }

    public function get_cart_money()
    {
        //获取票
        $ticket = $this->cart->get_money("ticket");
        //获取机票
        $plane = $this->cart->get_money("plane");
        //获取酒店
        $hotel = $this->cart->get_money("hotel");
        //获取周边商品
        $goods = $this->cart->get_money("goods");
        //获取套餐
        $combo = $this->cart->get_money("combo");
        //购物车总价
        $all = $this->cart->get_money("all");
        return array(
            "ticket" => number_format($ticket, 2, ".", ""),
            "plane" => number_format($plane, 2, ".", ""),
            "hotel" => number_format($hotel, 2, ".", ""),
            "goods" => number_format($goods, 2, ".", ""),
            "combo" => number_format($combo, 2, ".", ""),
            "all" => $all
        );
    }

    public function get_goods_info(Array $goods)
    {
        __load('GoodsService');
        $goods_obj = new GoodsService();

        foreach ($goods as $key => $value) {
            $goods[$key]['goods_info'] = $goods_obj->get_goods_info($value['goods_id']);
            $goods[$key]['money'] = number_format($value['goods_number'] * $value['goods_price'], 2, ".", "");
            $attr = $goods_obj->get_goods_attr($value['goods_attr_id']);
            $goods[$key]['attr_value'] = $attr['attr_value'];
        }
        return $goods;
    }

    public function get_ticket_info(Array $ticket)
    {
        __load("NumberService");
        $number_obj = new NumberService();
        __load('GoodsService');
        $goods_obj = new GoodsService();
        __load('GameService');
        $game_obj = new GameService();
        __load('HotelService');
        $hotel_obj = new HotelService();
        $game_id_list = Array();
        foreach ($ticket as $key => $value) {
            if (!in_array($ticket[$key]['game_id'], $game_id_list)) {
                Array_push($game_id_list, $ticket[$key]['game_id']);
            }
        }
        $game_info = Array();
        foreach ($game_id_list AS $key => $value) {
            $game_info[$key] = $game_obj->get_game_name($value);
        }
        foreach ($ticket as $key => $value) {

            $ticket[$key]['ticket_info'] = $goods_obj->get_ticket_info($value['goods_id'], $value['game_id']);
            if ($ticket[$key]['ticket_info']['region_id']) {
                $ticket[$key]['ticket_info']['hotel_num'] = $hotel_obj->get_hotel_id($ticket[$key]['ticket_info']['region_id']);
            } else {
                $ticket[$key]['ticket_info']['hotel_num'] = 0;
            }
//            $ticket[$key]['ticket_info']['num_start']=date('Y-m-d H:i',strtotime($ticket[$key]['ticket_info']['num_start']));
//            $ticket[$key]['ticket_info']['keywords']=explode(' ',$ticket[$key]['ticket_info']['keywords']);
//            $ticket[$key]['number_color']=$number_obj->get_number_color($ticket[$key]['ticket_info']['id']);
            $ticket[$key]['hotel_list'] = $this->get_ticket_hotel_list($value['rec_id']);
            $ticket[$key]['hotel_money'] = number_format($this->get_ticket_hotel_money($value['rec_id']), 2, ".", "");
            $ticket[$key]['ticket_money'] = number_format($value['goods_price'] * $value['goods_number'], 2, ".", "");

        }

        return array("t" => $ticket, "game_info" => $game_info);
    }

    //已废弃
//    public function  get_plane_info(Array $cart_plane)
//    {
//        __load("AirticketService");
//        $air_obj = new AirticketService();
//        foreach ($cart_plane as $key => $plane) {
//
//            $cart_plane[$key]['air_info'] = $air_obj->get_air_info2($plane['air_id']);
//            $cart_plane[$key]['space_name'] = $air_obj->get_space_name($plane['space_id']);
//            $cart_plane[$key]['money'] = number_format($cart_plane[$key]['goods_price'] * $cart_plane[$key]['goods_number'], 2, ".", "");
//        }
//        return $cart_plane;
//    }

    /**
     * 获取购物车机票航程信息
     *
     * @param array $cart_plane
     * @return array
     */
    public function  get_plane_info(Array $cart_plane)
    {
        __load("Air_ticketService");
        $air_obj = new Air_ticketService();
        __load("Air_lineService");
        $air_line_service = new Air_lineService();

        $cart_plane_list = array(
            1 => array(),
            2 => array(),
        );
        foreach ($cart_plane as $key => $plane) {
            $air_line = $air_line_service->getById($plane['air_id']);
            if (empty($air_line)) {
                continue;
            }
            $cart_plane[$key]['space_name'] = '';
            $cart_plane[$key]['air_line'] = $air_line;
            $cart_plane[$key]['money'] = number_format($air_line['price'] * $cart_plane[$key]['goods_number'], 2, ".", "");

            $cart_plane[$key]['air_info'][1] = $air_obj->get_all_air_ticket_list($plane['air_id'], 1);
            $cart_plane[$key]['air_info'][2] = $air_obj->get_all_air_ticket_list($plane['air_id'], 2);
        }
        return $cart_plane;
    }

    public function  get_ticket_hotel_list($rec_id)
    {
        $hotel_cart_list = $this->cart->ticket_hotel_list($rec_id);
        if (empty($hotel_cart_list)) {
            return null;
        }
        __load("HotelService");
        $hotel_obj = new HotelService();
        foreach ($hotel_cart_list as $key => $hotel)
            $hotel_cart_list[$key]['hotel_info'] = $hotel_obj->get_Hotel($hotel['hotel_id']);
        return $hotel_cart_list;

    }

    public function  get_ticket_hotel_money($rec_id)
    {
        $hotel_cart_list = $this->cart->ticket_hotel_list($rec_id);
        if (empty($hotel_cart_list)) {
            return null;
        }

        $money = 0;
        foreach ($hotel_cart_list as $key => $hotel) {

            $datetime1 = date_create($hotel['hotel_start_date']);
            $datetime2 = date_create($hotel['hotel_end_date']);
            $day = round(($datetime2->format('U') - $datetime1->format('U')) / (60 * 60 * 24));
            if ($day > 0) {
                $day = abs($day);
                $money += $hotel['goods_price'] * $hotel['goods_number'] * $day;
            }
        }
        return number_format($money, 2, ".", "");
    }

//    public function  get_ticket_hotel_money($rec_id){
//        $hotel_cart_list=$this->cart->ticket_hotel_list($rec_id);
//        if(empty($hotel_cart_list)){
//            return null;
//        }
//
//        $money=0;
//        foreach($hotel_cart_list as $key=>$hotel){
//            $money+=$hotel['goods_price']*$hotel['goods_number'];
//        }
//        return number_format($money,2,".","");
//
//    }

    public function add_ticket_to_cart($data)
    {
        //先获取商品的信息
        foreach ($data as $info) {
            $rec = $this->cart->get_cart_ticket($info['goods_id']);

            if (empty($rec)) {
                $cart_info = array(
                    "goods_id" => $info['goods_id'],
                    "goods_name" => $info['goods_name'],
                    "goods_number" => $info['goods_number'],
                    "goods_price" => $info['goods_price'],
                    "game_id" => $info['game_id'],
                    "goods_type" => "ticket"
                );
                $this->cart->add_cart($cart_info);
            } else {
                $this->cart->update_cart($rec['rec_id'], $info['goods_number'], $info['goods_price']);
            }
        }

    }

    function add_combo_to_cart($data)
    {
        //先获取商品的信息
        $rec = $this->cart->get_cart_combo($data['combo_id']);
        if (empty($rec)) {
            $cart_info = array(
                "combo_id" => $data['combo_id'],
                "goods_name" => $data['goods_name'],
                "goods_number" => $data['goods_number'],
                "goods_price" => $data['goods_price'],
                "goods_type" => "combo"
            );
            return $this->cart->add_cart($cart_info);
        } else {
            return $this->cart->update_cart($rec['rec_id'], $data['goods_number'], $data['goods_price']);
        }
    }

    public function add_goods_to_cart($data)
    {
        __load("GoodsService");
        $goods_obj = new GoodsService();
        foreach ($data as $info) {
            $goods_info = $goods_obj->get_goods_info($info['goods_id']);

            $goods_attr_info = $goods_obj->get_goods_attr($info['goods_attr_id']);

            $product = $goods_obj->get_goods_product($info['goods_id'], $info['goods_attr_id']);
            $rec = $this->cart->get_cart_goods($info['goods_id'], $info['goods_attr_id']);
            if (empty($rec)) {
                $cart_info = array(
                    "goods_id" => $info['goods_id'],
                    "goods_attr_id" => $info['goods_attr_id'],
                    "goods_sn" => $goods_info['goods_sn'],
                    "goods_number" => $info['goods_number'],
                    "goods_name" => $goods_info['goods_name'],
                    "goods_price" => $goods_info['shop_price'] + $goods_attr_info['attr_price'],
                    "product_id" => $product['product_id'],
                    "goods_attr" => $goods_attr_info['attr_value'] . "(" . $goods_attr_info['attr_price'] . ")",
                    "goods_type" => "goods"
                );
                $this->cart->add_cart($cart_info);
            } else {
                $this->cart->update_cart($rec['rec_id'], $info['goods_number'], $goods_attr_info['attr_price']);
            }
        }
    }

    public function get_ticket_total($session_id)
    {
        return $this->cart->get_ticket_total($session_id);
    }

    public function add_cart($data)
    {
        $this->cart->add_cart($data);
    }

    public function remove_cart($type, $id)
    {
        $this->cart->remove_cart($type, $id);
    }

    public function remove_hotel($rec_id)
    {
        $this->cart->remove_hotel($rec_id);
    }
}
