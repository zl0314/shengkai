<?php

/**
 * Created by PhpStorm.
 * User: stepreal
 * Date: 17/8/20
 * Time: 下午8:59
 */
__load("Controller", "controller");

class Air_line_numController extends Controller
{

    public function __construct()
    {
        parent::__construct();
        __load("Air_lineService");
        $this->air_line_service = new Air_lineService();
        __load("Air_line_numService");
        $this->air_line_num_service = new Air_line_numService();
    }

    public function index()
    {
        $air_line_id = isset($_GET['air_line_id']) ? intval($_GET['air_line_id']) : 0;
        $type = isset($_GET['type']) ? intval($_GET['type']) : 0;
        $air_line = $this->air_line_service->getById($air_line_id);
        if (empty($air_line)) {
            sys_msg("航程不存在", 0);
        }
        $this->assign('ur_here', "航程{$air_line['title']}库存列表");
        $this->assign('action_link', array('text' => "航程列表", 'href' => 'route.php?con=air_line'));
        $this->assign('action_link2', array('text' => "添加航程库存", 'href' => 'route.php?con=air_line_num&act=add&air_line_id=' . $air_line_id));
        $air_line_num_list = $this->air_line_num_service->get_air_line_num_list($air_line_id, $type);
//        echo '<pre>';print_r($air_line_num_list);exit;
        $this->assign('air_line_num_list', $air_line_num_list['list']);
        $this->assign("record_count", $air_line_num_list['record_count']);
        $this->assign("page_count", $air_line_num_list['page_count']);
        $this->assign("filter", $air_line_num_list['filter']);
        $this->assign("air_line_id", $air_line_id);
        $this->assign("full_page", 1);
        $this->display("air_line_num/air_line_num_list.html");
    }

    /* ------------------------------------------------------ */
    //-- js 分页
    /* ------------------------------------------------------ */
    public function index_query()
    {
        $air_line_id = isset($_GET['air_line_id']) ? intval($_GET['air_line_id']) : 0;
        $air_line = $this->air_line_service->getById($air_line_id);
        if (empty($air_line)) {
            sys_msg("航程不存在", 0);
        }
        $air_line_num_list = $this->air_line_num_service->get_air_line_num_list($air_line_id);
        $this->assign('air_line_num_list', $air_line_num_list['list']);
        $this->assign("record_count", $air_line_num_list['record_count']);
        $this->assign("page_count", $air_line_num_list['page_count']);
        $this->assign("filter", $air_line_num_list['filter']);
        make_json_result($this->fetch("air_line_num/air_line_num_list.html"), '', array('filter' => $air_line_num_list['filter'], 'page_count' => $air_line_num_list['page_count']));
    }

    public function add()
    {
        $air_line_id = isset($_GET['air_line_id']) ? intval($_GET['air_line_id']) : 0;
        $air_line = $this->air_line_service->getById($air_line_id);
        if (empty($air_line)) {
            sys_msg("航程不存在", 0);
        }

        $this->assign('ur_here', "添加航程库存");
        $this->assign('action_link', array('text' => "航程库存列表", 'href' => 'route.php?con=air_line_num&air_line_id=' . $air_line_id));
        $this->assign('act', "add");
        $this->assign('air_line_id', $air_line_id);
        $this->assign('air_line', $air_line);
        $this->display("air_line_num/air_line_num_info.html");
    }

    public function update()
    {
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $air_line_num = $this->air_line_num_service->getById($id);
        if (empty($air_line_num)) {
            sys_msg("库存不存在", 0);
        }
        $this->assign("air_line_num", $air_line_num);

        $air_line_id = $air_line_num['air_line_id'];
        $air_line = $this->air_line_service->getById($air_line_id);
        if (empty($air_line)) {
            sys_msg("航程不存在", 0);
        }

        $this->assign("air_line_num", $air_line_num);
        $this->assign("air_line", $air_line);
        $this->assign('ur_here', "编辑库存信息");
        $this->assign('action_link', array('text' => "机票库存信息列表", 'href' => 'route.php?con=air_line_num&air_line_id=' . $air_line_id));

        $this->assign("act", 'edit');
        $this->display("air_line_num/air_line_num_info.html");
    }

    public function edit()
    {
        $act = empty($_POST['act']) ? "add" : trim($_POST['act']);
        if ($act == "add") {
            $air_line_id = isset($_POST['air_line_id']) ? intval($_POST['air_line_id']) : 0;
            $air_line = $this->air_line_service->getById($air_line_id);
            if (empty($air_line)) {
                sys_msg("航程不存在", 0);
            }

            $res = $this->air_line_num_service->add($_POST);
            $link = array(array('text' => "航程库存列表", 'href' => 'route.php?con=air_line_num&air_line_id=' . $air_line_id), array('text' => "继续添加航程库存", 'href' => 'route.php?con=air_line_num&act=add&air_line_id=' . $air_line_id));
            if (empty($res)) {
                sys_msg("添加失败", 0, $link);
            } else {
                sys_msg("添加成功", 0, $link);
            }
        } else {
            $air_line_num_id = isset($_POST['id']) ? intval($_POST['id']) : 0;
            $air_line_num = $this->air_line_num_service->getById($air_line_num_id);
            if (empty($air_line_num)) {
                sys_msg("航程库存不存在", 0);
            }

            $res = $this->air_line_num_service->update($_POST);
            $link = array(array('text' => "航程库存列表", 'href' => 'route.php?con=air_line_num&air_line_id=' . $air_line_num['air_line_id']));
            if ($res) {
                sys_msg("更新成功", 0, $link);
            } else {
                sys_msg("更新失败", 0, $link);
            }
        }
    }

    public function remove()
    {
        $res = $this->air_line_num_service->remove($_GET['id']);
        $link = array(array('text' => "航程库存列表", 'href' => 'route.php?con=air_line_num'));
        if ($res) {
            sys_msg("删除成功", 0, $link);
        } else {
            sys_msg("删除失败", 0, $link);
        }
    }

    public function batch_drop()
    {
        $link = array(array('text' => "航程库存列表", 'href' => 'route.php?con=air_line_num'));
        $res = $this->air_line_num_service->remove_b($_POST['checkboxes']);
        if ($res) {
            sys_msg("批量删除航程库存成功", 0, $link);
        } else {
            sys_msg("批量删除航程库存失败", 0, $link);
        }
    }
}