<?php

/*
 * 作者：戎青松
 * 时间：9:59:10
 * 
 */

/**
 * Description of HotelController
 *
 * @author Kevin
 */
__load("Controller", "controller");

class AgentController extends Controller {

    //put your code here
    private $agent, $game, $schedule, $number;

    public function __construct() {
        parent::__construct();
        __load("AgentService");
        __load("GameService");
        __load("ScheduleService");
        __load("NumberService");
        $this->agent = new AgentService();
        $this->game = new GameService();
        $this->schedule = new ScheduleService();
        $this->number = new NumberService();
    }

    public function index() {
        $this->assign('ur_here', "代理商列表");
        $this->assign('action_link', array('text' => "添加代理商", 'href' => 'route.php?con=agent&act=add'));
        $agents = $this->agent->get_AllAgent();  
        $this->assign('agents', $agents['agent']);
        $this->assign("record_count", $agents['record_count']);
        $this->assign("page_count", $agents['page_count']);
        $this->assign("filter", $agents['filter']);
        $this->assign("full_page", 1);
        $this->display("agent/agent_list.html");
    }
    
    /* ------------------------------------------------------ */
    //-- js 分页
    /* ------------------------------------------------------ */
    public function index_query(){
        $agents = $this->agent->get_AllAgent();        
        $this->assign('agents', $agents['agent']);
        $this->assign("record_count", $agents['record_count']);
        $this->assign("page_count", $agents['page_count']);
        $this->assign("filter", $agents['filter']);        
        make_json_result($this->fetch("agent/agent_list.html"), '', array('filter' => $agents['filter'], 'page_count' => $agents['page_count']));
    }

    public function add() {
        $this->assign('act', "add");
//        $this->assign('countries', $this->hotel->get_AllRegion(0));
        $this->assign('ur_here', "添加代理商");
        $this->assign('action_link', array('text' => "代理商列表", 'href' => 'route.php?con=agent'));
        $this->display("agent/agent_info.html");
    }

    public function status() {
        $state = !empty($_GET['s']) ? $_GET['s'] : 0;
        if ($state == 0) {
            $msg = "启用成功";
        } elseif ($state == 1) {
            $msg = "冻结成功";
        }
        $data = array('state' => $state);
        $res = $this->agent->agent_status($data, $_GET['hotel_id']);
        $link = array(array('text' => "代理商列表", 'href' => 'route.php?con=agent'));
        if ($res) {
            sys_msg("$msg", 0, $link);
        } else {
            sys_msg("$msg", 0, $link);
        }
    }

    public function remove() {
        $res = $this->agent->remove($_GET['id']);
        $link = array(array('text' => "代理商列表", 'href' => 'route.php?con=agent'));
        if ($res) {
            sys_msg("删除成功", 0, $link);
        } else {
            sys_msg("删除失败", 0, $link);
        }
    }

    public function edit() {
        $act = empty($_POST['act']) ? "add" : trim($_POST['act']);
        if ($act == "add") {
            $res = $this->agent->add($_POST);
            $link = array(array('text' => "代理商列表", 'href' => 'route.php?con=agent'), array('text' => "继续添加代理商", 'href' => 'route.php?con=agent&act=add'));
            if (empty($res)) {
                sys_msg("添加失败", 0, $link);
            } else {
                sys_msg("添加成功", 0, $link);
            }
        } else {
            $res = $this->agent->update($_POST);
            $link = array(array('text' => "代理商列表", 'href' => 'route.php?con=agent'));
            if ($res) {
                sys_msg("更新成功", 0, $link);
            } else {
                sys_msg("更新失败", 0, $link);
            }
        }
    }

    public function update() {
        $agent = $this->agent->get_Agent($_GET['id']);
        $this->assign('agent', $agent);
        $this->assign('ur_here', "编辑代理商");
        $this->assign('action_link', array('text' => "代理商列表", 'href' => 'route.php?con=agent'));
        $this->assign('act', "update");
        $this->display("agent/agent_info.html");
    }

    public function batch_drop() {
        $link = array(array('text' => "代理商列表", 'href' => 'route.php?con=agent'));
        //$res = $this->hotel->remove_b($_POST['checkboxes']);
        $res = $this->agent->remove_b($_POST['checkboxes']);
        if ($res) {
            sys_msg("批量删除代理商成功", 0, $link);
        } else {
            sys_msg("批量删除代理商失败", 0, $link);
        }
    }

