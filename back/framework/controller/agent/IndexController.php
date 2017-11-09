<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of IndexController
 *
 * @author qs
 */
__load("Controller", "controller");

class IndexController extends Controller {

    //put your code here
    public function __construct() {
        parent::__construct();
        __load("IndexService");        
        $this->agent = new IndexService(); 
        if($this->agent->is_freeze() == 1){   
            $_SESSION['agent_id']    = '';
            $_SESSION['agent_name']  = '';
            setcookie('ECSCP[agent_id]',   '',-1);
            setcookie('ECSCP[agent_pass]', '', -1);  
            $link = array(array('text' => "登陆页", 'href' => 'route.php?con=index'));
            sys_msg("该账号已经被冻结!", 0, $link);
            exit;
        }
    }

    public function index() {
        $this->assign('shop_url', urlencode($ecs->url()));
        $this->display('index.htm');
    }
    
    public function edit_pad(){   
       $this->assign('ur_here', "修改密码");
       $this->display('agent/user_pwd.html');
    }
    
    public function edit(){
        $agent_user_pwd = $this->agent->get_agent_user_pwd($_SESSION['agent_id']);
   
        if(md5($_POST['password']) === $agent_user_pwd){
            $data = array('user_pwd'=>md5($_POST['newpass']));
            
            $reg = $this->agent->update_pwd($data,$_SESSION['agent_id']);
            if($reg){
                $link = array(array('text' => "修改密码", 'href' => 'route.php?con=index&act=edit_pad'));
                sys_msg("密码修改成功", 0, $link);
            }else{
                $link = array(array('text' => "修改密码", 'href' => 'route.php?con=index&act=edit_pad'));
                sys_msg("密码修改失败，请重新修改", 0, $link);
            }
        }else{
            $link = array(array('text' => "修改密码", 'href' => 'route.php?con=index&act=edit_pad'));
            sys_msg("原始密码错误", 0, $link);
        }
    }
    
    public function pitch(){
       $this->assign('ur_here', "我的球票");
       $pitch = $this->agent->pitch_info();
       $this->assign('agent_orders', $pitch['pitch_info']);
       $this->assign("record_count", $pitch['record_count']);
       $this->assign("page_count", $pitch['page_count']);
       $this->assign("filter", $pitch['filter']);
       $this->assign("full_page", 1);       
       $this->display('agent/pitch.html');
    }
    
    /* ------------------------------------------------------ */
    //-- js 分页
    /* ------------------------------------------------------ */
    public function pitch_query(){
       $pitch = $this->agent->pitch_info();
       $this->assign('agent_orders', $pitch['pitch_info']);
       $this->assign("record_count", $pitch['record_count']);
       $this->assign("page_count", $pitch['page_count']);
       $this->assign("filter", $pitch['filter']);
        make_json_result($this->fetch("agent/pitch.html"), '', array('filter' => $pitch['filter'], 'page_count' => $pitch['page_count']));
    }
    
    public function examine_bearer(){
       $order_info_id = isset($_GET['order_info_id']) ? $_GET['order_info_id'] : 0;
       $this->assign('ur_here', "查看持票人信息");
       $this->assign('action_link', array('text' => "添加持票人信息", 'href' => "route.php?con=index&act=add_examine_bearer&order_info_id=$order_info_id"));
       $bearer_info = $this->agent->get_examine_bearer_info();
       $this->assign('bearer_info', $bearer_info['bearer_info']);
       $this->assign("record_count", $bearer_info['record_count']);
       $this->assign("page_count", $bearer_info['page_count']);
       $this->assign("filter", $bearer_info['filter']);
       $this->assign("full_page", 1);  
       $this->display('agent/examine_bearer.html');
    }
    
