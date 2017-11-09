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

class Set_meal_orderController extends Controller {

    //put your code here
    public function __construct() {
        parent::__construct();
        
    }

    public function index() {
        __load("Set_meal_orderService");
        $set_meal_order = new Set_meal_orderService();
        $this->assign('ur_here', "套餐订单列表");
        $set_meal_order = $set_meal_order->get_set_meal_order_list();
        $this->assign("set_meal_order_list", $set_meal_order['set_meal_order']);
        $this->assign("record_count", $set_meal_order['record_count']);
        $this->assign("page_count", $set_meal_order['page_count']);
        $this->assign("filter", $set_meal_order['filter']);   
        $this->assign('full_page', 1);
        $this->display("set_meal_order/set_meal_order_list.html");
    }
    
    /* ------------------------------------------------------ */
    //-- js 分页
    /* ------------------------------------------------------ */
    public function index_query(){
         __load("Set_meal_orderService");
        $set_meal_order = new Set_meal_orderService();
        $set_meal_order = $set_meal_order->get_set_meal_order_list();
        $this->assign("set_meal_order_list", $set_meal_order['set_meal_order']);
        $this->assign("record_count", $set_meal_order['record_count']);
        $this->assign("page_count", $set_meal_order['page_count']);
        $this->assign("filter", $set_meal_order['filter']);    
        make_json_result($this->fetch("set_meal_order/set_meal_order_list.html"), '', array('filter' => $set_meal_order['filter'], 'page_count' => $set_meal_order['page_count']));
    }

    public function update() {
        __load("Set_meal_orderService");
        $set_meal_order = new Set_meal_orderService();
        $set_meal_order_info = $set_meal_order->get_set_meal_order($_GET['id']);
        $this->assign('set_meal_order_info', $set_meal_order_info);
        $this->assign('ur_here', "添加套餐订单");
        $this->assign('action_link', array('text' => "套餐订单列表", 'href' => 'route.php?con=set_meal_order'));
        $this->assign('act', "update");
        $this->display("set_meal_order/set_meal_order_info.html");
    }

    public function edit() {
        __load("Set_meal_orderService");
        $set_meal_order = new Set_meal_orderService();
        if($_GET['is_manage']==0){
        $res = $set_meal_order->update_set_meal_order($_GET);
        }else if($_GET['is_manage']==1){
        $_POST['is_manage']=$_GET['is_manage'];
        $res = $set_meal_order->update_set_meal_order($_POST);
        }
        $link = array(array('text' => "套餐订单列表", 'href' => 'route.php?con=set_meal_order'));
            if ($res) {
                sys_msg("处理成功", 0, $link);
            } else {
                sys_msg("处理失败", 0, $link);
            }
        $this->display("set_meal_order/set_meal_order_info.html");
    }

}
