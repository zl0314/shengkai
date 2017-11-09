<?php

__load("Controller", "controller");

class Air_ticketController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        __load("Air_ticketService");
        $this->air_ticket_service = new Air_ticketService();
        __load("Air_lineService");
        $this->air_line_service = new Air_lineService();
    }

    /**
     * 机票列表
     */
    public function index()
    {
        $air_line_id = isset($_REQUEST['air_line_id']) ? intval($_REQUEST['air_line_id']) : 0;
        $type = isset($_REQUEST['type']) ? intval($_REQUEST['type']) : 1;

        $air_line = $this->air_line_service->getById($air_line_id);
        if (empty($air_line)) {
            sys_msg("航线不存在", 0);
        }

        $type_text = '';
        if ($type == 1) {
            $type_text = '去程';
        } elseif ($type == 2) {
            $type_text = '返程';
        }

        $this->assign('ur_here', "({$air_line['title']})机票列表");
        $this->assign('action_link', array('text' => "添加{$type_text}机票", 'href' => 'route.php?con=air_ticket&act=add&air_line_id=' . $air_line_id . '&type=' . $type));
        $this->assign('action_link2', array('text' => "航程列表", 'href' => 'route.php?con=air_line'));
        $air_ticket_list = $this->air_ticket_service->get_air_ticket_list($air_line_id, $type);
        $this->assign('air_ticket_list', $air_ticket_list['air_ticket_list']);
        $this->assign("record_count", $air_ticket_list['record_count']);
        $this->assign("page_count", $air_ticket_list['page_count']);
        $this->assign("filter", $air_ticket_list['filter']);
        $this->assign("full_page", 1);
        $this->display("air_ticket/air_ticket_list.html");
    }

    /* ------------------------------------------------------ */
    //-- js 分页
    /* ------------------------------------------------------ */
    public function index_query()
    {
        $air_line_id = isset($_REQUEST['air_line_id']) ? intval($_REQUEST['air_line_id']) : 0;
        $type = isset($_REQUEST['type']) ? intval($_REQUEST['type']) : 1;

        $air_line = $this->air_line_service->getById($air_line_id);
        if (empty($air_line)) {
            sys_msg("航线不存在", 0);
        }

        $air_ticket_list = $this->air_ticket_service->get_air_ticket_list($air_line_id, $type);
        $this->assign('air_ticket_list', $air_ticket_list['air_ticket_list']);
        $this->assign("record_count", $air_ticket_list['record_count']);
        $this->assign("page_count", $air_ticket_list['page_count']);
        $this->assign("filter", $air_ticket_list['filter']);
        make_json_result($this->fetch("air_ticket/air_ticket_list.html"), '', array('filter' => $air_ticket_list['filter'], 'page_count' => $air_ticket_list['page_count']));
    }

    public function add()
    {
        $air_line_id = isset($_REQUEST['air_line_id']) ? intval($_REQUEST['air_line_id']) : 0;
        $type = isset($_REQUEST['type']) ? intval($_REQUEST['type']) : 1;
        $type_text = '';
        if ($type == 1) {
            $type_text = '去程';
        } elseif ($type == 2) {
            $type_text = '返程';
        }
        $this->assign('ur_here', "添加{$type_text}机票");
        $this->assign('action_link', array('text' => "机票列表", 'href' => 'route.php?con=air_ticket&air_line_id=' . $air_line_id . '&type=' . $type));
        $this->assign('air_line_id', $air_line_id);
        $this->assign('type', $type);
        $this->assign('act', "add");
        $this->assign('countries', $this->air_ticket_service->get_AllRegion(0));
        $this->display("air_ticket/air_ticket_info.html");
    }

    public function remove()
    {
        $air_ticket_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $air_ticket = $this->air_ticket_service->getById($air_ticket_id);
        if (empty($air_ticket)) {
            sys_msg("机票不存在", 0);
        }
        $res = $this->air_ticket_service->remove($air_ticket_id);
        $link = array(array('text' => "机票列表", 'href' => 'route.php?con=air_ticket&air_line_id=' . $air_ticket['air_line_id']));
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

            $air_line_id = isset($_POST['air_line_id']) ? intval($_POST['air_line_id']) : 0;
            $type = isset($_REQUEST['type']) ? intval($_REQUEST['type']) : 1;

            $air_line = $this->air_line_service->getById($air_line_id);
            if (empty($air_line)) {
                sys_msg("航线不存在", 0);
            }

            $res = $this->air_ticket_service->add($_POST);
            $link = array(array('text' => "机票列表", 'href' => 'route.php?con=air_ticket&air_line_id=' . $air_line_id . '&type=' . $type), array('text' => "继续添加机票", 'href' => 'route.php?con=air_ticket&act=add&air_line_id=' . $air_line_id));
            if (empty($res)) {
                sys_msg("添加失败", 0, $link);
            } else {
                sys_msg("添加成功", 0, $link);
            }
        } else {

            $air_ticket = $this->air_ticket_service->getById($_POST['id']);
            if (empty($air_ticket)) {
                sys_msg("机票不存在", 0);
            }

            $res = $this->air_ticket_service->update($_POST);
            $link = array(array('text' => "机票列表", 'href' => 'route.php?con=air_ticket&air_line_id=' . $air_ticket['air_line_id']. '&type=' . $air_ticket['type']));
            if ($res) {
                sys_msg("更新成功", 0, $link);
            } else {
                sys_msg("更新失败", 0, $link);
            }
        }
    }

    /**
     * 更新机票信息
     */
    public function update()
    {
        $air_ticket = $this->air_ticket_service->getById($_GET['id']);
        if (empty($air_ticket)) {
            sys_msg("机票不存在", 0);
        }
        $air_line = $this->air_line_service->getById($air_ticket['air_line_id']);
        if (empty($air_line)) {
            sys_msg("机票的航线不存在", 0);
        }
        $this->assign('air_line', $air_line);
        $this->assign('type', $air_ticket['type']);
        $this->assign('air_ticket', $air_ticket);
        $this->assign('ur_here', "编辑机票");
        $this->assign('countries', $this->air_ticket_service->get_AllRegion(0));
        $this->assign('action_link', array('text' => "返回 {$air_line['title']} 机票列表", 'href' => 'route.php?con=air_ticket&air_line_id=' . $air_ticket['air_line_id']));
        $this->assign('act', "update");
        $this->display("air_ticket/air_ticket_info.html");
    }

    public function batch_drop()
    {
        $link = array(array('text' => "机票列表", 'href' => 'route.php?con=air_ticket'));
        $res = $this->air_ticket_service->remove_b($_POST['checkboxes']);
        if ($res) {
            sys_msg("批量删除机票成功", 0, $link);
        } else {
            sys_msg("批量删除机票失败", 0, $link);
        }
    }
}
