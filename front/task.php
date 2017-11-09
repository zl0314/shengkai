<?php

/**
 *定时任务
 */

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require(ROOT_PATH . 'includes/lib_order.php');

//$sql = "SELECT order_id FROM " . $GLOBALS['ecs']->table('order_info') . " WHERE pay_status = 0 And order_status = 0 AND unix_timestamp(now())-add_time>(60*60*24*3)";
//$order_id = $GLOBALS['db']->getCol($sql);
$sql = "SELECT * FROM " . $GLOBALS['ecs']->table('order_info') . " WHERE pay_status = 0 And order_status = 0 AND unix_timestamp(now())-add_time>(60*60*24*3)";
$orders = $GLOBALS['db']->getAll($sql);
foreach ($orders as $order) {
    $order_id = $order['order_id'];
    $user_id = $order['user_id'];
    change_order_goods_storage($order_id, false, 1);
    $sql = "UPDATE " . $GLOBALS['ecs']->table('order_info') . " SET order_status = 2 WHERE order_id= '{$order_id}'";
    $GLOBALS['db']->query($sql);

    //检查是否有酒店信息
    $sql = "SELECT room_id, goods_number, hotel_start_date, hotel_end_date FROM " . $ecs->table('order_hotel') . " WHERE order_id = '{$order_id}'";
    $result = $GLOBALS['db']->getAll($sql);
    if (!empty($result)) {
        __load("Room_numService");
        $room_num_service = new Room_numService();
        foreach ($result as $value) {
            //$ret = $room_num_service->unlock_room_num_from_in_date($value['room_id'], $value['goods_number'], $value['hotel_start_date'], $value['hotel_end_date']);
            //这里只验证取消开始预定时间的库存了
            $ret = $room_num_service->unlock_room_num_from_in_date($value['room_id'], $value['goods_number'], $value['hotel_start_date'], $value['hotel_start_date']);
            __log("定时任务:取消酒店预订锁定数量:{$ret}, ROOM_ID = {$value['room_id']}, NUM: {$value['goods_number']}, START DATE: {$value['hotel_start_date']}, END DATE: {$value['hotel_start_date']}");
        }
    } else {
        __log("定时任务:取消酒店预订锁定数量,没有酒店订单");
    }

    //检查是否有机票信息
    $sql = "SELECT air_id, fly_date, return_fly_date, goods_number FROM " . $ecs->table('order_plane') . " WHERE order_id = '{$order_id}'";
    $result = $GLOBALS['db']->getAll($sql);
    if (!empty($result)) {
        __load("Air_line_numService");
        $air_line_num_service = new Air_line_numService();
        foreach ($result as $value) {
            //$ret = $room_num_service->unlock_room_num_from_in_date($value['room_id'], $value['goods_number'], $value['hotel_start_date'], $value['hotel_end_date']);
            //这里只验证取消开始预定时间的库存了
            $ret = $air_line_num_service->unlock_air_line_num_from_in_date($value['air_id'], $value['goods_number'], $value['fly_date'], $value['return_fly_date']);
            __log("定时任务:取消酒机票预订锁定数量:{$ret}, AIR_ID = {$value['air_id']}, NUM: {$value['goods_number']}, START DATE: {$value['fly_date']}, END DATE: {$value['return_fly_date']}");
        }
    } else {
        __log("定时任务:取消机票预订锁定数量,没有机票订单");
    }

    //检查是否使用了优惠券
    if (!empty($order['coupon_id']) && $order['coupon_id'] > 0) {
        __log("定时任务:退回优惠券, {$order['coupon_id']}, {$order['coupon_paid']}, {$order_id}");
        //优惠券
        __load("UserCouponService");
        $user_coupon_service = new UserCouponService();

        //设置优惠券已使用
        $user_coupon_service->set_coupon_unused(array(
            'coupon_id' => $order['coupon_id'],
            'order_id' => $order_id,
        ));
    }

    //积分退回
    if (!empty($order['credits_paid']) && $order['credits_paid'] > 0) {
        $credits_num = $order['credits_paid'] * $_CFG['credits_exchange'];
        __log("定时任务:退回积分, {$order['credits_paid']}, {$order_id}");
        $sql = "UPDATE " . $ecs->table('users') . " SET credits_num = credits_num + '{$credits_num}', used_credits_num = used_credits_num - '{$credits_num}' WHERE user_id = '{$user_id}'";
        $GLOBALS['db']->query($sql);
    }
}