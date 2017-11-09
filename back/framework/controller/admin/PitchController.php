<?php

/*
 * 作者：戎青松
 * 时间：9:59:10
 * 
 */

/**
 * Description of PitchController
 *
 * @author Kevin
 */
__load("Controller", "controller");

class PitchController extends Controller {

    //put your code here
    private $pitch;

    public function __construct() {
        parent::__construct();
        __load("PitchService");
        $this->pitch = new PitchService();
    }

    public function index() {
        $this->assign('ur_here', "赛场列表");
        $this->assign('action_link', array('text' => "添加赛场", 'href' => 'route.php?con=pitch&act=add'));
        $pitchs = $this->pitch->get_All_Pitch();
        $this->assign('pitchs', $pitchs['pitchs']);     
        $this->assign("record_count", $pitchs['record_count']);
        $this->assign("page_count", $pitchs['page_count']);
        $this->assign("filter", $pitchs['filter']);
        $this->assign("full_page", 1);
        $this->display("pitch/pitch_list.html");
    }
    
    /* ------------------------------------------------------ */
    //-- js 分页
    /* ------------------------------------------------------ */
    public function index_query(){
        $pitchs = $this->pitch->get_All_Pitch();
        $this->assign('pitchs', $pitchs['pitchs']);
        $this->assign("record_count", $pitchs['record_count']);
        $this->assign("page_count", $pitchs['page_count']);
        $this->assign("filter", $pitchs['filter']);        
        make_json_result($this->fetch("pitch/pitch_list.html"), '', array('filter' => $pitchs['filter'], 'page_count' => $pitchs['page_count']));
    }

    public function add() {
        $this->assign('act', "add");
        $this->assign('countries', $this->pitch->get_AllRegion(0));
        $this->assign('ur_here', "添加赛场");
        $this->assign('action_link', array('text' => "赛场列表", 'href' => 'route.php?con=pitch'));
        $this->display("pitch/pitch_info.html");
    }

    public function remove() {
        //--hechengbin--删除赛场 --start--
        $result = $this->pitch->get_Pitch($_GET['id']);
        //--hechengbin --end--
        $res = $this->pitch->remove($_GET['id']);
        $link = array(array('text' => "赛场列表", 'href' => 'route.php?con=pitch'));
        if ($res) {
            admin_log(addslashes($result['pitch_name']), 'remove', 'pitchs');// 记录日志
            sys_msg("删除成功", 0, $link);
        } else {
            sys_msg("删除失败", 0, $link);
        }
    }

    public function edit() {
        $act = empty($_POST['act']) ? "add" : trim($_POST['act']);
        if ($act == "add") {
            $res = $this->pitch->add($_POST);
            $link = array(array('text' => "赛场列表", 'href' => 'route.php?con=pitch'), array('text' => "继续添加赛场", 'href' => 'route.php?con=pitch&act=add'));
            if (empty($res)) {
                sys_msg("添加失败", 0, $link);
            } else {
                admin_log(addslashes($_POST['pitch_name']), 'add', 'pitchs');// 记录日志
                sys_msg("添加成功", 0, $link);
            }
        } else {
            $res = $this->pitch->update($_POST);
            $link = array(array('text' => "赛场列表", 'href' => 'route.php?con=pitch'));
            if ($res) {
                admin_log(addslashes($_POST['pitch_name']), 'edit', 'pitchs');// 记录日志
                sys_msg("更新成功", 0, $link);
            } else {
                sys_msg("更新失败", 0, $link);
            }
        }
    }

    public function update() {
        $pitch = $this->pitch->get_Pitch($_GET['id']);
        $this->assign('pitch', $pitch);
        $this->assign('ur_here', "编辑赛场");
        $this->assign('countries', $this->pitch->get_AllRegion(0));
        $this->assign('action_link', array('text' => "赛场列表", 'href' => 'route.php?con=pitch'));
        $this->assign('act', "update");
        $this->display("pitch/pitch_info.html");
    }

     public function batch_drop() {
        $link = array(array('text' => "赛场列表", 'href' => 'route.php?con=pitch'));
        $res = $this->pitch->remove_b($_POST['checkboxes']);
        if ($res) {
            sys_msg("批量删除赛场成功", 0, $link);
        } else {
            sys_msg("批量删除赛场失败", 0, $link);
        }
    }
}
