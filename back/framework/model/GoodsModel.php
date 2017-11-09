<?php

/**
 * 
 */
__load("Model", "model");

class GoodsModel extends Model {
    private $goods_info = array(
                'goods_id', 'goods_number', 'goods_name','goods_price','session_id','goods_type','goods_attr_id'
                    ),$goods;
    public function __construct() {
        parent::__construct();
    }
    public function get($id) {
        $sql = "SELECT * FROM " . $this->ecs->table("goods") . "  WHERE goods_id=$id";
        return $this->db->getRow($sql);
    }


    public function get_goods_product($goods_id=0,$goods_attr=0){

        $sql = "SELECT * FROM " . $this->ecs->table("products") . "  WHERE goods_id=$goods_id AND goods_attr=$goods_attr";
        return $this->db->getRow($sql);
    }
    public function set(Array $array) {
        foreach ($this->goods_info as $value) {
            if (isset($array[$value])) {
                $this->goods[$value] = $array[$value];
            }
        }
    }
    public function get_goods_attr($attr){
        $sql = "SELECT * FROM " . $this->ecs->table("goods_attr") . "WHERE goods_attr_id=$attr";
        return $this->db->getRow($sql);


    }
    public function save($data) {
        $res = $this->db->autoExecute($this->ecs->table("cart"), $data, 'INSERT');
        if ($res) {
            return $this->db->insert_id();
        } else {
            return 0;
        }
    }
    
    public function get_ticket_info($goods_id,$game_id){
        $user_id=$_SESSION['user_id'];

        $sql="SELECT
                    goods.*,
                    color.*,
                    n.num_name,
                    n.id,
                    n.numbers,
                    n.num_name,
                    n.num_start,
                    r.region_name,
                    game.game_name,
                    c.goods_number,
                    c.goods_price,
                    r.region_id,
                    c.goods_number * c.goods_price as item,
                    p.pitch_name,p.pitch_img,p.big_pitch_img
               FROM
                  ".$this->ecs->table("goods")."   AS goods
                    LEFT JOIN ".$this->ecs->table("number")." AS n ON goods.number_id = n.id
                    LEFT JOIN ".$this->ecs->table("pitch")." AS p ON p.id = n.pitch_id
                    LEFT JOIN ".$this->ecs->table("region")." AS r ON r.region_id=p.region_id
                    LEFT JOIN ".$this->ecs->table("game")." AS game ON goods.game_id=game.id
                    LEFT JOIN ".$this->ecs->table("cart")." AS c ON goods.goods_id=c.goods_id
                    LEFT JOIN ".$this->ecs->table("color_manage")." AS color ON color.color_id=n.color_id
                WHERE
                    goods.goods_id = {$goods_id} ";
            return $this->db->getRow($sql);
    }
    public function add_hotel($data){
       $res=$this->db->autoExecute($this->ecs->table("cart"), $data, 'INSERT');
        if ($res) {
            return $this->db->insert_id();
        } else {
            return 0;
        }
    }
}

?>