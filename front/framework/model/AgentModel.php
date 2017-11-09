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

class AgentModel extends Model {

    //put your code here
    public function add($data) {
        $data['user_pwd'] = md5($data['user_pwd']);
        return $this->db->autoExecute($this->ecs->table("agent"), $data, 'INSERT');
    }

    public function update($data) {
        $id = $data['id'];
        unset($data['id']);
        if (empty($data['user_pwd'])) {
            unset($data['user_pwd']);
        } else {
            $data['user_pwd'] = md5($data['user_pwd']);
        }
        return $this->db->autoExecute($this->ecs->table("agent"), $data, 'UPDATE', " id=$id");
    }

    public function get_All() {      
        $sql = "SELECT id,user_name,agent,satrap,bank_card,state FROM " . $this->ecs->table("agent");
        return $this->db->getAll($sql);  
    }
    
    public function get_All_query() {
        /* 记录总数 */
        $sql = "SELECT COUNT(*) FROM " .$GLOBALS['ecs']->table('agent');
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        /* 分页大小 */
        $filter = page_and_size($filter);
        
        $sql = "SELECT id,user_name,agent,satrap,bank_card,state,email FROM " . $this->ecs->table("agent")." LIMIT " . $filter['start'] . ",$filter[page_size]";
        $agent = $this->db->getAll($sql);  
        return array('agent' => $agent, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
    }

    public function get($id) {
        $sql = "SELECT * FROM " . $this->ecs->table("agent") . " WHERE id=$id";
        return $this->db->getRow($sql);
    }
    
    public function update_agent_status($data,$id){
      
       return $this->db->autoExecute($this->ecs->table("agent"), $data, 'UPDATE'," id = '$id'");
    }
    
    public function remove($id) {
        $sql = "DELETE FROM" . $this->ecs->table("agent") . " WHERE id=$id";
        return $this->db->query($sql);
    }

    public function remove_batch($data) {
        foreach ($data as $key => $id) {
            $sql = "DELETE FROM" . $this->ecs->table("agent") . " WHERE id=$id";
            $res = $this->db->query($sql);
        }
        return $res;
    }

    public function add_order($date) {
        if ($this->db->autoExecute($this->ecs->table("agent_order"), $date, 'INSERT')) {
            return $this->db->insert_id();
        } else {
            return false;
        }
    }

    public function add_order_info($data) {


        $id_array = array(); //新增明细id 缓存池
        //验证订单库存
        foreach ($data as $value) {
            //$db_attr_number = $this->get_attr_number($value['attr_id']);
            $db_attr_number = $this->get_attr_number($value['goods_id']);
            if ($db_attr_number < $value['number']) {
                $sql = "DELETE FROM" . $this->ecs->table("agent_order") . " WHERE id = " . $value['order_id'];
                $this->db->query($sql);
                return false;
            }
        }

        foreach ($data as $value) {
//            $sql = "UPDATE " . $this->ecs->table("products") . " SET product_number = product_number-" . $value['number'] . "  WHERE product_id =" . $value['attr_id'];
            $sql = "UPDATE " . $this->ecs->table("goods") . " SET goods_number = goods_number-" . $value['number'] . "  WHERE goods_id =" . $value['goods_id'];
            $res = $this->db->query($sql);
            if ($res) {
                $flag = $this->db->autoExecute($this->ecs->table("agent_order_info"), $value, 'INSERT');
                if ($flag) {
                    $id_array[] = $this->db->insert_id();
                } else {
                    
                    if (count($id_array)>0) {
                        foreach ($id_array as $key => $id) {
                            $temp_value = $data[$key];
//                            $sql = "UPDATE " . $this->ecs->table("products") . " SET product_number = product_number+" . $temp_value['number'] . "  WHERE product_id =" . $temp_value['attr_id'];
                              $sql = "UPDATE " . $this->ecs->table("goods") . " SET goods_number = goods_number+" . $temp_value['number'] . "  WHERE goods_id =" . $temp_value['goods_id'];
                            $res = $this->db->query($sql);
                        }
                        $sql = "DELETE FROM" . $this->ecs->table("agent_order_info") . " WHERE id in ( " . implode(",", $id_array) . ")";
                        $this->db->query($sql);
                        return false;
                    }
                }
            } else {
                if (count($id_array)>0) {
                    foreach ($id_array as $key => $id) {
                        $temp_value = $data[$key];
//                        $sql = "UPDATE " . $this->ecs->table("products") . " SET product_number = product_number+" . $temp_value['number'] . "  WHERE product_id =" . $temp_value['attr_id'];
                        $sql = "UPDATE " . $this->ecs->table("goods") . " SET goods_number = goods_number+" . $temp_value['number'] . "  WHERE goods_id =" . $temp_value['goods_id'];
                        $res = $this->db->query($sql);
                    }
                    $sql = "DELETE FROM" . $this->ecs->table("agent_order_info") . " WHERE id in ( " . implode(",", $id_array) . ")";
                    $this->db->query($sql);
                    return false;
                }
            }
        }
        return true;
    }
    private function get_attr_number($arrt_id) {
        $sql = "SELECT goods_number FROM " . $this->ecs->table("goods") . " WHERE goods_id=$arrt_id";
        return $this->db->getOne($sql);
    }
    public function get_order(){
        if(!empty($_POST['agent_name'])){
            $sql = "SELECT COUNT(*) FROM ".$this->ecs->table('agent_order')." AS ao LEFT JOIN ".$this->ecs->table('agent')." AS a ON ao.agent_id = a.id WHERE a.agent LIKE '%{$_POST['agent_name']}%'";
        }else{
            //票号录入状态
            $where = ' WHERE 1';
            $code_status = isset($_POST['code_status']) ? intval($_POST['code_status']) : -1;
            switch ($code_status) {
                case 0:
                    $where .= " AND b.ticket_code = ''";
                    $sql = "SELECT COUNT(DISTINCT ao.id) FROM ".$this->ecs->table('agent_order')." AS ao LEFT JOIN ".$this->ecs->table('agent_order_info')." AS b ON b.order_id = ao.id" . $where;
                    break;
                case 1:
                    $where .= " AND b.ticket_code != ''";
                    $sql = "SELECT COUNT(DISTINCT ao.id) FROM ".$this->ecs->table('agent_order')." AS ao LEFT JOIN ".$this->ecs->table('agent_order_info')." AS b ON b.order_id = ao.id" . $where;
                    break;
                default:
                    $sql = "SELECT COUNT(*) FROM ".$this->ecs->table('agent_order')." AS ao LEFT JOIN ".$this->ecs->table('agent')." AS a ON ao.agent_id = a.id"; 
                    break;
            }
        }
        $filter['record_count'] = $this->db->getOne($sql); 
        /* 分页大小 */
        $filter = page_and_size($filter);
        if(!empty($_POST['agent_name'])){
            $filter['agent_name']=$_POST['agent_name'];
            $sql = "SELECT ao.*, a.agent FROM ".$this->ecs->table('agent_order')." AS ao LEFT JOIN ".$this->ecs->table('agent')." AS a ON ao.agent_id = a.id WHERE a.agent LIKE '%{$_POST['agent_name']}%' ORDER BY ao.add_time DESC LIMIT " . $filter['start'] . ",$filter[page_size]";
        }else{
            if(isset($_POST['code_status'])){
                $sql = "SELECT ao.*, a.agent FROM ".$this->ecs->table('agent_order')." AS ao " .
                       " LEFT JOIN ".$this->ecs->table('agent')." AS a ON ao.agent_id = a.id " .
                       " LEFT JOIN ".$this->ecs->table('agent_order_info')." AS b ON b.order_id = ao.id " . $where .
                       " GROUP BY ao.id " .
                       " ORDER BY ao.add_time DESC " .
                       " LIMIT " . $filter['start'] . ",$filter[page_size]";
            }else{
                $sql = "SELECT ao.*, a.agent FROM ".$this->ecs->table('agent_order')." AS ao LEFT JOIN ".$this->ecs->table('agent')." AS a ON ao.agent_id = a.id ORDER BY ao.add_time DESC LIMIT " . $filter['start'] . ",$filter[page_size]";
            }
        }

        $agent_order_all = $this->db->getAll($sql); //获取订单   
    
        $agent_data = array();//预定义列表信息数组
        foreach ($agent_order_all as $keys=>$agent_order_list_data){           
           $sql = "SELECT aoi.*, aoi.id AS order_info_id, g.game_name, s.sche_name, n.*, p.pitch_name, r.region_name FROM ".$this->ecs->table('agent_order_info')." AS aoi LEFT JOIN ".$this->ecs->table('game')." AS g ON aoi.game_id = g.id LEFT JOIN ".$this->ecs->table('number')." AS n ON aoi.number_id = n.id LEFT JOIN ".$this->ecs->table('schedule')." AS s ON aoi.sche_id = s.id LEFT JOIN ".$this->ecs->table('pitch')." AS p ON n.pitch_id = p.id LEFT JOIN ".$this->ecs->table('region')." AS r ON p.region_id = r.region_id WHERE order_id = '$agent_order_list_data[id]'";
           $agent_order_all[$keys]['datas'] = $this->db->getAll($sql);//列表信息    
           $agent_data['datas'] = $this->db->getAll($sql);     
           //获取作为类型
           foreach ($agent_data['datas'] as $key=>$data){
                //$sql = "SELECT ga.attr_value FROM ".$this->ecs->table('products')." AS pr LEFT JOIN ".$this->ecs->table('goods_attr')." AS ga ON pr.goods_attr = ga.goods_attr_id WHERE pr.product_id = '$data[attr_id]'";
                $agent_order_all[$keys]['datas'][$key]['record'] = $this->db->getOne("SELECT count(*) FROM ".$this->ecs->table('bearer_info')." WHERE order_id = '$data[order_info_id]'");
                $sql = "SELECT goods_name FROM " . $this->ecs->table('goods') ." WHERE goods_id = '$data[goods_id]'";
                //$agent_order_all[$keys]['datas'][$key]['attr_value'] =  $this->db->getOne($sql);  
                $agent_order_all[$keys]['datas'][$key]['goods_name'] =  $this->db->getOne($sql);
                $agent_order_all[$keys]['datas'][$key]['agent'] = $agent_order_list_data['agent'];
                $agent_order_all[$keys]['datas'][$key]['date'] = date("H:i:s",local_strtotime($data['num_start']))."-".date("H:i:s",local_strtotime($data['num_end']));
                $agent_order_all[$keys]['row'] = $key+1;              
           }
           $agent_order_all[$keys]['add_time'] = date("Y-m-d H:i:s",$agent_order_list_data['add_time']);
        }
        return array('agent_order' => $agent_order_all, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
           
    }
 
    public function pitch_info($agent_id){
        /* 记录总数 */
        $sql = "SELECT COUNT(*) FROM ".$this->ecs->table("agent_order")." AS ao LEFT JOIN ".$this->ecs->table("agent")." AS a ON ao.agent_id = a.id WHERE agent_id = '$agent_id'";
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        /* 分页大小 */
        $filter = page_and_size($filter);
        
       $sql = "SELECT ao.*, a.agent FROM ".$this->ecs->table("agent_order")." AS ao LEFT JOIN ".$this->ecs->table("agent")." AS a ON ao.agent_id = a.id WHERE agent_id = '$agent_id' ORDER BY add_time DESC LIMIT " . $filter['start'] . ",$filter[page_size]";
       $pitch_order = $this->db->getAll($sql);//代理商所有球票     
        foreach ($pitch_order as $keys=>$pitch_order_info){
           //$sql = "SELECT aoi.*,aoi.id AS order_info_id, g.game_name, s.sche_name, n.num_name, n.num_start, n.num_end, p.pitch_name, r.region_name, ga.attr_value, ga.attr_price FROM ".$this->ecs->table("agent_order_info")." AS aoi LEFT JOIN ".$this->ecs->table('game')." AS g ON aoi.game_id = g.id LEFT JOIN ".$this->ecs->table('schedule')." AS s ON aoi.sche_id = s.id LEFT JOIN  ".$this->ecs->table('number')." AS n ON aoi.number_id = n.id LEFT JOIN ".$this->ecs->table('pitch')." AS p ON n.pitch_id = p.id LEFT JOIN ".$this->ecs->table('region')." AS r ON p.region_id = r.region_id LEFT JOIN ".$this->ecs->table('products')." AS ps ON aoi.attr_id = ps.product_id LEFT JOIN ".$this->ecs->table('goods_attr')." AS ga ON ps.goods_attr = ga.goods_attr_id WHERE aoi.order_id = '$pitch_order_info[id]'";
           $sql = "SELECT aoi.*,aoi.id AS order_info_id, g.game_name, s.sche_name, n.num_name, n.num_start, n.num_end, p.pitch_name, r.region_name, gs.goods_name, gs.shop_price FROM ".$this->ecs->table("agent_order_info")." AS aoi LEFT JOIN ".$this->ecs->table('game')." AS g ON aoi.game_id = g.id LEFT JOIN ".$this->ecs->table('schedule')." AS s ON aoi.sche_id = s.id LEFT JOIN  ".$this->ecs->table('number')." AS n ON aoi.number_id = n.id LEFT JOIN ".$this->ecs->table('pitch')." AS p ON n.pitch_id = p.id LEFT JOIN ".$this->ecs->table('region')." AS r ON p.region_id = r.region_id LEFT JOIN ".$this->ecs->table('goods')." AS gs ON aoi.goods_id = gs.goods_id WHERE aoi.order_id = '$pitch_order_info[id]'";
           $pitch_order[$keys]["datas"] = $this->db->getAll($sql);     
        }       
        
        foreach ($pitch_order AS $p_key=>$pitch_info){
            foreach ($pitch_info['datas'] AS $key=>$pitch){                
                $pitch_order[$p_key]['datas'][$key]['record'] = $this->db->getOne("SELECT count(*) FROM ".$this->ecs->table('bearer_info')." WHERE order_id = '$pitch[order_info_id]' AND submit = '2'");
                $sql = "SELECT GROUP_CONCAT(t.team_name separator ' VS ') FROM ".$this->ecs->table('num_team')." AS nt LEFT JOIN ".$this->ecs->table('teams')." AS t ON nt.team_id = t.id WHERE nt.num_id = '$pitch[number_id]'";   
                $pitch_order[$p_key]['datas'][$key]['team'] = $this->db->getOne($sql);//参赛方
                $pitch_order[$p_key]['datas'][$key]['date'] = date("H:i:s",  local_strtotime($pitch['num_start'])).'-'.date("H:i:s",  local_strtotime($pitch['num_end']));
                $pitch_order[$p_key]['datas'][$key]['subtotal'] = $pitch['shop_price']*$pitch['number'];
                $pitch_order[$p_key]['total'] += $pitch['shop_price']*$pitch['number'];
                $pitch_order[$p_key]['row'] = $key+1;   
            }
            $pitch_order[$p_key]['add_time'] = date("Y-m-d H:i:s",$pitch_info['add_time']);
        }   
        return array('pitch_order' => $pitch_order, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
    }
    
    public function get_pitch_bearer(){

       /* 查询条件 */
       $filter = array();
       $filter['id'] = empty($_REQUEST['id']) ? 0 : intval($_REQUEST['id']);
       $where = (!empty($filter['id'])) ? " AND aoi.id = '$filter[id]' " : '';
        /* 记录总数 */
        $sql = "SELECT COUNT(*) FROM ".$this->ecs->table('agent_order_info')." AS aoi LEFT JOIN ".$this->ecs->table('game')." AS g ON aoi.game_id = g.id LEFT JOIN ".$this->ecs->table('schedule')." AS s ON aoi.sche_id = s.id LEFT JOIN ".$this->ecs->table('number')." AS n ON aoi.number_id = n.id LEFT JOIN ".$this->ecs->table('goods')." AS gs ON aoi.goods_id = gs.goods_id LEFT JOIN ".$this->ecs->table('bearer_info')." AS bi ON aoi.id = bi.order_id WHERE bi.submit = '2'".$where;
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        /* 分页大小 */
        $filter = page_and_size($filter);
        $sql = "SELECT aoi.*, g.game_name, s.sche_name, n.num_name, gs.goods_name, bi.*, bi.id AS bearer_info FROM ".$this->ecs->table('agent_order_info')." AS aoi LEFT JOIN ".$this->ecs->table('game')." AS g ON aoi.game_id = g.id LEFT JOIN ".$this->ecs->table('schedule')." AS s ON aoi.sche_id = s.id LEFT JOIN ".$this->ecs->table('number')." AS n ON aoi.number_id = n.id LEFT JOIN ".$this->ecs->table('goods')." AS gs ON aoi.goods_id = gs.goods_id LEFT JOIN ".$this->ecs->table('bearer_info')." AS bi ON aoi.id = bi.order_id WHERE bi.submit = '2'".$where." LIMIT " . $filter['start'] . ",$filter[page_size]";
        $bearer_info = $this->db->getAll($sql);
        return array('bearer_info' => $bearer_info, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
    }
    
    public function bearer_info($bearer_id){
        $sql = "SELECT * FROM ".$this->ecs->table('bearer_info')." WHERE id = '$bearer_id'";
        return $this->db->getRow($sql);
    } 
    
    public function update_audit($data,$bearer_id){
      return $this->db->autoExecute($this->ecs->table("bearer_info"), $data, 'UPDATE'," id = '$bearer_id'");
    }
    
    public function examine_bearer_list(){                
//        $sql = "SELECT bi.*, bi.id as bearer_id, g.game_name, s.sche_name, n.num_name, ga.attr_value, a.agent, a.id , aoi.* FROM ".$this->ecs->table('bearer_info')." AS bi LEFT JOIN ".$this->ecs->table('agent_order_info')." AS aoi ON bi.order_id = aoi.id LEFT JOIN ".$this->ecs->table('game')." AS g ON aoi.game_id = g.id LEFT JOIN ".$this->ecs->table('schedule')." AS s ON aoi.sche_id = s.id LEFT JOIN ".$this->ecs->table('number')." AS n ON aoi.number_id = n.id LEFT JOIN ".$this->ecs->table('products')." AS p ON aoi.attr_id = p.product_id LEFT JOIN ".$this->ecs->table('goods_attr')." AS ga ON p.goods_attr = ga.goods_attr_id LEFT JOIN ".$this->ecs->table('agent')." AS a ON bi.agent_id = a.id WHERE bi.submit = '2' ORDER BY bi.id DESC";
       $result = get_filter();
       if($result === false){
            $filter = array();
            $filter['agent'] = empty($_REQUEST['agent']) ? 0 : intval($_REQUEST['agent']);
            $filter['game_id'] = empty($_REQUEST['game_id']) ? 0 : intval($_REQUEST['game_id']);
            $filter['sche_id'] = empty($_REQUEST['sche_id']) ? 0 : intval($_REQUEST['sche_id']);
            $filter['number_id'] = empty($_REQUEST['number_id']) ? 0 : intval($_REQUEST['number_id']);
            $filter['audit_bearer'] = $_REQUEST['audit_bearer'];
            $filter['cn_customer_name'] = empty($_REQUEST['cn_customer_name']) ? '' : ($_REQUEST['cn_customer_name']);
            $where = "";
            if($filter['agent']){
               $where .= " AND a.id = '$filter[agent]'"; 
            }
            if($filter['game_id']){
               $where .= " AND aoi.game_id = '$filter[game_id]'"; 
            }
            if($filter['sche_id']){
               $where .= " AND aoi.sche_id = '$filter[sche_id]'"; 
            }
            if($filter['number_id']){
               $where .= " AND aoi.number_id = '$filter[number_id]'"; 
            }          
            if($filter['audit_bearer'] != ''){
               $where .= " AND bi.audit_bearer = '$filter[audit_bearer]'"; 
            }
           if ($filter['cn_customer_name'])
           {
               $where .= " AND bi.cn_customer_name LIKE '%" . mysql_like_quote($filter['cn_customer_name']) . "%'";
           }

            /* 记录总数 */
           $sql = "SELECT count(*) FROM ".$this->ecs->table('bearer_info')." AS bi LEFT JOIN ".$this->ecs->table('agent_order_info')." AS aoi ON bi.order_id = aoi.id LEFT JOIN ".$this->ecs->table('products')." AS p ON aoi.attr_id = p.product_id LEFT JOIN ".$this->ecs->table('goods_attr')." AS ga ON p.goods_attr = ga.goods_attr_id LEFT JOIN ".$this->ecs->table('agent')." AS a ON bi.agent_id = a.id WHERE bi.submit = '2'".$where;
           $filter['record_count'] = $this->db->getOne($sql);
           
            /* 分页大小 */
            $filter = page_and_size($filter);

            $sql = "SELECT bi.*, bi.id as bearer_id, FROM_UNIXTIME(bi.sub_time,'%Y-%m-%d %H:%i:%s') AS sub_time, g.game_name, s.sche_name, n.num_name, gs.goods_name, a.agent, a.id , aoi.* FROM ".$this->ecs->table('bearer_info')." AS bi LEFT JOIN ".$this->ecs->table('agent_order_info')." AS aoi ON bi.order_id = aoi.id LEFT JOIN ".$this->ecs->table('game')." AS g ON aoi.game_id = g.id LEFT JOIN ".$this->ecs->table('schedule')." AS s ON aoi.sche_id = s.id LEFT JOIN ".$this->ecs->table('number')." AS n ON aoi.number_id = n.id LEFT JOIN ".$this->ecs->table('goods')." AS gs ON aoi.goods_id = gs.goods_id LEFT JOIN ".$this->ecs->table('agent')." AS a ON bi.agent_id = a.id WHERE bi.submit = '2' ".$where." ORDER BY bi.sub_time DESC LIMIT " . $filter['start'] . ",$filter[page_size]";
            set_filter($filter, $sql);
     
        }else{
            $sql    = $result['sql'];
            $filter = $result['filter'];
            
        }        
        $row = $this->db->getAll($sql);
        return array('bearer_list' => $row, 'filter'=>$filter,'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
            
    }    
    
    public function submit_state($data){        
        foreach ($data as $id){
           $bearer_info = array("audit_bearer"=>2);
           $where = "id = '$id'";
           $res = $this->db->autoExecute($this->ecs->table("bearer_info"), $bearer_info, "UPDATE", $where);
        }
        return $res;
    }
    
    public function agent_list(){
        return $this->db->getAll("SELECT * FROM ".$this->ecs->table('agent'));
    }
    
    public function get_attr_id($data){
        $sql = "SELECT product_id FROM ".$this->ecs->table("products")." WHERE goods_id = '$data'";
        return $this->db->getOne($sql);
    }
    
    public function audit_bearer_info(){
        /* 查询条件 */
       $filter = array();
       $filter['id'] = empty($_REQUEST['id']) ? 0 : intval($_REQUEST['id']);
       $where = (!empty($filter['id'])) ? " AND aoi.id = '$filter[id]' " : '';
       $filter['audit_bearer'] = empty($_REQUEST['audit_bearer']) ? 0 : intval($_REQUEST['audit_bearer']);
       $where .= " AND bi.audit_bearer = '$filter[audit_bearer]'";
        /* 记录总数 */
        $sql = "SELECT COUNT(*) FROM ".$this->ecs->table('agent_order_info')." AS aoi LEFT JOIN ".$this->ecs->table('game')." AS g ON aoi.game_id = g.id LEFT JOIN ".$this->ecs->table('schedule')." AS s ON aoi.sche_id = s.id LEFT JOIN ".$this->ecs->table('number')." AS n ON aoi.number_id = n.id LEFT JOIN ".$this->ecs->table('goods')." AS gs ON aoi.goods_id = gs.goods_id LEFT JOIN ".$this->ecs->table('bearer_info')." AS bi ON aoi.id = bi.order_id WHERE bi.submit = '2'".$where;
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);
        

        /* 分页大小 */
        $filter = page_and_size($filter);
        $sql = "SELECT aoi.*, g.game_name, s.sche_name, n.num_name, gs.goods_name, bi.*, bi.id AS bearer_info FROM ".$this->ecs->table('agent_order_info')." AS aoi LEFT JOIN ".$this->ecs->table('game')." AS g ON aoi.game_id = g.id LEFT JOIN ".$this->ecs->table('schedule')." AS s ON aoi.sche_id = s.id LEFT JOIN ".$this->ecs->table('number')." AS n ON aoi.number_id = n.id LEFT JOIN ".$this->ecs->table('goods')." AS gs ON aoi.goods_id = gs.goods_id LEFT JOIN ".$this->ecs->table('bearer_info')." AS bi ON aoi.id = bi.order_id WHERE bi.submit = '2'".$where." LIMIT " . $filter['start'] . ",$filter[page_size]";
        $bearer_info = $this->db->getAll($sql);
        return array('bearer_info' => $bearer_info, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
    }
}
