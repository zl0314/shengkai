<?php

/**
 * Description of ColorController
 *
 * @author Kevin
 */
__load("Controller", "controller");

class ComboController extends Controller {

    //put your code here
    private $combo;

    public function __construct() {
        parent::__construct();
        __load("ComboService");
        $this->combo = new ComboService();
        __load("Combo_pitchService");
        $this->combo_pitch = new Combo_pitchService();
        __load("Combo_travelService");
        $this->combo_travel = new Combo_travelService();
        __load("Combo_travel_typeService");
        $this->combo_travel_type = new Combo_travel_typeService();
        __load("SportcatService");
        $this->sportcat = new SportcatService();
    }

    public function index() {
        $this->assign('ur_here', "套餐列表");
        $this->assign('action_link', array('text' => "添加套餐", 'href' => 'route.php?con=combo&act=add'));
        $this->assign('action_link2', array('text' => "行程分类", 'href' => 'route.php?con=combo_travel_type'));
        $combo_list = $this->combo->get_All_Combo();
        $this->assign('combo_list', $combo_list['combo_list']);
        $this->assign("record_count", $combo_list['record_count']);
        $this->assign("page_count", $combo_list['page_count']);
        $this->assign("filter", $combo_list['filter']);
        $this->assign("full_page", 1);
        $this->display("combo/combo_list.html");
    }

    /* ------------------------------------------------------ */
    //-- js 分页
    /* ------------------------------------------------------ */
    public function index_query(){
        $combo_list = $this->combo->get_All_Combo();
        $this->assign('combo_list', $combo_list['combo_list']);
        $this->assign("record_count", $combo_list['record_count']);
        $this->assign("page_count", $combo_list['page_count']);
        $this->assign("filter", $combo_list['filter']);
        make_json_result($this->fetch("combo/combo_list.html"), '', array('filter' => $combo_list['filter'], 'page_count' => $combo_list['page_count']));
    }

    public function add(){
        $this->assign('act', "add");
        $this->assign('ur_here', "添加套餐信息");
        $this->assign('action_link', array('text' => "套餐列表", 'href' => 'route.php?con=combo'));
        //获取可使用的运动类别信息
        $sportcat_list = $this->sportcat->get_sportcat_list();
        $this->assign('sportcat_list', $sportcat_list);
        //获取套餐场馆信息
        $combo_pitch_list = $this->combo_pitch->get_All_Combo_pitch();
        $this->assign('combo_pitch_list', $combo_pitch_list['combo_pitch_list']);
        //获取套餐行程信息
        $combo_travel_list = $this->combo_travel->get_All_Combo_travel();
        $this->assign('combo_travel_list', $combo_travel_list['combo_travel_list']);
        $this->display("combo/combo_info.html");
    }

