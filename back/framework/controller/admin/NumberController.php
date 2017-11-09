<?php

/**
 * 作者：鞠嵩
 * 时间：9:59:10
 * 
 */
/**
 * Description of NumberController
 *
 * @author jusong
 */
__load("Controller", "controller");

class NumberController extends Controller {

    //put your code here
    private $number, $schedule, $pitch, $game, $team;

    public function __construct() {
        parent::__construct();
        __load("NumberService");
        __load("ScheduleService");
        __load("PitchService");
        __load("GameService");
        __load("TeamService");
        __load("ColorService");
        $this->number = new NumberService();
        $this->schedule = new ScheduleService();
        $this->pitch = new PitchService();
        $this->game = new GameService();
        $this->team = new TeamService();
        $this->color = new ColorService();
    }

    public function index() {
        $this->assign('ur_here', "场次列表");
        $this->assign('action_link', array('text' => "添加场次", 'href' => 'route.php?con=number&act=add&id=' . $_GET['id'] . '&p_id=' . $_GET['p_id']));
        $this->assign('action_link2', array('text' => "返回赛程列表", 'href' => 'route.php?con=schedule&id=' . $_GET['p_id']));
        $numbers = $this->number->get_number_list();
        $this->assign('number_list', $numbers['number']);
        $this->assign("record_count", $numbers['record_count']);
        $this->assign("page_count", $numbers['page_count']);
        $this->assign("filter", $numbers['filter']);
        $this->assign("full_page", 1);
        $this->assign('schedule_id', $_GET['id']);
        $this->assign('p_id', $_GET['p_id']);
        $this->display("number/number_list.html");
    }

    /* ------------------------------------------------------ */

    //-- js 分页
    /* ------------------------------------------------------ */
    public function index_query() {
        $numbers = $this->number->get_number_list();
        $this->assign('number_list', $numbers['number']);
        $this->assign("record_count", $numbers['record_count']);
        $this->assign("page_count", $numbers['page_count']);
        $this->assign("filter", $numbers['filter']);
        $this->assign('schedule_id', $_REQUEST['id']);
        $this->assign('p_id', $_REQUEST['p_id']);
        __log("p_id:" . $_REQUEST['p_id']);
        __log("id:" . $_REQUEST['id']);
        make_json_result($this->fetch("number/number_list.html"), '', array('filter' => $numbers['filter'], 'page_count' => $numbers['page_count']));
    }

    public function add() {
        
        $this->assign('ur_here', "添加场次");
        $this->assign('action_link', array('text' => "场次列表", 'href' => 'route.php?con=number&id=' . $_GET['id'] . "&p_id=" . $_GET['p_id']));
        $this->assign('action_link2', array('text' => "返回赛程列表", 'href' => 'route.php?con=schedule&&id=' . $_GET['p_id']));
        $this->assign('pitch_list', $this->pitch->get_AllPitch());
        $this->assign('game_list', $this->game->get_list());
        $this->assign('team_list', $this->team->get_list());
        $this->assign('num_list', $this->number->get_AllNumber());
        $this->assign('color_list', $this->color->get_Color_info($_GET['p_id']));
        $this->assign('sche_id', $_GET['id']);
        $this->assign('game_id', $_GET['p_id']);
        $partent_info = $this->schedule->get_scheduleName($_GET['id']);
        $this->assign('game_name', $partent_info['game_name']);
        $this->assign('sche_name', $partent_info['sche_name']);
        $this->assign('act', 'add');
        $this->display("number/number_info.html");
    }

    public function remove() {
        //--hechengbin -- 删除场次 --start
        $result = $this->number->get_Number($_GET['id']);
        //--hechengbin -- end
        $res = $this->number->remove($_GET['id']);
        $link = array(array('text' => "场次列表", 'href' => 'route.php?con=number&id=' . $_GET['sche_id'] . "&p_id=" . $_GET['p_id']));
        if ($res) {
            admin_log(addslashes($result['num_name']), 'remove', 'number');// 记录日志
            sys_msg("删除成功", 0, $link);
        } else {
            sys_msg("删除失败", 0, $link);
        }
    }

    public function update() {
        $team_list = $this->team->get_list(); //所有的参赛方列表
        $teams = $this->number->get_team($_GET['id']); //属于该场次的参赛方'
        $this->array_diff($team_list, $teams);
        $this->assign('ur_here', "编辑场次");
        $this->assign('action_link', array('text' => "场次列表", 'href' => 'route.php?con=number&id=' . $_GET['sche_id'].'&p_id='. $_GET['p_id']));
        $this->assign('action_link2', array('text' => "返回赛程列表", 'href' => 'route.php?con=schedule&&id=' . $_GET['p_id']));
        $this->assign('act', 'update');
        $num = $this->number->get_Number($_GET['id']);
        $this->assign('color_list', $this->color->get_Color_info($_GET['p_id']));
        $num['teams'] = $this->get_json_teams($teams);
        $this->assign("num", $num);
        $this->assign('pitch_list', $this->pitch->get_AllPitch());
        $this->assign("teams", $teams);
        $this->assign('sche_id', $num['sche_id']);
        $this->assign('game_id', $_GET['p_id']);
        $this->assign('team_list', $team_list);
        $this->display("number/number_info.html");
    }

    public function edit() {
        $act = empty($_POST['act']) ? "add" : trim($_POST['act']);
        if ($act == "add") {
            $res = $this->number->add_Number($_POST);
            $link = array(array('text' => "场次列表", 'href' => 'route.php?con=number&id=' . $_POST['sche_id'] . "&p_id=" . $_POST['game_id']), array('text' => "继续添加场次", 'href' => 'route.php?con=number&act=add&id=' . $_POST['sche_id'] . "&p_id=" . $_POST['game_id']));
            if (empty($res)) {
                sys_msg("添加失败", 0, $link);
            } else {
                admin_log(addslashes($_POST['num_name']), 'add', 'number');// 记录日志
                sys_msg("添加成功", 0, $link);
            }
        } else {
            $res = $this->number->update($_POST);
            $link = array(array('text' => "场次列表", 'href' => 'route.php?con=number&id=' . $_POST['sche_id'] . "&p_id=" . $_POST['game_id']));
            if ($res) {
                admin_log(addslashes($_POST['num_name']), 'edit', 'number');// 记录日志
                sys_msg("更新成功", 0, $link);
            } else {
                sys_msg("更新失败", 0, $link);
            }
        }
    }

    public function batch_drop() {
        $link = array(array('text' => "场次列表", 'href' => 'route.php?con=number&id=' . $_GET['sche_id'] . "&p_id=" . $_GET['p_id']));
        $res = $this->number->remove_b($_POST['checkboxes']);
        if ($res) {
            sys_msg("批量删除场次成功", 0, $link);
        } else {
            sys_msg("批量删除场次失败", 0, $link);
        }
    }

    /**
     * 计算两个数组间的差集
     * @param type $array1 主数组（被比较的数组）
     * @param type $array2 副数组
     * @return type
     */
    private function array_diff(&$array1, $array2) {
        if (empty($array1)) {
            return null;
        } else {
            if (empty($array2)) {
                return $array1;
            } else {
                foreach ($array2 as $value2) {
                    foreach ($array1 as $key => $value) {
                        if ($value2 == $value) {
                            unset($array1[$key]);
                        }
                    }
                }
                return $array1;
            }
        }
    }

    private function get_json_teams($teams) {
        $temp = array();
        if (empty($teams)) {
            return null;
        }
        foreach ($teams as $value) {
            array_push($temp, $value['id']);
        }
        return json_encode($temp);
    }

}
