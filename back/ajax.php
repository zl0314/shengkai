<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 2015/7/16
 * Time: 10:37
 */


define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
include_once('includes/cls_json.php');
$act = empty($_POST['act']) ? false : trim($_POST['act']);

if ($act == false) {
    exit("�Ƿ�����");
}

elseif ($act == "get_region") {
    $game_id = empty($_POST['game_id']) ? 0 : intval($_POST['game_id']);
    if (empty($game_id)) {

    } else {
        __load("GameSerivce");
        $game_obj = new GameService();
        $region_list = $game_obj->get_region_list($game_id);
        exit($json->encode($region_list));
    }
    /***************************������*******************************************/

    function success_json($data)
    {
        $json = new JSON();
        $d = array(
            status => true,
            data => $data
        );
        exit($json->encode($d));
    }
}