    /* ------------------------------------------------------ */
    //-- js 分页
    /* ------------------------------------------------------ */
    public function examine_bearer_query(){
       $bearer_info = $this->agent->get_examine_bearer_info();
       $this->assign('bearer_info', $bearer_info['bearer_info']);
       $this->assign("record_count", $bearer_info['record_count']);
       $this->assign("page_count", $bearer_info['page_count']);
       $this->assign("filter", $bearer_info['filter']);
       make_json_result($this->fetch("agent/examine_bearer.html"), '', array('filter' => $bearer_info['filter'], 'page_count' => $bearer_info['page_count']));
    }
       
    public function add_examine_bearer(){
       $order_info_id = isset($_GET['order_info_id']) ? $_GET['order_info_id'] : 0;
       $ticket_bearer = $this->agent->get_ticket_bearer($order_info_id);
        //判断添加持票人
        if($ticket_bearer['ticket'] <= $ticket_bearer['bearer']){
            $link = array(array('text' => "查看持票人信息", 'href' => "route.php?con=index&act=examine_bearer&order_info_id=$order_info_id"));
            sys_msg("添加的持票人数已达上线", 0, $link);
        }
       $this->assign('ur_here', "添加持票人信息");
       //$this->assign('action_link', array('text' => "查看持票人信息", 'href' => "route.php?con=index&act=examine_bearer&order_info_id=$order_info_id"));
        $this->assign('action', 'insert');
       $this->assign('order_info_id', $order_info_id);
       $this->display('agent/bearer_info.html');
    }
    
   public function edit_bearer(){       
       $bearer_id = isset($_GET['bearer_id']) ? $_GET['bearer_id'] : 0;
       if($_GET['flag']){
           $this->assign('flag', 'list');
       }
       $this->assign('ur_here', "编辑持票人信息");
       $bearer_info = $this->agent->bearer_infos($bearer_id);     
       $this->assign('action', 'update');
       $this->assign('bearer_info', $bearer_info);
       $this->display('agent/bearer_info.html');
   }
   
