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

class RoomController extends Controller {

    private $room;

    //put your code here
    public function __construct() {
        parent::__construct();
        __load("RoomService");
        $this->room = new RoomService();
    }

    public function index() {
        $hotel_id = empty($_GET['hotel_id']) ? 0 : $_GET['hotel_id'];
        $room_list = $this->room->get_room_list();
        $this->assign("room_list",  $room_list['rooms']);
        $this->assign("record_count", $room_list['record_count']);
        $this->assign("page_count", $room_list['page_count']);
        $this->assign("filter", $room_list['filter']);
        $this->assign("full_page", 1);
        $this->assign('ur_here', "房间列表");
        $this->assign('action_link', array('text' => "添加房间", 'href' => 'route.php?con=room&act=add&hotel_id=' . $hotel_id));
        $this->display("room/room_list.html");
    }
    
    /* ------------------------------------------------------ */
    //-- js 分页
    /* ------------------------------------------------------ */
    public function index_query(){
        $room_list = $this->room->get_room_list();
        $this->assign("room_list",  $room_list['rooms']);
        $this->assign("record_count", $room_list['record_count']);
        $this->assign("page_count", $room_list['page_count']);
        $this->assign("filter", $room_list['filter']);
        make_json_result($this->fetch("room/room_list.html"), '', array('filter' => $room_list['filter'], 'page_count' => $room_list['page_count']));
    }

    public function add() {
        $hotel_id = $_GET['hotel_id'];
        if (empty($hotel_id)) {
            $link = array(array('text' => "酒店列表", 'href' => 'route.php?con=hotel'));
            sys_msg("未检测到酒店", 0, $link);
        }
        $this->assign('ur_here', "添加房间");
        $this->assign('action_link', array('text' => "房间列表", 'href' => 'route.php?con=room'));
        $this->assign('act', "add");
        $this->assign('hotel_id', $hotel_id);
        $this->assign("type_list", $this->room->get_type_list());
        $this->assign('hotel_name', $this->room->get_hotel_name($hotel_id));
        $this->display("room/room_info.html");
    }

    public function update() {
        $room_info = $this->room->get_room($_GET['id']);
        $this->assign('room', $room_info);
        $this->assign("type_list", $this->room->get_type_list());
        $this->assign('hotel_id', $room_info['hotel_id']);
        $this->assign('hotel_name', $room_info['hotel_name']);
        $this->assign('ur_here', "修改房间");
        $this->assign('action_link', array('text' => "房间类型列表", 'href' => 'route.php?con=Room&hotel_id=' . $room_info['hotel_id']));
        $this->assign('act', "update");
        $this->display("room/room_info.html");
    }

    public function edit() {
        $act = empty($_POST['act']) ? "add" : trim($_POST['act']);

        if ($act == "add") {
            $hotel_id = $_POST['hotel_id'];
            if (empty($hotel_id)) {
                $link = array(array('text' => "酒店列表", 'href' => 'route.php?con=hotel'));
                sys_msg("未检测到酒店", 0, $link);
            }
            $link = array(array('text' => "房间列表", 'href' => 'route.php?con=room&hotel_id=' . $hotel_id), array('text' => "继续添加房间", 'href' => 'route.php?con=room&act=add&hotel_id=' . $hotel_id));

            $res = $this->room->save($_POST);
            if (empty($res)) {
                sys_msg("添加失败", 0, $link);
            } else {
                sys_msg("添加成功", 0, $link);
            }
        } else {
            $res = $this->room->update($_POST);
             $hotel_id = $_POST['hotel_id'];
           $link = array(array('text' => "房间列表", 'href' => 'route.php?con=room&hotel_id=' . $hotel_id));
            if ($res) {
                sys_msg("更新成功", 0, $link);
            } else {
                sys_msg("更新失败", 0, $link);
            }
        }
    }

    public function remove() {
        $res = $this->room->remove($_GET['id']);
        $hotel_id = $_GET['hotel_id'];
       $link = array(array('text' => "房间列表", 'href' => 'route.php?con=room&hotel_id=' . $hotel_id));
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
