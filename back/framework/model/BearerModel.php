<?php

/*
 * 作者：鞠嵩
 * 时间：9:58:15
 * 
 */

/**
 * Description of HotelModel
 *
 * @author Kevin
 */
__load("Model", "model");

class BearerModel extends Model {
    
    //保存持票人信息
    public function save_bearer($bearer_info,$way,$where){
        return $this->db->autoExecute($this->ecs->table("bearer_info"), $bearer_info, $way, $where);
    }
    
    //更新球票信息
    public function order_ticket($order_info,$way,$where){
        return $this->db->autoExecute($this->ecs->table("order_ticket"), $order_info, $way, $where);
    }
   
    //根据user_id查询持票人名字
    public function bearer_name($user_id){
        return $bearer_info = $this->db->getAll("SELECT  cn_customer_name,id FROM ".$this->ecs->table('bearer_info')."WHERE user_id=$user_id");
    }
    
    //根据订单id查询持票人购物信息
    public function bearer_info_list($order_id){
        __load("NumberService");
        $number_obj=new NumberService();
        $sql="SELECT t.* FROM " .$this->ecs->table('order_ticket')."AS t WHERE t.order_id=$order_id";
        $res=$this->db->getAll($sql);
        foreach($res AS $key=>$value){
            $goods_id=$value['goods_id'];
            $number_id=$this->db->getOne("SELECT number_id FROM " .$this->ecs->table('goods'). "WHERE goods_id=$goods_id");
            $arr=$number_obj->get_Number_info($number_id);
            foreach($arr AS $n){
            $res[$key]['numbers']=$n['numbers'];
            $res[$key]['num_start']=$n['num_start'];
            }
            $res[$key]['seat_logo']=$this->db->getOne("SELECT seat_logo FROM " .$this->ecs->table('goods'). "WHERE goods_id=$goods_id");
            $res[$key]['keywords']=$this->db->getOne("SELECT keywords FROM " .$this->ecs->table('goods'). "WHERE goods_id=$goods_id");
            $res[$key]['number_color']=$number_obj->get_number_color($number_id);
        }
        return $res;
    }
    //根据订单id查询购买套餐信息
    public function bearer_info_combo($order_id){
        __load("NumberService");
        $number_obj=new NumberService();
        $sql="SELECT c.* FROM " .$this->ecs->table('order_combo')."AS c WHERE c.order_id=$order_id";
        $res=$this->db->getAll($sql);
        foreach($res AS $key=>$value){
            $goods_id=$value['goods_id'];
            $number_id=$this->db->getOne("SELECT number_id FROM " .$this->ecs->table('goods'). "WHERE goods_id=$goods_id");
            $arr=$number_obj->get_Number_info($number_id);
            foreach($arr AS $n){
                $res[$key]['numbers']=$n['numbers'];
                $res[$key]['num_start']=$n['num_start'];
            }
            $res[$key]['seat_logo']=$this->db->getOne("SELECT seat_logo FROM " .$this->ecs->table('goods'). "WHERE goods_id=$goods_id");
            $res[$key]['keywords']=$this->db->getOne("SELECT keywords FROM " .$this->ecs->table('goods'). "WHERE goods_id=$goods_id");
            $res[$key]['number_color']=$number_obj->get_number_color($number_id);
        }
        return $res;
    }
    //根据持票人id获取护照号码
    public function bearer_passport_number($bearer_id){
        $sql="SELECT passport_number FROM ".$this->ecs->table('bearer_info')." WHERE id =$bearer_id";
        return $this->db->getOne($sql);
    }
    
    //根据订单号获取订单id
    public function get_order_id($order_sn){
        return $this->db->getOne("SELECT order_id FROM ".$this->ecs->table('order_info')." WHERE order_sn ='".$order_sn."'");
    }
    
     //根据订单号获取订单id
    public function check_order_user($order_sn,$user_id){
        $sql="SELECT order_id FROM ".$this->ecs->table('order_info')." WHERE order_sn =$order_sn AND user_id=".$user_id;

        return $this->db->getOne( $sql);

    }
    
