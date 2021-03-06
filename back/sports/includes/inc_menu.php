<?php

/**
 * ECSHOP 管理中心菜单数组
 * ============================================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: inc_menu.php 17217 2011-01-19 06:29:08Z liubo $
 */
if (!defined('IN_ECS')) {
    die('Hacking attempt');
}

$modules['02_cat_and_goods']['01_goods_list'] = 'goods.php?act=list';         // 商品列表
$modules['02_cat_and_goods']['02_goods_add'] = 'goods.php?act=add';          // 添加商品

//$modules['02_cat_and_goods']['05_comment_manage'] = 'comment_manage.php?act=list';
//$modules['02_cat_and_goods']['06_goods_brand_list'] = 'brand.php?act=list';

$modules['02_cat_and_goods']['11_goods_trash'] = 'goods.php?act=trash';        // 商品回收站
$modules['02_cat_and_goods']['02_hotel'] = 'route.php?con=hotel';
//$modules['02_cat_and_goods']['01_roomtype'] = 'route.php?con=roomtype';
$modules['02_cat_and_goods']['combo_list'] = 'route.php?con=combo';

$modules['02_cat_and_goods']['02_airticket'] = 'route.php?con=airticket';
$modules['02_cat_and_goods']['02_air_line'] = 'route.php?con=air_line';//机票
$modules['02_cat_and_goods']['02_question_paper_r201860'] = 'route.php?con=r201860';//问卷
//$modules['02_cat_and_goods']['01_space'] = 'route.php?con=space';

//$modules['02_cat_and_goods']['12_batch_pic'] = 'picture_batch.php';
//$modules['02_cat_and_goods']['13_batch_add'] = 'goods_batch.php?act=add';    // 商品批量上传
//$modules['02_cat_and_goods']['14_goods_export'] = 'goods_export.php?act=goods_export';
//$modules['02_cat_and_goods']['15_batch_edit'] = 'goods_batch.php?act=select'; // 商品批量修改


//$modules['02_cat_and_goods']['16_goods_script'] = 'gen_goods_script.php?act=setup';
//$modules['02_cat_and_goods']['17_tag_manage'] = 'tag_manage.php?act=list';
//$modules['02_cat_and_goods']['50_virtual_card_list'] = 'goods.php?act=list&extension_code=virtual_card';
//$modules['02_cat_and_goods']['51_virtual_card_add'] = 'goods.php?act=add&extension_code=virtual_card';
//$modules['02_cat_and_goods']['52_virtual_card_change'] = 'virtual_card.php?act=change';
//$modules['02_cat_and_goods']['goods_auto'] = 'goods_auto.php?act=list';
//$modules['03_promotion']['02_snatch_list'] = 'snatch.php?act=list';
//$modules['03_promotion']['04_bonustype_list'] = 'bonus.php?act=list';
//$modules['03_promotion']['06_pack_list'] = 'pack.php?act=list';
//$modules['03_promotion']['07_card_list'] = 'card.php?act=list';
//$modules['03_promotion']['08_group_buy'] = 'group_buy.php?act=list';
//$modules['03_promotion']['09_topic'] = 'topic.php?act=list';
//$modules['03_promotion']['10_auction'] = 'auction.php?act=list';
//$modules['03_promotion']['12_favourable'] = 'favourable.php?act=list';
//$modules['03_promotion']['13_wholesale'] = 'wholesale.php?act=list';
//$modules['03_promotion']['14_package_list'] = 'package.php?act=list';
////$modules['03_promotion']['ebao_commend']            = 'ebao_commend.php?act=list';
//$modules['03_promotion']['15_exchange_goods'] = 'exchange_goods.php?act=list';


$modules['03_order']['03_order_list'] = 'order.php?act=list';
$modules['03_order']['20_order_combo_list'] = 'order.php?act=list&order_type=1';//套餐
$modules['03_order']['21_order_ticket_list'] = 'order.php?act=list&order_type=2';//单票
$modules['03_order']['22_order_goods_list'] = 'order.php?act=list&order_type=3';//纪念品
$modules['03_order']['23_order_airplane_list'] = 'order.php?act=list&order_type=4';//机票
$modules['03_order']['24_order_hotel_list'] = 'order.php?act=list&order_type=5';//酒店
//$modules['04_order']['03_order_query'] = 'order.php?act=order_query';
//$modules['04_order']['04_merge_order'] = 'order.php?act=merge';
//$modules['04_order']['05_edit_order_print'] = 'order.php?act=templates';
//$modules['04_order']['06_undispose_booking'] = 'goods_booking.php?act=list_all';
////$modules['04_order']['07_repay_application']        = 'repay.php?act=list_all';
//$modules['04_order']['08_add_order'] = 'order.php?act=add';
//$modules['04_order']['09_delivery_order'] = 'order.php?act=delivery_list';
//$modules['04_order']['10_back_order'] = 'order.php?act=back_list';

