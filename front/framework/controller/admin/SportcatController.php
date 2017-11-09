<?php

/**
 * Description of ColorController
 *
 * @author Kevin
 */
__load("Controller", "controller");

class SportcatController extends Controller {

    //put your code here
    private $sportcat;

    public function __construct() {
        parent::__construct();
        __load("SportcatService"); 
        $this->sportcat = new SportcatService();
    }

    public function index() {
        $this->assign('ur_here', "运动类别列表");
        $this->assign('action_link', array('text' => "添加运动类别", 'href' => 'route.php?con=sportcat&act=add'));
        $sportcat_list = $this->sportcat->get_All_Sportcat();
        $this->assign('sportcat_list', $sportcat_list['sportcat_list']);     
        $this->assign("record_count", $sportcat_list['record_count']);
        $this->assign("page_count", $sportcat_list['page_count']);
        $this->assign("filter", $sportcat_list['filter']);
        $this->assign("full_page", 1);
        $this->display("sportcat/sportcat_list.html");
    }
    
    /* ------------------------------------------------------ */
    //-- js 分页
    /* ------------------------------------------------------ */
    public function index_query(){
        $sportcat_list = $this->sportcat->get_All_Sportcat();
        $this->assign('sportcat_list', $sportcat_list['sportcat_list']);     
        $this->assign("record_count", $sportcat_list['record_count']);
        $this->assign("page_count", $sportcat_list['page_count']);
        $this->assign("filter", $sportcat_list['filter']);  
        make_json_result($this->fetch("sportcat/sportcat_list.html"), '', array('filter' => $sportcat_list['filter'], 'page_count' => $sportcat_list['page_count']));  
    }

    public function add(){
        $this->assign('act', "add");
        $this->assign('ur_here', "添加运动类别信息");
        $this->assign('action_link', array('text' => "运动类别列表", 'href' => 'route.php?con=sportcat'));
        $this->display("sportcat/sportcat_info.html");
    }

    public function edit() {
        $act = empty($_POST['act']) ? "add" : trim($_POST['act']);
        if ($act == "add")
            {
            $res = $this->sportcat->add_Sportcat($_POST);
            $link = array(array('text' => "运动类别列表", 'href' => 'route.php?con=sportcat'), array('text' => "继续添加运动类别", 'href' => 'route.php?con=sportcat&act=add'));
            if (empty($res)) {
                sys_msg("添加失败", 0, $link);
            } else {
                admin_log(addslashes($_POST['name']), 'add', 'sportcat');// 记录日志
                sys_msg("添加成功", 0, $link);
            }
        } else {
            $res = $this->sportcat->update($_POST);
            $link = array(array('text' => "运动类别列表", 'href' => 'route.php?con=sportcat'));
            if ($res) {
                admin_log(addslashes($_POST['name']), 'edit', 'sportcat');// 记录日志
                sys_msg("更新成功", 0, $link);
            } else {
                sys_msg("更新失败", 0, $link);
            }
        }
    }

    public function update() {
        $sportcat = $this->sportcat->get_sportcat($_GET['id']);
        $this->assign('sportcat', $sportcat);
        $this->assign('ur_here', "编辑运动类别");
        $this->assign('action_link', array('text' => "运动类别列表", 'href' => 'route.php?con=sportcat'));
        $this->assign('act', "update");
        $this->display("sportcat/sportcat_info.html");
    }

    public function batch_drop() {
        $link = array(array('text' => "运动类别列表", 'href' => 'route.php?con=sportcat'));
        $res = $this->sportcat->remove_b($_POST['checkboxes']);
        if ($res) {
            sys_msg("批量删除运动类别信息成功", 0, $link);
        } else {
            sys_msg("批量删除运动类别信息失败", 0, $link);
        }
    }
    
    public function remove() {
        //--hechengbin -- 删除运动类别 --start
        $result = $this->sportcat->get_Sportcat($_GET['id']);
        //--hechengbin -- end
        $res = $this->sportcat->remove($_GET['id']);
        $link = array(array('text' => "运动类别列表", 'href' => 'route.php?con=sportcat'));
        if ($res) {
            admin_log(addslashes($result['name']), 'remove', 'sportcat');// 记录日志
            sys_msg("删除成功", 0, $link);
        } else {
            sys_msg("删除失败", 0, $link);
        }
    }
}
