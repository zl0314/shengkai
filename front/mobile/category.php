<?php

/**
 * ECSHOP 商品分类页
 * ============================================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: testyang $
 * $Id: category.php 15013 2008-10-23 09:31:42Z testyang $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
/* 获取赛事列表 start */
__load("GameService");

$game_obj = new GameService();
$game_list = $game_obj->get_list();
if(!empty($game_id)){
    $game = $game_obj->get_game($game_id);

    $smarty->assign('game', $game);

    //根据赛事自动查询城市
    __load("RegionService");
    $RegionService = new RegionService();
    $schedule_id = $RegionService->get_schedule($game_id);

    $smarty->assign('schedule_id', $schedule_id);
}
/* 获取赛事列表 end */
$combo= $GLOBALS['db']->getALL('select * from sk_combo');
$sportcat_list = $GLOBALS['db']->getAll('SELECT id,name FROM sk_sportcat where is_display=1');
$smarty->assign('sportcat_list', $sportcat_list);
$c_id = !empty($_GET['c_id']) ? intval($_GET['c_id']) : 0;
if ($c_id <= 0)
{
    $pcat_array = get_categories_tree();
    foreach ($pcat_array as $key => $pcat_data)
    {
        $pcat_array[$key]['name'] = encode_output($pcat_data['name']);
        if ($pcat_data['cat_id'])
        {
            foreach ($pcat_data['cat_id'] as $k => $v)
            {
                $pcat_array[$key]['cat_id'][$k]['name'] = encode_output($v['name']);
            }
        }
    }
    $smarty->assign('cat_array' , $pcat_array);
    $smarty->assign('all_cat' , 1);
}
else
{
    $cat_array = get_categories_tree($c_id);
    $smarty->assign('c_id', $c_id);
    $cat_name = $db->getOne('SELECT cat_name FROM ' . $ecs->table('category') . ' WHERE cat_id=' . $c_id);
    $smarty->assign('cat_name', encode_output($cat_name));
    if (!empty($cat_array[$c_id]['cat_id']))
    {
        foreach ($cat_array[$c_id]['cat_id'] as $key => $child_data)
        {
            $cat_array[$c_id]['cat_id'][$key]['name'] = encode_output($child_data['name']);
        }
        $smarty->assign('cat_children', $cat_array[$c_id]['cat_id']);
    }

    if (empty($_GET['order_price']))
    {
        $order_rule = 'ORDER BY g.shop_price ASC, g.sort_order';
    }
    else
    {
        $order_rule = 'ORDER BY g.shop_price DESC, g.sort_order';
    }

    $cat_goods = assign_cat_goods($c_id, 0, 'wap', $order_rule);
    $num = count($cat_goods['goods']);
    if ($num > 0)
    {
        $page_num = '10';
        $page = !empty($_GET['page']) ? intval($_GET['page']) : 1;
        $pages = ceil($num / $page_num);
        if ($page <= 0)
        {
            $page = 1;
        }
        if ($pages == 0)
        {
            $pages = 1;
        }
        if ($page > $pages)
        {
            $page = $pages;
        }
        $i = 1;
        foreach ($cat_goods['goods'] as $goods_data)
        {
            if (($i > ($page_num * ($page - 1 ))) && ($i <= ($page_num * $page)))
            {
                $price = empty($goods_info['promote_price_org']) ? $goods_data['shop_price'] : $goods_data['promote_price'];
                //$wml_data .= "<a href='goods.php?id={$goods_data['id']}'>".encode_output($goods_data['name'])."</a>[".encode_output($price)."]<br/>";
                $data[] = array('i' => $i , 'price' => encode_output($price) , 'id' => $goods_data['id'] , 'name' => encode_output($goods_data['name']));
            }
            $i++;
        }
        $smarty->assign('goods_data', $data);
        $pagebar = get_wap_pager($num, $page_num, $page, 'category.php?c_id='.$c_id.'&order_price='.(empty($order_price)?0:$order_price), 'page');
        $smarty->assign('pagebar', $pagebar);
    }

    $pcat_array = get_parent_cats($c_id);
    if (!empty($pcat_array[1]['cat_name']))
    {
        $pcat_array[1]['cat_name'] = encode_output($pcat_array[1]['cat_name']);
        $smarty->assign('pcat_array', $pcat_array[1]);
    }

    $smarty->assign('cat_array', $cat_array);
}

$smarty->assign('footer', get_footer());
$smarty->display('category.html');

?>