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

class Set_mealController extends Controller {

    //put your code here
    public function __construct() {
        parent::__construct();
        
    }

    public function index() {
        __load("Set_mealService");
        $set_meal = new Set_mealService();
        $this->assign('ur_here', "套餐列表");
        $this->assign('action_link', array('text' => "添加套餐", 'href' => 'route.php?con=set_meal&act=add'));
        $set_meal = $set_meal->get_set_meal_list();
        $this->assign("set_meal_list", $set_meal['set_meal']);
        $this->assign("record_count", $set_meal['record_count']);
        $this->assign("page_count", $set_meal['page_count']);
        $this->assign("filter", $set_meal['filter']);   
        $this->assign('full_page', 1);
        $this->display("set_meal/set_meal_list.html");
    }
    
    /* ------------------------------------------------------ */
    //-- js 分页
    /* ------------------------------------------------------ */
    public function index_query(){
         __load("Set_mealService");
        $set_meal = new Set_mealService();
        $set_meal = $set_meal->get_set_meal_list();
        $this->assign("set_meal_list", $set_meal['set_meal']);
        $this->assign("record_count", $set_meal['record_count']);
        $this->assign("page_count", $set_meal['page_count']);
        $this->assign("filter", $set_meal['filter']);    
        make_json_result($this->fetch("set_meal/set_meal_list.html"), '', array('filter' => $set_meal['filter'], 'page_count' => $set_meal['page_count']));
    }

    public function add() {
        $this->assign('ur_here', "添加套餐");
        $this->assign('action_link', array('text' => "套餐列表", 'href' => 'route.php?con=set_meal'));
        $this->assign('act', "add");
        $this->display("set_meal/set_meal_info.html");
    }
    public function update() {
        __load("Set_mealService");
        $set_meal = new Set_mealService();
        $set_meal_info = $set_meal->get_set_meal($_GET['id']);
        $this->assign('set_meal_info', $set_meal_info);
        $this->assign('ur_here', "添加套餐");
        $this->assign('action_link', array('text' => "套餐列表", 'href' => 'route.php?con=set_meal'));
        $this->assign('act', "update");
        $this->display("set_meal/set_meal_info.html");
    }

    public function edit() {
        $act = empty($_POST['act']) ? "add" : trim($_POST['act']);
        $_POST['is_show'] = empty($_POST['is_show']) ? 0 : $_POST['is_show'];
        __load("Set_mealService");
        $set_meal = new Set_mealService();
        if ($act == "add") {
            $link = array(array('text' => "套餐列表", 'href' => 'route.php?con=set_meal'), array('text' => "继续添加套餐", 'href' => 'route.php?con=set_meal&act=add'));
            $res = $set_meal->add_set_meal($_POST);
            if (empty($res)) {
                sys_msg("添加失败", 0, $link);
            } else {
                sys_msg("添加成功", 0, $link);
            }
        } else {
            $res = $set_meal->update_set_meal($_POST);
            $link = array(array('text' => "套餐列表", 'href' => 'route.php?con=set_meal'));
            if ($res) {
                sys_msg("更新成功", 0, $link);
            } else {
                sys_msg("更新失败", 0, $link);
            }
        }
        $this->display("set_meal/set_meal_info.html");
    }

    public function remove() {
        __load("Set_mealService");
        $set_meal = new Set_mealService();
        $res = $set_meal->remove_set_meal($_GET['id']);
        $link = array(array('text' => "套餐列表", 'href' => 'route.php?con=set_meal'));
        if ($res) {
            sys_msg("删除成功", 0, $link);
        } else {
            sys_msg("删除失败", 0, $link);
        }
    }
    public function batch_drop() {
        __load("Set_mealService");
        $set_meal = new Set_mealService();
        $link = array(array('text' => "套餐列表", 'href' => 'route.php?con=set_meal'));
        $res = $set_meal->remove_b($_POST['checkboxes']);
        if ($res) {
            sys_msg("批量删除套餐成功", 0, $link);
        } else {
            sys_msg("批量删除套餐失败", 0, $link);
        }
    }

}
