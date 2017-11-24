<?php

/*
 * 作者：戎青松
 * 时间：13:58:53
 * 
 */

/**
 * 包引入
 * @param type $class_name 类名
 * @param string $type 类型 参考 service（引入service目录下的类）、service_impl（引入service/impl目录下的类）默认为service_impl
 * @return type
 */
function __load($class_name, $type = "")
{
    if (empty($type)) {
        $type = "service_impl";
    }
    $file = "";
    switch ($type) {
        case "service_impl":
            $file = IMPL_PAHT . $class_name . '.php';
            break;
        case "service":
            $file = SERVICE_PAHT . $class_name . '.php';
            break;
        case "util":
            $file = UTIL_PATH . $class_name . '.php';
            break;
        case "controller":
            $file = CON_PATH . $class_name . '.php';
            break;
        case "agent_controller":
            $file = AGENT_CON_PATH . $class_name . '.php';
            break;
        case "admin_controller":
            $file = ADMIN_CON_PATH . $class_name . '.php';
            break;
        case "model":
            $file = MODEL_PATH . $class_name . '.php';
            break;
        default :
            break;
    }

    if (file_exists($file)) {
        return include_once $file;
    } else {
        exit(" no class" . $file);
    }
}


/**
 * 记录日志
 * @param type $msg 日志内容
 * @param type $type 日志类型（会根据类型去创建相应的文件夹）
 */
function __log($msg, $type = "")
{
    __load("Log", "util");
    new Log($msg, $type);
}

function set_redis_cache($key, $value)
{
    $redis = new Redis();
    $redis->connect(REDIS_HOST, REDIS_PORT);
    $redis->set($key, $value);
}

function get_redis_cache($key)
{
    $redis = new Redis();
    $redis->connect(REDIS_HOST, REDIS_PORT);
    return $redis->get($key);
}

function select_address($id)
{
    __load("RegionModel", "model");
    $region = new RegionModel();
    if (empty($id)) {
        return "地址为空";
    } else {
        return $region->get_region_name($id);
    }
}

/**
 * 生成二维码
 * @param type $file_name 文件名字
 * @param type $value 二维码内容
 */
function create_qr($value, $file_name = false)
{
    __load("phpqrcode/phpqrcode", "util");
    $errorCorrectionLevel = 'L';//容错级别   
    $matrixPointSize = 6;//生成图片大小   
    //生成二维码图片
    QRcode::png($value, $file_name, $errorCorrectionLevel, $matrixPointSize, 2);
}

/**保存pdf
 * @param $html_content
 * @param $file_name
 */
function sava_pdf($html_content, $file_name, $print_header = true, $print_footer = true, $print_type = "F")
{
    __load("tcpdf/config/lang/chi", "util");
    __load("tcpdf/tcpdf", "util");
//实例化
    $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
// 设置文档信息
    $pdf->SetCreator('Helloweba');
    $pdf->SetAuthor('yueguangguang');
    $pdf->SetSubject('TCPDF Tutorial');
    $pdf->SetKeywords('TCPDF, PDF, PHP');
    $font_name = $pdf->addTTFfont(ROOT_PATH . 'data/pdf/fonts/Droid_Sans_Fallback.ttf', 'TrueTypeUnicode', '', 32);
    //设置页眉和页脚信息
    $pdf->SetHeaderData('logo.png', PDF_HEADER_LOGO_WIDTH, '', '');
    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
// 设置页眉和页脚字体
    $pdf->setHeaderFont(Array($font_name, '', '10'));
    $pdf->SetDefaultMonospacedFont('courier');
    if ($print_header) {
        $pdf->setPrintHeader(true); //设置打印页眉
    } else {
        $pdf->setPrintHeader(false); //设置打印页眉
    }
    if ($print_footer) {
        $pdf->setPrintFooter(true); //设置打印页脚
    } else {
        $pdf->setPrintFooter(false); //设置打印页脚
    }
// 设置间距
    $pdf->SetMargins(15, 27, 15);
    $pdf->SetHeaderMargin(5);
    $pdf->SetFooterMargin(10);
// 设置分页
    $pdf->SetAutoPageBreak(TRUE, 25);
    $pdf->setImageScale(1.25);
    $pdf->setFontSubsetting(true);
//设置字体
    $pdf->SetFont($font_name, '', 12);
    $pdf->AddPage();
    $pdf->writeHTML($html_content, true, false, false, false, '');
    if (file_exists($file_name)) {
        @unlink($file_name);
    }
    $pdf->Output($file_name, $print_type);
}


/** 生成购票凭证
 * @param $order_id 订单号
 */
function create_vouch_pdf($order_id)
{
    __load("OrderService");
    $order_obj = new OrderService();
    $order = $order_obj->get_order_sn($order_id);
    $order_sn = $order['order_sn'];
    $file_name = ROOT_PATH . "data/pdf_data/tmp_qr/qr_{$order_sn}.png";
    $value = 'http://webshop.shankaisports.com/user.php?act=show_pdf_info&order_sn=' . $order_sn; //二维码内容
    
    create_qr($value, $file_name);
    $GLOBALS['smarty']->assign("order_sn", $order_sn);
    $GLOBALS['smarty']->assign("qr_url", $file_name);
    $html = $GLOBALS['smarty']->fetch("pdf_tpl/vouch.tpl", null, null, false);

    sava_pdf($html, ROOT_PATH . "data/pdf_data/vouch/vouch_{$order_sn}.pdf", false, false, "F");
    if (file_exists($file_name)) {
        @unlink($file_name);
    }
}

/** 快捷打印
 * @param $array
 */
function P($array){
    echo "<pre>";
    print_r($array);
    var_dump($array);
    die;
}