    //根据user_id查询持票人信息
    public function get_bearer_info($user_id){
        return $this->db->getAll("SELECT * FROM ".$this->ecs->table('bearer_info')." WHERE user_id =$user_id");
    }
    //--hechengbin--根据user_id查询是否为代理商 --start
    public function get_type($user_id){
        return $this->db->getOne("SELECT type FROM ".$this->ecs->table('users')." WHERE user_id='".$user_id."'");
    }
    //--hechengbin --end
    //根据订单内球票的持票人id查询持票人信息
    public function get_combo_ticket_bearer_info($bearer_id){
        return $this->db->getAll("SELECT * FROM ".$this->ecs->table('bearer_info')." WHERE id=$bearer_id");
    }
    //根据持票人id查询持票人信息
     public function bearer_info($bearer_id){
        return $this->db->getRow("SELECT * FROM ".$this->ecs->table('bearer_info')." WHERE id =$bearer_id");
    }
    //根据持票人id查询持票人信息
    public function combo_bearer_info($bearer_id){
        return $this->db->getRow("SELECT * FROM ".$this->ecs->table('bearer_info')." WHERE id =$bearer_id");
    }

    //g=根据订单号查询球票的rec_id
    public function get_rec_id($order_id){
         return $this->db->getAll("SELECT rec_id FROM ".$this->ecs->table('order_ticket')." WHERE order_id =$order_id");
    }
    //获取订单ID,根据订单ID查出对应的订单号、购票人证件人号码、购票人证件人类型、购票人姓名、购票人姓名、购票人手机号码
    public function get_rec_order_sn($order_id_num){
        return $this->db->getRow("SELECT od.order_id,od.order_sn,od.email,ad.consignee,ad.mobile,od.user_id,ad.card_type,
            ad.card_num FROM sk_order_info as od ,sk_user_address as ad 
            WHERE od.order_id =$order_id_num AND od.user_id=ad.user_id AND od.address_id=ad.address_id GROUP BY ad.address_id DESC");
    }
    //根据订单ID查出包含的所有门票
    public function get_rec_order_menpiao_list($order_id_num){
        return $this->db->getAll("select ge.id,ot.code,ge.game_name,nb.num_name,nb.num_start,nb.numbers,c.color_value,c.is_color,c.color_name,gs.seat_logo,gs.keywords,c.color_name,r.region_name,ot.goods_number,
            ot.goods_name,ot.goods_price from sk_order_ticket as ot, sk_goods as gs ,sk_color_manage as c ,sk_pitch as p ,sk_region as r ,
            sk_game as ge, sk_number as nb where ot.order_id=$order_id_num 
                AND ot.goods_id=gs.goods_id AND ge.id=gs.game_id AND nb.id=gs.number_id AND c.color_id=nb.color_id AND nb.pitch_id=p.id AND p.region_id=r.region_id AND ge.is_insurance=1");
    }
    //根据订单ID查询套餐门票
    public function get_order_combo_menpiao($order_id_num){
//        return $this->db->getAll("SELECT oc.*,g.*,ge.* FROM sk_order_combo as oc,sk_goods as g,sk_game as ge where oc.order_id=$order_id_num AND oc.goods_id=g.goods_id AND oc.game_id=ge.id");
        return $this->db->getAll("select co.*,ge.id,ge.game_name,nb.num_name,nb.num_start,nb.numbers,c.color_value,c.is_color,c.color_name,gs.seat_logo,gs.keywords,c.color_name,r.region_name,oc.goods_number,
            oc.goods_name,oc.goods_price from sk_combo as co, sk_order_combo as oc, sk_goods as gs ,sk_color_manage as c ,sk_pitch as p ,sk_region as r ,
            sk_game as ge, sk_number as nb where oc.order_id=$order_id_num
                AND oc.combo_id=co.combo_id AND oc.goods_id=gs.goods_id AND ge.id=gs.game_id AND nb.id=gs.number_id AND c.color_id=nb.color_id AND nb.pitch_id=p.id AND p.region_id=r.region_id AND ge.is_insurance=1");
    }
    //根据门票code更新数据库的is_pay_bx字段，is_pay_bx=1 表示需要购买保险
    public function set_pay_bx($tk_code){
        $sql="update sk_order_ticket set is_pay_bx=1 where code=$tk_code";
        $this->db->getAll($sql);
    }
    //根据门票code查出：票务编码、票额、赛事名称(票务使用时间)、比赛日期、比赛场次
    public function get_menpiao_messages_ticket($order_id_num){
        
            $res="SELECT
	ot.code,
	ge.game_name,
	nb.num_name,
	nb.num_start,
        ot.goods_price
FROM
	sk_order_ticket AS ot,
	sk_goods AS gs,
	sk_color_manage AS c,
	sk_pitch AS p,
	sk_region AS r,
	sk_game AS ge,
	sk_number AS nb
WHERE
	ot.order_id = $order_id_num
AND ot.goods_id = gs.goods_id
AND ge.id = gs.game_id
AND nb.id = gs.number_id
AND c.color_id = nb.color_id
AND nb.pitch_id = p.id
AND p.region_id = r.region_id
AND ge.is_insurance = 1
AND ot.is_pay_bx=1";
            
         
         return $this->db->getAll($res);
    }
    //根据门票code查出：票务编码、票额、赛事名称(票务使用时间)、比赛日期、比赛场次
    public function get_menpiao_messages_combo($order_id_num){

        $res="SELECT
	ot.code,
	ge.game_name,
	nb.num_name,
	nb.num_start,
        ot.goods_price
FROM
	sk_order_combo AS ot,
	sk_goods AS gs,
	sk_color_manage AS c,
	sk_pitch AS p,
	sk_region AS r,
	sk_game AS ge,
	sk_number AS nb
WHERE
	ot.order_id = $order_id_num
AND ot.goods_id = gs.goods_id
AND ge.id = gs.game_id
AND nb.id = gs.number_id
AND c.color_id = nb.color_id
AND nb.pitch_id = p.id
AND p.region_id = r.region_id
AND ge.is_insurance = 1
AND ot.is_pay_bx=1";


        return $this->db->getAll($res);
    }
    
    public function get_game_info($order_id_num){
        return $this->db->getRow("select ge.game_name,ge.game_logo from sk_order_ticket as ot, sk_goods as gs ,
            sk_game as ge where ot.order_id=$order_id_num 
                AND ot.goods_id=gs.goods_id AND ge.id=gs.game_id GROUP BY ge.id");
    }
    public function remove($bearer_id){
         return $this->db->query("DELETE FROM ".$this->ecs->table('bearer_info')." WHERE id =$bearer_id");
    }
//    public function remove_order_combo($order_id,$bearer_id){
//        return $this->db->query("DELETE FROM ".$this->ecs->table('bearer_info')." WHERE bearer_id=(\"SELECT bearer_id FROM \" . $this->ecs->table('order_combo').\" WHERE order_id='\".$order_id.\"' AND bearer_id='\".$bearer_id.\"'\";)");
//
//    }
//    public function remove_order_ticket($order_id,$bearer_id){
//        return $this->db->query("DELETE FROM ".$this->ecs->table('bearer_info')." WHERE bearer_id=(\"SELECT bearer_id FROM \" . $this->ecs->table('order_combo').\" WHERE order_id='\".$order_id.\"' AND bearer_id='\".$bearer_id.\"'\";)");
//
//    }
    public function get_pay_status($order_id,$user_id){
         return $this->db->getOne("SELECT pay_status FROM ".$this->ecs->table('order_info')." WHERE order_id =$order_id  AND user_id=$user_id");
    }
    public function get_email_mobile($order_id,$user_id){
        return $this->db->getOne("SELECT email,mobile FROM ".$this->ecs->table('order_info')." WHERE order_id =$order_id  AND user_id=$user_id");
    }
    public function get_order_email($order_id,$user_id){
        return $this->db->getOne("SELECT email FROM ".$this->ecs->table('order_info')." WHERE order_id =$order_id  AND user_id=$user_id");
    }
    //获取当前用户的手机和邮箱
    public function query_list($user_id){
        return $this->db->getRow("SELECT mobile_phone,email FROM ".$this->ecs->table('users')." WHERE user_id=".$user_id);
    }
    //根据rec_id查询球票信息
//     public function get_code($rec_id){
//         $sql="SELECT t.code  FROM ".$this->ecs->table('order_ticket')."AS t,".$this->ecs->table('goods')."AS g,"
//                 .$this->ecs->table('game')."AS game,".$this->ecs->table('number')."AS n"." WHERE t.rec_id =$rec_id  AND t.goods_id=g.goods_id AND g.game_id = game.id AND g.number_id = n.id";
//         return $this->db->getOne($sql);
//    }
//        public function get_goods_price($rec_id){
//         $sql="SELECT t.goods_price FROM ".$this->ecs->table('order_ticket')."AS t,".$this->ecs->table('goods')."AS g,"
//                 .$this->ecs->table('game')."AS game,".$this->ecs->table('number')."AS n"." WHERE t.rec_id =$rec_id  AND t.goods_id=g.goods_id AND g.game_id = game.id AND g.number_id = n.id";
//         return $this->db->getOne($sql);
//    }
//        public function get_game_name($rec_id){
//         $sql="SELECT game.game_name  FROM ".$this->ecs->table('order_ticket')."AS t,".$this->ecs->table('goods')."AS g,"
//                 .$this->ecs->table('game')."AS game,".$this->ecs->table('number')."AS n"." WHERE t.rec_id =$rec_id  AND t.goods_id=g.goods_id AND g.game_id = game.id AND g.number_id = n.id";
//         return $this->db->getOne($sql);
//    }
//        public function get_num_start($rec_id){
//         $sql="SELECT n.num_start  FROM ".$this->ecs->table('order_ticket')."AS t,".$this->ecs->table('goods')."AS g,"
//                 .$this->ecs->table('game')."AS game,".$this->ecs->table('number')."AS n"." WHERE t.rec_id =$rec_id  AND t.goods_id=g.goods_id AND g.game_id = game.id AND g.number_id = n.id";
//         return $this->db->getOne($sql);
//    }
//        public function get_num_name($rec_id){
//         $sql="SELECT n.num_name FROM ".$this->ecs->table('order_ticket')."AS t,".$this->ecs->table('goods')."AS g,"
//                 .$this->ecs->table('game')."AS game,".$this->ecs->table('number')."AS n"." WHERE t.rec_id =$rec_id  AND t.goods_id=g.goods_id AND g.game_id = game.id AND g.number_id = n.id";
//         return $this->db->getOne($sql);
//    }
//     public function get_order_info($order_id){
//         return $this->db->getRow("SELECT  order_sn,consignee,card_type,card_num,mobile FROM ".$this->ecs->table('order_info')." WHERE order_id =$order_id");
//    }
     public function get_ticket_info($rec_id){
         $sql="SELECT t.code,t.goods_price,game.game_name,n.num_start,n.num_name FROM ".$this->ecs->table('order_ticket')."AS t,".$this->ecs->table('goods')."AS g,"
                 .$this->ecs->table('game')."AS game,".$this->ecs->table('number')."AS n"." WHERE t.rec_id =$rec_id  AND t.goods_id=g.goods_id AND g.game_id = game.id AND g.number_id = n.id";
         return $this->db->getRow($sql);
    }
    public function bearer_info_order_id($order_id){
        return $this->db->getAll("SELECT * FROM " .$this->ecs->table('bearer_info')." WHERE order_id=$order_id");
    }
    
    public function bearer_info_id($id){
        return $this->db->getRow("SELECT * FROM " .$this->ecs->table('bearer_info')." WHERE id=$id");
    }
    
    public function bearer_info_page($order_id){
           /* 记录总数 */
        $sql = "SELECT COUNT(*) FROM " .$this->ecs->table('bearer_info')." WHERE order_id=$order_id";
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        /* 分页大小 */
        $filter = page_and_size($filter);
        
        $sql = "SELECT * FROM " .$this->ecs->table('bearer_info')." WHERE order_id=$order_id  LIMIT " . $filter['start'] . ",$filter[page_size]";
        $row = $this->db->getAll($sql);
        return array('bearer_info' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
    }

    public function get_order_bearer_id($order_id){
        $sql = "SELECT bearer_id FROM ".$this->ecs->table('order_ticket')."WHERE order_id='".$order_id."'";
        $res = $this->db->getAll($sql);
        $sql1 = "SELECT bearer_id FROM ".$this->ecs->table('order_combo')."WHERE order_id='".$order_id."'";
        $result = $this->db->getAll($sql1);
        $arr = array_merge($res,$result);
        foreach($arr as $key=>$value){
            if($value['bearer_id'] == 0 ){
                return 0;
            }else{
                return 1;
            }
        }
    }
}
?>
