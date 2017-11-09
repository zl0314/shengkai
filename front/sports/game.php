<?php
/**
 * Created by PhpStorm.
 * User: shuwang
 * Date: 2017/1/17 0017
 * Time: 19:43
 */

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require(ROOT_PATH . 'includes/cls_json.php');
$act = !empty($_REQUEST['act']) ? $_REQUEST['act'] : '';

if($act == 'number'){
    $game_id = $_REQUEST['game_id'];
    //查询赛事下的场次
    $sql = "SELECT n.id,n.num_name FROM ".$GLOBALS['ecs']->table('schedule')." AS s,".$GLOBALS['ecs']->table('number')." AS n WHERE s.id=n.sche_id AND s.game_id=$game_id";
    $number_list = $GLOBALS['db']->getAll($sql);
    if($number_list) {
        $json = new JSON;
        echo $json->encode($number_list);
        exit;
    }
}
if($act == 'pitch'){
    $number_id = $_REQUEST['number_id'];
    //查询场次下的场馆
    $sql = "SELECT p.id,p.pitch_name FROM ".$GLOBALS['ecs']->table('number')."AS n,".$GLOBALS['ecs']->table('pitch')." AS p WHERE n.pitch_id=p.id AND n.id=$number_id";
    $pitch_list = $GLOBALS['db']->getAll($sql);
    if($pitch_list){
        $json = new JSON;
        echo $json->encode($pitch_list);
        exit;
    }
}
if($act == 'rank'){
    $game_id = $_REQUEST['game_id'];
    $number_id = $_REQUEST['number_id'];
    $pitch_id = $_REQUEST['pitch_id'];
    //查询这个球场的票的等级
    $sql = "SELECT DISTINCT g.rank FROM ".$GLOBALS['ecs']->table('goods')." AS g,".$GLOBALS['ecs']->table('number')." AS n WHERE g.game_id=$game_id AND g.number_id=$number_id AND g.number_id=n.id AND n.pitch_id=$pitch_id";
    $rank = $GLOBALS['db']->getAll($sql);
    $num = count($rank);
    for($i=0;$i<$num;$i++){
        if(empty($rank[$i]['rank'])){
            unset($rank[$i]);
        }
    }
    $rank = array_merge($rank);
    if($rank){
        $json = new JSON;
        echo $json->encode(($rank));
        exit;
    }
}