    public function order_list() {
        $this->assign('ur_here', "代理商订单列表");
        $this->assign('action_link', array('text' => "新增代理商订单", 'href' => 'route.php?con=agent&act=add_order'));
        $agent_order = $this->agent->get_All_Order();
//        echo $agent_order['datas']['num_name'];
//        echo "<pre>";
//        print_r($agent_order);
        $this->assign('agent_orders', $agent_order['agent_order']);
        $this->assign('num_name', $agent_order['datas']['num_name']);
        $this->assign('region_name', $agent_order['datas']['region_name']);
        $this->assign('pitch_name', $agent_order['datas']['pitch_name']);
        $this->assign("record_count", $agent_order['record_count']);
        $this->assign("page_count", $agent_order['page_count']);
        $this->assign("filter", $agent_order['filter']);
        $this->assign("full_page", 1);
        $this->display("agent/agent_order_list.html");
    }
    
    /* ------------------------------------------------------ */
    //-- js 分页
    /* ------------------------------------------------------ */
    public function order_list_query(){
        $agent_order = $this->agent->get_All_Order();        
        $this->assign('agent_orders', $agent_order['agent_order']);
        $this->assign("record_count", $agent_order['record_count']);
        $this->assign("page_count", $agent_order['page_count']);
        $this->assign("filter", $agent_order['filter']);
        make_json_result($this->fetch("agent/agent_order_list.html"), '', array('filter' => $agent_order['filter'], 'page_count' => $agent_order['page_count']));
    }

    public function pitch_list() {
        $agent_id = empty($_GET['id']) ? 0 : intval($_GET['id']);
        $this->assign('ur_here', "查看代理商票务信息");
        $pitch = $this->agent->get_pitch_list($agent_id);
        $this->assign("agent_orders", $pitch['pitch_order']);
        $this->assign("record_count", $pitch['record_count']);
        $this->assign("page_count", $pitch['page_count']);
        $this->assign("filter", $pitch['filter']);
        $this->assign("full_page", 1);
        $_SESSION['query_agent_id'] = $agent_id;
        $this->display("agent/agent_pitch_list.html");
    }
    
    /* ------------------------------------------------------ */
    //-- js 分页
    /* ------------------------------------------------------ */
    public function pitch_list_query(){
        $pitch = $this->agent->get_pitch_list($_SESSION['query_agent_id']);
        $this->assign("agent_orders", $pitch['pitch_order']);
        $this->assign("record_count", $pitch['record_count']);
        $this->assign("page_count", $pitch['page_count']);
        $this->assign("filter", $pitch['filter']);
        make_json_result($this->fetch("agent/agent_pitch_list.html"), '', array('filter' => $pitch['filter'], 'page_count' => $pitch['page_count']));
    }
    
    public function pitch_info() {
        $order_info_id = empty($_GET['id']) ? 0 : intval($_GET['id']);
        $this->assign('ur_here', "查看票务信息详情");
        $pitch_info = $this->agent->pitch_bearer();
        $this->assign("pitch_info", $pitch_info['bearer_info']);
        $this->assign("record_count", $pitch_info['record_count']);
        $this->assign("page_count", $pitch_info['page_count']);
        $this->assign("filter", $pitch_info['filter']);
        $this->assign("full_page", 1);
        $this->display("agent/agent_pitch_info.html");
    }
    
    /* ------------------------------------------------------ */
    //-- js 分页
    /* ------------------------------------------------------ */
    public function pitch_info_query(){
        $pitch_info = $this->agent->pitch_bearer();
        $this->assign("pitch_info", $pitch_info['bearer_info']);
        $this->assign("record_count", $pitch_info['record_count']);
        $this->assign("page_count", $pitch_info['page_count']);
        $this->assign("filter", $pitch_info['filter']);
        make_json_result($this->fetch("agent/agent_pitch_info.html"), '', array('filter' => $pitch_info['filter'], 'page_count' => $pitch_info['page_count']));
    }

    public function bearer_info() {
        $this->assign('ur_here', "查看持票人信息");
        $bearer_id = empty($_GET['bearer_id']) ? 0 : intval($_GET['bearer_id']);
        $is_list = $_GET['flag'] == 'list';
        $bearer_info = $this->agent->get_bearer_info($bearer_id);
        if ($is_list) {
            $this->assign('action_link', array('text' => "审批列表", 'href' => "route.php?con=agent&act=examine"));
        } else {
            $this->assign('action_link', array('text' => "查看票务信息详情", 'href' => "route.php?con=agent&act=pitch_info&id=$bearer_info[order_id]"));
        }
        $this->assign("bearer_info", $bearer_info);
        $this->assign("bearer_id", $bearer_id);
        $this->display("agent/bearer_info.html");
    }

