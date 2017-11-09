<?php

__load("Controller", "controller");

class SpaceController extends Controller {

    //put your code here
    private $space;

    public function __construct() {
        parent::__construct();
        __load("SpaceService");
        $this->space = new SpaceService();
    }

    public function index() {
        $this->assign('ur_here', "舱位列表");
        $this->assign('action_link', array('text' => "添加舱位", 'href' => 'route.php?con=space&act=add'));
        $space_all = $this->space->get_space_all();
        $this->assign('space_all', $space_all['space']);
        $this->assign("record_count", $space_all['record_count']);
        $this->assign("page_count", $space_all['page_count']);
        $this->assign("filter", $space_all['filter']);
        $this->assign("full_page", 1);
        $this->display("space/space_list.html");
    }
    
    /* ------------------------------------------------------ */
    //-- js 分页
    /* ------------------------------------------------------ */
    public function index_query(){
       $space_all = $this->space->get_space_all();
        $this->assign('space_all', $space_all['space']);
        $this->assign("record_count", $space_all['record_count']);
        $this->assign("page_count", $space_all['page_count']);
        $this->assign("filter", $space_all['filter']);
        make_json_result($this->fetch("space/space_list.html"), '', array('filter' => $space_all['filter'], 'page_count' => $space_all['page_count']));
    }

    public function add() {
        $this->assign('ur_here', "添加舱位");
        $this->assign('action_link', array('text' => "舱位列表", 'href' => 'route.php?con=space'));
        $this->assign('act', "add");        
        $this->display("space/space_info.html");
    }

    public function remove() {
        $res = $this->space->remove($_GET['id']);
        $link = array(array('text' => "舱位列表", 'href' => 'route.php?con=space'));
        if ($res) {
            sys_msg("删除成功", 0, $link);
        } else {
            sys_msg("删除失败", 0, $link);
        }
    }

    public function edit() {
        $act = empty($_POST['act']) ? "add" : trim($_POST['act']);
        if ($act == "add") {            
            $res = $this->space->add($_POST);
            $link = array(array('text' => "舱位列表", 'href' => 'route.php?con=space'), array('text' => "继续添加舱位", 'href' => 'route.php?con=space&act=add'));
            if (empty($res)) {
                sys_msg("添加失败", 0, $link);
            }else{
                sys_msg("添加成功", 0, $link);
            }
        }else{
            $res = $this->space->update($_POST);
            $link = array(array('text' => "舱位列表", 'href' => 'route.php?con=space'));
            if ($res) {
                sys_msg("更新成功", 0, $link);
            } else {
                sys_msg("更新失败", 0, $link);
            }
        }
    }

    public function update() {
        $this->assign('ur_here', "编辑舱位");
        $this->assign('action_link', array('text' => "舱位列表", 'href' => 'route.php?con=space'));
        $space = $this->space->get_space_info($_GET['id']);
        $this->assign('space', $space);        
        $this->assign('act', "update");
        $this->display("space/space_info.html");
    }
    
     public function batch_drop() {
        $link = array(array('text' => "舱位列表", 'href' => 'route.php?con=space'));
        $res = $this->space->remove_b($_POST['checkboxes']);
        if ($res) {
            sys_msg("批量删除舱位成功", 0, $link);
        } else {
            sys_msg("批量删除舱位失败", 0, $link);
        }
    }
}
