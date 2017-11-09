<?php
header('Content-type: application/json');

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
include_once(ROOT_PATH . 'includes/cls_image.php');

if($_REQUEST['act'] == 'index'){
    $game_id = empty($_POST['game_id']) ? 0 : $_POST['game_id'];
    //根据赛事id查询赛程
    $sche = get_game_sche($game_id);
    //查询所有场次
    foreach($sche as $k => $v){
        $game_number[] = get_game_number($v['id']);
    }
    foreach($game_number as $key => $val){
        $game_sche_number[] = $val;
    }
//    echo "<pre>";
//    print_r($game_sche_number);die;
//    $jsonData=json_encode(array("data"=>$game_sche_number));
//    echo $jsonData;
//    exit;

//    exit(json_encode(['data'=>$game_sche_number]));

//    exit($data);
//    exit(json_encode($game_sche_number));
    print_r($game_sche_number);die();
}



//查询赛事下的赛程
function get_game_sche($game_id){
    $sql = "SELECT * FROM ".$GLOBALS['ecs']->table('schedule')." WHERE game_id = $game_id";
    return $GLOBALS['db']->getAll($sql);
}
//查询场次
function get_game_number($sche_id){
    $sql = "SELECT * FROM ".$GLOBALS['ecs']->table('number')." WHERE sche_id = $sche_id";
    return $GLOBALS['db']->getAll($sql);
}