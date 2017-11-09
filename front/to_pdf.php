<?php

/* 
 *
 */

define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
__load("tcpdf/config/lang/chi","util");
__load("tcpdf/tcpdf","util");
//实例化 
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
// 设置文档信息 
$pdf->SetCreator('Helloweba'); 
$pdf->SetAuthor('yueguangguang'); 
$pdf->SetSubject('TCPDF Tutorial'); 
$pdf->SetKeywords('TCPDF, PDF, PHP');
$pdf->SetDefaultMonospacedFont('courier'); 
$pdf->setPrintHeader(false); //设置打印页眉
$pdf->setPrintFooter(false); //设置打印页脚
// 设置间距 
$pdf->SetMargins(15, 27, 15); 
$pdf->SetHeaderMargin(5); 
$pdf->SetFooterMargin(10);
// 设置分页 
$pdf->SetAutoPageBreak(TRUE, 25);
// set image scale factor 
$pdf->setImageScale(1.25);
$font_name=$pdf->addTTFfont('data/pdf/fonts/Droid_Sans_Fallback.ttf', 'TrueTypeUnicode', '', 32);
// set default font subsetting mode 
$pdf->setFontSubsetting(true);
//设置字体 
$pdf->SetFont($font_name, '', 14);
$pdf->AddPage();
$order_id=empty($_GET['order_id'])?0:intval($_GET['order_id']);
//$pdf->Image('logo.png', 10, 10, 40, 100, 'PNG', '', '', false, 300, '', false, false, 0, 0, false, false);
$pdf->Cell(0, 0, 'OFFICIAL TICKET CONFIRMATION', 0, 1, 'C', 0, '', 0);
$pdf->Cell(0, 0, '官方购票凭证', 0, 1, 'C', 0, '', 0);
$pdf->Cell(0, 0, '', 0, 1, 'C', 0, '', 0);
$pdf->Cell(0, 0, 'GUEST CONFIRMATION CERTIFICATE NUMBER', 0, 1, 'C', 0, '', 0);
$pdf->Cell(0, 0, '凭证号码:', 0, 1, 'C', 0, '', 0);
$pdf->Cell(0, 0, 'ORDER NUMBER:XXXXXXXXXXX', 0, 1, 'C', 0, '', 0);
$pdf->Cell(0, 0, '订单号', 0, 1, 'C', 0, '', 0);
$pdf->Cell(0, 0, '', 0, 1, 'C', 0, '', 0);
$pdf->Cell(0, 0, '', 0, 1, 'C', 0, '', 0);
$pdf->Cell(0, 0, '', 0, 1, 'C', 0, '', 0);
$pdf->Cell(0, 0, '', 0, 1, 'C', 0, '', 0);
$pdf->Cell(0, 0, '', 0, 1, 'C', 0, '', 0);
$pdf->Cell(0, 0, '', 0, 1, 'C', 0, '', 0);
$pdf->Cell(0, 0, '', 0, 1, 'C', 0, '', 0);
$pdf->Cell(0, 0, '', 0, 1, 'C', 0, '', 0);
//$pdf->Image("http://webshop.shankaisports.com/user.php?act=voucher_img&order_id=$order_id", 82, 70, 50, 100, 'PNG', '', '', false, 300, '', false, false, 0, $fitbox, false, false);
$pdf->Cell(0, 0, 'This is to cetify that official Guest Confirmation Certificate', 0, 1, 'C', 0, '', 0);
$pdf->Cell(0, 0, 'Number:xxxxx has been identified as a ticket purchase to attend sports event.', 0, 1, 'C', 0, '', 0);
$pdf->Cell(0, 0, '', 0, 1, 'C', 0, '', 0);
$pdf->Cell(0, 0, 'If a visa is required in advance for you to travel,you may produce this certificate', 0, 1, 'C', 0, '', 0);
$pdf->Cell(0, 0, '(as well as other required documents) when applying for your visa.', 0, 1, 'C', 0, '', 0);
$pdf->Cell(0, 0, '', 0, 1, 'C', 0, '', 0);
$pdf->Cell(0, 0, 'THIS IS NOT A TICKET', 0, 1, 'C', 0, '', 0);
$pdf->Cell(0, 0, '', 0, 1, 'C', 0, '', 0);
$pdf->Cell(0, 0, '此购票凭证证明持票人已成功购票并准予参加赛事', 0, 1, 'C', 0, '', 0);
$pdf->Cell(0, 0, '此凭证可提供给使馆申请签证的辅助资料', 0, 1, 'C', 0, '', 0);
$pdf->Cell(0, 0, '', 0, 1, 'C', 0, '', 0);
$pdf->Cell(0, 0, '请注意此凭证不是球票', 0, 1, 'C', 0, '', 0);
$pdf->Output('shankai.pdf', 'D');




