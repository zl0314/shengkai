<?php

/**
 * 酒店房间库存
 *
 * User: stepreal
 * Date: 17/8/12
 * Time: 上午11:12
 */
__load("Controller", "controller");

class Room_numController extends Controller
{

    private $hotel;
    private $room_service;
    private $room_num_service;

    public function __construct()
    {
        parent::__construct();
        __load("HotelService");
        $this->hotel = new HotelService();

        __load("RoomService");
        $this->room_service = new RoomService();

        __load("Room_numService");
        $this->room_num_service = new Room_numService();
    }

    public function index()
    {
        $room_id = isset($_GET['room_id']) ? intval($_GET['room_id']) : 0;

        $room = $this->room_service->get_room($room_id);
        if (empty($room)) {
            sys_msg("房间不存在", 0);
        }

        $result = $this->room_num_service->get_room_num_list($room_id);

        $this->assign('ur_here', "{$room['hotel_name']}{$room['type_name']}库存列表");
        $this->assign('action_link', array('text' => "返回房间列表", 'href' => 'route.php?con=room&act=index&hotel_id=' . $room['hotel_id']));
        $this->assign('action_link2', array('text' => "添加库存", 'href' => 'route.php?con=room_num&act=add&room_id=' . $room_id));
        $this->assign('room', $room);
        $this->assign('room_num_list', $result['list']);
        $this->assign("record_count", $result['record_count']);
        $this->assign("page_count", $result['page_count']);
        $this->assign("filter", $result['filter']);
        $this->assign('full_page', 1);
        $this->display("room_num/room_num_list.html");
    }

    /* ------------------------------------------------------ */
    //-- js 分页
    /* ------------------------------------------------------ */
    public function index_query()
    {

        $room_id = isset($_GET['room_id']) ? intval($_GET['room_id']) : 0;

        $room = $this->room_service->get_room($room_id);
        if (empty($room)) {
            sys_msg("房间不存在", 0);
        }

        $result = $this->room_num_service->get_room_num_list($room_id);

        $this->assign('room_num_list', $result['list']);
        $this->assign("record_count", $result['record_count']);
        $this->assign("page_count", $result['page_count']);
        $this->assign("filter", $result['filter']);
        make_json_result($this->fetch("room_num/room_num_list.html"), '', array('filter' => $hotels['filter'], 'page_count' => $hotels['page_count']));
    }

    public function add()
    {
        $room_id = isset($_GET['room_id']) ? intval($_GET['room_id']) : 0;

        $room = $this->room_service->get_room($room_id);
        if (empty($room)) {
            sys_msg("房间不存在", 0);
        }

        $this->assign('room_num', 0);

        $room = $this->room_service->get_room($room_id);
        if (empty($room)) {
            sys_msg("房间不存在", 0);
        }

        $hotel = $this->hotel->get_Hotel($room['hotel_id']);
        if (empty($hotel)) {
            sys_msg("酒店不存在", 0);
        }

        $this->assign('hotel', $hotel);
        $this->assign('room_num_id', 0);
        $this->assign('room', $room);
        $this->assign('room_id', $room['id']);

        $this->assign('act', "add");
        $this->assign('ur_here', "添加酒店");
        $this->assign('action_link', array('text' => "库存列表", 'href' => 'route.php?con=room_num&act=index&room_id=' . $room['id']));
        $this->display("room_num/room_num_info.html");
    }

    public function remove()
    {
        $room_num_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $room_num = $this->room_num_service->get_room_num($room_num_id);
        if (empty($room_num)) {
            sys_msg("房间库存不存在", 0);
        }
        $res = $this->hotel->remove($room_num_id);
        $link = array(array('text' => "库存列表", 'href' => 'route.php?con=room_num&act=index&room_id=' . $room_num['room_id']));
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
            $room_id = isset($_POST['room_id']) ? intval($_POST['room_id']) : 0;

            $room = $this->room_service->get_room($room_id);
            if (empty($room)) {
                sys_msg("房间不存在", 0);
            }

            $link = array(array('text' => "库存列表", 'href' => 'route.php?con=room_num&a=index&room_id=' . $room['id']), array('text' => "继续库存添加", 'href' => 'route.php?con=room_num&act=add&room_id=' . $room['id']));
            $res = $this->room_num_service->save($_POST);
            if (empty($res)) {
                sys_msg("添加失败", 0, $link);
            } else {
                sys_msg("添加成功", 0, $link);
            }
        } else {
            $room_num_id = isset($_POST['id']) ? intval($_POST['id']) : 0;
            $room_num = $this->room_num_service->get_room_num($room_num_id);

            if (empty($room_num)) {
                sys_msg("房间库存不存在", 0);
            }

            $res = $this->room_num_service->update($_POST);
            $link = array(array('text' => "库存列表", 'href' => 'route.php?con=room_num&a=index&room_id=' . $room_num['room_id']));
            if ($res) {
                sys_msg("更新成功", 0, $link);
            } else {
                sys_msg("更新失败", 0, $link);
            }
        }
    }

    public function update()
    {
        $room_num_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $room_num = $this->room_num_service->get_room_num($room_num_id);

        if (empty($room_num)) {
            sys_msg("房间库存不存在", 0);
        }

        $this->assign('room_num', $room_num);

        $room_id = $room_num['room_id'];
        $room = $this->room_service->get_room($room_id);
        if (empty($room)) {
            sys_msg("房间不存在", 0);
        }

        $hotel = $this->hotel->get_Hotel($room['hotel_id']);
        if (empty($hotel)) {
            sys_msg("酒店不存在", 0);
        }

        $this->assign('hotel', $hotel);
        $this->assign('room_num_id', $room_num_id);
        $this->assign('room', $room);
        $this->assign('room_id', $room['id']);
        $this->assign('ur_here', "编辑库存");
        $this->assign('action_link', array('text' => "酒店库存列表", 'href' => 'route.php?con=room_num&a=index&room_id='));
        $this->assign('act', "update");
        $this->display("room_num/room_num_info.html");
    }

    public function batch_drop()
    {
        $checkboxes = $_POST['checkboxes'];
        if (empty($checkboxes)) {
            sys_msg("请选择删除的内容", 0);
        }
        $room_num_id = intval($checkboxes[0]);
        $room_num = $this->room_num_service->get_room_num($room_num_id);
        if (empty($checkboxes)) {
            sys_msg("库存不存在", 0);
        }

        $link = array(array('text' => "酒店列表", 'href' => 'route.php?con=room_num&act=index&room_id=' . $room_num['room_id']));
        $res = $this->room_num_service->remove_b($_POST['checkboxes']);
        if ($res) {
            sys_msg("批量删除酒店成功", 0, $link);
        } else {
            sys_msg("批量删除酒店失败", 0, $link);
        }
    }
}