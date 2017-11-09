<?php

/**
 * Description of ColorController
 *
 * @author Kevin
 */
__load("Controller", "controller");

class Combo_pitchController extends Controller {

    //put your code here
    private $combo_pitch;

    public function __construct() {
        parent::__construct();
        __load("Combo_pitchService"); 
        $this->combo_pitch = new Combo_pitchService();
    }

    public function index() {
        $this->assign('ur_here', "套餐列表");
        $this->assign('action_link', array('text' => "添加套餐", 'href' => 'route.php?con=combo_pitch&act=add'));
        $combo_pitch_list = $this->combo_pitch->get_All_Combo_pitch();
        $this->assign('combo_pitch_list', $combo_pitch_list['combo_pitch_list']);     
        $this->assign("record_count", $combo_pitch_list['record_count']);
        $this->assign("page_count", $combo_pitch_list['page_count']);
        $this->assign("filter", $combo_pitch_list['filter']);
        $this->assign("full_page", 1);
        $this->display("combo_pitch/combo_pitch_list.html");
    }
    
    /* ------------------------------------------------------ */
    //-- js 分页
    /* ------------------------------------------------------ */
    public function index_query(){
        $combo_pitch_list = $this->combo_pitch->get_All_Combo_pitch();
        $this->assign('combo_pitch_list', $combo_pitch_list['combo_pitch_list']);     
        $this->assign("record_count", $combo_pitch_list['record_count']);
        $this->assign("page_count", $combo_pitch_list['page_count']);
        $this->assign("filter", $combo_pitch_list['filter']);  
        make_json_result($this->fetch("combo_pitch/combo_pitch_list.html"), '', array('filter' => $combo_pitch_list['filter'], 'page_count' => $combo_pitch_list['page_count']));  
    }

    public function add(){
        $this->assign('act', "add");
        $this->assign('ur_here', "添加套餐信息");
        $this->assign('action_link', array('text' => "套餐列表", 'href' => 'route.php?con=combo_pitch'));
        //获取套餐场馆信息
        $this->display("combo_pitch/combo_pitch_info.html");
    }
    
    public function edit() {
        $act = empty($_POST['act']) ? "add" : trim($_POST['act']);
        if ($act == "add")
            {
            $res = $this->combo_pitch->add_Combo_pitch($_POST);
            $link = array(array('text' => "套餐列表", 'href' => 'route.php?con=combo'), array('text' => "继续添加套餐", 'href' => 'route.php?con=combo_pitch&act=add'));
            if (empty($res)) {
                sys_msg("添加失败", 0, $link);
            } else {
                admin_log(addslashes($_POST['combo_pitch_name']), 'add', 'combo_pitch');// 记录日志
                sys_msg("添加成功", 0, $link);
            }
        } else {
            $res = $this->combo_pitch->update($_POST);
            $link = array(array('text' => "套餐列表", 'href' => 'route.php?con=combo'));
            if ($res) {
                admin_log(addslashes($_POST['combo_pitch_name']), 'edit', 'combo_pitch');// 记录日志
                sys_msg("更新成功", 0, $link);
            } else {
                sys_msg("更新失败", 0, $link);
            }
        }
    }

    public function update() {
        $combo_pitch = $this->combo_pitch->get_combo_pitch($_GET['id']);
        $this->assign('combo_pitch', $combo_pitch);
        $this->assign('ur_here', "编辑套餐场馆");
        $this->assign('action_link', array('text' => "套餐列表", 'href' => 'route.php?con=combo','text' => '添加套餐场馆','href' => 'route.php?con=combo_pitch&act=add'));
        $this->assign('act', "update");
        $this->display("combo_pitch/combo_pitch_info.html");
    }

    public function batch_drop() {
        $link = array(array('text' => "套餐列表", 'href' => 'route.php?con=combo_pitch'));
        $res = $this->combo_pitch->remove_b($_POST['checkboxes']);
        if ($res) {
            sys_msg("批量删除套餐信息成功", 0, $link);
        } else {
            sys_msg("批量删除套餐信息失败", 0, $link);
        }
    }
    
    public function remove() {
        //--hechengbin -- 删除套餐 --start
        $result = $this->combo_pitch->get_Combo_pitch($_GET['id']);
        //--hechengbin -- end
        $res = $this->combo_pitch->remove($_GET['id']);
        $link = array(array('text' => "套餐列表", 'href' => 'route.php?con=combo'));
        if ($res) {
            admin_log(addslashes($result['combo_pitch_name']), 'remove', 'combo_pitch');// 记录日志
            sys_msg("删除成功", 0, $link);
        } else {
            sys_msg("删除失败", 0, $link);
        }
    }
    
    public function ajaxEdit() {
        $returnArray = array("error"=>0,"content"=>"","message"=>"");
        $act = empty($_POST['act']) ? "add" : trim($_POST['act']);
        if ($act == "add")
        {
            $result = $this->combo_pitch->add_Combo_pitch($_POST);
            $combo_insert_id = mysql_insert_id();
            if (!empty($result)) {
                $content = $this->combo_pitch->get_Combo_pitch($combo_insert_id);
                $returnArray = array("error"=>0, "content"=>$content, "message"=>"新增套餐场馆成功");
            }else{
                $returnArray = array("error"=>1, "content"=>"", "message"=>"新增套餐场馆失败");
            }
        } else {
            $result = $this->combo_pitch->update($_POST);
            if (!empty($result)) {
                $returnArray = array("error"=>0,"content"=>"","message"=>"更新套餐场馆成功");
            }else{
                $returnArray = array("error"=>1,"content"=>"","message"=>"更新套餐场馆失败");
            }
        }
        die(json_encode($returnArray));
    }
    
    public function ajaxRemove() {
        $returnArray = array("error"=>0,"content"=>"","message"=>"");
        $result = $this->combo_pitch->remove($_REQUEST['id']);
        if (!empty($result)) {
            $combo_pitch_list = $this->combo_pitch->get_All_Combo_pitch();
            $returnArray = array("error"=>0, "content"=>$combo_pitch_list['combo_pitch_list'], "message"=>"删除场馆成功");
        }else{
            $returnArray = array("error"=>1, "content"=>"", "message"=>"删除场馆失败");
        }
        die(json_encode($returnArray));
    }
}
