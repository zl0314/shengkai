<?php

/*
 * 作者：戎青松
 * 时间：9:59:10
 * 
 */

/**
 * Description of HotelController
 *
 * @author Kevin
 */
__load("Controller", "controller");

class HotelController extends Controller {

    //put your code here
    private $hotel;

    public function __construct() {
        parent::__construct();
        __load("HotelService");
        $this->hotel = new HotelService();
    }

    public function index() {
        $this->assign('ur_here', "酒店列表");
        $this->assign('action_link', array('text' => "添加酒店", 'href' => 'route.php?con=hotel&act=add'));
        $this->assign('action_link2', array('text' => "房间类型", 'href' => 'route.php?con=roomtype&act=index'));
        $hotels = $this->hotel->get_AllHotel(); 
        $this->assign('hotels', $hotels['hotel']); 
        $this->assign("record_count", $hotels['record_count']);
        $this->assign("page_count", $hotels['page_count']);
        $this->assign("filter", $hotels['filter']);        
        $this->assign('full_page', 1);
        $this->display("hotel/hotel_list.html");
    }
    
    /* ------------------------------------------------------ */
    //-- js 分页
    /* ------------------------------------------------------ */
    public function index_query(){
        $hotels = $this->hotel->get_AllHotel(); 
        $this->assign('hotels', $hotels['hotel']); 
        $this->assign("record_count", $hotels['record_count']);
        $this->assign("page_count", $hotels['page_count']);
        $this->assign("filter", $hotels['filter']);         
        make_json_result($this->fetch("hotel/hotel_list.html"), '', array('filter' => $hotels['filter'], 'page_count' => $hotels['page_count']));
    }

    public function add() {
        $this->assign('act', "add");
        $this->assign('countries', $this->hotel->get_AllRegion(0));
        $this->assign('ur_here', "添加酒店");
        $this->assign('action_link', array('text' => "酒店列表", 'href' => 'route.php?con=hotel'));
        $this->display("hotel/hotel_info.html");
    }

    public function remove() {
        $res = $this->hotel->remove($_GET['id']);
        $link = array(array('text' => "酒店列表", 'href' => 'route.php?con=hotel'));
        if ($res) {
            sys_msg("删除成功", 0, $link);
        } else {
            sys_msg("删除失败", 0, $link);
        }
    }

    public function edit() {
        $act = empty($_POST['act']) ? "add" : trim($_POST['act']);
        if ($act == "add") {
            $res = $this->hotel->add($_POST);
            $link = array(array('text' => "酒店列表", 'href' => 'route.php?con=hotel'), array('text' => "继续添加酒店", 'href' => 'route.php?con=hotel&act=add'));
            if (empty($res)) {
                sys_msg("添加失败", 0, $link);
            } else {
                sys_msg("添加成功", 0, $link);
            }
        } else {
            $res = $this->hotel->update($_POST);
            $link = array(array('text' => "酒店列表", 'href' => 'route.php?con=hotel'));
            if ($res) {
                sys_msg("更新成功", 0, $link);
            } else {
                sys_msg("更新失败", 0, $link);
            }
        }
    }

    public function update() {
        $hotel = $this->hotel->get_Hotel($_GET['id']);
        $this->assign('hotel', $hotel);
        $this->assign('ur_here', "编辑酒店");
        $this->assign('countries', $this->hotel->get_AllRegion(0));
        $this->assign('action_link', array('text' => "酒店列表", 'href' => 'route.php?con=hotel'));
        $this->assign('act', "update");
        $this->display("hotel/hotel_info.html");
    }
    
     public function batch_drop() {
        $link = array(array('text' => "酒店列表", 'href' => 'route.php?con=hotel'));
        $res = $this->hotel->remove_b($_POST['checkboxes']);
        if ($res) {
            sys_msg("批量删除酒店成功", 0, $link);
        } else {
            sys_msg("批量删除酒店失败", 0, $link);
        }
    }
}
