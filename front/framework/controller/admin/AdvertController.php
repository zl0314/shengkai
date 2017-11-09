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

class AdvertController extends Controller {

    //put your code here
    public function __construct() {
        parent::__construct();
        
    }

    public function index() {
        __load("AdvertService");
        $advert = new AdvertService();
        $this->assign('ur_here', "广告列表");
        $this->assign('action_link', array('text' => "添加广告", 'href' => 'route.php?con=advert&act=add'));
        $advert = $advert->get_advert_list();
        $this->assign("advert_list", $advert['advert']);
        $this->assign("record_count", $advert['record_count']);
        $this->assign("page_count", $advert['page_count']);
        $this->assign("filter", $advert['filter']);   
        $this->assign('full_page', 1);
        $this->display("advert/advert_list.html");
    }
    
    /* ------------------------------------------------------ */
    //-- js 分页
    /* ------------------------------------------------------ */
    public function index_query(){
         __load("AdvertService");
        $advert = new AdvertService();
        $advert = $advert->get_advert_list();
        $this->assign("advert_list", $advert['advert']);
        $this->assign("record_count", $advert['record_count']);
        $this->assign("page_count", $advert['page_count']);
        $this->assign("filter", $advert['filter']);    
        make_json_result($this->fetch("advert/advert_list.html"), '', array('filter' => $advert['filter'], 'page_count' => $advert['page_count']));
    }

    public function add() {
        $this->assign('ur_here', "添加广告");
        $this->assign('action_link', array('text' => "广告列表", 'href' => 'route.php?con=advert'));
        $this->assign('act', "add");
        $this->display("advert/advert_info.html");
    }
    public function update() {
        __load("AdvertService");
        $advert = new AdvertService();
        $advert_info = $advert->get_advert($_GET['id']);
        $this->assign('advert_info', $advert_info);
        $this->assign('ur_here', "添加广告");
        $this->assign('action_link', array('text' => "广告列表", 'href' => 'route.php?con=advert'));
        $this->assign('act', "update");
        $this->display("advert/advert_info.html");
    }

    public function edit() {
        $act = empty($_POST['act']) ? "add" : trim($_POST['act']);
        $_POST['is_show'] = empty($_POST['is_show']) ? 0 : $_POST['is_show'];
        __load("AdvertService");
        $advert = new AdvertService();
        if ($act == "add") {
            $link = array(array('text' => "广告列表", 'href' => 'route.php?con=advert'), array('text' => "继续添加广告", 'href' => 'route.php?con=advert&act=add'));
            $res = $advert->add_advert($_POST);
            if (empty($res)) {
                sys_msg("添加失败", 0, $link);
            } else {
                sys_msg("添加成功", 0, $link);
            }
        } else {
            $res = $advert->update_advert($_POST);
            $link = array(array('text' => "广告列表", 'href' => 'route.php?con=advert'));
            if ($res) {
                sys_msg("更新成功", 0, $link);
            } else {
                sys_msg("更新失败", 0, $link);
            }
        }
        $this->display("advert/advert_info.html");
    }

    public function remove() {
        __load("AdvertService");
        $advert = new AdvertService();
        $res = $advert->remove_advert($_GET['id']);
        $link = array(array('text' => "广告列表", 'href' => 'route.php?con=advert'));
        if ($res) {
            sys_msg("删除成功", 0, $link);
        } else {
            sys_msg("删除失败", 0, $link);
        }
    }
    public function batch_drop() {
        __load("AdvertService");
        $advert = new AdvertService();
        $link = array(array('text' => "广告列表", 'href' => 'route.php?con=advert'));
        $res = $advert->remove_b($_POST['checkboxes']);
        if ($res) {
            sys_msg("批量删除广告成功", 0, $link);
        } else {
            sys_msg("批量删除广告失败", 0, $link);
        }
    }

}
