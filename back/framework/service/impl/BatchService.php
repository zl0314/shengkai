<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BatchService
 *
 * @author qs
 */
class BatchService {

    //put your code here

    private $batch;

    public function __construct() {
        __load("BatchModel", "model");
        $this->batch = new BatchModel();
    }

    public function add($data) { 
        $game = $data['game'];
        $sche = $data['sche'];
        $number = $data['number'];
        $goods = $data['goods_id'];
        $g_number = $data['g_number'];
        $batch_sn = $data['batch_sn'];
        $ticket_prefix = $data['ticket_prefix'];
        $ticket_code = $data['ticket_code'];
        $ticket_postfix = $data['ticket_postfix'];
        $sum=count($game);
        if(empty($g_number)){
            return false;
        }
        if($sum<1||$sum!=count($sche)||$sum!=count($number)||$sum!=count($goods)||$sum!=count($g_number)||empty($batch_sn)){
            return false;
        }
        foreach ($goods as $key => $value) {
            if(empty($value)||$value==0){
                return false;
            }
        }
//        for ($i = 0; $i < count($game); $i++) {
//            $batch_info_list_code = array(
//                "ticket_prefix" => $ticket_prefix[$i],
//                "ticket_code" => $ticket_code[$i],
//                "ticket_postfix" => $ticket_postfix[$i]
//            );
//            $res = $this->batch->select_batch_info($batch_info_list_code);
//            if($res){
//                return false;
//            }
//        }

        $batch = array(
            "batch_sn" => $batch_sn,
            "add_time" => time(),
            "status" => 0
        );
        $batch_id = $this->batch->add_batch($batch);
        if (!empty($batch_id)) {
            $batch_info = array();
            for ($i = 0; $i < count($game); $i++) {
                if($ticket_code[$i]){
                   $str = str_replace('"',"@shengkai@",$ticket_prefix[$i]);
                   $str1 = str_replace("'","&shankai&",$str);
                   $str_postfix = str_replace('"',"@shengkai@",$ticket_postfix[$i]);
                   $str_postfix1 = str_replace("'","&shankai&",$str_postfix);
                }
                $batch_info[] = array(
                    "game_id" => $game[$i],
                    "sche_id" => $sche[$i],
                    "number_id" => $number[$i],
                    "attr_id" => $this->batch->get_attr_id($goods[$i]),
                    "goods_id" => $goods[$i],
                    "number" => $g_number[$i],
                    "batch_id" => $batch_id,
//                    "ticket_prefix" => $ticket_prefix[$i],
                    "ticket_prefix" => $str1,
                    "ticket_code" => $ticket_code[$i],
//                    "ticket_postfix" => $ticket_postfix[$i]
                    "ticket_postfix" => $str_postfix1
                );
//                echo "<pre>";
//                print_r($batch_info);die;
                if($ticket_code[$i]){
                    $isCode="/^([0-9]+-)[0-9]+$/";
                    $ticket_code_list = trim($ticket_code[$i]);
                    if(!preg_match($isCode,$ticket_code_list)){
                        $GLOBALS['db']->query("DELETE FROM ".$GLOBALS['ecs']->table('batch')." WHERE id=$batch_id");
                        return false;
                    }
                    $code = explode('-',$ticket_code_list);
                    for($j=$code[0];$j<$code[1]+1;$j++){
                        $batch_code = array(
                            "goods_id" => $goods[$i],
                            'ticket_code' => $str1.$j.$str_postfix1,
//                            'ticket_code' => $ticket_prefix[$i].$j.$ticket_postfix[$i],
                            'type' => 0
                        );
                        //查询是否存在重复
                        $res = $this->batch->select_batch_chongfu($batch_code);
                        if($res){
                            $GLOBALS['db']->query("DELETE FROM ".$GLOBALS['ecs']->table('batch')." WHERE id=$batch_id");
                            return false;
                        }
//                    $this->batch->add_batch_code($batch_code);
                    }
                    for ($j = $code[0]; $j < $code[1] + 1; $j++) {
                        $batch_code = array(
                            "goods_id" => $goods[$i],
//                            'ticket_code' => $ticket_prefix[$i] . $j . $ticket_postfix[$i],
                            'ticket_code' => $str1.$j.$str_postfix1,
                            'type' => 0
                        );
                        $this->batch->add_batch_code($batch_code);
                    }
                }
                $falg = $this->batch->add_batch_info($batch_info);
            }
//            'b1shengkai@'20"b1@shengkai"
            if ($falg) {
                __log("数据添加成功");
                return true;
            } else {
                __log("数据添加失败");
                return false;
            }
        } else {
            __log("订单添加失败");
            return false;
        }
    }
    
    public function void_batch($id){
        return  $this->batch->void_batch($id);
        
        
    }
      public function getAllNumber(){        
        return  $this->batch->getAllNumber(); 
    }

    public function getAllGame(){
        return $this->batch->getAllGame();
    }
        public function getAllPitch(){        
        return  $this->batch->getAllPitch(); 
    }
    public function get_list(){        
        return  $this->batch->get_list(); 
    }
    //--hechengbin --查找批次
    public function get_batch($id){
        return $this->batch->get_batch($id);
    }
    //查询票号并写入
    public function get_ticket_code(){
        return $this->batch->get_ticket_code();
    }
}
