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

class GameController extends Controller {

    //put your code here
    public function __construct() {
        parent::__construct();
        __load("SportcatService");
    }

    public function index() {
        __load("GameService");
        $game = new GameService();
        $this->assign('ur_here', "赛事列表");
        $this->assign('action_link', array('text' => "添加赛事", 'href' => 'route.php?con=game&act=add'));
        $game = $game->get_game_list();
        $this->assign("game_list", $game['game']);
        $this->assign("record_count", $game['record_count']);
        $this->assign("page_count", $game['page_count']);
        $this->assign("filter", $game['filter']);   
        $this->assign('full_page', 1);
        $this->display("game/game_list.html");
    }
    
    /* ------------------------------------------------------ */
    //-- js 分页
    /* ------------------------------------------------------ */
    public function index_query(){
         __load("GameService");
        $game = new GameService();
        $game = $game->get_game_list();
        $this->assign("game_list", $game['game']);
        $this->assign("record_count", $game['record_count']);
        $this->assign("page_count", $game['page_count']);
        $this->assign("filter", $game['filter']);    
        make_json_result($this->fetch("game/game_list.html"), '', array('filter' => $game['filter'], 'page_count' => $game['page_count']));
    }

    public function add() {
        $sportcat = new SportcatService();
        $sportcat_list = $sportcat->get_sportcat_list();
        $this->assign('sportcat_list', $sportcat_list);
        $this->assign('ur_here', "添加赛事");
        $this->assign('action_link', array('text' => "赛事列表", 'href' => 'route.php?con=game'));
        $this->assign('act', "add");
        $this->display("game/game_info.html");
    }
    public function update() {
        __load("GameService");
        $game = new GameService();
        $game_info = $game->get_game($_GET['id']);
        $this->assign('game', $game_info);
        $sportcat = new SportcatService();
        $sportcat_list = $sportcat->get_sportcat_list();
        $this->assign('sportcat_list', $sportcat_list);
        $this->assign('ur_here', "添加赛事");
        $this->assign('action_link', array('text' => "赛事列表", 'href' => 'route.php?con=game'));
        $this->assign('act', "update");
        $this->display("game/game_info.html");
    }

    public function edit() {
        $act = empty($_POST['act']) ? "add" : trim($_POST['act']);
        __load("GameService");
        $game = new GameService();
        if ($act == "add") {
            $link = array(array('text' => "赛事列表", 'href' => 'route.php?con=game'), array('text' => "继续添加赛事", 'href' => 'route.php?con=game&act=add'));
            $res = $game->add_game();
            if (empty($res)) {
                sys_msg("添加失败", 0, $link);
            } else {
                admin_log(addslashes($_POST['game_name']), 'add', 'games');// 记录日志
                sys_msg("添加成功", 0, $link);
            }
        } else {
            $res = $game->update_game();
            $link = array(array('text' => "赛事列表", 'href' => 'route.php?con=game'));
            if ($res) {
                admin_log(addslashes($_POST['game_name']), 'edit', 'games');// 记录日志
                sys_msg("更新成功", 0, $link);
            } else {
                sys_msg("更新失败", 0, $link);
            }
        }
        $this->display("game/game_info.html");
    }

    public function remove() {
        __load("GameService");
        $game = new GameService();
        //--hechengbin--查询当前赛事--start
        $str = $game->get_game($_GET['id']);
        //--hechengbin -- end
        $res = $game->remove_game($_GET['id']);
        $link = array(array('text' => "赛事列表", 'href' => 'route.php?con=game'));
        if ($res) {
            admin_log(addslashes($str['game_name']), 'remove', 'games');// 记录日志
            sys_msg("删除成功", 0, $link);
        } else {
            sys_msg("该赛事下存在赛程，不能删除", 0, $link);
        }
    }
    public function batch_drop() {
        __load("GameService");
        $game = new GameService();
        $link = array(array('text' => "赛事列表", 'href' => 'route.php?con=game'));
        $res = $game->remove_b($_POST['checkboxes']);
        if ($res) {
            sys_msg("批量删除赛事成功", 0, $link);
        } else {
            sys_msg("没有可删除的赛事", 0, $link);
        }
    }
    
    public function game_schedule(){       
       __load("GameService");
       $game = new GameService();
       $res = $game->get_game_schedule($_GET['id']);
       //获取赛场
       $pitch = $game->get_game_pitch();   
       //给 $pitch 压入一数组
       array_unshift($pitch,array('pitch_name'=>'&nbsp;'));       
       $this->assign('pitch_list', $pitch); 
       //给每条记录一个与赛场匹配标志
       foreach ($res as $key=>$data){
           foreach ($pitch as $k=>$pitch_info){      
               if($data['pitch_id'] == $pitch_info['id']){
                 $data['pitchid'] = $k;                       
               }
           }
           $res[$key] = $data;
       }
       $schedule = array();
       foreach($res as $k=>$data){  
            for($i=0;$i<count($pitch);$i++){
                if($i == 0){
                   $schedule[$k][$i] = local_date("D d F",local_strtotime($data['num_start']));
                   //$schedule[$k][$i] = $data['num_start'];
                }else{
                    if($data['pitchid'] == $i){
                       $schedule[$k][$i] = $data['num_name'];
                    }else{
                       $schedule[$k][$i] = '';
                    }                        
                }                    
            }                                
       }
       $this->assign('schedule_list', $schedule); 
       $this->assign('ur_here', "比赛赛程");
       $this->assign('action_link', array('text' => "赛事列表", 'href' => 'route.php?con=game'));
       $this->display("game/game_schedule.html");
    }
}
