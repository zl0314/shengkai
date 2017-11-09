<?php

/**
 * 作者：鞠嵩
 * 时间：10:59:10
 * 
 */
/**
 * Description of ScheduleController
 *
 * @author jusong
 */
__load("Controller", "controller");

class ScheduleController extends Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        __load("ScheduleService");
        $id = $_GET['id'];
        $schedule = new ScheduleService();
        $schedules = $schedule->get_sche_list();
        $this->assign("schedule_list",$schedules['schedule']);        
        $this->assign("record_count", $schedules['record_count']);
        $this->assign("page_count", $schedules['page_count']);
        $this->assign("filter", $schedules['filter']);
        $this->assign("full_page", 1);
        $this->assign("p_id", $id);
        $this->assign('ur_here', "赛程列表");
        $this->assign('action_link', array('text' => "添加赛程", 'href' => 'route.php?con=schedule&act=add&id=' . $id));
        $this->assign('action_link2', array('text' => "返回赛事列表", 'href' => 'route.php?con=game&act=index&id=' . $id));
        $this->display("schedule/schedule_list.html");
    }
    
    /* ------------------------------------------------------ */
    //-- js 分页
    /* ------------------------------------------------------ */
    public function index_query(){
        __load("ScheduleService");
        $schedule = new ScheduleService();
        $schedules = $schedule->get_sche_list();
        $this->assign("schedule_list",$schedules['schedule']);        
        $this->assign("record_count", $schedules['record_count']);
        $this->assign("page_count", $schedules['page_count']);
        $this->assign("filter", $schedules['filter']);
        $this->assign("p_id", $_REQUEST['id']);
        make_json_result($this->fetch("schedule/schedule_list.html"), '', array('filter' => $schedules['filter'], 'page_count' => $schedules['page_count']));
    }

    public function remove() {
        __load("ScheduleService");
        $id = $_GET['id'];
        $p_id = $_GET['p_id'];
        $schedule = new ScheduleService();
        //--hechengbin -- 删除赛程 --start
        $result = $schedule->get_schedule($_GET['id']);
        //--hechengbin -- end
        $res = $schedule->remove($id);
        $link = array(array('text' => "赛程列表", 'href' => 'route.php?con=schedule&id=' . $p_id));
        if ($res) {
            admin_log(addslashes($result['sequence']), 'remove', 'schedule');// 记录日志
            sys_msg("删除成功", 0, $link);
        } else {
            sys_msg("该赛程下存在场次，不能删除", 0, $link);
        }
    }

    public function update() {
        __load("ScheduleService");
        $schedule = new ScheduleService();
        $p_id = $_GET['p_id'];
        $this->assign('schedule_id', $_GET['id']);
        $this->assign('p_id', $p_id);
        $this->assign('schedule', $schedule->get_schedule($_GET['id']));
        $this->assign('game_name', $schedule->get_game_name($_GET['p_id']));
        $this->assign('game_id', $schedule->get_gameid($_GET['id']));
        $this->assign('sche_name', $schedule->get_scheName($_GET['id']));
        $this->assign('ur_here', "编辑赛程");
        $this->assign('act', "update");
        $this->assign('action_link', array('text' => "赛程列表", 'href' => 'route.php?con=schedule&id=' . $_GET['p_id']));
        $this->assign('action_link2', array('text' => "返回赛事列表", 'href' => 'route.php?con=game&act=index&id=' . $p_id));
        $this->display("schedule/schedule_info.html");
    }

    public function add() {
        __load("ScheduleService");
        $schedule = new ScheduleService();
        $this->assign('game_id', $_GET['id']);
        $this->assign('game_name', $schedule->get_game_name(empty($_GET['id'])? 0 : intval($_GET['id'])));
        $this->assign('ur_here', "添加赛程");
        $this->assign('action_link', array('text' => "赛程列表", 'href' => 'route.php?con=schedule&id=' . $_GET['id']));
        $this->assign('action_link2', array('text' => "返回赛事列表", 'href' => 'route.php?con=game&act=index&id=' . $_GET['id']));
        $this->assign('act', "add");
        $this->display("schedule/schedule_info.html");
    }

    public function edit() {
        $act = empty($_POST['act']) ? "add" : trim($_POST['act']);
        __load("ScheduleService");
        $schedule = new ScheduleService();
        $p_id = $_GET['p_id'];
        if ($act == 'add') {
            $res = $schedule->add_sche($_POST);
            $link = array(array('text' => "赛程列表", 'href' => 'route.php?con=schedule&id=' . $p_id));
            if (empty($res)) {
                sys_msg("添加失败", 0, $link);
            } else {
                admin_log(addslashes($_POST['sequence']), 'add', 'schedule');// 记录日志
                sys_msg("添加成功", 0, $link);
            }
        } else {
            $res = $schedule->update_sche($_POST);
            $link = array(array('text' => "赛程列表", 'href' => 'route.php?con=schedule&id=' . $p_id));
            if ($res) {
                admin_log(addslashes($_POST['sequence']), 'edit', 'schedule');// 记录日志
                sys_msg("更新成功", 0, $link);
            } else {
                sys_msg("更新失败", 0, $link);
            }
        }
        $this->display("schedule/schedule_info.html");
    }

    public function batch_drop() {
        __load("ScheduleService");
        $schedule = new ScheduleService();
        $link = array(array('text' => "赛程列表", 'href' => 'route.php?con=schedule&id=' . $_GET['id']));
        $res = $schedule->remove_batch($_POST['checkboxes']);
        if ($res) {
            sys_msg("批量删除赛程成功", 0, $link);
        } else {
            sys_msg("没有可删除的赛程", 0, $link);
        }
    }
}
?>