    public function audit_bearer() {
        $bearer_id = empty($_GET['bearer_id']) ? 0 : intval($_GET['bearer_id']);
        $audit_bearer = empty($_GET['status']) ? 0 : intval($_GET['status']);
        //根据用户信息id查询用户信息
        $bearer_info = $this->agent->get_bearer_info($bearer_id);
        echo "<pre>";
        print_r($bearer_info);die;
        $data = array("audit_bearer" => $audit_bearer);
        $res = $this->agent->update_audit_bearer($data, $bearer_id);
        if ($audit_bearer == 3) {
            $mas = "审核不通过";
            require_once(ROOT_PATH . 'includes/lib_sms.php');
            //----hechengbin --- start ----
            if(substr($bearer_info['mobile'],0,2) == '82'){
                sendsms($bearer_info['mobile'], "The verification of the holder's information is rejected. Please fill out again.");
            }else{
                sendsms($bearer_info['mobile'], "【盛开国际旅行社】您已提交的持票人{$bearer_info['cn_customer_name']}审核不通过，请重新填写。【400-885-0132】");
            }
            //----hechengbin --- end ----
            $this->agent->send_mail('shenhe_No', array("to" => array($bearer_info['mail']), "sub" => array('%cn_customer_name%' => array($bearer_info['cn_customer_name']))));
        } else {
            $mas = "审核通过";
            require_once(ROOT_PATH . 'includes/lib_sms.php');
            //----hechengbin --- start ----
            if(substr($bearer_info['mobile'],0,2) == '82'){
                sendsms($bearer_info['mobile'], "The holder's information of the order 000000 has been successfully approved. Please check and download the ticket voucher in the personal center.");
            }else{
                sendsms($bearer_info['mobile'], "【盛开国际旅行社】您已提交的持票人{$bearer_info['cn_customer_name']}审核成功，您可以在管理中心查看和下载购票凭证。【400-885-0132】");
            }
            //----hechengbin --- end ----
            $this->agent->send_mail('shenhe_Yes', array("to" => array($bearer_info['mail']), "sub" => array('%cn_customer_name%' => array($bearer_info['cn_customer_name']))));

        }
        if ($res) {
            $mas .= "操作成功";
        } else {
            $mas .= "操作失败";
        }
        die($mas);
    }

    public function add_order() {
        $this->assign('ur_here', "新增代理商订单");
        $this->assign('action_link', array('text' => "代理商订单列表", 'href' => 'route.php?con=agent&act=order_list'));
        $this->assign('agents', $this->agent->get_All_Agent());
        $this->assign('games', $this->game->get_list());
        $this->display("agent/agent_order_info.html");
    }

    public function ajax_game() {
        $game_id = $_GET['id'];
        $schedules = $this->schedule->get_list($game_id);
        exit(json_encode($schedules));
    }

    public function ajax_sche() {
        $sche_id = $_GET['id'];
        $numbers = $this->number->get_list($sche_id);
        exit(json_encode($numbers));
    }

    public function ajax_good_attr() {
        $number_id = $_GET['id'];
        $arrt = $this->number->get_number_attr($number_id);
        exit(json_encode($arrt));
    }

    public function insert_order() {

        $link = array(array('text' => "代理商订单列表", 'href' => 'route.php?con=agent&act=order_list'));

        if ($this->agent->add_order($_POST)) {
            sys_msg("代理商订单添加成功", 0, $link);
        } else {
            sys_msg("代理商订单添加失败", 0, $link);
        }
    }

    public function examine() {
        $this->assign('ur_here', "审批列表");
        $bearer_list = $this->agent->get_examine_bearer_list();
        $this->assign("pitch_info", $bearer_list['bearer_list']);
        $this->assign("record_count", $bearer_list['record_count']);
        $this->assign("page_count", $bearer_list['page_count']);
        $this->assign("filter", $bearer_list['filter']);
        $this->assign("full_page", 1);
        /* 代理商 */
        $this->assign('agents', $this->agent->get_agent_list());
        /* 赛事 */
        $this->assign('game_list', $this->game->get_list());
        $this->display("agent/examine_list.html");
    }

