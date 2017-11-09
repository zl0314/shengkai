<?php
/**
 * Created by PhpStorm.
 * User: shuwang
 * Date: 2016/6/23 0023
 * Time: 16:09
 */
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
include_once('includes/cls_json.php');
     $act = empty($_REQUEST['act']) ? "index" : trim($_REQUEST['act']);
        $game_list=$GLOBALS['db']->getAll('SELECT id,game_name FROM sk_game');
        $smarty->assign('game_list', $game_list);
        $namel="<img src='images/logo.png'>";
        $smarty->assign( 'name1',$namel);
        $smarty->display("agreement.html");
   