<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BatchController
 *
 * @author qs
 */
__load("Controller", "controller");

class BatchController extends Controller {

    private $batch, $game;

    //put your code here
    public function __construct() {
        parent::__construct();
        __load("GameService");
        __load("BatchService");
        $this->batch = new BatchService();
        $this->game = new GameService();
    }

    public function add_batch() {
        $this->assign('ur_here', "添加批次");
        $this->assign('action_link', array('text' => "批次列表", 'href' => 'route.php?con=batch'));
        $this->assign('games', $this->game->get_list());
        $this->display("batch/batch_info.html");
    }

    public function index() {
        $filter['game_id'] = empty($_REQUEST['game_id']) ? 0 : intval($_REQUEST['game_id']);
        $filter['game_id_1'] = empty($_REQUEST['game_id_1']) ? 0 : intval($_REQUEST['game_id_1']);
        $filter['number_id'] = empty($_REQUEST['number_id']) ? 0 : intval($_REQUEST['number_id']);
        $filter['number_id_1'] = empty($_REQUEST['number_id_1']) ? 0 : intval($_REQUEST['number_id_1']);
        $filter['pitch_id'] = empty($_REQUEST['pitch_id']) ? 0 : intval($_REQUEST['pitch_id']);
        $filter['pitch_id_1'] = empty($_REQUEST['pitch_id_1']) ? 0 : intval($_REQUEST['pitch_id_1']);
        $filter['change'] = empty($_REQUEST['change']) ? 0 : intval($_REQUEST['change']);
        $filter['change_1'] = empty($_REQUEST['change_1']) ? 0 : intval($_REQUEST['change_1']);
        $filter['ticket'] = empty($_REQUEST['ticket']) ? 0 : intval($_REQUEST['ticket']);
        if($filter['change']){
            $this->assign("change", $filter['change']);
        }else{
            $this->assign("change", $filter['change_1']);
        }
        $this->assign('ur_here', "批次列表");
        $this->assign('action_link', array('text' => "添加批次", 'href' => 'route.php?con=batch&act=add_batch'));
        $number = $this->batch->getAllNumber();
        $game = $this->batch->getAllGame();
        $pitch = $this->batch->getAllPitch();
        $batch_list = $this->batch->get_list();
        $this->assign('batch_list', $batch_list['batch_all']);
        $this->assign('num_name',$number);
        $this->assign('game',$game);
        $this->assign('pitch_name',$pitch);
        $this->assign("record_count", $batch_list['record_count']);
        $this->assign("page_count", $batch_list['page_count']);
        $this->assign("filter", $batch_list['filter']);
        $this->assign("full_page", 1);
        $this->assign("game_id", $filter['game_id']);
        $this->assign("game_id_1", $filter['game_id_1']);
        $this->assign("number_id", $filter['number_id']);
        $this->assign("number_id_1", $filter['number_id_1']);
        $this->assign("pitch_id", $filter['pitch_id']);
        $this->assign("pitch_id_1", $filter['pitch_id_1']);
        $this->assign("ticket",$filter['ticket']);
        $this->display("batch/batch_list.html");
    }
    /*------------------------------------------------------ */
//-- 排序、分页、查询
/*------------------------------------------------------ */

    /* ------------------------------------------------------ */
    //-- js 分页
    /* ------------------------------------------------------ */
    public function index_query(){
//        if ($_REQUEST['act'] == 'query') {
//              $get_list = get_list();
        $batchs = $this->batch->get_list();
        $this->assign('batch_list', $batchs['batch_all']);
        $this->assign("record_count", $batchs['record_count']);
        $this->assign("page_count", $batchs['page_count']);
        $this->assign("filter", $batchs['filter']);
        make_json_result($this->fetch("batch/batch_list.html"), '', array('filter' => $batchs['filter'], 'page_count' => $batchs['page_count']));
//        }
    }
// public function query(){
//     
//     if ($_REQUEST['act'] == 'query') {
//    /* 检查权限 */
//    admin_priv('order_view');
//    $get_list = get_list();
//     $this->assign('batchs', $batchs['batch_all']);
//        $this->assign("record_count", $batchs['record_count']);
//        $this->assign("page_count", $batchs['page_count']);
//        $this->assign("filter", $batchs['filter']);
//        make_json_result($this->fetch("batch/batch_list.html"), '', array('filter' => $batchs['filter'], 'page_count' => $batchs['page_count']));
//}
//    }
    public function batchs()
    {
        /* 记录总数 */
        $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('batch');
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        /* 分页大小 */
        $filter = page_and_size($filter);

        $sql = "SELECT *FROM " . $this->ecs->table('batch') . " ORDER BY add_time DESC LIMIT " . $filter['start'] . ",$filter[page_size]";
        $batch_all = $this->db->getAll($sql); //获取订单   

        $batch_data = array(); //预定义列表信息数组
        foreach ($batch_all as $keys => $agent_order_list_data) {
            $sql = "SELECT aoi.*, g.game_name, s.sche_name, n.*, p.pitch_name, r.region_name FROM " . $this->ecs->table('batch_info') . " AS aoi LEFT JOIN " . $this->ecs->table('game') . " AS g ON aoi.game_id = g.id LEFT JOIN " . $this->ecs->table('number') . " AS n ON aoi.number_id = n.id LEFT JOIN " . $this->ecs->table('schedule') . " AS s ON aoi.sche_id = s.id LEFT JOIN " . $this->ecs->table('pitch') . " AS p ON n.pitch_id = p.id LEFT JOIN " . $this->ecs->table('region') . " AS r ON p.region_id = r.region_id WHERE batch_id = '$agent_order_list_data[id]'";
            $batch_all[$keys]['datas'] = $this->db->getAll($sql); //列表信息    
            $batch_data['datas'] = $this->db->getAll($sql);
            //获取作为类型
            foreach ($batch_data['datas'] as $key => $data) {
                $sql = "SELECT goods_name FROM " . $this->ecs->table('goods') . " WHERE goods_id = '$data[goods_id]'";
                $batch_all[$keys]['datas'][$key]['goods_name'] = $this->db->getOne($sql);
                $batch_all[$keys]['datas'][$key]['agent'] = $agent_order_list_data['agent'];
                $batch_all[$keys]['datas'][$key]['date'] = date("H:i:s", $agent_order_list_data['num_start']) . "-" . date("H:i:s", $agent_order_list_data['num_end']);
                $batch_all[$keys]['add_time'] = date("Y-m-d H:i:s", $agent_order_list_data['add_time']);
                $batch_all[$keys]['row'] = $key + 1;
            }
        }
        return array('batch_all' => $batch_all, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
    }

    public function void() {
        $id = $_GET['id'];
        //--hechengbin -- 设为无效 --start
        $result = $this->batch->get_batch($id);
        //--hechengbin -- end
        $link = array(array('text' => "批次列表", 'href' => 'route.php?con=batch'));
        if ($this->batch->void_batch($id)) {
            admin_log(addslashes($result['batch_sn']), 'setup', 'batch');// 记录日志
            sys_msg("操作成功", 0, $link);
        } else {
            sys_msg("操作失败", 0, $link);
        }
    }

    public function insert_batch() {
        $link = array(array('text' => "批次列表", 'href' => 'route.php?con=batch'));
        if ($this->batch->add($_POST)) {
            admin_log(addslashes($_POST['batch_sn']), 'add', 'batch');// 记录日志
            sys_msg("添加成功", 0, $link);
        } else {
            sys_msg("添加失败", 0, $link);
        }
    }

}
