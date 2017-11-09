<?php

/*
 * 作者：鞠嵩
 * 时间：10:18:02
 * 
 */

/**
 * Description of team
 *
 * @author jusong
 */
__load("Controller", "controller");

class TeamController extends Controller {

    //put your code here
    public function __construct() {
        parent::__construct();
    }

    public function index() {
        __load("TeamService");
        $team = new TeamService();
        $list = $team->get_team_list();        
        $this->assign('ur_here', "参赛方列表");
        $this->assign('action_link', array('text' => "添加参赛方", 'href' => 'route.php?con=team&act=add'));
        $this->assign("team_list", $list['teams']);
        $this->assign('filter',       $list['filter']);
        $this->assign('record_count', $list['record_count']);
        $this->assign('page_count',   $list['page_count']);
        $this->assign('full_page',   1);
        $this->display("team/team_list.html");
    }
   
    /* ------------------------------------------------------ */
    //-- js 分页
    /* ------------------------------------------------------ */
    public function index_query(){
         __load("TeamService");
        $team = new TeamService();
        $list = $team->get_team_list();
        $this->assign("team_list", $list['teams']);
        $this->assign('filter',       $list['filter']);
        $this->assign('record_count', $list['record_count']);
        $this->assign('page_count',   $list['page_count']);
        make_json_result($this->fetch("team/team_list.html"), '', array('filter' => $list['filter'], 'page_count' => $list['page_count']));
    }
    
    public function add() {
        __load("TeamService");
        $this->assign('ur_here', "添加参赛方");
        $this->assign('action_link', array('text' => "参赛方列表", 'href' => 'route.php?con=team'));
        $this->display("team/team_info.html");
    }

    public function update() {
        __load("TeamService");
        $team = new TeamService();
        $team_info = $team->get_team($_GET['id']);
        $this->assign('team', $team_info);
        $this->assign('ur_here', "编辑参赛方");
        $this->assign('action_link', array('text' => "参赛方列表", 'href' => 'route.php?con=team'));
        $this->assign('act', "update");
        $this->display("team/team_info.html");
    }

    public function edit() {
        $act = empty($_POST['act']) ? "add" : trim($_POST['act']);
        __load("TeamService");
        $team = new TeamService();
        if ($act == 'add') {
            $link = array(array('text' => "参赛方列表", 'href' => 'route.php?con=team'), array('text' => "继续添加参赛方", 'href' => 'route.php?con=team&act=add'));
            $res = $team->add_team();
            if (empty($res)) {
                sys_msg("添加失败", 0, $link);
            } else {
                admin_log(addslashes($_POST['team_name']), 'add', 'teams');// 记录日志
                sys_msg("添加成功", 0, $link);
            }
        } else {
            $res = $team->update_team();
            $link = array(array('text' => "参赛方列表", 'href' => 'route.php?con=team'));
            if ($res) {
                admin_log(addslashes($_POST['team_name']), 'edit', 'teams');// 记录日志
                sys_msg("更新成功", 0, $link);
            } else {
                sys_msg("更新失败", 0, $link);
            }
        }
        $this->display("team/team_info.html");
    }

    public function remove() {
        $res = __load("TeamService");
        $team = new TeamService();
        //--hechengbin -- 删除参赛方 --start
        $result = $team->get_team($_GET['id']);
        //--hechengbin -- end
        $res = $team->remove_team($_GET['id']);
        $link = array(array('text' => "参赛方列表", 'href' => 'route.php?con=team'));
        if ($res) {
            admin_log(addslashes($result['team_name']), 'remove', 'teams');// 记录日志
            sys_msg("删除成功", 0, $link);
        } else {
            sys_msg("删除失败", 0, $link);
        }
    }

    public function batch_drop() {
        __load("TeamService");
        $team = new TeamService();
        $link = array(array('text' => "参赛方列表", 'href' => 'route.php?con=team'));
        $res = $team->remove_b($_POST['checkboxes']);
        if ($res) {
            sys_msg("批量删除参赛方成功", 0, $link);
        } else {
            sys_msg("批量删除参赛方失败", 0, $link);
        }
    }

}