    /* ------------------------------------------------------ */
    //-- js 分页、查询
    /* ------------------------------------------------------ */
    public function examine_query() {     
        $bearer_list = $this->agent->get_examine_bearer_list($_REQUEST);
        $this->assign("pitch_info", $bearer_list['bearer_list']);
        $this->assign("record_count", $bearer_list['record_count']);
        $this->assign("page_count", $bearer_list['page_count']);
        $this->assign("filter", $bearer_list['filter']);
        make_json_result($this->fetch("agent/examine_list.html"), '', array('filter' => $bearer_list['filter'], 'page_count' => $bearer_list['page_count']));
    }   

    public function batch_update() {
        $res = $this->agent->update_submit($_POST['checkboxes']);
        if ($res) {
            $link = array(array('text' => "审批列表", 'href' => "route.php?con=agent&act=examine"));
            sys_msg("批量审核完成", 0, $link);
        } else {
            $link = array(array('text' => "审批列表", 'href' => "route.php?con=agent&act=examine"));
            sys_msg("批量审核失败", 0, $link);
        }
    }
    
    public function batch_update_audit() {
        
        $res = $this->agent->update_submit($_POST['checkboxes']);
        $link = array(array('text' => "查看持票人审核状态", 'href' => "route.php?con=agent&act=see_audit_bearer&id=$_REQUEST[id]&audit_bearer=$_REQUEST[audit_bearer]"));
        if ($res) {            
            sys_msg("批量审核完成", 0, $link);
        } else {            
            sys_msg("批量审核失败", 0, $link);
        }
    }
    
    public function see_audit_bearer(){    
        $this->assign('ur_here', "查看持票人审核状态");     
        $this->assign('action_link', array('text' => "代理商订单列表", 'href' => 'route.php?con=agent&act=order_list'));
        $audit_bearer_info = $this->agent->get_audit_bearer_info();
        $this->assign("pitch_info", $audit_bearer_info['bearer_info']);
        $this->assign("record_count", $audit_bearer_info['record_count']);
        $this->assign("page_count", $audit_bearer_info['page_count']);
        $this->assign("filter", $audit_bearer_info['filter']);
        $this->assign("full_page", 1);
        $this->assign('id', $_REQUEST['id']);
        $this->assign('audit_bearer', $_REQUEST['audit_bearer']);
        $this->display("agent/audit_bearer_list.html");
    }
    
    public function see_audit_bearer_query(){
        $audit_bearer_info = $this->agent->get_audit_bearer_info();
        $this->assign("pitch_info", $audit_bearer_info['bearer_info']);
        $this->assign("record_count", $audit_bearer_info['record_count']);
        $this->assign("page_count", $audit_bearer_info['page_count']);
        $this->assign("filter", $audit_bearer_info['filter']);
        make_json_result($this->fetch("agent/audit_bearer_list.html"), '', array('filter' => $audit_bearer_info['filter'], 'page_count' => $audit_bearer_info['page_count']));
    }
    
    public function edit_agent_ticket_code(){
        /* 检查权限 */
        check_authz_json('order_edit');
        $id = empty($_POST['id']) ? 0 : intval($_POST['id']);
        $ticket_code = empty($_POST['val']) ? "" : json_str_iconv(trim($_POST['val']));

        if($id == 0) {
            exit(json_encode("系统错误，请稍后再试"));
        }else{
            if(preg_match("/[\x7f-\xff]/", $ticket_code)){
                exit(json_encode("系统错误，请稍后再试"));
            }else{
                $is_agentCode_repeat = $GLOBALS['db']->getOne("SELECT id FROM " . $GLOBALS['ecs']->table('bearer_info') . " WHERE ticket_code='$ticket_code'");//代理商球票票号判重
                $is_commonCode_repeat = $GLOBALS['db']->getOne("SELECT rec_id FROM " . $GLOBALS['ecs']->table('order_ticket') . " WHERE ticket_code='$ticket_code'");//直销球票票号判重
                if(!empty($is_agentCode_repeat) || !empty($is_commonCode_repeat)){
                   exit(json_encode("票号已存在"));
                }else{
                   $sql = 'UPDATE ' . $GLOBALS['ecs']->table('bearer_info') . " SET ticket_code='$ticket_code' WHERE id = '$id'";
                   if ($GLOBALS['db']->query($sql)) {
                        if (empty($ticket_code)) {
                            exit(json_encode("更新票号失败"));
                        } else {
                            exit(json_encode("更新票号成功"));
                        }
                   } else {
                        exit(json_encode($GLOBALS['db']->errorMsg()));
                   } 
                } 
            }
        }
    }
}
