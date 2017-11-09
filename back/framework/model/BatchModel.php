<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BatchModel
 *
 * @author qs
 */
__load("Model", "model");

class BatchModel extends Model
{

    //put your code here

    public function add_batch($data)
    {
        $res = $this->db->autoExecute($this->ecs->table("batch"), $data, 'INSERT');
        if ($res) {
            return $this->db->insert_id();
        } else {
            return null;
        }
    }
    //查询票号是否存在
    public function select_batch_info($data){
        $sql = "SELECT * FROM ".$GLOBALS['ecs']->table('batch_info')." WHERE ticket_prefix='".$data['ticket_prefix']."' AND ticket_code='".$data['ticket_code']."' AND ticket_postfix='".$data['ticket_postfix']."' AND type=0";
        return $GLOBALS['db']->getRow($sql);
    }

    public function void_batch($id)
    {
        $sql = "UPDATE " . $this->ecs->table("batch") . " SET status = 1  WHERE id=$id";
        $res = $this->db->query($sql);
        if ($res) {
            $sql1 = "UPDATE ".$GLOBALS['ecs']->table('batch_info')." SET type=1 WHERE batch_id=$id";
            $GLOBALS['db']->query($sql1);
            //将票号设为无效--hechengbin
            //查找现在批次的票号区间
            $sql = "SELECT goods_id,ticket_prefix,ticket_postfix,ticket_code FROM ".$this->ecs->table("batch_info")." WHERE batch_id=$id";
            $ticket_code = $this->db->getRow($sql);
            if($ticket_code['ticket_code']){
                $ticket_code_list = trim($ticket_code['ticket_code']);
                $code = explode('-',$ticket_code_list);
                for($j=$code[0];$j<$code[1]+1;$j++) {
                    $ticket_code_list = $ticket_code['ticket_prefix'].$j.$ticket_code['ticket_postfix'];
//                    //查找票号是否已经被使用
//                    $result = $GLOBALS['db']->getRow("SELECT * FROM ".$GLOBALS['ecs']->table('batch_goods')." WHERE ticket_code='".$ticket_code_list."' AND type=0 AND goods_id=$ticket_code[goods_id]");
//                    if($result){
//                        return false;
//                    }
                    $sql = "UPDATE ".$this->ecs->table("batch_goods")." SET valid=1 WHERE ticket_code='".$ticket_code_list."' AND goods_id=$ticket_code[goods_id]";
                    $this->db->query($sql);
                }
            }
            //--end
            $sql = "SELECT number,goods_id FROM " . $this->ecs->table("batch_info") . " WHERE batch_id = $id";
            $data = $this->db->getAll($sql);
            try {
                foreach ($data as $value) {
                    //查找商品goods_number
                    $res = $GLOBALS['db']->getOne("SELECT goods_number FROM ".$GLOBALS['ecs']->table("goods")." WHERE goods_id=$value[goods_id]");
                    if($res){
                        $ticket_num = $res - $value['number'];
                        if($ticket_num > 0){
                            $sql = "UPDATE " . $this->ecs->table("goods") . " SET sum_number=sum_number-".$value['number'].",goods_number=goods_number-".$value['number']." WHERE goods_id =" . $value['goods_id'];
                            $this->db->query($sql);
                        }else{
                            //查找现在已售为多少
                            $sql = "SELECT goods_number,sum_number FROM ".$GLOBALS['ecs']->table('goods')." WHERE goods_id=$value[goods_id]";
                            $number = $GLOBALS['db']->getRow($sql);
                            $yishou = $number['sum_number'] - $number['goods_number'];
                            $sql = "UPDATE " . $this->ecs->table("goods") . " SET sum_number=$yishou,goods_number=0 WHERE goods_id =" . $value['goods_id'];
                            $this->db->query($sql);
                        }
                    }
                }
            } catch (Exception $e) {
                return false;
            }
            return true;
        } else {
            return false;
        }
    }
    //插入票号--hechengbin-
    public function add_batch_code($data){
        $res = $this->db->autoExecute($this->ecs->table("batch_goods"), $data, 'INSERT');
        if ($res) {
            return $this->db->insert_id();
        } else {
            return null;
        }
    }
    //查询是否重复
    public function select_batch_chongfu($batch_info){
        $sql = "SELECT * FROM ".$GLOBALS['ecs']->table('batch_goods')." WHERE ticket_code='".$batch_info['ticket_code']."' AND valid=0 AND goods_id=".$batch_info['goods_id'];
        return $GLOBALS['db']->getRow($sql);
    }
    public function add_batch_info($data)
    {

        $temp_array = array();
        $id_array = array();
        foreach ($data as $key => $value) {
            $id_array[] = $key;
            $sql = "UPDATE " . $this->ecs->table("goods") . " SET goods_number = goods_number +" . $value['number'] . ",sum_number= sum_number+" . $value['number'] . "  WHERE goods_id =" . $value['goods_id'];
            $res = $this->db->query($sql);
            if ($res) {
                $flag = $this->db->autoExecute($this->ecs->table("batch_info"), $value, 'INSERT');
                if ($flag) {
                    $temp_array[] = $this->db->insert_id();
                } else {
                    if (count($temp_array) > 0) {
                        foreach ($id_array as $key => $id) {
                            $temp_value = $data[$key];
                            $sql = "UPDATE " . $this->ecs->table("goods") . " SET goods_number = goods_number -" . $temp_value['number'] . ",sum_number= sum_number -" . $temp_value['number'] . " WHERE goods_id =" . $temp_value['goods_id'];
                            $res = $this->db->query($sql);
                        }
                        $sql = "DELETE FROM" . $this->ecs->table("batch_info") . " WHERE id in ( " . implode(",", $temp_array) . ")";
                        $this->db->query($sql);
                        return false;
                    }
                }
            } else {
                if (count($temp_array) > 0) {
                    foreach ($id_array as $key => $id) {
                        $temp_value = $data[$key];
                        $sql = "UPDATE " . $this->ecs->table("goods") . " SET goods_number = goods_number -" . $temp_value['number'] . ",sum_number= sum_number -" . $temp_value['number'] . "  WHERE goods_id =" . $temp_value['goods_id'];
                        $res = $this->db->query($sql);
                    }
                    $sql = "DELETE FROM" . $this->ecs->table("batch_info") . " WHERE id in ( " . implode(",", $temp_array) . ")";
                    $this->db->query($sql);
                    return false;
                }
            }
        }

        return true;
    }
    public function getAllNumber(){
        $sql = "SELECT * FROM " . $this->ecs->table('number');
   
        return $this->db->getAll($sql);
    }