    public function edit() {
        $act = empty($_POST['act']) ? "add" : trim($_POST['act']);
        //构造套餐表数据
        $_POST['combo_intro_title'] = !empty($_POST['is_use_title']) ? $_POST['combo_name'] : $_POST['combo_intro_title'];
        $comboArray = array(
            "id" => !empty($_POST['id']) ? trim($_POST['id']) : '',
            "combo_name" => !empty($_POST['combo_name']) ? trim($_POST['combo_name']) : '',
            "combo_title" => !empty($_POST['combo_title']) ? trim($_POST['combo_title']) : '',
            "combo_intro" => !empty($_POST['combo_intro']) ? trim($_POST['combo_intro']) : '',
            "combo_color" => !empty($_POST['combo_foot']) ? trim($_POST['combo_color']) : '',
            "combo_logo" => !empty($_POST['combo_logo']) ? trim($_POST['combo_logo']) : '',
            "combo_img" => !empty($_POST['combo_img']) ? trim($_POST['combo_img']) : '',
            "combo_tickets" => str_replace("\\","",$_POST['combo_tickets']),
            "combo_pitchs" => empty($_POST['combo_pitchs']) ? '' : implode('|',$_POST['combo_pitchs']),
            "combo_travels" => !empty($_POST['combo_travels']) ? implode('|',$_POST['combo_travels']) : '',
            //介绍
            "combo_intro_title" => !empty($_POST['combo_intro_title']) ? trim($_POST['combo_intro_title']) : '',
            "combo_intro_big" => !empty($_POST['combo_intro_big']) ? trim($_POST['combo_intro_big']) : '',
            "combo_editor" => !empty($_POST['combo_editor']) ? trim($_POST['combo_editor']) : '',
            "combo_charge" => !empty($_POST['combo_charge']) ? trim($_POST['combo_charge']) : '',
            "combo_visa" => !empty($_POST['combo_visa']) ? trim($_POST['combo_visa']) : '',
            "combo_intro_small" => !empty($_POST['combo_intro_small']) ? trim($_POST['combo_intro_small']) : '',
            "combo_intro_button" => !empty($_POST['combo_intro_button']) ? trim($_POST['combo_intro_button']) : '',
            "is_show" => !empty($_POST['is_show']) ? trim($_POST['is_show']) : '',
            "combo_price" => !empty($_POST['combo_price']) ? trim($_POST['combo_price']) : '',
            "combo_head" => !empty($_POST['combo_head']) ? trim($_POST['combo_head']) : '',
            "combo_foot" => !empty($_POST['combo_foot']) ? trim($_POST['combo_foot']) : '',
            "combo_travel_type_id" => !empty($_POST['combo_travel_type_id']) ? intval($_POST['combo_travel_type_id']) : '0',
        );

        $combo_travels = array();
        //兼容旧的套餐行程
        if ($comboArray['combo_travel_type_id'] > 0) {
            $combo_travel_type_info = $this->combo_travel_type->get_Combo_travel_type($comboArray['combo_travel_type_id']);
            if (!empty($combo_travel_type_info)) {
                $combo_travel_info = $this->combo_travel->get_All_Combo_travel_by_type_id($comboArray['combo_travel_type_id']);
                foreach ($combo_travel_info['combo_travel_list'] as $value) {
                    $combo_travels[] = $value['combo_travel_id'];
                }
                if (!empty($combo_travels)) {
                    $comboArray['combo_travels'] = implode('|', $combo_travels);
                }
            } else {
                //如果没有找到套餐行程的分类,那么该值设置为0,即没有选中行程分类
                $comboArray['combo_travel_type_id'] = 0;
            }
        }

        if ($act == "add")
        {
            $res = $this->combo->add_Combo($comboArray);
            $link = array(array('text' => "套餐列表", 'href' => 'route.php?con=combo'), array('text' => "继续添加套餐", 'href' => 'route.php?con=combo&act=add'));
            if (empty($res)) {
                sys_msg("添加失败", 0, $link);
            } else {
                admin_log(addslashes($comboArray['combo_name']), 'add', 'combo');// 记录日志
                sys_msg("添加成功", 0, $link);
            }
        } else {
            $res = $this->combo->update($comboArray);
            $link = array(array('text' => "套餐列表", 'href' => 'route.php?con=combo'));
            if ($res) {
                admin_log(addslashes($comboArray['combo_name']), 'edit', 'combo');// 记录日志
                sys_msg("更新成功", 0, $link);
            } else {
                sys_msg("更新失败", 0, $link);
            }
        }
    }

