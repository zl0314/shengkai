<?php

/**
 * Created by PhpStorm.
 * User: stepreal
 * Date: 17/9/24
 * Time: 上午11:17
 */
__load("Controller", "controller");

class R201860Controller extends Controller
{

    public function __construct()
    {
        parent::__construct();
        __load("R201860Service");
        $this->r201860Service = new r201860Service();
    }

    public function index()
    {
        $this->assign('ur_here', "贵宾订制列表");
        $this->assign('action_link', array('text' => "添加贵宾订制", 'href' => 'route.php?con=r201860&act=add'));
        $this->assign('action_link2', array('text' => "舱位列表", 'href' => 'route.php?con=space'));
        $r201860_list = $this->r201860Service->get_list();
        $this->assign('r201860_list', $r201860_list['list']);
        $this->assign("record_count", $r201860_list['record_count']);
        $this->assign("page_count", $r201860_list['page_count']);
        $this->assign("filter", $r201860_list['filter']);
        $this->assign("full_page", 1);
        $this->display("question_paper/r201860_list.html");
    }

    /* ------------------------------------------------------ */
    //-- js 分页
    /* ------------------------------------------------------ */
    public function index_query()
    {
        $airs = $this->r201860Service->get_list();
        $this->assign('r201860_list', $airs['list']);
        $this->assign("record_count", $airs['record_count']);
        $this->assign("page_count", $airs['page_count']);
        $this->assign("filter", $airs['filter']);
        make_json_result($this->fetch("question_paper/r201860_list.html"), '', array('filter' => $airs['filter'], 'page_count' => $airs['page_count']));
    }

    public function batch_drop()
    {
        $link = array(array('text' => "贵宾订制列表", 'href' => 'route.php?con=r201860'));
        $res = $this->r201860Service->remove_b($_POST['checkboxes']);
        if ($res) {
            sys_msg("批量删除贵宾订制成功", 0, $link);
        } else {
            sys_msg("批量删除贵宾订制失败", 0, $link);
        }
    }
}
