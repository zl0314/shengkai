<?php

/**
 * ECSHOP 权限对照表
 * ============================================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: sunxiaodong $
 * $Id: inc_priv.php 15503 2008-12-24 09:22:45Z sunxiaodong $
 */
if (!defined('IN_ECS')) {
    die('Hacking attempt');
}
$un_purview['Agent/ajax_game'] = true; //AJAX
$un_purview['Agent/ajax_sche'] = true; //AJAX
$un_purview['Agent/ajax_good_attr'] = true; //AJAX
$un_purview['Agent/see_audit_bearer'] = true;
$un_purview['Agent/order_list_query'] = true;
$un_purview['Agent/examine_query'] = true;//搜索
$un_purview['Agent/index_query'] = true;//分页
$un_purview['Batch/index_query'] = true;
//    $un_purview['Agent/ajax_game']        = true;//AJAX
$un_purview['Game/update'] = true; //编辑赛事界面
$un_purview['Schedule/update'] = true; //编辑赛程界面
$un_purview['Number/update'] = true; //编辑场次界面
$un_purview['Hotel/update'] = true; //编辑酒店界面
$un_purview['Room/update'] = true; //编辑酒店界面
$un_purview['Roomtype/update'] = true; //编辑酒店界面
$un_purview['Roomtype/add'] = true; //添加酒店页面
$un_purview['Pitch/update'] = true; //添加赛场页面
$un_purview['Team/update'] = true; //参赛方编辑页面
$un_purview['Agent/update'] = true; //参赛方编辑页面
$un_purview['Agent/pitch_list'] = true; //参赛方编辑页面
$un_purview['Agent/pitch_info'] = true; //参赛方编辑页面
$un_purview['Agent/bearer_info'] = true; //参赛方编辑页面
$un_purview['Color/update'] = true; //参赛方编辑页面
$un_purview['Set_meal/update'] = true; //
$un_purview['Advert/update'] = true; //
$un_purview['Advert/update'] = true; //
$un_purview['Space/update'] = true; //
$un_purview['Airticket/update'] = true; //

//Banner
$un_purview['Banner/add_place'] = true; //
$un_purview['Banner/update_place'] = true; //
$un_purview['Banner/edit'] = true; //
$un_purview['Banner/banner_list'] = true; //
$un_purview['Banner/update_banner'] = true; //
$un_purview['Banner/add_banner'] = true; //
$un_purview['Banner/banner_edit'] = true; //
$un_purview['Banner/banner_remove'] = true; //
$un_purview['Banner/place_remove'] = true; //


?>