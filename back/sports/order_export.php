<?php

/**
 * ECSHOP 订单管理
 * ============================================================================
 * 版权所有 2005-2010 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: yehuaixiao $
 * $Id: order.php 17219 2011-01-27 10:49:19Z yehuaixiao $
 */

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . 'includes/lib_order.php');
require_once(ROOT_PATH . 'includes/lib_goods.php');
require_once(ROOT_PATH . 'includes/cls_json.php');
require_once(ROOT_PATH . 'includes/modules/payment/func/log.php');
__load("BearerService");
$bearer_obj = new BearerService();
/*------------------------------------------------------ */
//-- 订单查询
/*------------------------------------------------------ */

//初始化日志
$logHandler = new CLogFileHandler(ROOT_PATH . "/logs/" . date('Y-m-d') . '.log');
$log = Logger::Init($logHandler, 15);
if ($_REQUEST['act'] == "export") {

    $start_date = isset($_REQUEST['start_date']) && !empty($_REQUEST['start_date']) ? trim($_REQUEST['start_date']) : '';
    $end_date = isset($_REQUEST['end_date']) && !empty($_REQUEST['end_date']) ? trim($_REQUEST['start_date']) : '';
    $status = isset($_REQUEST['status']) && !empty($_REQUEST['status']) ? intval($_REQUEST['start_date']) : 2;
    $goods_id = isset($_REQUEST['goods_id']) && !empty($_REQUEST['goods_id']) ? intval($_REQUEST['goods_id']) : 0;

    if (empty($goods_id)) {
        die('商品id不能为空');
    }

    $where = array();

    if (!empty($goods_id)) {
        $where[] = "a.goods_id = '{$goods_id}'";
    }
    if (!empty($start_date)) {
        $start_timestamp = strtotime($start_date);
        $where[] = "b.add_time > '{$start_timestamp}'";
    }

    if (!empty($end_date)) {
        $end_timestamp = strtotime($end_date . ' 23:59:59');
        $where[] = "b.add_time < '{$end_timestamp}'";
    }

    if ($status > -1) {
        $where[] = "b.pay_status = '{$status}'";
    }


    //订单号，日期，价格，数量，支付方式，订单状态，收货人，下单日期，手机号码，购票票务名称，场次名称，货号总金额
    $sql = "select b.order_id, b.order_sn, pay_name, pay_status, b.consignee, FROM_UNIXTIME(b.add_time, '%Y-%m-%d %H:%i:%s') as add_date, b.mobile, a.goods_name, '' as pitch, b.goods_amount as 'amount', count(a.rec_id) as num from sk_order_ticket a right join sk_order_info b
on a.order_id = b.order_id";

    if (!empty($where)) {
        $sql .= " where " . implode(" and ", $where);
    }

    $sql .= " group by a.order_id order by a.order_id desc";

    $allData = $db->getAll($sql);

    __load("phpexcel/PHPExcel", "util");
    __load("phpexcel/PHPExcel/Writer/Excel2007", "util");
    $objPHPExcel = new PHPExcel();
    $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
    //设置单元格格式
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(100);
    $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);

    $objPHPExcel->setActiveSheetIndex(0);
    $objPHPExcel->getActiveSheet()->setTitle('票务销售情况');
    $objPHPExcel->getActiveSheet()->setCellValue('A1', '订单号');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', '支付方式');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', '订单状态');
    $objPHPExcel->getActiveSheet()->setCellValue('D1', '收货人');
    $objPHPExcel->getActiveSheet()->setCellValue('E1', '下单日期');
    $objPHPExcel->getActiveSheet()->setCellValue('F1', '手机号码');
    $objPHPExcel->getActiveSheet()->setCellValue('G1', '购票票务名称');
    $objPHPExcel->getActiveSheet()->setCellValue('H1', '场次名称');
    $objPHPExcel->getActiveSheet()->setCellValue('I1', '数量');
    $objPHPExcel->getActiveSheet()->setCellValue('J1', '总金额');

    foreach ($allData as $key => $data) {
        $objPHPExcel->getActiveSheet()->setCellValue('A' . ($key + 2), ' ' . $data['order_sn']);
        $objPHPExcel->getActiveSheet()->setCellValue('B' . ($key + 2), $data['pay_name']);
        $objPHPExcel->getActiveSheet()->setCellValue('C' . ($key + 2), $data['pay_status']);
        $objPHPExcel->getActiveSheet()->setCellValue('D' . ($key + 2), $data['consignee']);
        $objPHPExcel->getActiveSheet()->setCellValue('E' . ($key + 2), $data['add_date']);
        $objPHPExcel->getActiveSheet()->setCellValue('F' . ($key + 2), $data['mobile']);
        $objPHPExcel->getActiveSheet()->setCellValue('G' . ($key + 2), $data['goods_name']);
        $objPHPExcel->getActiveSheet()->setCellValue('H' . ($key + 2), $data['pitch']);
        $objPHPExcel->getActiveSheet()->setCellValue('I' . ($key + 2), $data['num']);
        $objPHPExcel->getActiveSheet()->setCellValue('J' . ($key + 2), $data['amount']);
    }
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
    header("Content-Type:application/force-download");
    header("Content-Type:application/vnd.ms-execl");
    header("Content-Type:application/octet-stream");
    header("Content-Type:application/download");;
    header('Content-Disposition:attachment;filename="export_data_order.xls"');
    header("Content-Transfer-Encoding:binary");
    $objWriter->save('php://output');
}
