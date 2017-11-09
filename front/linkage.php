<?php

define('IN_ECS', true);
define('INIT_NO_USERS', true);
define('INIT_NO_SMARTY', true);
require(dirname(__FILE__) . '/includes/init.php');
require(ROOT_PATH . 'includes/cls_json.php');
header('Content-type: text/html; charset=' . EC_CHARSET);

$parent = !empty($_REQUEST['parent']) ? intval($_REQUEST['parent']) : 0;
$target = !empty($_REQUEST['target']) ? trim($_REQUEST['target']) : '';

if($target == 'game_id'){
    $result['linkages'] = get_games($parent);
}elseif($target == 'city_id'){
    $result['linkages'] = get_citys($parent);
}

$result['target'] = $target;
$json = new JSON;
die($json->encode($result));


function get_games($scat_id){
    $games_array = array();
    $games_list = $GLOBALS['db']->getAll("SELECT id,game_name FROM ".$GLOBALS['ecs']->table('game')." WHERE scat_id = ".$scat_id);
    foreach($games_list as $key=>$list){
        $games_array[$key]['linkage_id'] = $list['id'];
        $games_array[$key]['linkage_name'] = $list['game_name'];
    }
    return $games_array;
}

function get_citys($game_id){
        $city_array = array();
        $sql = "SELECT r.region_id,r.region_name FROM ".$GLOBALS['ecs']->table('region')." as r," .
            $GLOBALS['ecs']->table('pitch') ." as p," .
            $GLOBALS['ecs']->table('number') ." as n," .
            $GLOBALS['ecs']->table('schedule') ." as s" .
            " WHERE r.region_id = p.region_id AND p.id = n.pitch_id AND n.sche_id = s.id AND s.game_id = '$game_id' GROUP BY r.region_id";
        $city_list = $GLOBALS['db']->getAll($sql);
        foreach($city_list as $key=>$list){
            $city_array[$key]['linkage_id'] = $list['region_id'];
            $city_array[$key]['linkage_name'] = $list['region_name'];
        }
        return $city_array;
}
?>