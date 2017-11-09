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

class ContractController extends Controller {

    //put your code here
    private $contract;
    public function __construct() {
        parent::__construct();
        __load("ContractService");
        $this->contract = new ContractService();
    }

    public function index() {
        $this->assign('full_page', 1);
        $contracts = $this->contract->get_AllContract();
        $this->assign('item_list', $contracts['contract']);
        $this->assign("record_count", $contracts['record_count']);
        $this->assign("page_count", $contracts['page_count']);
        $this->assign("filter", $contracts['filter']);

        $this->assign('ur_here', "合同列表");
//        $this->assign('action_link', array('text' => "添加合同", 'href' => 'route.php?con=contract&act=add'));
        $this->assign('action_link', array());
        $this->display("contract/index.html");
    }

    /* ------------------------------------------------------ */
    //-- js 分页
    /* ------------------------------------------------------ */
    public function index_query(){
        $contracts = $this->contract->get_AllContract();
        $this->assign('item_list', $contracts['contract']);
        $this->assign("record_count", $contracts['record_count']);
        $this->assign("page_count", $contracts['page_count']);
        $this->assign("filter", $contracts['filter']);
        make_json_result($this->fetch("contract/index.html"), '', array('filter' => $contracts['filter'], 'page_count' => $contracts['page_count']));
    }

    public function add() {
        $this->assign('act', "add");
        $this->assign('ur_here', "添加合同");
        $this->assign('action_link', array('text' => "合同列表", 'href' => 'route.php?con=contract'));
        $this->display("contract/add.html");
    }

    public function remove() {
        $res = $this->contract->remove($_GET['id']);
        $link = array(array('text' => "合同列表", 'href' => 'route.php?con=contract'));
        if ($res) {
            sys_msg("删除成功", 0, $link);
        } else {
            sys_msg("删除失败", 0, $link);
        }
    }

    public function edit() {
        $act = empty($_POST['act']) ? "add" : trim($_POST['act']);
        if ($act == "add") {
            //创建与合同对应的订单
            if(empty($_POST['order_sn'])){
                $order_result = $this->contract->createOrder($_POST);
                $_POST['order_sn'] = $_POST['contract_no'];
            }

            $res = $this->contract->add($_POST);


            $link = array(array('text' => "合同列表", 'href' => 'route.php?con=contract'));
            if (empty($res)) {
                sys_msg("添加失败", 0, $link);
            } else {
                sys_msg("添加成功", 0, $link);
            }
        } else {
            $res = $this->contract->update($_POST);
            $link = array(array('text' => "合同列表", 'href' => 'route.php?con=contract'));
            if ($res) {
                sys_msg("更新成功", 0, $link);
            } else {
                sys_msg("更新失败", 0, $link);
            }
        }
    }

    public function update() {
        $contract = $this->contract->get_Contract($_GET['id']);
        $this->assign('contract', $contract);
        $this->assign('ur_here', "编辑合同");
        $this->assign('action_link', array('text' => "合同列表", 'href' => 'route.php?con=contract'));
        $this->assign('act', "update");
        $this->display("contract/add.html");
    }

}