    public function examine_bearer_dispose(){        
        $is_save = $_POST['button'] == "保存"; 
        $is_insert = $_POST['action'] == 'insert';
        $is_flag = $_POST['flag'] == 'list';
        if($is_insert){
            $ticket_bearer = $this->agent->get_ticket_bearer($_POST['order_info_id']);
            //判断添加持票人
            if($ticket_bearer['ticket'] <= $ticket_bearer['bearer']){
                $link = array(array('text' => "查看持票人信息", 'href' => "route.php?con=index&act=examine_bearer&order_info_id=$_POST[order_info_id]"));
                sys_msg("添加的持票人数已达上线", 0, $link);
            }  
        }
                
        $bearer_info = array(
            "order_id" =>$_POST['order_info_id'],
            "agent_id" =>$_SESSION['agent_id'],     
            "cn_customer_name" =>$_POST['cn_customer_name'],  
            "us_customer_name" =>$_POST['us_customer_name'],              
            "gender_appellation" =>$_POST['gender_appellation'],
            "passport_number" =>$_POST['passport_number'],                    
            "date_birth" =>$_POST['date_birth'],                      
            "issue_date" =>$_POST['issue_date'],                       
            "expire_date" =>$_POST['expire_date'],   
            "cn_nationality" =>$_POST['cn_nationality'],
            "us_nationality" =>$_POST['us_nationality'],
            "cn_name_street" =>$_POST['cn_name_street'],
            "us_name_street" =>$_POST['us_name_street'],                        
            "post_code" =>$_POST['post_code'], 
            "cn_city" =>$_POST['cn_city'],
            "us_city" =>$_POST['us_city'],
            "cn_state" =>$_POST['cn_state'], 
            "us_state" =>$_POST['us_state'],
            "cn_country" =>$_POST['cn_country'],
            "us_country" =>$_POST['us_country'],                        
            "telephone" =>$_POST['telephone'],                       
            "mobile" =>$_POST['mobile'],                
            "mail" =>$_POST['mail'],                        
            "ticket_category" =>$_POST['ticket_category'],                        
            "quantity_pakcges" =>$_POST['quantity_pakcges'],                        
            "contacts_tel" =>$_POST['contacts_tel'],                       
            "emergency_contacts_tel" =>$_POST['emergency_contacts_tel'],                        
            "us_remarks" =>$_POST['us_remarks'],
            "cn_remarks" =>$_POST['cn_remarks'],
            "audit_bearer" => ''
        );
        
        if($is_save){
            $bearer_info["submit"] = "1";            
        }else{
            $bearer_info["submit"] = "2";
            $bearer_info['sub_time'] = time();
        }
        
        if($is_insert){
            
            $reg = $this->agent->save_bearer_infos($bearer_info,"INSERT",'');
            if($reg){
                $link = array(array('text' => "我的球票", 'href' => 'route.php?con=index&act=pitch'));
                sys_msg("添加持票人信息成功", 0, $link);
            }else{
                $link = array(array('text' => "添加持票人信息", 'href' => "route.php?con=index&act=add_examine_bearer&order_info_id=$_POST[order_info_id]"));
                sys_msg("添加持票人信息失败", 0, $link);
            }
        }else{            
            $reg = $this->agent->save_bearer_infos($bearer_info,"UPDATE"," id = '$_POST[bearer_id]'");
            if($is_flag){
                if($reg){           
                    $link = array(array('text' => "持票人信息列表", 'href' => "route.php?con=index&act=examine_bearer_list"));              
                    sys_msg("更新持票人信息成功", 0, $link);
                 }else{
                    $link = array(array('text' => "添加持票人信息", 'href' => "route.php?con=index&act=edit_bearer&bearer_id=$_POST[bearer_id]&flag=list"));
                    sys_msg("更新持票人信息失败", 0, $link);
                 }
            }else{
                if($reg){           
                    $link = array(array('text' => "查看持票人信息", 'href' => "route.php?con=index&act=examine_bearer&order_info_id=$_POST[order_info_id]"));              
                    sys_msg("更新持票人信息成功", 0, $link);
                 }else{
                     $link = array(array('text' => "添加持票人信息", 'href' => "route.php?con=index&act=edit_bearer&bearer_id=$_POST[bearer_id]"));
                     sys_msg("更新持票人信息失败", 0, $link);
                 }
            }            
        }        
    }
    
    public function examine_bearer_all(){
        $this->assign('ur_here', "查看全部持票人信息");
        $this->display('agent/examine_bearer_all.html');
    }
    
    public function examine_bearer_list(){
        $this->assign('ur_here', "持票人信息列表");
        /* 赛事列表 */
        $game_list = $this->agent->get_game_list();
        $this->assign("game_list", $game_list);        
        $bearer_list = $this->agent->bearer_list();
        $this->assign("bearer_list", $bearer_list['bearer_info']);
        $this->assign("record_count", $bearer_list['record_count']);
        $this->assign("page_count", $bearer_list['page_count']);
        $this->assign("filter", $bearer_list['filter']);
        $this->assign("full_page", 1);
        $this->display('agent/bearer_info_list.html');
    }
    
    /* ------------------------------------------------------ */
    //-- js 分页
    /* ------------------------------------------------------ */
    public function bearer_list_query(){
        $bearer_list = $this->agent->bearer_list();
        $this->assign("bearer_list", $bearer_list['bearer_info']);
        $this->assign("record_count", $bearer_list['record_count']);
        $this->assign("page_count", $bearer_list['page_count']);
        $this->assign("filter", $bearer_list['filter']);
       make_json_result($this->fetch("agent/bearer_info_list.html"), '', array('filter' => $bearer_list['filter'], 'page_count' => $bearer_list['page_count']));
    }
    
