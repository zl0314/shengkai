<?php

/*
 * 作者：戎青松
 * 时间：10:18:02
 * 
 */

/**
 * Description of game
 *
 * @author Kevin
 */
__load("Controller", "controller");

class BillController extends Controller {

    //put your code here
    public function __construct() {
        parent::__construct();
        
    }

    public function index() {
        __load("BillService");
        $bill = new BillService();
        $this->assign('ur_here', "广告列表");
        $this->assign('action_link', array('text' => "添加广告", 'href' => 'route.php?con=bill&act=add'));
        $bill = $bill->get_bill_list();
        $this->assign("bill_list", $bill['bill']);
        $this->assign("record_count", $bill['record_count']);
        $this->assign("page_count", $bill['page_count']);
        $this->assign("filter", $bill['filter']);   
        $this->assign('full_page', 1);
        $this->display("bill/bill_list.html");
    }
    
    /* ------------------------------------------------------ */
    //-- js 分页
    /* ------------------------------------------------------ */
    public function index_query(){
         __load("BillService");
        $bill = new BillService();
        $bill = $bill->get_bill_list();
        $this->assign("bill_list", $bill['bill']);
        $this->assign("record_count", $bill['record_count']);
        $this->assign("page_count", $bill['page_count']);
        $this->assign("filter", $bill['filter']);    
        make_json_result($this->fetch("bill/bill_list.html"), '', array('filter' => $bill['filter'], 'page_count' => $bill['page_count']));
    }

    public function add() {
        $this->assign('ur_here', "添加广告");
        $this->assign('action_link', array('text' => "广告列表", 'href' => 'route.php?con=bill'));
        $this->assign('act', "add");
        $this->display("bill/bill_info.html");
    }
    public function update() {
        __load("BillService");
        $bill = new BillService();
        $bill_info = $bill->get_bill($_GET['id']);
        $this->assign('bill_info', $bill_info);
        $this->assign('ur_here', "添加广告");
        $this->assign('action_link', array('text' => "广告列表", 'href' => 'route.php?con=bill'));
        $this->assign('act', "update");
        $this->display("bill/bill_info.html");
    }

    public function edit() {
        $act = empty($_POST['act']) ? "add" : trim($_POST['act']);
        __load("BillService");
        $bill = new BillService();
        
        //数据处理
        $_POST['start_time'] = local_strtotime($_POST['start_time']);
        $_POST['end_time'] = local_strtotime($_POST['end_time']);
        if($_POST['bill_source'] && $_POST['media_type'] == 0){
            $_POST['bill_source'] = $_POST['bill_source'];
        }elseif($_POST['bill_source1'] && $_POST['media_type'] == 1){
            $_POST['bill_source'] = $_POST['bill_source1'];
        }elseif($_POST['bill_source2'] && $_POST['media_type'] == 2){
            $_POST['bill_source'] = $_POST['bill_source2'];
        }
        if ($act == "add") {
            /* 查看广告名称是否有重复 */
            $sql = "SELECT COUNT(*) FROM " .$GLOBALS['ecs']->table('bill'). " WHERE bill_name = '$_POST[bill_name]'";
            if ($GLOBALS['db']->getOne($sql) > 0){
                $link[] = array('text' => "点击返回上一步", 'href' => 'javascript:history.back(-1)');
                sys_msg("广告名称已存在！", 0, $link);
            }
            
            $link = array(array('text' => "广告列表", 'href' => 'route.php?con=bill'), array('text' => "继续添加广告", 'href' => 'route.php?con=bill&act=add'));
            $res = $bill->add_bill($_POST);
            if (empty($res)) {
                sys_msg("添加失败", 0, $link);
            } else {
                admin_log(addslashes($_POST['bill_name']), 'add', 'bills');// 记录日志
                sys_msg("添加成功", 0, $link);
            }
        } else {
            $res = $bill->update_bill($_POST);
            $link = array(array('text' => "广告列表", 'href' => 'route.php?con=bill'));
            if ($res) {
                admin_log(addslashes($_POST['bill_name']), 'edit', 'bills');// 记录日志
                sys_msg("更新成功", 0, $link);
            } else {
                sys_msg("更新失败", 0, $link);
            }
        }
        $this->display("bill/bill_info.html");
    }

    public function remove() {
        __load("BillService");
        $bill = new BillService();
        //--hechengbin -- 删除广告 --start
        $result = $bill->get_bill($_GET['id']);
        //--hechengbin -- end
        $res = $bill->remove_bill($_GET['id']);
        $link = array(array('text' => "广告列表", 'href' => 'route.php?con=bill'));
        if ($res) {
            admin_log(addslashes($result['bill_name']), 'remove', 'bills');// 记录日志
            sys_msg("删除成功", 0, $link);
        } else {
            sys_msg("删除失败", 0, $link);
        }
    }
}
