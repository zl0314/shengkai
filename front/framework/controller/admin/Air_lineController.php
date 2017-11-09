<?php

/**
 * Created by PhpStorm.
 * User: stepreal
 * Date: 17/8/20
 * Time: 下午8:59
 */

__load("Controller", "controller");

class Air_lineController extends Controller
{

    public function __construct()
    {
        parent::__construct();
        __load("Air_lineService");
        $this->air_line_service = new Air_lineService();
    }

    public function index()
    {
        $this->assign('ur_here', "航程列表");
        $this->assign('action_link', array('text' => "添加航程", 'href' => 'route.php?con=air_line&act=add'));
        $this->assign('action_link2', array('text' => "舱位列表", 'href' => 'route.php?con=space'));
        $air_line_list = $this->air_line_service->get_All_air_line();
        $this->assign('air_line_list', $air_line_list['airs']);
        $this->assign("record_count", $air_line_list['record_count']);
        $this->assign("page_count", $air_line_list['page_count']);
        $this->assign("filter", $air_line_list['filter']);
        $this->assign("full_page", 1);
        $this->display("air_line/air_line_list.html");
    }

    /* ------------------------------------------------------ */
    //-- js 分页
    /* ------------------------------------------------------ */
    public function index_query()
    {
        $airs = $this->air_line_service->get_All_air_line();
        $this->assign('airs', $airs['airs']);
        $this->assign("record_count", $airs['record_count']);
        $this->assign("page_count", $airs['page_count']);
        $this->assign("filter", $airs['filter']);
        make_json_result($this->fetch("air_line/air_line_list.html"), '', array('filter' => $airs['filter'], 'page_count' => $airs['page_count']));
    }

    /**
     *
     */
    public function get_air_list_from_city()
    {
        $from_city_id = isset($_REQUEST['from_city_id']) ? intval($_REQUEST['from_city_id']) : 0;
        $this->air_line_service->get_air_list_from_city($from_city_id);
    }

    public function add()
    {
        $this->assign('ur_here', "添加航程");
        $this->assign('action_link', array('text' => "航程列表", 'href' => 'route.php?con=air_line'));
        $this->assign('act', "add");
        $this->assign('countries', $this->air_line_service->get_AllRegion(0));
        $this->display("air_line/air_line_info.html");
    }

    public function remove()
    {
        $res = $this->air_line_service->remove($_GET['id']);
        $link = array(array('text' => "航程列表", 'href' => 'route.php?con=air_line'));
        if ($res) {
            sys_msg("删除成功", 0, $link);
        } else {
            sys_msg("删除失败", 0, $link);
        }
    }

    public function edit()
    {
        $act = empty($_POST['act']) ? "add" : trim($_POST['act']);
        if ($act == "add") {
            $res = $this->air_line_service->add($_POST);
            $link = array(array('text' => "航程列表", 'href' => 'route.php?con=air_line'), array('text' => "继续添加航程", 'href' => 'route.php?con=air_line&act=add'));
            if (empty($res)) {
                sys_msg("添加失败", 0, $link);
            } else {
                sys_msg("添加成功", 0, $link);
            }
        } else {
            $air_line_id = isset($_POST['id']) ? intval($_POST['id']) : 0;
            $air_line = $this->air_line_service->getById($air_line_id);
            if (empty($air_line)) {
                sys_msg("航程不存在", 0);
            }

            $res = $this->air_line_service->update($_POST);
            $link = array(array('text' => "航程列表", 'href' => 'route.php?con=air_line'));
            if ($res) {
                sys_msg("更新成功", 0, $link);
            } else {
                sys_msg("更新失败", 0, $link);
            }
        }
    }

    public function update()
    {
        $air_line_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $air_line = $this->air_line_service->getById($air_line_id);
        if (empty($air_line)) {
            sys_msg("航程不存在", 0);
        }

        $this->assign('air_line', $air_line);
        $this->assign('ur_here', "编辑航程");
        $this->assign('countries', $this->air_line_service->get_AllRegion(0));
        $this->assign('action_link', array('text' => "航程列表", 'href' => 'route.php?con=air_line'));
        $this->assign('act', "update");
        $this->display("air_line/air_line_info.html");
    }

    public function batch_drop()
    {
        $link = array(array('text' => "航程列表", 'href' => 'route.php?con=air_line'));
        $res = $this->air_line_service->remove_b($_POST['checkboxes']);
        if ($res) {
            sys_msg("批量删除航程成功", 0, $link);
        } else {
            sys_msg("批量删除航程失败", 0, $link);
        }
    }
}
