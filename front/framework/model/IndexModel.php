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

class IndexModel extends Model {
    
    public function freeze(){
        $sql = "SELECT state FROM ".$this->ecs->table('agent')." WHERE id = '$_SESSION[agent_id]'";
        return $this->db->getOne($sql);
    }
    
    //put your code here
    public function agent_user_pwd($id){
        $sql = "SELECT user_pwd FROM ".$this->ecs->table('agent')." WHERE id = '$id'";
        return $this->db->getOne($sql);
    }
    
    public function update_agent_pwd($data,$id){
        return $this->db->autoExecute($this->ecs->table("agent"), $data, 'UPDATE'," id= '$id'");
    }
    
    public function get_pitch_list(){
       /* 查询条件 */
       $filter = array();
       $filter['agent_id'] = empty($_SESSION['agent_id']) ? 0 : intval($_SESSION['agent_id']);
       $where = (!empty($filter['agent_id'])) ? " WHERE agent_id = '$filter[agent_id]' " : '';
        /* 记录总数 */
        $sql = "SELECT COUNT(*) FROM ".$this->ecs->table("agent_order").$where;
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);
        /* 分页大小 */
        $filter = page_and_size($filter);
        
        $sql = "SELECT * FROM ".$this->ecs->table("agent_order").$where." ORDER BY add_time DESC LIMIT " . $filter['start'] . ",$filter[page_size]";
        $pitch_order = $this->db->getAll($sql);//代理商所有球票
        
        foreach ($pitch_order as $keys=>$pitch_order_info){
//           $sql = "SELECT aoi.*,aoi.id AS order_info_id, g.game_name, s.sche_name, n.num_name, n.num_start, n.num_end, p.pitch_name, r.region_name, ga.attr_value, ga.attr_price FROM ".$this->ecs->table("agent_order_info")." AS aoi LEFT JOIN ".$this->ecs->table('game')." AS g ON aoi.game_id = g.id LEFT JOIN ".$this->ecs->table('schedule')." AS s ON aoi.sche_id = s.id LEFT JOIN  ".$this->ecs->table('number')." AS n ON aoi.number_id = n.id LEFT JOIN ".$this->ecs->table('pitch')." AS p ON n.pitch_id = p.id LEFT JOIN ".$this->ecs->table('region')." AS r ON p.region_id = r.region_id LEFT JOIN ".$this->ecs->table('products')." AS ps ON aoi.attr_id = ps.product_id LEFT JOIN ".$this->ecs->table('goods_attr')." AS ga ON ps.goods_attr = ga.goods_attr_id WHERE aoi.order_id = '$pitch_order_info[id]'";
            $sql = "SELECT aoi.*,aoi.id AS order_info_id, g.game_name, s.sche_name, n.num_name, n.num_start, n.num_end, p.pitch_name, r.region_name, gs.shop_price, gs.goods_name FROM ".$this->ecs->table("agent_order_info")." AS aoi LEFT JOIN ".$this->ecs->table('game')." AS g ON aoi.game_id = g.id LEFT JOIN ".$this->ecs->table('schedule')." AS s ON aoi.sche_id = s.id LEFT JOIN  ".$this->ecs->table('number')." AS n ON aoi.number_id = n.id LEFT JOIN ".$this->ecs->table('pitch')." AS p ON n.pitch_id = p.id LEFT JOIN ".$this->ecs->table('region')." AS r ON p.region_id = r.region_id LEFT JOIN ".$this->ecs->table('goods')." AS gs ON aoi.goods_id = gs.goods_id WHERE aoi.order_id = '$pitch_order_info[id]'";
           $pitch_order[$keys]["datas"] = $this->db->getAll($sql);     
        }       
        
        foreach ($pitch_order AS $p_key=>$pitch_info){
            foreach ($pitch_info['datas'] AS $key=>$pitch){
                $pitch_order[$p_key]['datas'][$key]['record'] = $this->db->getOne("SELECT count(*) FROM ".$this->ecs->table('bearer_info')." WHERE order_id = '$pitch[order_info_id]'");
                $sql = "SELECT GROUP_CONCAT(t.team_name separator ' VS ') FROM ".$this->ecs->table('num_team')." AS nt LEFT JOIN ".$this->ecs->table('teams')." AS t ON nt.team_id = t.id WHERE nt.num_id = '$pitch[number_id]'";   
                $pitch_order[$p_key]['datas'][$key]['team'] = $this->db->getOne($sql);//参赛方
                $pitch_order[$p_key]['datas'][$key]['subtotal'] = $pitch['shop_price']*$pitch['number'];
                $pitch_order[$p_key]['total'] += $pitch['shop_price']*$pitch['number'];
                $pitch_order[$p_key]['datas'][$key]['date'] = date("Y-m-d H:i:s",  local_strtotime($pitch['num_start'])).'-'.date("H:i:s",  local_strtotime($pitch['num_end']));
                $pitch_order[$p_key]['row'] = $key+1; 
            }
            $pitch_order[$p_key]['add_time'] = date("Y-m-d H:i:s",$pitch_info['add_time']);
        }   
        return array('pitch_info' => $pitch_order, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
    }
    
