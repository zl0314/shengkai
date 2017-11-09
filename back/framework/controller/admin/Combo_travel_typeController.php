<?php

/**
 * Created by PhpStorm.
 * User: stepreal
 * Date: 17/6/22
 * Time: 下午11:07
 */
__load("Controller", "controller");

class Combo_travel_typeController extends Controller
{

    //put your code here
    private $combo_travel;

    public function __construct()
    {
        parent::__construct();
        __load("Combo_travel_typeService");
        $this->combo_travel_type = new Combo_travel_typeService();
    }

    public function index()
    {
        $this->assign('ur_here', "套餐行程分类列表");
        $this->assign('action_link', array('text' => "添加套餐行程分类", 'href' => 'route.php?con=combo_travel_type&act=add'));
        $combo_travel_list = $this->combo_travel_type->get_All_Combo_travel_type();
        $this->assign('combo_travel_type_list', $combo_travel_list['combo_travel_type_list']);
        $this->assign("record_count", $combo_travel_list['record_count']);
        $this->assign("page_count", $combo_travel_list['page_count']);
        $this->assign("filter", $combo_travel_list['filter']);
        $this->assign("full_page", 1);
        $this->display("combo_travel_type/combo_travel_type_list.html");
    }

    /* ------------------------------------------------------ */
    //-- js 分页
    /* ------------------------------------------------------ */
    public function index_query()
    {
        $combo_travel_list = $this->combo_travel_type->get_All_Combo_travel_type();
        $this->assign('combo_travel_type_list', $combo_travel_list['combo_travel_type_list']);
        $this->assign("record_count", $combo_travel_list['record_count']);
        $this->assign("page_count", $combo_travel_list['page_count']);
        $this->assign("filter", $combo_travel_list['filter']);
        make_json_result($this->fetch("combo_travel_type/combo_travel_type_list.html"), '', array('filter' => $combo_travel_list['filter'], 'page_count' => $combo_travel_list['page_count']));
    }

    public function add()
    {
        $this->assign('act', "add");
        $this->assign('ur_here', "添加套餐行程分类信息");
        $this->assign('action_link', array('text' => "套餐行程分类列表", 'href' => 'route.php?con=combo_travel_type'));
        //获取套餐行程分类行程信息
        $this->display("combo_travel_type/combo_travel_type_info.html");
    }

    public function edit()
    {
        $act = empty($_POST['act']) ? "add" : trim($_POST['act']);
        if ($act == "add") {
            $res = $this->combo_travel_type->add_Combo_travel_type($_POST);
            $link = array(array('text' => "套餐行程分类列表", 'href' => 'route.php?con=combo_travel_type'), array('text' => "继续添加套餐行程分类", 'href' => 'route.php?con=combo_travel_type&act=add'));
            if (empty($res)) {
                sys_msg("添加失败", 0, $link);
            } else {
                admin_log(addslashes($_POST['type_name']), 'add', 'combo_travel_type');// 记录日志
                sys_msg("添加成功", 0, $link);
            }
        } else {
            $res = $this->combo_travel_type->update($_POST);
            $link = array(array('text' => "套餐行程分类列表", 'href' => 'route.php?con=combo_travel_type'));
            if ($res) {
                admin_log(addslashes($_POST['combo_travel_title']), 'edit', 'combo_travel_type');// 记录日志
                sys_msg("更新成功", 0, $link);
            } else {
                sys_msg("更新失败", 0, $link);
            }
        }
    }

    public function update()
    {
        $combo_travel = $this->combo_travel_type->get_combo_travel_type($_GET['id']);
        $this->assign('combo_travel_type', $combo_travel);
        $this->assign('ur_here', "编辑套餐行程分类行程");
        $this->assign('action_link', array('text' => "套餐行程分类列表", 'href' => 'route.php?con=combo_travel_type', 'text' => '添加套餐行程分类行程', 'href' => 'route.php?con=combo_travel_type&act=add'));
        $this->assign('act', "update");
        $this->display("combo_travel_type/combo_travel_type_info.html");
    }

    public function batch_drop()
    {
        $link = array(array('text' => "套餐行程分类列表", 'href' => 'route.php?con=combo_travel_type'));
        $res = $this->combo_travel_type->remove_b($_POST['checkboxes']);
        if ($res) {
            sys_msg("批量删除套餐行程分类信息成功", 0, $link);
        } else {
            sys_msg("批量删除套餐行程分类信息失败", 0, $link);
        }
    }

    public function remove()
    {
        //--hechengbin -- 删除套餐行程分类行程 --start
        $result = $this->combo_travel_type->get_Combo_travel_type($_GET['id']);
        //--hechengbin -- end
        $res = $this->combo_travel_type->remove($_GET['id']);
        $link = array(array('text' => "套餐行程分类列表", 'href' => 'route.php?con=combo_travel_type'));
        if ($res) {
            admin_log(addslashes($result['combo_travel_type_title']), 'remove', 'combo_travel_type');// 记录日志
            sys_msg("删除成功", 0, $link);
        } else {
            sys_msg("删除失败", 0, $link);
        }
    }

    public function ajaxEdit()
    {
        $returnArray = array("error" => 0, "content" => "", "message" => "");
        $act = empty($_POST['act']) ? "add" : trim($_POST['act']);
        if ($act == "add") {
            $result = $this->combo_travel_type->add_Combo_travel_type($_POST);
            $combo_insert_id = mysql_insert_id();
            if (!empty($result)) {
                $content = $this->combo_travel_type->get_Combo_travel_type($combo_insert_id);
                $returnArray = array("error" => 0, "content" => $content, "message" => "新增套餐行程分类行程成功");
            } else {
                $returnArray = array("error" => 1, "content" => "", "message" => "新增套餐行程分类行程失败");
            }
        } else {
            $result = $this->combo_travel_type->update($_POST);
            if (!empty($result)) {
                $returnArray = array("error" => 0, "content" => "", "message" => "更新套餐行程分类行程成功");
            } else {
                $returnArray = array("error" => 1, "content" => "", "message" => "更新套餐行程分类行程失败");
            }
        }
        die(json_encode($returnArray));
    }

    public function ajaxRemove()
    {
        $returnArray = array("error" => 0, "content" => "", "message" => "");
        $result = $this->combo_travel_type->remove($_REQUEST['id']);
        if (!empty($result)) {
            $combo_travel_list = $this->combo_travel_type->get_All_Combo_travel_type();
            $returnArray = array("error" => 0, "content" => $combo_travel_list['combo_travel_type_list'], "message" => "删除场馆成功");
        } else {
            $returnArray = array("error" => 1, "content" => "", "message" => "删除场馆失败");
        }
        die(json_encode($returnArray));
    }
}