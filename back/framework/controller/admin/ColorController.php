<?php

/*
 * 作者：鞠嵩
 * 时间：9:59:10
 * 
 */

/**
 * Description of ColorController
 *
 * @author Kevin
 */
__load("Controller", "controller");

class ColorController extends Controller {

    //put your code here
    private $color;

    public function __construct() {
        parent::__construct();
        __load("ColorService"); 
        __load("GameService");  
        $this->color = new ColorService();
        $this->game = new GameService();
    }

    public function index() {
        $this->assign('ur_here', "小组列表");
        $this->assign('action_link', array('text' => "添加小组", 'href' => 'route.php?con=color&act=add'));
        $color_list = $this->color->get_All_Color();
        $this->assign('color_list', $color_list['colors']);     
        $this->assign("record_count", $color_list['record_count']);
        $this->assign("page_count", $color_list['page_count']);
        $this->assign("filter", $color_list['filter']);
        $this->assign("full_page", 1);
        $this->display("color/color_list.html");
    }
    
    /* ------------------------------------------------------ */
    //-- js 分页
    /* ------------------------------------------------------ */
    public function index_query(){
        $colors = $this->color->get_All_Color();
        $this->assign('color_list', $colors['colors']);     
        $this->assign("record_count", $colors['record_count']);
        $this->assign("page_count", $colors['page_count']);
        $this->assign("filter", $colors['filter']);  
        make_json_result($this->fetch("color/color_list.html"), '', array('filter' => $colors['filter'], 'page_count' => $colors['page_count']));  
    }

    public function add(){
        $game_info=$this->game->get_name_list();
        $this->assign('game_info', $game_info);
        $this->assign('act', "add");
        $this->assign('ur_here', "添加小组信息");
        admin_log(addslashes($_POST['color_name']), 'add', 'colors');// 记录日志
        $this->assign('action_link', array('text' => "小组列表", 'href' => 'route.php?con=color'));
        $this->display("color/color_info.html");
    }

    public function edit() {
        $act = empty($_POST['act']) ? "add" : trim($_POST['act']);
        if ($act == "add")
            {
            $res = $this->color->add_Color($_POST);
            $link = array(array('text' => "小组列表", 'href' => 'route.php?con=color'), array('text' => "继续添加小组", 'href' => 'route.php?con=color&act=add'));
            if (empty($res)) {
                sys_msg("添加失败", 0, $link);
            } else {
                admin_log(addslashes($_POST['color_name']), 'add', 'colors');// 记录日志
                sys_msg("添加成功", 0, $link);
            }
        } else {
            $arr=Array();
            $arr=$_POST;
            if($arr['is_color']==''){
                $arr['is_color']=0;
            }
            $res = $this->color->update($arr);
            $link = array(array('text' => "小组列表", 'href' => 'route.php?con=color'));
            if ($res) {
                admin_log(addslashes($_POST['color_name']), 'edit', 'colors');// 记录日志
                sys_msg("更新成功", 0, $link);
            } else {
                sys_msg("更新失败", 0, $link);
            }
        }
    }

    public function update() {
        $color = $this->color->get_color($_GET['id']);
        $game_info=$this->game->get_name_list();
        $this->assign('color', $color);
        $this->assign('game_info', $game_info);
        $this->assign('ur_here', "编辑小组");
        $this->assign('action_link', array('text' => "小组列表", 'href' => 'route.php?con=color'));
        $this->assign('act', "update");
        $this->display("color/color_info.html");
    }

     public function batch_drop() {
        $link = array(array('text' => "小组列表", 'href' => 'route.php?con=color'));
        $res = $this->color->remove_b($_POST['checkboxes']);
        if ($res) {
            admin_log(addslashes($_POST['color_name']), 'add', 'colors');// 记录日志
            sys_msg("批量删除小组信息成功", 0, $link);
        } else {
            sys_msg("批量删除小组信息失败", 0, $link);
        }
    }
       public function remove() {
           //--hechengbin--删除小组--start
           $result = $this->color->get_Color($_GET['id']);
           //--hechengbin--
        $res = $this->color->remove($_GET['id']);
        $link = array(array('text' => "小组列表", 'href' => 'route.php?con=color'));
        if ($res) {
            admin_log(addslashes($result['color_name']), 'remove', 'colors');// 记录日志
            sys_msg("删除成功", 0, $link);
        } else {
            sys_msg("删除失败", 0, $link);
        }
    }
}