    public function save_bearer($bearer_info,$way,$where){
   
        return $this->db->autoExecute($this->ecs->table("bearer_info"), $bearer_info, $way, $where);
    }
    
    public function get_bearer_info(){
//        $sql = "SELECT aoi.*, g.game_name, s.sche_name, n.num_name, ga.attr_value, bi.*, bi.id AS bearer_id FROM ".$this->ecs->table('agent_order_info')." AS aoi LEFT JOIN ".$this->ecs->table('game')." AS g ON aoi.game_id = g.id LEFT JOIN ".$this->ecs->table('schedule')." AS s ON aoi.sche_id = s.id LEFT JOIN ".$this->ecs->table('number')." AS n ON aoi.number_id = n.id LEFT JOIN ".$this->ecs->table('products')." AS p ON aoi.attr_id = p.product_id LEFT JOIN ".$this->ecs->table('goods_attr')." AS ga ON p.goods_attr = ga.goods_attr_id LEFT JOIN ".$this->ecs->table('bearer_info')." AS bi ON aoi.id = bi.order_id WHERE aoi.id = '$order_info_id'";
        /* 查询条件 */
        $filter = array();
        $filter['order_info_id'] = empty($_REQUEST['order_info_id']) ? 0 : intval($_REQUEST['order_info_id']);
        $where = (!empty($filter['order_info_id'])) ? " WHERE aoi.id = '$filter[order_info_id]' " : '';
        /* 记录总数 */
        $sql = "SELECT COUNT(*) FROM ".$this->ecs->table('agent_order_info')." AS aoi LEFT JOIN ".$this->ecs->table('game')." AS g ON aoi.game_id = g.id LEFT JOIN ".$this->ecs->table('schedule')." AS s ON aoi.sche_id = s.id LEFT JOIN ".$this->ecs->table('number')." AS n ON aoi.number_id = n.id LEFT JOIN ".$this->ecs->table('goods')." AS gs ON aoi.goods_id = gs.goods_id LEFT JOIN ".$this->ecs->table('bearer_info')." AS bi ON aoi.id = bi.order_id".$where;
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        /* 分页大小 */
        $filter = page_and_size($filter);
        $sql = "SELECT aoi.*, g.game_name, s.sche_name, n.num_name, gs.goods_name, bi.*, bi.id AS bearer_id FROM ".$this->ecs->table('agent_order_info')." AS aoi LEFT JOIN ".$this->ecs->table('game')." AS g ON aoi.game_id = g.id LEFT JOIN ".$this->ecs->table('schedule')." AS s ON aoi.sche_id = s.id LEFT JOIN ".$this->ecs->table('number')." AS n ON aoi.number_id = n.id LEFT JOIN ".$this->ecs->table('goods')." AS gs ON aoi.goods_id = gs.goods_id LEFT JOIN ".$this->ecs->table('bearer_info')." AS bi ON aoi.id = bi.order_id ".$where." LIMIT " . $filter['start'] . ",$filter[page_size]";
        $bearer_info = $this->db->getAll($sql);
        return array('bearer_info' => $bearer_info, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
    }
    
    public function get_bearer_list(){
//        $sql = "SELECT bi.*, g.game_name, s.sche_name, n.num_name, ga.attr_value FROM ".$this->ecs->table('bearer_info')." AS bi LEFT JOIN ".$this->ecs->table('agent_order_info')." AS aoi ON bi.order_id = aoi.id LEFT JOIN ".$this->ecs->table('game')." AS g ON aoi.game_id = g.id LEFT JOIN ".$this->ecs->table('schedule')." AS s ON aoi.sche_id = s.id LEFT JOIN ".$this->ecs->table('number')." AS n ON aoi.number_id = n.id LEFT JOIN ".$this->ecs->table('products')." AS p ON aoi.attr_id = p.product_id LEFT JOIN ".$this->ecs->table('goods_attr')." AS ga ON p.goods_attr = ga.goods_attr_id WHEREi.agent_id = '$_SESSION[agent_id]' ORDER BY bi.id DESC";
       $result = get_filter();
       if($result === false){
            /* 查询条件 */
            $filter = array();
            $filter['agent_id'] = empty($_SESSION['agent_id']) ? 0 : intval($_SESSION['agent_id']);
            $where = (!empty($filter['agent_id'])) ? " WHERE bi.agent_id = '$filter[agent_id]' " : '';
            $filter['game_id'] = empty($_REQUEST['game_id']) ? 0 : intval($_REQUEST['game_id']);
            $filter['sche_id'] = empty($_REQUEST['sche_id']) ? 0 : intval($_REQUEST['sche_id']);
            $filter['number_id'] = empty($_REQUEST['number_id']) ? 0 : intval($_REQUEST['number_id']);
            $filter['cn_customer_name'] = empty($_REQUEST['cn_customer_name']) ? '' : $_REQUEST['cn_customer_name'];
            if($filter['game_id']){
               $where .= " AND aoi.game_id = '$filter[game_id]'"; 
            }
            if($filter['sche_id']){
               $where .= " AND aoi.sche_id = '$filter[sche_id]'"; 
            }
            if($filter['number_id']){
               $where .= " AND aoi.number_id = '$filter[number_id]'"; 
            }
            if($filter['cn_customer_name']){
               $where .= " AND bi.cn_customer_name like '%$filter[cn_customer_name]%'"; 
            }
            /* 记录总数 */
             $sql = "SELECT COUNT(*) FROM ".$this->ecs->table('bearer_info')." AS bi LEFT JOIN ".$this->ecs->table('agent_order_info')." AS aoi ON bi.order_id = aoi.id LEFT JOIN ".$this->ecs->table('game')." AS g ON aoi.game_id = g.id LEFT JOIN ".$this->ecs->table('schedule')." AS s ON aoi.sche_id = s.id LEFT JOIN ".$this->ecs->table('number')." AS n ON aoi.number_id = n.id LEFT JOIN ".$this->ecs->table('goods')." AS gs ON aoi.goods_id = gs.goods_id ".$where;
      
            $filter['record_count'] = $GLOBALS['db']->getOne($sql);
            /* 分页大小 */
            $filter = page_and_size($filter);
            $sql = "SELECT bi.*, g.game_name, s.sche_name, n.num_name, gs.goods_name FROM ".$this->ecs->table('bearer_info')." AS bi LEFT JOIN ".$this->ecs->table('agent_order_info')." AS aoi ON bi.order_id = aoi.id LEFT JOIN ".$this->ecs->table('game')." AS g ON aoi.game_id = g.id LEFT JOIN ".$this->ecs->table('schedule')." AS s ON aoi.sche_id = s.id LEFT JOIN ".$this->ecs->table('number')." AS n ON aoi.number_id = n.id LEFT JOIN ".$this->ecs->table('goods')." AS gs ON aoi.goods_id = gs.goods_id ".$where." ORDER BY bi.id DESC LIMIT " . $filter['start'] . ",$filter[page_size]";
            set_filter($filter, $sql);
       }else{
           $sql    = $result['sql'];
           $filter = $result['filter'];
       }
       $bearer_info = $this->db->getAll($sql);
       return array('bearer_info' => $bearer_info, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);       
    }
    
    public function get_bearer_infos($bearer_id){
        $sql = "SELECT * FROM ".$this->ecs->table('bearer_info')." WHERE id ='$bearer_id'";
        return $this->db->getRow($sql);
    }
    
    public function ticket_bearer($order_info_id){
        $ticket_bearer = array();
        $ticket_bearer['ticket'] = $this->db->getOne("SELECT number FROM ".$this->ecs->table('agent_order_info')." WHERE id = '$order_info_id'");
        $ticket_bearer['bearer'] = $this->db->getOne("SELECT count(*) FROM ".$this->ecs->table('bearer_info')." WHERE order_id = '$order_info_id'");
        return $ticket_bearer;
    }
    
    public function submit_state($data){        
        foreach ($data as $id){
           $bearer_info = array("submit"=>2,'sub_time'=>time());         
           $where = "id = '$id'";
           $res = $this->db->autoExecute($this->ecs->table("bearer_info"), $bearer_info, "UPDATE", $where);
        }
        return $res;
    }
    
    public function submit_state_update($bearer_info,$bearer_id){
        return $this->db->autoExecute($this->ecs->table("bearer_info"), $bearer_info, "UPDATE", " id = '$bearer_id'");
    }
    
    public function get_game(){
        return $this->db->getAll("SELECT * FROM ".$this->ecs->table("game"));
    }
    
    public function get_search_list($data){     
        if($data['game_id']){
           $where .= " AND aoi.game_id = '$data[game_id]'"; 
        }
        if($data['sche_id']){
           $where .= " AND aoi.sche_id = '$data[sche_id]'"; 
        }
        if($data['number_id']){
           $where .= " AND aoi.number_id = '$data[number_id]'"; 
        }
        if($data['cn_customer_name']){
           $where .= " AND bi.cn_customer_name ="."'$data[cn_customer_name]'"; 
        }
        $sql = "SELECT bi.*, g.game_name, s.sche_name, n.num_name, ga.attr_value FROM ".$this->ecs->table('bearer_info')." AS bi LEFT JOIN 
            ".$this->ecs->table('agent_order_info')." AS aoi ON bi.order_id = aoi.id LEFT JOIN 
                ".$this->ecs->table('game')." AS g ON aoi.game_id = g.id LEFT JOIN 
                    ".$this->ecs->table('schedule')." AS s ON aoi.sche_id = s.id LEFT JOIN 
                        ".$this->ecs->table('number')." AS n ON aoi.number_id = n.id LEFT JOIN 
                            ".$this->ecs->table('products')." AS p ON aoi.attr_id = p.product_id LEFT JOIN 
                                ".$this->ecs->table('goods_attr')." AS ga ON p.goods_attr = ga.goods_attr_id WHERE bi.agent_id = '$_SESSION[agent_id]' ".$where." ORDER BY bi.id DESC";
        
        return $bearer_info = $this->db->getAll($sql);
    }
}
?>