    public function bearer_info(){
        $bearer_id = !empty($_GET['id']) ? $_GET['id'] : 0;
        $this->assign('ur_here', "持票人信息");
        $bearer_info = $this->agent->bearer_infos($bearer_id);
        $this->assign("bearer_info", $bearer_info);
        $this->assign('action_link', array('text' => "持票人信息列表", 'href' => "route.php?con=index&act=examine_bearer_list"));
        $this->display('agent/see_bearer_info.html');
    }
    
    public function cheak_bearer(){
        $bearer_id = !empty($_GET['bearer_id']) ? $_GET['bearer_id'] : 0;
        $this->assign('ur_here', "查看持票人信息");       
        $bearer_info = $this->agent->bearer_infos($bearer_id);
        $this->assign("bearer_info", $bearer_info);
        $this->assign('action_link', array('text' => "查看持票人信息", 'href' => "route.php?con=index&act=examine_bearer&order_info_id=$bearer_info[order_id]"));
        $this->display('agent/see_bearer_info.html');
    }
    
    /* 批量提交 */
    public function batch_update(){    
        $res = $this->agent->update_submit($_POST['checkboxes']);
        if($res){
            $link = array(array('text' => "持票人信息列表", 'href' => "route.php?con=index&act=examine_bearer_list"));              
            sys_msg("提交持票人信息成功", 0, $link);
        }else{
            $link = array(array('text' => "查看持票人信息", 'href' => "route.php?con=index&act=examine_bearer&order_info_id=$_POST[order_info_id]"));              
            sys_msg("提交持票人信息失败", 0, $link);
        }
    }
    
    /* 单个提交 */
    public function single_submit(){
        $bearer_id = !empty($_POST['bearer_id']) ? $_POST['bearer_id'] : 0; ;
        $bearer_info["submit"] = "2";
        $bearer_info["sub_time"] = time();
        $res = $this->agent->single_submit_state($bearer_info,$bearer_id);
        if($res){
            $link = array(array('text' => "持票人信息列表", 'href' => "route.php?con=index&act=examine_bearer_list"));              
            sys_msg("提交持票人信息成功", 0, $link);
        }else{
            $link = array(array('text' => "查看持票人信息", 'href' => "route.php?con=index&act=cheak_bearer&bearer_id=$bearer_id"));              
            sys_msg("提交持票人信息失败", 0, $link);
        }
    }

    /* 搜索 */
    public function search(){
        $bearer_list = $this->agent->get_search($_POST);
        $this->assign('ur_here', "持票人信息列表");
        /* 赛事列表 */
        $game_list = $this->agent->get_game_list();
        $this->assign("game_list", $game_list);      
        $this->assign("bearer_list", $bearer_list);          
        $this->display('agent/bearer_info_list.html');
    }
     public function downBearerPDF(){
         $bearer_id = !empty($_GET['id']) ? $_GET['id'] : 0;
         $this->assign('ur_here', "持票人信息");
         $bearer_info = $this->agent->bearer_infos($bearer_id);
         $this->assign("info", $bearer_info);
         sava_pdf($this->fetch("tpl/pdf.tpl", null), "bearer.pdf", true, false,"D");
     }

    public function downVouchPDF(){
        $bearer_id = !empty($_GET['id']) ? $_GET['id'] : 0;
        $file_name = ROOT_PATH . "data/pdf_data/tmp_qr/qr_{$bearer_id}.png";
        $value = 'http://webshop.shankaisports.com/user.php?act=show_pdf_info_agent&bearer_id=' . $bearer_id; //二维码内容
        create_qr($value, $file_name);
        $GLOBALS['smarty']->assign("order_sn", $bearer_id);
        $GLOBALS['smarty']->assign("qr_url", $file_name);
        $html = $GLOBALS['smarty']->fetch("tpl/vouch.tpl", null, null, false);
        sava_pdf($html,"vouch.pdf", false, false, "D");
        if (file_exists($file_name)) {
            @unlink($file_name);
        }
    }
}
