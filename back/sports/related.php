<?php
/**
 * ECSHOP 地区切换程序
 * ============================================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: region.php 17217 2011-01-19 06:29:08Z liubo $
*/


define('IN_ECS', true);
define('INIT_NO_USERS', true);
define('INIT_NO_SMARTY', true);

require(dirname(__FILE__) . '/includes/init.php');
require(ROOT_PATH . 'includes/cls_json.php');

header('Content-type: text/html; charset=' . EC_CHARSET);

$type   = !empty($_REQUEST['type'])   ? intval($_REQUEST['type'])   : 0;
$grand  = !empty($_REQUEST['grand']) ? intval($_REQUEST['grand']) : 0;
$parent = !empty($_REQUEST['parent']) ? intval($_REQUEST['parent']) : 0;

$arr['relateds'] = get_relateds($type,$parent);
$arr['type']     = $type;
$arr['target']   = !empty($_REQUEST['target']) ? stripslashes(trim($_REQUEST['target'])) : '';
$arr['target']   = htmlspecialchars($arr['target']);

$json = new JSON;
echo $json->encode($arr);
/**
 * @param type $type
 * @param type $grand
 * @param type $parent
 * @return type
 */
function get_relateds($type,$parent){
    $arr = array();
    if($type == 1){
        $res = $GLOBALS['db']->getAll("select * from sk_schedule where game_id = $parent");
        foreach ($res as $key=>$val){
            $arr[$key]['related_id'] =  $val['id'];
            $arr[$key]['related_name'] =  $val['sche_name'];
            
        }   
    }
    if($type==2){
        $res = $GLOBALS['db']->getAll("select n.*,p.pitch_name from sk_number as n , sk_pitch as p where n.pitch_id=p.id AND n.sche_id = $parent");
        foreach ($res as $key=>$val){
            $arr[$key]['related_id'] =  $val['id'];
            $arr[$key]['related_name'] =  $val['num_name']."--".$val['pitch_name'];
        }
    }
    return $arr;
}
?>