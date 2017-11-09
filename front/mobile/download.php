<?php
/* 
 * 作者：戎青松
 * 时间：11:36:41
 * 
 */
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
$file_type=$_GET['type'];
$file_name=$_GET['file_name'];
$user_info_dir="../data/pdf_data/user_info/";
$vouch_dir="../data/pdf_data/vouch/";
$file_dir="../data/down_file/";
$file="";
if($file_type=="vouch"){
    $file=$vouch_dir.$file_name;
}elseif($file_type=="user_info"){
    $file=$user_info_dir.$file_name;
}elseif($file_type=="file"){
    $file=$file_dir.$file_name;
//}else{
////    show_message("未知的文件类型", '', '', 'warning');
//    exit;
//}
//if (!file_exists($file)) { //检查文件是否存在
////    show_message("找不到下载文件", '', '', 'warning');
//    exit;
}
//else {
    $file_obj = fopen($file,"r"); // 打开文件
// 输入文件标签
    Header("Content-type: application/pdf;charset=utf-8");
    Header("Accept-Ranges: bytes");
    Header("Accept-Length: ".filesize($file));
    Header("Content-Disposition: attachment; filename=" . $file_name);
// 输出文件内容
    echo fread($file_obj,filesize($file));
    fclose($file_obj);
    exit;
//}



