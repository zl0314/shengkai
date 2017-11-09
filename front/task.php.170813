<?php

/**
 *定时任务
 */

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require(ROOT_PATH . 'includes/lib_order.php');


$sql = "SELECT order_id FROM " . $GLOBALS['ecs']->table('order_info') . " WHERE pay_status = 0 And order_status = 0 AND unix_timestamp(now())-add_time>(60*60*24*3)";
$order_id = $GLOBALS['db']->getCol($sql);
foreach($order_id as $id){
    change_order_goods_storage($id,false,1);
    $sql="UPDATE ".$GLOBALS['ecs']->table('order_info')." SET order_status = 2 WHERE order_id= '$id'";
    $GLOBALS['db']->query($sql);
}




?>