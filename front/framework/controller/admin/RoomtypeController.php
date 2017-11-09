<?php

/*
 * 作者：戎青松
 * 时间：10:18:02
 * 
 */

/**
 * Description of game
 *
 * @author Kevin
 */
__load("Controller", "controller");

class RoomtypeController extends Controller {

    private $roomtype;

    //put your code here
    public function __construct() {
        parent::__construct();
        __load("RoomtypeService");
        $this->roomtype = new RoomtypeService();
    }

    public function index() {
        $this->assign('ur_here', "房间类型列表");
        $this->assign('action_link', array('text' => "添加房间类型", 'href' => 'route.php?con=Roomtype&act=add'));
        $type_list = $this->roomtype->get_room_type_list();
        $this->assign("type_list", $type_list['type_list']);
        $this->assign("record_count", $type_list['record_count']);
        $this->assign("page_count", $type_list['page_count']);
        $this->assign("filter", $type_list['filter']);
        $this->assign("full_page", 1);
       $this->display("roomtype/roomtype_list.html");
    }
    
     /* ------------------------------------------------------ */
    //-- js 分页
    /* ------------------------------------------------------ */
    public function index_query(){
        $type_list = $this->roomtype->get_room_type_list();    
        $this->assign("type_list", $type_list['type_list']);
        $this->assign("record_count", $type_list['record_count']);
        $this->assign("page_count", $type_list['page_count']);
        $this->assign("filter", $type_list['filter']);
        make_json_result($this->fetch("roomtype/roomtype_list.html"), '', array('filter' => $type_list['filter'], 'page_count' => $type_list['page_count']));
    }

    public function add() {
        $this->assign('ur_here', "添加房间类型");
        $this->assign('action_link', array('text' => "房间类型列表", 'href' => 'route.php?con=Roomtype'));
        $this->assign('act', "add");
        $this->display("roomtype/roomtype_info.html");
    }

    public function update() {
        $type_info = $this->roomtype->get_type($_GET['id']);
        $this->assign('type', $type_info);
        $this->assign('ur_here', "修改房间类型");
        $this->assign('action_link', array('text' => "房间类型列表", 'href' => 'route.php?con=Roomtype'));
        $this->assign('act', "update");
        $this->display("roomtype/roomtype_info.html");
    }

    public function edit() {
        $act = empty($_POST['act']) ? "add" : trim($_POST['act']);
        if ($act == "add") {
            $link = array(array('text' => "房间类型列表", 'href' => 'route.php?con=roomtype'), array('text' => "继续添加房间类型", 'href' => 'route.php?con=roomtype&act=add'));
            $res = $this->roomtype->save($_POST);
            if (empty($res)) {
                sys_msg("添加失败", 0, $link);
            } else {
                sys_msg("添加成功", 0, $link);
            }
        } else {
            $res =$this->roomtype->update($_POST);
            $link = array(array('text' => "房间类型列表", 'href' => 'route.php?con=roomtype'));
            if ($res) {
                sys_msg("更新成功", 0, $link);
            } else {
                sys_msg("更新失败", 0, $link);
            }
        }
   
    }

    public function remove() {
        $res = $this->roomtype->remove($_GET['id']);
        $link = array(array('text' => "房间类型列表", 'href' => 'route.php?con=roomtype'));
        if ($res) {
            sys_msg("删除成功", 0, $link);
        } else {
            sys_msg("删除失败", 0, $link);
        }
    }

    public function batch_drop() {
        __load("GameService");
        $game = new GameService();
        $link = array(array('text' => "赛事列表", 'href' => 'route.php?con=game'));
        $res = $game->remove_b($_POST['checkboxes']);
        if ($res) {
            sys_msg("批量删除赛事成功", 0, $link);
        } else {
            sys_msg("没有可删除的赛事", 0, $link);
        }
    }

}
