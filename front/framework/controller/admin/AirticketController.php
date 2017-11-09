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

class AirticketController extends Controller {

    //put your code here
    private $airticket;

    public function __construct() {
        parent::__construct();
        __load("AirticketService");
        $this->airticket = new AirticketService();
    }

    public function index() {
        $this->assign('ur_here', "机票列表");
        $this->assign('action_link', array('text' => "添加机票", 'href' => 'route.php?con=airticket&act=add'));
        $this->assign('action_link2', array('text' => "舱位列表", 'href' => 'route.php?con=space'));
        $airs = $this->airticket->get_All_Airticket();
        $this->assign('airs', $airs['airs']);
        $this->assign("record_count", $airs['record_count']);
        $this->assign("page_count", $airs['page_count']);
        $this->assign("filter", $airs['filter']);
        $this->assign("full_page", 1);
        $this->display("airticket/airticket_list.html");
    }
    
    /* ------------------------------------------------------ */
    //-- js 分页
    /* ------------------------------------------------------ */
    public function index_query(){
        $airs = $this->airticket->get_All_Airticket();
        $this->assign('airs', $airs['airs']);
        $this->assign("record_count", $airs['record_count']);
        $this->assign("page_count", $airs['page_count']);
        $this->assign("filter", $airs['filter']);
        make_json_result($this->fetch("airticket/airticket_list.html"), '', array('filter' => $airs['filter'], 'page_count' => $airs['page_count']));
    }

    public function see_space(){
        $this->assign('ur_here', "机票舱位信息列表");
        $this->assign('action_link', array('text' => "添加机票舱位信息", 'href' => 'route.php?con=airticket&act=add_air_info&air_id='.$_GET['air_id']));
        $this->assign('action_link2', array('text' => "机票列表", 'href' => 'route.php?con=airticket'));
        $airs = $this->airticket->get_air_info();
        $this->assign('airs', $airs['airs']);
        $this->assign("record_count", $airs['record_count']);
        $this->assign("page_count", $airs['page_count']);
        $this->assign("filter", $airs['filter']);
        $this->assign("full_page", 1);
        $this->display("airticket/air_space_list.html");
    }
    
    /* ------------------------------------------------------ */
    //-- js 分页
    /* ------------------------------------------------------ */
    public function see_space_query(){
        $airs = $this->airticket->get_air_info();
        $this->assign('airs', $airs['airs']);
        $this->assign("record_count", $airs['record_count']);
        $this->assign("page_count", $airs['page_count']);
        $this->assign("filter", $airs['filter']);
        make_json_result($this->fetch("airticket/air_space_list.html"), '', array('filter' => $airs['filter'], 'page_count' => $airs['page_count']));
    }
    
    public function add() {
        $this->assign('ur_here', "添加机票");
        $this->assign('action_link', array('text' => "机票列表", 'href' => 'route.php?con=airticket'));
        $this->assign('act', "add");
        $this->assign('countries', $this->airticket->get_AllRegion(0));       
        $this->display("airticket/airticket_info.html");
    }
    
    public function add_air_info(){
        $this->assign('ur_here', "添加机票舱位信息");
        $air_id = isset($_GET['air_id']) ? intval($_GET['air_id']) : 0;
        $this->assign('action_link', array('text' => "机票舱位信息列表", 'href' => 'route.php?con=airticket&act=see_space&air_id='.$air_id));
        $flight = $this->airticket->get_flight_num($air_id);
        $this->assign("flight", $flight);
        $this->assign("air_id", $air_id);
        $space_all = $this->airticket->get_space_all();        
        $this->assign('space_all', $space_all);
        $this->assign("act", 'add');
        $this->display("airticket/air_space_info.html");
    }
    