//$modules['08_banner']['ad_position'] = 'ad_position.php?act=list';
//$modules['08_banner']['ad_list'] = 'ads.php?act=list';
//
//$modules['06_stats']['flow_stats'] = 'flow_stats.php?act=view';
//$modules['06_stats']['searchengine_stats'] = 'searchengine_stats.php?act=view';
//$modules['06_stats']['z_clicks_stats'] = 'adsense.php?act=list';
//$modules['06_stats']['report_guest'] = 'guest_stats.php?act=list';
//$modules['06_stats']['report_order'] = 'order_stats.php?act=list';
//$modules['06_stats']['report_sell'] = 'sale_general.php?act=list';
//$modules['06_stats']['sale_list'] = 'sale_list.php?act=list';
//$modules['06_stats']['sell_stats'] = 'sale_order.php?act=goods_num';
//$modules['06_stats']['report_users'] = 'users_order.php?act=order_num';
//$modules['06_stats']['visit_buy_per'] = 'visit_sold.php?act=list';

$modules['11_content']['03_article_list'] = 'article.php?act=list';
$modules['11_content']['02_articlecat_list'] = 'articlecat.php?act=list';
//$modules['07_content']['vote_list'] = 'vote.php?act=list';
//$modules['07_content']['article_auto'] = 'article_auto.php?act=list';
//$modules['07_content']['shop_help']                 = 'shophelp.php?act=list_cat';
//$modules['07_content']['shop_info']                 = 'shopinfo.php?act=list';


$modules['10_members']['03_users_list'] = 'users.php?act=list';
$modules['10_members']['04_users_add'] = 'users.php?act=add';
$modules['10_members']['05_users_wx'] = 'wx_bind.php?act=wx_list';
//$modules['10_members']['05_user_rank_list'] = 'user_rank.php?act=list';
//$modules['08_members']['06_list_integrate'] = 'integrate.php?act=list';
//$modules['08_members']['08_unreply_msg'] = 'user_msg.php?act=list_all';
//$modules['08_members']['09_user_account'] = 'user_account.php?act=list';
//$modules['08_members']['10_user_account_manage'] = 'user_account_manage.php?act=list';

$modules['10_priv_admin']['admin_logs'] = 'admin_logs.php?act=list';
$modules['10_priv_admin']['admin_list'] = 'privilege.php?act=list';
$modules['10_priv_admin']['admin_role'] = 'role.php?act=list';
//$modules['10_priv_admin']['agency_list'] = 'agency.php?act=list';
//$modules['10_priv_admin']['suppliers_list'] = 'suppliers.php?act=list'; // 供货商

$modules['11_system']['01_shop_config'] = 'shop_config.php?act=list_edit';
//$modules['11_system']['shop_authorized'] = 'license.php?act=list_edit';
$modules['11_system']['02_payment_list'] = 'payment.php?act=list';
//$modules['11_system']['03_shipping_list'] = 'shipping.php?act=list';
//$modules['11_system']['04_mail_settings'] = 'shop_config.php?act=mail_settings';
$modules['11_system']['05_area_list'] = 'area_manage.php?act=list';
//$modules['11_system']['06_plugins']                 = 'plugins.php?act=list';
//$modules['11_system']['07_cron_schcron'] = 'cron.php?act=list';
//$modules['11_system']['08_friendlink_list'] = 'friend_link.php?act=list';
//$modules['11_system']['sitemap'] = 'sitemap.php';
$modules['11_system']['check_file_priv'] = 'check_file_priv.php?act=check';
$modules['11_system']['captcha_manage'] = 'captcha_manage.php?act=main';
//$modules['11_system']['ucenter_setup'] = 'integrate.php?act=setup&code=ucenter';
//$modules['11_system']['flashplay'] = 'flashplay.php?act=list';
//$modules['11_system']['navigator'] = 'navigator.php?act=list';
//$modules['11_system']['file_check'] = 'filecheck.php';
//$modules['11_system']['fckfile_manage']             = 'fckfile_manage.php?act=list';
//$modules['11_system']['021_reg_fields'] = 'reg_fields.php?act=list';