    public function update() {
        $combo_id = $_REQUEST['id'];
        $combo = $this->combo->get_combo($_GET['id']);
        $combo_all = $this->combo->get_combo_all();
        $combo_pitchs = explode('|',$combo['combo_pitchs']);
        $combo_travel = explode('|',$combo['combo_travels']);

        $combo_travel_info = array();
        //行程分类id
        $combo_travel_type_id = $combo['combo_travel_type_id'];
        if (!empty($combo_travel_type_id)) {
            $combo_travel_info = $this->combo_travel_type->get_Combo_travel_type($combo_travel_type_id);
        }
        $this->assign('combo_travel_info', $combo_travel_info);

        $combo_travel['combo_id'] = $combo_id;
        $this->assign('combo', $combo);
        $this->assign('combo_pitch', $combo_pitchs);
        $this->assign('ur_here', "编辑套餐");
        $this->assign('action_link', array('text' => "添加场馆", 'href' => 'route.php?con=combo_pitch&act=add&combo_id={$combo_id}'));
        $this->assign('action_link2', array('text' => "添加行程", 'href' => 'route.php?con=combo_travel&act=add&combo_id={$combo_id}'));
        $this->assign('act', "update");
        //获取可使用的运动类别信息
        $sportcat_list = $this->sportcat->get_sportcat_list();
        $this->assign('sportcat_list', $sportcat_list);
        //获取赛事信息--hechengbin--
        $game_list = $this->sportcat->get_all_game();
        $this->assign('game_list', $game_list);

        //获取套餐金额
        $combo_money = $this->combo->get_combo_money($_GET['id']);
        $this->assign('combo_money',$combo_money);
        //获取套餐场馆信息
        $combo_pitch_list = $this->combo_pitch->get_All_Combo_pitch();
        foreach($combo_pitchs as $key=>$val){
            foreach($combo_pitch_list['combo_pitch_list'] as $key2=>$val2){
                if($val==$val2['combo_pitch_id']){
                    $combo_pitch_list['combo_pitch_list'][$key2]['is_true']=1;
                }
            }
        }
        $this->assign('combo_pitch_list', $combo_pitch_list['combo_pitch_list']);
        //获取套餐行程信息
        $combo_travel_list = $this->combo_travel->get_All_Combo_travel();

        unset($combo_travel['combo_id']);

        foreach($combo_travel as $key=>$val){
            foreach($combo_travel_list['combo_travel_list'] as $key2=>$val2){
                if($val==$val2['combo_travel_id']){
                    $combo_travel_list['combo_travel_list'][$key2]['is_true']=1;
                }
                foreach($combo_all as $key3=>$val3){
                    $combo_travels_list_res[$key3]['tra_id']=explode('|',$val3['combo_travels']);
                    $combo_travels_list_res[$key3]['tra_name']=$val3['combo_name'];
                }

                foreach($combo_travels_list_res as $key4=>$val4){
                    foreach($val4['tra_id'] as $key5=>$val5){
                        if($val5==$val2['combo_travel_id']){
                            $combo_travel_list['combo_travel_list'][$key2]['combo_name']=$val4['tra_name'];
                        }
                    }
                }
            }
        }
        $this->assign('combo_travel_list', $combo_travel_list['combo_travel_list']);

        //获取套餐行程分类
        $combo_travel_type_list = $this->combo_travel_type->get_All_Combo_travel_type();
        $this->assign('combo_travel_type_list', $combo_travel_type_list['combo_travel_type_list']);

        $this->display("combo/combo_info.html");
    }

    public function batch_drop() {
        $link = array(array('text' => "套餐列表", 'href' => 'route.php?con=combo'));
        $res = $this->combo->remove_b($_POST['checkboxes']);
        if ($res) {
            admin_log(addslashes($_POST['checkboxes']), 'batch_remove', 'combo');// 记录日志
            sys_msg("批量删除套餐信息成功", 0, $link);
        } else {
            sys_msg("批量删除套餐信息失败", 0, $link);
        }
    }

    public function remove() {
        //--hechengbin--start--查询套餐信息
        $str = $this->combo->get_Combo($_GET['id']);
        //--hechengbin --end
        $res = $this->combo->remove($_GET['id']);
        $link = array(array('text' => "套餐列表", 'href' => 'route.php?con=combo'));
        if ($res) {
            admin_log(addslashes($str['combo_name']), 'remove', 'combo');// 记录日志
            sys_msg("删除成功", 0, $link);
        } else {
            sys_msg("删除失败", 0, $link);
        }
    }

    public function search() {
        $filters = (object)array(
            'game_id' => $_GET['game_id'],
            'keyword' => $_GET['keyword'],
            'is_ticket' => $_GET['is_ticket']
        );
        $arr = get_tickets_list($filters);
        $opt = array();
        foreach ($arr AS $val){
            $opt[] = array(
                'value' => $val['goods_id'],
                'text'  => $val['goods_name']
            );
        }
        make_json_result($opt);
    }
}
