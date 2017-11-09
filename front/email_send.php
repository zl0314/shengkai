<?php
//普通触发发送方式
function send_mail($email) {
        $url = 'http://sendcloud.sohu.com/webapi/mail.send.json';
        $API_USER = 'Kitsch_test_64atKZ';
        $API_KEY = 'HoQct0SUY22Z4BzV';
        $from='Kitsch@my.im';
        $fromname='Zc';
        $to=$email;
        $subject= '来自SendCloud的第一封邮件！';
        $html='你太棒了！你已成功的从SendCloud发送了一封测试邮件，接下来快登录前台去完善账户信息吧！';
        $param = array(
            'api_user' => $API_USER, # 使用api_user和api_key进行验证
            'api_key' => $API_KEY,
            'from' =>   $from , # 发信人，用正确邮件地址替代
            'fromname' =>$fromname ,
            'to' =>$to ,# 收件人地址, 用正确邮件地址替代, 多个地址用';'分隔  
            'subject' =>$subject,
            'html' => $html,
            'resp_email_id' => 'true'
        );


        $data = http_build_query($param);

        $options = array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Content-Type: application/x-www-form-urlencoded',
                'content' => $data
        ));
        $context  = stream_context_create($options);
        $result = file_get_contents($url, FILE_TEXT, $context);

        return $result;
}

?>