$modules['12_template']['02_template_select'] = 'template.php?act=list';
$modules['12_template']['03_template_setup'] = 'template.php?act=setup';
$modules['12_template']['04_template_library'] = 'template.php?act=library';
$modules['12_template']['05_edit_languages'] = 'edit_languages.php?act=list';
$modules['12_template']['06_template_backup'] = 'template.php?act=backup_setting';
//$modules['12_template']['mail_template_manage'] = 'mail_template.php?act=list';


//$modules['13_backup']['02_db_manage'] = 'database.php?act=backup';
//$modules['13_backup']['03_db_optimize'] = 'database.php?act=optimize';
//$modules['13_backup']['04_sql_query'] = 'sql.php?act=main';
////$modules['13_backup']['05_synchronous']             = 'integrate.php?act=sync';
//$modules['13_backup']['convert'] = 'convert.php?act=main';
//$modules['14_sms']['02_sms_my_info']                = 'sms.php?act=display_my_info';
//$modules['14_sms']['03_sms_send'] = 'sms.php?act=display_send_ui';
//$modules['14_sms']['04_sms_sign'] = 'sms.php?act=sms_sign';
//$modules['14_sms']['04_sms_charge']                 = 'sms.php?act=display_charge_ui';
//$modules['14_sms']['05_sms_send_history']           = 'sms.php?act=display_send_history_ui';
//$modules['14_sms']['06_sms_charge_history']         = 'sms.php?act=display_charge_history_ui';
//$modules['15_rec']['affiliate'] = 'affiliate.php?act=list';
//$modules['15_rec']['affiliate_ck'] = 'affiliate_ck.php?act=list';
//$modules['16_email_manage']['email_list'] = 'email_list.php?act=list';
//$modules['16_email_manage']['magazine_list'] = 'magazine_list.php?act=list';
//$modules['16_email_manage']['attention_list'] = 'attention_list.php?act=list';
//$modules['16_email_manage']['view_sendlist'] = 'view_sendlist.php?act=list';

$modules['05_team']['team'] = 'route.php?con=team';
//$modules['06_set_meal']['set_meal']='route.php?con=set_meal';
//$modules['06_set_meal']['set_meal_order']='route.php?con=set_meal_order';
//$modules['06_set_meal']['advert']='route.php?con=advert';
//$modules['08_hotel']['02_hotel'] = 'route.php?con=hotel';
//$modules['08_hotel']['01_roomtype'] = 'route.php?con=roomtype';
$modules['11_agent']['agent'] = 'route.php?con=agent';
$modules['11_agent']['agent_order'] = 'route.php?con=agent&act=order_list';
$modules['11_agent']['examine'] = 'route.php?con=agent&act=examine';
$modules['11_batch']['batch'] = 'route.php?con=batch';
//$modules['08_airticket']['02_airticket'] = 'route.php?con=airticket';
//$modules['03_space']['space'] = 'route.php?con=space';
//$modules['08_airticket']['01_space'] = 'route.php?con=space';
$modules['08_banner']['08_banner'] = 'route.php?con=banner';


//基础数据管理列
$modules['04_color']['08_goods_type'] = 'goods_type.php?act=manage';
$modules['04_color']['03_category_list'] = 'category.php?act=list';
$modules['04_color']['pitch'] = 'route.php?con=pitch';
$modules['04_color']['color'] = 'route.php?con=color';
$modules['04_color']['game'] = 'route.php?con=game';
$modules['04_color']['sportcat'] = 'route.php?con=sportcat';
//$modules['02_advert']['advert'] = 'route.php?con=advert';


//套餐管理列
//$modules['17_combo_manage']['combo_list'] = 'route.php?con=combo';
//$modules['17_combo_manage']['combo_travel_type_list'] = 'route.php?con=combo_travel_type';
//$modules['17_combo_manage']['combo_travel_list'] = 'route.php?con=combo_travel';

//自定义广告管理列
$modules['18_bill_manage']['bill_list'] = 'route.php?con=bill';
$modules['50_coupon_cluster']['coupon_cluster_list'] = 'route.php?con=coupon_cluster';

//合同管理
$modules['55_contract_manager']['01_contract_list'] = 'route.php?con=contract';
//$modules['55_contract_manager']['02_contract_add'] = 'route.php?con=contract&act=add';


?>
