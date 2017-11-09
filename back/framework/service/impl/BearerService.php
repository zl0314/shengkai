<?php

/*
 * 作者：鞠嵩
 * 时间：10:37:56
 * 
 */

/**
 * Description of HotelService
 *
 * @author Kevin
 */
class BearerService {
    //put your code here
    private $bearer;
    public function __construct() {
        __load("BearerModel", "model");
        $this->bearer = new BearerModel();
    }
    public function insert_id(){
        return $this->bearer->insert_id();
    }
    
    //发送邮件
//    function send_mail($email) {
//        $url = 'http://sendcloud.sohu.com/webapi/mail.send.json';
//        $API_USER = 'Kitsch_test_64atKZ';
//        $API_KEY = 'HoQct0SUY22Z4BzV';
//        $from='Kitsch@my.im';
//        $fromname='Kitsch';
//        $to=$email;
//        $subject= '来自SendCloud的第一封邮件！';
//        $html='你太棒了！你已成功的从SendCloud发送了一封测试邮件，接下来快登录前台去完善账户信息吧！';
//        $param = array(
//            'api_user' => $API_USER, # 使用api_user和api_key进行验证
//            'api_key' => $API_KEY,
//            'from' =>   $from , # 发信人，用正确邮件地址替代
//            'fromname' =>$fromname ,
//            'to' =>$to ,# 收件人地址, 用正确邮件地址替代, 多个地址用';'分隔
//            'subject' =>$subject,
//            'html' => $html,
//            'resp_email_id' => 'true'
//        );
//
//
//        $data = http_build_query($param);
//
//        $options = array(
//            'http' => array(
//                'method' => 'POST',
//                'header' => 'Content-Type: application/x-www-form-urlencoded',
//                'content' => $data
//        ));
//        $context  = stream_context_create($options);
//        $result = file_get_contents($url, FILE_TEXT, $context);
//
//        return $result;
//}
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
    
    

    public function save_bearer_infos($bearer_info,$way,$where){
       return $this->bearer->save_bearer($bearer_info,$way,$where);
    }
    public function order_ticket($order_info,$way,$where){
       return $this->bearer->order_ticket($order_info,$way,$where);
    }
    public function get_pay_status($order_id,$user_id){
        return $this->bearer->get_pay_status($order_id,$user_id);
    }
    public function bearer_info_list($order_id){
        return $this->bearer->bearer_info_list($order_id);
    }
    public function bearer_info_combo($order_id){
        return $this->bearer->bearer_info_combo($order_id);
    }
    public function get_order_id($order_sn){
        return $this->bearer->get_order_id($order_sn);
    }
     public function check_order_user($order_sn,$user_id){
        return $this->bearer->check_order_user($order_sn,$user_id);
    }
    public function bearer_name($user_id){
        return $this->bearer->bearer_name($user_id);
    }
    public function bearer_passport_number($bearer_id){
        return $this->bearer->bearer_passport_number($bearer_id);
    }
    public function get_bearer_info($user_id){
        return $this->bearer->get_bearer_info($user_id);
    }
    //--hechengbin -- 查询是否为代理商 -- start
    public function get_type($user_id){
        return $this->bearer->get_type($user_id);
    }
    //--hechengbin --end
    public function get_combo_ticket_bearer_info($bearer_id){
        return $this->bearer->get_combo_ticket_bearer_info($bearer_id);
    }
    public function bearer_info($bearer_id){
        return $this->bearer->bearer_info($bearer_id);
    }
    public function combo_bearer_info($bearer_id){
        return $this->bearer->combo_bearer_info($bearer_id);
    }
    public function get_rec_id($order_id){
        return $this->bearer->get_rec_id($order_id);
    }
    //获取订单ID,根据订单ID查出对应的订单号、购票人证件人号码、购票人证件人类型、购票人姓名、购票人姓名、购票人手机号码
    public function get_order_sn($order_id_num){
        return $this->bearer->get_rec_order_sn($order_id_num);
    }
    //根据订单号查出包含的所有门票
    public function get_order_menpiao_list($order_id_num){
        return $this->bearer->get_rec_order_menpiao_list($order_id_num);
    }
    public function get_order_combo_menpiao($order_id_num){
        return $this->bearer->get_order_combo_menpiao($order_id_num);
    }
    //根据门票code更新数据库的is_pay_bx字段，is_pay_bx=1 表示需要购买保险
    public function set_pay_bx($tk_code){
        return $this->bearer->set_pay_bx($tk_code);
    }
    //根据门票code查出：票务编码、票额、赛事名称(票务使用时间)、比赛日期、比赛场次
    public function get_menpiao_messages_ticket($order_id_num){
        return $this->bearer->get_menpiao_messages_ticket($order_id_num);
    }
   //根据门票code查出：票务编码、票额、赛事名称(票务使用时间)、比赛日期、比赛场次
    public function get_menpiao_messages_combo($order_id_num){
        return $this->bearer->get_menpiao_messages_combo($order_id_num);
    }
    //获取当前用户的手机和邮箱
    public  function query_list($user_id){
        return $this->bearer->query_list($user_id);
    }


    public function remove($bearer_id){
        return $this->bearer->remove($bearer_id);
    }
//    public function remove_order($order_id,$bearer_id){
//        return $this->bearer->remove_order_combo($order_id,$bearer_id);
//    }
//    public function remove_order_ticket($order_id,$bearer_id){
//        return $this->bearer->remove_order_ticket($order_id,$bearer_id);
//    }
    public function get_ticket_info($rec_id){
        return $this->bearer->get_ticket_info($rec_id);
    }
    public function get_code($rec_id){
        return $this->bearer->get_code($rec_id);
    }
    public function get_goods_price($rec_id){
        return $this->bearer->get_goods_price($rec_id);
    }
    public function get_game_name($rec_id){
        return $this->bearer->get_game_name($rec_id);
    }
    public function get_num_start($rec_id){
        return $this->bearer->get_num_start($rec_id);
    }
    public function get_num_name($rec_id){
        return $this->bearer->get_num_name($rec_id);
    }
    public function get_game_info($order_id_num){
        return $this->bearer->get_game_info($order_id_num);
    }
    public function bearer_info_order_id($order_id){
        return $this->bearer->bearer_info_order_id($order_id);
    }
    public function bearer_info_id($id){
        return $this->bearer->bearer_info_id($id);
    }
    public function bearer_info_page($order_id){
        return $this->bearer->bearer_info_page($order_id);
    }
    //查询订单里的持票人
    public function get_order_bearer_id($order_id){
        return $this->bearer->get_order_bearer_id($order_id);
    }
}