    public function update_air_info(){
        $this->assign('ur_here', "编辑机票舱位信息");
        $air_id = isset($_GET['air_id']) ? intval($_GET['air_id']) : 0;
        $this->assign('action_link', array('text' => "机票舱位信息列表", 'href' => 'route.php?con=airticket&act=see_space&air_id='.$air_id));
        $flight = $this->airticket->get_flight_num($air_id);
        $this->assign("flight", $flight);
        $space_all = $this->airticket->get_space_all();        
        $this->assign('space_all', $space_all);
        $air_info = $this->airticket->get_air_space_info();
        $this->assign('air_info', $air_info);
        $this->assign("act", 'edit');
        $this->display("airticket/air_space_info.html");
    }

    public function edit_air_info(){
        $act = empty($_POST['act']) ? "add" : trim($_POST['act']);        
        if ($act == "add") {
            $res = $this->airticket->add_air_space($_POST);
            $link = array(array('text' => "机票舱位信息列表", 'href' => 'route.php?con=airticket&act=see_space&air_id='.$_POST['air_id']), array('text' => "继续添加机票舱位信息", 'href' => 'route.php?con=airticket&act=add_air_info&air_id='.$_POST['air_id']));
            if(empty($res)){
                sys_msg("添加失败", 0, $link);
            }else{
                sys_msg("添加成功", 0, $link);
            }
        }else{
            $res = $this->airticket->update_air_space($_POST);
            $link = array(array('text' => "机票舱位信息列表", 'href' => 'route.php?con=airticket&act=see_space&air_id='.$_POST['air_id']));
            if(empty($res)){
                sys_msg("修改失败", 0, $link);
            }else{
                sys_msg("修改成功", 0, $link);
            }
        }
    }
    
    public function remove_air_info(){
        $res = $this->airticket->remove_air_space_info();
        $link = array(array('text' => "机票舱位信息列表", 'href' => 'route.php?con=airticket&act=see_space&air_id='.$_GET['air_id']));
        if($res){
            sys_msg("删除成功", 0, $link);
        }else{
            sys_msg("删除失败", 0, $link);
        }
    }
    
    public function remove() {
        $res = $this->airticket->remove($_GET['id']);
        $link = array(array('text' => "机票列表", 'href' => 'route.php?con=airticket'));
        if($res){
            sys_msg("删除成功", 0, $link);
        }else{
            sys_msg("删除失败", 0, $link);
        }
    }

    public function edit() {
        $act = empty($_POST['act']) ? "add" : trim($_POST['act']);
        if ($act == "add") {
            $res = $this->airticket->add($_POST);
            $link = array(array('text' => "机票列表", 'href' => 'route.php?con=airticket'), array('text' => "继续添加机票", 'href' => 'route.php?con=airticket&act=add'));
            if (empty($res)) {
                sys_msg("添加失败", 0, $link);
            } else {
                sys_msg("添加成功", 0, $link);
            }
        } else {
            $res = $this->airticket->update($_POST);
            $link = array(array('text' => "机票列表", 'href' => 'route.php?con=airticket'));
            if ($res) {
                sys_msg("更新成功", 0, $link);
            } else {
                sys_msg("更新失败", 0, $link);
            }
        }
    }

    public function update() {
        $airticket = $this->airticket->get_air($_GET['id']);
        $this->assign('airticket', $airticket);
        $this->assign('ur_here', "编辑机票");
        $this->assign('countries', $this->airticket->get_AllRegion(0));
        $this->assign('action_link', array('text' => "机票列表", 'href' => 'route.php?con=airticket'));
        $this->assign('act', "update");
        $this->display("airticket/airticket_info.html");
    }
    
    public function drop_air_space(){
        $link = array(array('text' => "机票舱位信息列表", 'href' => 'route.php?con=airticket&act=see_space&air_id='.$_POST['air_id']));
        $res = $this->airticket->drop($_POST['checkboxes']);
        if($res){
            sys_msg("删除成功", 0, $link);
        }else{
            sys_msg("删除失败", 0, $link);
        }
    }

    public function batch_drop() {
        $link = array(array('text' => "机票列表", 'href' => 'route.php?con=airticket'));
        $res = $this->airticket->remove_b($_POST['checkboxes']);
        if ($res) {
            sys_msg("批量删除机票成功", 0, $link);
        } else {
            sys_msg("批量删除机票失败", 0, $link);
        }
    }
}
