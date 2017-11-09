<?php

/*
 * 作者：戎青松
 * 时间：10:37:56
 * 
 */

/**
 * Description of HotelService
 *
 * @author Kevin
 */
class AgentService {

    //put your code here
    private $agent;

    public function __construct() {
        __load("AgentModel", "model");
        $this->agent = new AgentModel();
    }
    function send_mail($email_tel,$data) {
        $API_USER = 'kiss_Dd_test_EZh6kQ';
        $API_KEY = 'vh47rza9yUkTeHBE';
        // $vars = json_encode( array("to" => array($email)));
        $vars = json_encode($data);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_URL, 'http://sendcloud.sohu.com/webapi/mail.send_template.json');
        curl_setopt($ch, CURLOPT_POSTFIELDS, array(
            'api_user' => $API_USER, # 使用api_user和api_key进行验证
            'api_key' => $API_KEY,
            'from'=>'hospitality@shankaisports.com',
            'template_invoke_name'=>$email_tel,
            'substitution_vars'=>$vars
        ));
        $result = curl_exec($ch);
        if($result === false) {

            echo curl_error($ch);
        }
        __log("发送邮件--模版：".$email_tel,"email");
        curl_close($ch);
        return $result;
    }
    public function get_AllRegion($type) {
        return $this->region->get_all($type);
    }

    public function add($data) {
        return $this->agent->add($data);
    }

    public function update($data) {

        return $this->agent->update($data);
    }
    
    public function get_AllAgent() {

        return $this->agent->get_All_query();
    }
    
    public function get_All_Agent() {

        return $this->agent->get_All();
    }

    public function get_Agent($id) {
        return $this->agent->get($id);
    }
   
    public function agent_status($data,$id){
        return $this->agent->update_agent_status($data,$id);
    }
    
    public function remove($id) {
        return $this->agent->remove($id);
    }

    public function remove_b($data) {
        //return $this->hotel->remove_batch($data);
        return $this->agent->remove_batch($data);
    }
    
    public function get_All_Order(){
        return $this->agent->get_order();
    }
    
    public function get_pitch_list($agent_id){
        return $this->agent->pitch_info($agent_id);
    }
    
    public function add_order($data) {
        $game = $data['game'];
        $sche = $data['sche'];
        $number = $data['number'];
        $goods_id = $data['goods_id'];
        $g_number = $data['g_number'];
        $agent_id = $data['agent_id'];
        $batch_sn = $data['order_sn'];
        $sum=count($game);
        if($sum<1||$sum!=count($sche)||$sum!=count($number)||$sum!=count($goods_id)||$sum!=count($g_number)/*||empty($batch_sn)*/||empty($agent_id)){
            return false;
        }
        $order = array(
            "agent_id" => $agent_id,
            "add_time" => time(),
            "status" => 0,
            "order_sn" =>$data['order_sn']
        );
        $order_id = $this->agent->add_order($order);
        if (!empty($order_id)) {
            $order_info = array();
            for ($i = 0; $i < count($game); $i++) {
                $order_info[] = array(
                    "game_id" => $game[$i],
                    "sche_id" => $sche[$i],
                    "number_id" => $number[$i],
                    "goods_id" => $goods_id[$i],
                    "attr_id" => $this->agent->get_attr_id($attr[$i]),
                    "number" => $g_number[$i],
                    "order_id" => $order_id
                );
            }
            $falg = $this->agent->add_order_info($order_info);
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
    
    public function pitch_bearer(){
        return $this->agent->get_pitch_bearer();
    }
    
    public function get_bearer_info($bearer_id){
        return $this->agent->bearer_info($bearer_id);
    }
    
    public function update_audit_bearer($data,$bearer_id){
        return $this->agent->update_audit($data,$bearer_id);
    }
    
    public function get_examine_bearer_list(){
        return $this->agent->examine_bearer_list();
    }    
    
    public function update_submit($data){
        return $this->agent->submit_state($data);
    }
    
    public function get_agent_list(){
        return $this->agent->agent_list();
    }
    
    public function get_audit_bearer_info(){
        return $this->agent->audit_bearer_info();
    }
}