    public function getAllGame(){
        $sql = "SELECT * FROM ".$this->ecs->table('game');
        return $this->db->getAll($sql);
    }
      public function getAllPitch(){
        $sql = "SELECT * FROM " . $this->ecs->table('Pitch');
   
        return $this->db->getAll($sql);
    }
    //查找批次
    public function get_batch($id){
        $sql = "SELECT * FROM " . $this->ecs->table('batch')."WHERE id = '".$id."'";
        return $this->db->getAll($sql);
    }
    //查询票号并写入
    public function get_ticket_code($id){
        //查询是否存在票号
        $sql = "SELECT ticket_code FROM ".$GLOBALS['ecs']->table('batch_info')." WHERE ";
    }

    public function get_list()
    {
        $result = get_filter();
        if($result == false) {
            $game_id = empty($_REQUEST['game_id']) ? 0 : intval($_REQUEST['game_id']);
            $game_id_1 = empty($_REQUEST['game_id_1']) ? 0 : intval($_REQUEST['game_id_1']);
            $number_id = empty($_REQUEST['number_id']) ? 0 : intval($_REQUEST['number_id']);
            $number_id_1 = empty($_REQUEST['number_id_1']) ? 0 : intval($_REQUEST['number_id_1']);
            $pitch_id = empty($_REQUEST['pitch_id']) ? 0 : intval($_REQUEST['pitch_id']);
            $pitch_id_1 = empty($_REQUEST['pitch_id_1']) ? 0 : intval($_REQUEST['pitch_id_1']);
//            $filter['game_id'] = empty($_REQUEST['game_id']) ? 0 : intval($_REQUEST['game_id']);
//            $filter['number_id'] = empty($_REQUEST['number_id']) ? 0 : intval($_REQUEST['number_id']);
//            $filter['pitch_id'] = empty($_REQUEST['pitch_id']) ? 0 : intval($_REQUEST['pitch_id']);
            $filter['ticket'] = empty($_REQUEST['ticket']) ? 0 : intval($_REQUEST['ticket']);
            if($game_id){
                $filter['game_id'] = $game_id;
            }else{
                $filter['game_id'] = $game_id_1;
            }
            if($number_id){
                $filter['number_id'] = $number_id;
            }else{
                $filter['number_id'] = $number_id_1;
            }
            if($pitch_id){
                $filter['pitch_id'] = $pitch_id;
            }else{
                $filter['pitch_id'] = $pitch_id_1;
            }
            $where = 1;

            if($filter['game_id']){
                $where .= " AND  aoi.game_id = " .$filter['game_id'];
            }
            if($filter['number_id']){
                $where .= " AND aoi.number_id = " .$filter['number_id'];
            }
            if($filter['pitch_id']){
                $where .= " AND p.id = " .$filter['pitch_id'];
            }else{
                $where .="";
            }
            /* 记录总数 */
            if(($filter['game_id'] || $filter['number_id'] || $filter['pitch_id']) && $filter['ticket'] == 0){
                $sql = "SELECT COUNT(*) FROM " .$GLOBALS['ecs']->table('batch') ." AS b LEFT JOIN " .$GLOBALS['ecs']->table('batch_info'). " AS aoi ON b.id=aoi.batch_id LEFT JOIN ".$GLOBALS['ecs']->table('number')." AS n ON aoi.number_id=n.id LEFT JOIN ".$GLOBALS['ecs']->table('pitch')." AS p ON n.pitch_id=p.id WHERE " . $where;
//                echo $sql;die;
                $filter['record_count'] = $GLOBALS['db']->getOne($sql);
            }elseif(($filter['game_id'] || $filter['number_id'] || $filter['pitch_id']) && $filter['ticket'] == 1){
                //查询所有套餐
                $sql = "SELECT * FROM ".$GLOBALS['ecs']->table('combo');
                $combo = $GLOBALS['db']->getAll($sql);
                foreach($combo as $key => $val){
                    $josn_res = json_decode($val['combo_tickets'],true);
                    foreach ($josn_res['default'] as $key1 => $val2) {
                        $id[] = explode('|', $val2);
                    }
                }
                foreach($id as $key3 => $val3){
                    $goods_id[] = $val3[1];
                }
                $goods_id = array_flip($goods_id);
                $goods_id = array_flip($goods_id);
                $goods_id = array_merge($goods_id);
                $str = join(',',$goods_id);
                    $sql = "SELECT COUNT(*) FROM ".$GLOBALS['ecs']->table('batch')."AS b LEFT JOIN ".$GLOBALS['ecs']->table('batch_info')."AS aoi ON b.id=aoi.batch_id LEFT JOIN ".$GLOBALS['ecs']->table('game')."AS g ON aoi.game_id=g.id LEFT JOIN ".$GLOBALS['ecs']->table('number')."AS n ON n.id=aoi.number_id LEFT JOIN".$GLOBALS['ecs']->table('schedule')."AS s ON aoi.sche_id=s.id LEFT JOIN ".$GLOBALS['ecs']->table('pitch')."AS p ON p.id=n.pitch_id LEFT JOIN " .$GLOBALS['ecs']->table('region')." AS r ON p.region_id=r.region_id LEFT JOIN ".$GLOBALS['ecs']->table('goods')."AS good ON good.goods_id=aoi.goods_id WHERE " .$where." AND good.goods_id in ($str)";
                $filter['record_count'] = $GLOBALS['db']->getOne($sql);
            }elseif($filter['game_id'] ==0 && $filter['number_id']==0 && $filter['pitch_id']==0 && $filter['ticket'] == 1){
                //查询所有套餐
                $sql = "SELECT * FROM ".$GLOBALS['ecs']->table('combo');
                $combo = $GLOBALS['db']->getAll($sql);
                foreach($combo as $key => $val){
                    $josn_res = json_decode($val['combo_tickets'],true);
                    foreach ($josn_res['default'] as $key1 => $val2) {
                        $id[] = explode('|', $val2);
                    }
                }
                foreach($id as $key3 => $val3){
                    $goods_id[] = $val3[1];
                }
                $goods_id = array_flip($goods_id);
                $goods_id = array_flip($goods_id);
                $goods_id = array_merge($goods_id);
                $str = join(',',$goods_id);
                $sql = "SELECT COUNT(*) FROM ".$GLOBALS['ecs']->table('batch')."AS b LEFT JOIN ".$GLOBALS['ecs']->table('batch_info')."AS aoi ON b.id=aoi.batch_id LEFT JOIN ".$GLOBALS['ecs']->table('game')."AS g ON aoi.game_id=g.id LEFT JOIN ".$GLOBALS['ecs']->table('number')."AS n ON n.id=aoi.number_id LEFT JOIN".$GLOBALS['ecs']->table('schedule')."AS s ON aoi.sche_id=s.id LEFT JOIN ".$GLOBALS['ecs']->table('pitch')."AS p ON p.id=n.pitch_id LEFT JOIN " .$GLOBALS['ecs']->table('region')." AS r ON p.region_id=r.region_id LEFT JOIN ".$GLOBALS['ecs']->table('goods')."AS good ON good.goods_id=aoi.goods_id WHERE " .$where." AND good.goods_id in ($str)";
                $filter['record_count'] = $GLOBALS['db']->getOne($sql);
            }elseif(($filter['game_id'] || $filter['number_id'] || $filter['pitch_id']) && $filter['ticket'] == 2){
                $sql = "SELECT COUNT(*) FROM ".$GLOBALS['ecs']->table('batch')."AS b LEFT JOIN ".$GLOBALS['ecs']->table('batch_info')."AS aoi ON b.id=aoi.batch_id LEFT JOIN ".$GLOBALS['ecs']->table('game')."AS g ON aoi.game_id=g.id LEFT JOIN ".$GLOBALS['ecs']->table('number')."AS n ON n.id=aoi.number_id LEFT JOIN".$GLOBALS['ecs']->table('schedule')."AS s ON aoi.sche_id=s.id LEFT JOIN ".$GLOBALS['ecs']->table('pitch')."AS p ON p.id=n.pitch_id LEFT JOIN " .$GLOBALS['ecs']->table('region')." AS r ON p.region_id=r.region_id LEFT JOIN ".$GLOBALS['ecs']->table('goods')."AS good ON good.goods_id=aoi.goods_id WHERE ".$where;
                $filter['record_count'] = $GLOBALS['db']->getOne($sql);
            }else {
                $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('batch');
                $filter['record_count'] = $GLOBALS['db']->getOne($sql);
            }
            /* 分页大小 */
            $filter = page_and_size($filter);
            if(empty($filter['game_id']) && empty($filter['number_id']) && empty($filter['pitch_id']) && $filter['ticket'] == 1){
                //查询所有套餐
                $sql = "SELECT * FROM ".$GLOBALS['ecs']->table('combo');
                $combo = $GLOBALS['db']->getAll($sql);
                foreach($combo as $key => $val){
                    $josn_res[] = json_decode($val['combo_tickets'],true);
                    foreach ($josn_res['default'] as $key1 => $val2) {
                        $id[] = explode('|', $val2);
                        foreach($id as $key3 => $val3){
                            $goods_id[] = $val3[1];
                        }
                    }
                }
                $goods_id = array_flip($goods_id);
                $goods_id = array_flip($goods_id);
                $goods_id = array_merge($goods_id);
                $str = join(',',$goods_id);
                $sql = "SELECT b.*, aoi.number, aoi.ticket_code, aoi.ticket_prefix, aoi.ticket_postfix, g.game_name, good.goods_name, good.goods_number, s.sche_name, n.num_name, n.num_start, n.num_end, p.pitch_name, r.region_name FROM ".$GLOBALS['ecs']->table('batch')."AS b LEFT JOIN ".$GLOBALS['ecs']->table('batch_info')."AS aoi ON b.id=aoi.batch_id LEFT JOIN ".$GLOBALS['ecs']->table('game')."AS g ON aoi.game_id=g.id LEFT JOIN ".$GLOBALS['ecs']->table('number')."AS n ON n.id=aoi.number_id LEFT JOIN".$GLOBALS['ecs']->table('schedule')."AS s ON aoi.sche_id=s.id LEFT JOIN ".$GLOBALS['ecs']->table('pitch')."AS p ON p.id=n.pitch_id LEFT JOIN " .$GLOBALS['ecs']->table('region')." AS r ON p.region_id=r.region_id LEFT JOIN ".$GLOBALS['ecs']->table('goods')."AS good ON good.goods_id=aoi.goods_id WHERE good.goods_id in ($str) ORDER BY b.add_time DESC LIMIT ". $filter['start'] . ",$filter[page_size]";
//                $batch_all = $GLOBALS['db']->getAll($sql);
            }elseif($filter['game_id'] && $filter['number_id'] && $filter['pitch_id'] && $filter['ticket'] == 0){
                $sql = "SELECT b.*, aoi.number, aoi.ticket_code, aoi.ticket_prefix, aoi.ticket_postfix, g.game_name, good.goods_name, good.goods_number, s.sche_name, n.num_name, n.num_start, n.num_end, p.pitch_name, r.region_name FROM ".$GLOBALS['ecs']->table('batch')."AS b LEFT JOIN ".$GLOBALS['ecs']->table('batch_info')."AS aoi ON b.id=aoi.batch_id LEFT JOIN ".$GLOBALS['ecs']->table('game')."AS g ON aoi.game_id=g.id LEFT JOIN ".$GLOBALS['ecs']->table('number')."AS n ON n.id=aoi.number_id LEFT JOIN".$GLOBALS['ecs']->table('schedule')."AS s ON aoi.sche_id=s.id LEFT JOIN ".$GLOBALS['ecs']->table('pitch')."AS p ON p.id=n.pitch_id LEFT JOIN " .$GLOBALS['ecs']->table('region')." AS r ON p.region_id=r.region_id LEFT JOIN ".$GLOBALS['ecs']->table('goods')."AS good ON good.goods_id=aoi.goods_id WHERE " .$where ." ORDER BY b.add_time DESC LIMIT ". $filter['start'] . ",$filter[page_size]";
            }elseif($filter['game_id'] && $filter['number_id'] && $filter['pitch_id'] && $filter['ticket'] == 1){
                //查询所有套餐
                $sql = "SELECT * FROM ".$GLOBALS['ecs']->table('combo');
                $combo = $GLOBALS['db']->getAll($sql);
                foreach($combo as $key => $val){
                    $josn_res[] = json_decode($val['combo_tickets'],true);
                    foreach ($josn_res[$key]['default'] as $key1 => $val2) {
                        $id[] = explode('|', $val2);
                        foreach($id as $key3 => $val3){
                            $goods_id[] = $val3[1];
                        }
                    }
                }
                $goods_id = array_flip($goods_id);
                $goods_id = array_flip($goods_id);
                $goods_id = array_merge($goods_id);
                $str = join(',',$goods_id);
                $sql = "SELECT b.*, aoi.number, aoi.ticket_code, aoi.ticket_prefix, aoi.ticket_postfix, g.game_name, good.goods_name, good.goods_number, s.sche_name, n.num_name, n.num_start, n.num_end, p.pitch_name, r.region_name FROM ".$GLOBALS['ecs']->table('batch')."AS b LEFT JOIN ".$GLOBALS['ecs']->table('batch_info')."AS aoi ON b.id=aoi.batch_id LEFT JOIN ".$GLOBALS['ecs']->table('game')."AS g ON aoi.game_id=g.id LEFT JOIN ".$GLOBALS['ecs']->table('number')."AS n ON n.id=aoi.number_id LEFT JOIN".$GLOBALS['ecs']->table('schedule')."AS s ON aoi.sche_id=s.id LEFT JOIN ".$GLOBALS['ecs']->table('pitch')."AS p ON p.id=n.pitch_id LEFT JOIN " .$GLOBALS['ecs']->table('region')." AS r ON p.region_id=r.region_id LEFT JOIN ".$GLOBALS['ecs']->table('goods')."AS good ON good.goods_id=aoi.goods_id WHERE " .$where ." AND good.goods_id in ($str) ORDER BY b.add_time DESC LIMIT ". $filter['start'] . ",$filter[page_size]";
            }elseif($filter['game_id'] && $filter['ticket'] == 1){
                //查询所有套餐
                $sql = "SELECT * FROM ".$GLOBALS['ecs']->table('combo');
                $combo = $GLOBALS['db']->getAll($sql);
                foreach($combo as $key => $val){
                    $josn_res[] = json_decode($val['combo_tickets'],true);
                    foreach ($josn_res[$key]['default'] as $key1 => $val2) {
                        $id[] = explode('|', $val2);
                        foreach($id as $key3 => $val3){
                            $goods_id[] = $val3[1];
                        }
                    }
                }
                $goods_id = array_flip($goods_id);
                $goods_id = array_flip($goods_id);
                $goods_id = array_merge($goods_id);
                $str = join(',',$goods_id);
                $sql = "SELECT b.*, aoi.number, aoi.ticket_code, aoi.ticket_prefix, aoi.ticket_postfix, g.game_name, good.goods_name, good.goods_number, s.sche_name, n.num_name, n.num_start, n.num_end, p.pitch_name, r.region_name FROM ".$GLOBALS['ecs']->table('batch')."AS b LEFT JOIN ".$GLOBALS['ecs']->table('batch_info')."AS aoi ON b.id=aoi.batch_id LEFT JOIN ".$GLOBALS['ecs']->table('game')."AS g ON aoi.game_id=g.id LEFT JOIN ".$GLOBALS['ecs']->table('number')."AS n ON n.id=aoi.number_id LEFT JOIN".$GLOBALS['ecs']->table('schedule')."AS s ON aoi.sche_id=s.id LEFT JOIN ".$GLOBALS['ecs']->table('pitch')."AS p ON p.id=n.pitch_id LEFT JOIN " .$GLOBALS['ecs']->table('region')." AS r ON p.region_id=r.region_id LEFT JOIN ".$GLOBALS['ecs']->table('goods')."AS good ON good.goods_id=aoi.goods_id WHERE " .$where ." AND good.goods_id in ($str) ORDER BY b.add_time DESC LIMIT ". $filter['start'] . ",$filter[page_size]";
            }else{
            $sql = "SELECT b.*, aoi.number, aoi.ticket_code, aoi.ticket_prefix, aoi.ticket_postfix, g.game_name, good.goods_name, good.goods_number, s.sche_name, n.num_name, n.num_start, n.num_end, p.pitch_name, r.region_name FROM ".$GLOBALS['ecs']->table('batch')."AS b LEFT JOIN ".$GLOBALS['ecs']->table('batch_info')."AS aoi ON b.id=aoi.batch_id LEFT JOIN ".$GLOBALS['ecs']->table('game')."AS g ON aoi.game_id=g.id LEFT JOIN ".$GLOBALS['ecs']->table('number')."AS n ON n.id=aoi.number_id LEFT JOIN".$GLOBALS['ecs']->table('schedule')."AS s ON aoi.sche_id=s.id LEFT JOIN ".$GLOBALS['ecs']->table('pitch')."AS p ON p.id=n.pitch_id LEFT JOIN " .$GLOBALS['ecs']->table('region')." AS r ON p.region_id=r.region_id LEFT JOIN ".$GLOBALS['ecs']->table('goods')."AS good ON good.goods_id=aoi.goods_id WHERE " .$where ." ORDER BY b.add_time DESC LIMIT ". $filter['start'] . ",$filter[page_size]";
//                echo $sql;die;
//                $batch_all = $GLOBALS['db']->getAll($sql);
            }
           // echo $sql;die;
            $batch_all = $GLOBALS['db']->getAll($sql);
        }
        foreach($batch_all as $k => $v){
            $result = explode('-',$batch_all[$k]['ticket_code']);
            $ticket_prefix = str_replace("@shengkai@","'",$batch_all[$k]['ticket_prefix']);
            $ticket_prefix1 = str_replace("&shankai&",'"',$ticket_prefix);
            $ticket_postfix = str_replace("@shengkai@","'",$batch_all[$k]['ticket_postfix']);
            $ticket_postfix1 = str_replace("&shankai&",'"',$ticket_postfix);
//            $result1 = $batch_all[$k]['ticket_prefix'].$result[0].$batch_all[$k]['ticket_postfix'];
            $result1 = $ticket_prefix1.$result[0].$ticket_postfix1;
//            $result2 = $batch_all[$k]['ticket_prefix'].$result[1].$batch_all[$k]['ticket_postfix'];
            $result2 = $ticket_prefix1.$result[1].$ticket_postfix1;
            $batch_all[$k]['ticket_code'] = $result1.'-'.$result2;
            $batch_all[$k]['add_time'] = date("Y-m-d H:i:s",$v['add_time']);
            $batch_all[$k]['row'] = $k + 1;
        }
        return array('batch_all' => $batch_all, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
    }

    public function get_attr_id($data)
    {
        $sql = "SELECT product_id FROM " . $this->ecs->table("products") . " WHERE goods_id = '$data'";
        return $this->db->getOne($sql);
    }
}
