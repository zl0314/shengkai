<?php
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
include_once('includes/cls_json.php');
//获取赛事信息

$act = empty($_REQUEST['act']) ? "index" : trim($_REQUEST['act']);
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
if ($act == "index"){  
     __load("GameService");
    $game_obj = new GameService();
    $game_info=$game_obj->get_game($_GET['game_id']);
     $time1=strtotime (date("Y-m-d H:i:s")); //当前时间  ,注意H 是24小时 h是12小时 
        $time2=strtotime ("2016-7-15 00:00:00");  //过年时间，不能写2014-1-21 24:00:00  这样不对 
        $kaimushi=ceil(($time2-$time1)/86400); //60s*60min*24h     
        $smarty->assign('kaimushi', $kaimushi);
    $smarty->assign('game_info', $game_info);
    $namel="<img src='images/logo.png'>";
    $smarty->assign( 'name1',$namel);
    $smarty->display("special.html");
    exit;
}
elseif($act == "tpl"){
    __load("GameService");
    $game_obj = new GameService();
    if($_GET['game_id']&&$_GET['city_id']){
    $city_id=$game_obj->get_game($_GET['game_id'],$_GET['city_id']);
    $smarty->assign('city_id', $city_id);
    }else if($_GET['game_id']){
        $game_info=$game_obj->get_game($_GET['game_id']);
    $smarty->assign('game_info', $game_info);
        $namel="<img src='images/logo.png'>";
        $smarty->assign( 'name1',$namel);
    }
    if(empty($game_info['template'])){
        ecs_header("Location:game_search.php?game_id=".$_GET['game_id']);
        exit;
    }else{
        $smarty->display("gametpl/" . $game_info['template']);
    }

    exit;
}


$act = empty($_REQUEST['act']) ? "index" : trim($_REQUEST['act']);


if ($act == "index") {

    //检查当前用户购物车中是否有数据
   
    $game_id = empty($_REQUEST['game_id']) ? 0 : intval($_REQUEST['game_id']);
    $cart_info = get_cart_info(SESS_ID, $game_id);
    $cart_num = count($cart_info);
    $_SESSION['cart_num'] = $cart_num;
    if (count($cart_info) > 0) {
        $cart_html .= '';
        $n = 0;
        foreach ($cart_info as $key => $value) {
            $n += $value['goods_price'];
            if (count($cart_info) == 1) {
                $cart_html .= '<div class="col-lg-1 col-md-1 cell" id="0"><dl><dt class="red" style="background:#';
                $cart_html .= $value['color_value'] . ';" >';
                $cart_html .= $value['numbers'] . '</dt><dd class="gold"></dd></dl></div>';
            } elseif (count($cart_info) > 1) {
                if ($key < 5) {
                    if (count($cart_info) == ($key + 1)) {
                        $cart_html .= '<div class="col-lg-1 col-md-1 cell" id="' . $n . '"><dl><dt class="red" style="background:#';
                        $cart_html .= $value['color_value'] . ';">';
                        $cart_html .= $value['numbers'] . '</dt><dd class="gold"></dd></dl></div>';
                    } else {
                        $cart_html .= '<div class="col-lg-1 col-md-1 cell" id="' . $n . '"><dl><dt class="red" style="background:#';
                        $cart_html .= $value['color_value'] . ';" >';
                        $cart_html .= $value['numbers'] . '</dt><dd class="gold"></dd></dl></div>';
                        $cart_html .= '<div class="col-lg-1 col-md-1 "><p><img src="themes/sk_themes/images/jiahao.png" /></p></div>';
                    }
                } elseif ($key == 5) {
                    $cart_html .= '<div class="col-lg-1 col-md-1 cell" id="' . $n . '"><dl><dt class="red" style="background:#';
                    $cart_html .= $value['color_value'] . ';" >';
                    $cart_html .= $value['numbers'] . '</dt><dd class="gold"></dd></dl></div>';
                } elseif ($key == 6) {
                    $cart_html .= '<div class="col-lg-1 col-md-1"><span><img src="themes/sk_themes/images/dots.png" /> </span></div>';
                }
            }
        }
    }
    $smarty->assign('cart_list', $cart_info);
    $smarty->assign('cart_list_count', count($cart_info));
    __load("CartService");
    $cart_obj = new CartService();
    $smarty->assign("cart_money", $cart_obj->get_cart_money());
    $smarty->assign('total_price', sprintf("%10.2f", $n));


    //获取城市信息
    $region_id = empty($_REQUEST['region_id']) ? 0 : intval($_REQUEST['region_id']);
    __load("GameService");
    __load("RegionService");
    $region_obj = new RegionService();
    $this_region = $region_obj->get_region($region_id);
    if (empty($region_id)) {
        $smarty->assign('region_name', "所有城市");
    } else {
        $smarty->assign('region_name', $this_region['region_name']);
    }
    $game = new GameService();
    $left_info = $game->get_game_info($game_id, $region_id);
    $arr = Array();
    $arr = $left_info;
    foreach ($arr AS $key => $value) {
        $arr[$key]['keywords'] = explode(' ', trim($value['keywords']));
        $arr[$key]['num_start'] = date('Y-m-d H:i', strtotime($arr[$key]['num_start']));
    }
    if ($_GET['sche_id']) {
        $left_info = $game->get_game_sche($game_id, $region_id, $_GET['sche_id']);
        $arr = $left_info;
        foreach ($arr AS $key => $value) {
            $arr[$key]['keywords'] = explode(' ', trim($value['keywords']));
        }
    }
    /* 获取地区列表  */
    $region_list = $game->get_region_list($game_id, $_GET['sche_id']);
    $smarty->assign('region_list', $region_list);
    $smarty->assign("game_id", $game_id);
    $smarty->assign("left_info", $arr);
    $game_info = $game->get_game_name($game_id);
    $smarty->assign("game_info", $game_info);
    if (empty($game_info['template'])) {
        $smarty->assign("game_more", 0);
    } else {
        $smarty->assign("game_more", 1);
    }

    /* 获取赛场列表 start */
    __load("PitchService");
    $pitch_obj = new PitchService();
    $pitch = $pitch_obj->get_pitch_list($_GET['game_id']);

    $pitch_html = '<div class="container-fluid city"style="">
            <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1"></div>';
    foreach ($pitch AS $info) {
        $pitch_html .= '<div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 pitch_div">
            <dl>
              <dt>
                <img src="' . $info['pitch_img'] . '" title="' . $info['pitch_name'] . '"/>
              </dt>
            </dl>
            <div class="tankuang1 dpn">
                ' . $info['pitch_name'] . '
            </div>
          </div>';
    }
    $pitch_html .= '</div>';
    array_unshift($pitch, array('pitch_name' => '&nbsp;'));

    /* 获取赛程列表 start */
    __load("ScheduleService");
    $sche_obj = new ScheduleService();
    $sche_list = $sche_obj->sche_list_info($game_id);
    if (empty($_GET['sche_id'])) {
        $smarty->assign('sche_name', "所有赛段");
    } else {
        $sche_name = $sche_obj->get_ScheName($_GET['sche_id']);
        $smarty->assign('sche_name', $sche_name);
    }

    $smarty->assign('sche_list', $sche_list);
    $schedule_html = '';

    __load("NumberService");
    $num_obj = new NumberService();
    foreach ($sche_list AS $schedule) {
        //小组赛名称
        $schedule_html .= $pitch_html . '<div class="container-fluid group01"><div class="row">
              <div class="col-md-12 one">' . $schedule['sche_name'] . '</div>
            </div>';
        /* 获取赛程下场次 */
        $num = $num_obj->get_schel_num($schedule['id']);
        //统一 场次行 与 赛场 下标
        $day_list = array();
        foreach ($num as $key => $data) {
            array_push($day_list, local_date("Y-m-d", local_strtotime($data['num_start'])));
            foreach ($pitch as $k => $pitch_info) {
                if ($data['pitch_id'] == $pitch_info['id']) {
                    $data['pitchid'] = $k;
                }
            }
            $num[$key] = $data;
        }
        $day_list = array_unique($day_list);
        foreach ($day_list as $day_key => $day) {
            $schedule_html .= '<div class="row"><div class="container-fluid date">';
            $schedule_html .= '<div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                                      <dl>
                                        <dt>' . zh_date(local_date("D", local_strtotime($day))) . '</dt>
                                        <dd>' . local_date("m.d", strtotime($day)) . '</dd>
                                      </dl>
                                    </div>';
            //生成赛事表
            foreach ($pitch as $i => $pitch_info) {
                if ($i > 0) {


                    $tmp_str = "";
                    foreach ($num AS $num_info) {
                        if (local_date("Y-m-d", local_strtotime($num_info['num_start'])) == $day) {
                            if ($num_info['pitch_id'] == $pitch_info['id']) {
                                $arr = Array();
                                $arr = explode('vs', $num_info['num_name']);
                                $tmp_str = '<div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                                            <div class="purple purple-match" style="background:#';
                                $tmp_str .= $num_info['color_value'] . '"><p class="nums purple" style="color:#';
                                $tmp_str .= $num_info['color_value'] . ';">' . $num_info['numbers'] . '</p>
                                     <span style="padding-top:5px;"class="vs">' . $arr[0] . '</span> <span class="vs">vs</span><span style="padding-bottom:5px;"class="vs">' . $arr[1] . '</span> <i class="time">' . local_date("H:i", local_strtotime($num_info['num_start'])) . '</i>
                                    </div>
                                  </div>';
                            }
                        }
                    }
                    if (empty($tmp_str)) {
                        $tmp_str = '<div class="col-lg-1 col-md-1 col-sm-1 col-xs-1"></div>';
                    }
                    $schedule_html .= $tmp_str;
                }
            }

            $schedule_html .= '</div></div>';
        }

        $schedule_html .= '</div>';
    }
    $smarty->assign('schedule_html', $schedule_html);
    __load("ColorService");
    $col_obj = new ColorService();
    $legend_info = $col_obj->color_List($game_id);
    if (!empty($legend_info)) {
        $legend_html = '  <ul class="">';
        foreach ($legend_info AS $key => $val) {
            $legend_html .= '  <li class="a" style="background:#';
            $legend_html .= $val['color_value'] . '">';
            $legend_html .= $val['color_name'] . '</li>';
        }
        $legend_html .= '</ul>';
        $smarty->assign('legend_html', $legend_html);
    }
    $smarty->assign('game_id', $game_id);
    /* 获取赛程列表 end */
    $namel="<img src='images/logo.png'>";
    $smarty->assign( 'name1',$namel);
    $smarty->display("game_search.html");
    exit;
} else if ($act == "search") {
    if ($_POST['search'] != null) {

        $game = search_game($_POST['search']);
        $teams = search_teams($_POST['search']);
        $city = search_city($_POST['search']);
        $smarty->assign('like', $_POST['search']);
        if ($game) {
            $smarty->assign('game_info', $game);
            $smarty->assign('game_count', game_count($_POST['search']));
        } else if ($teams) {
            $smarty->assign('game_info', $teams);
            $smarty->assign('game_count', teams_count($_POST['search']));
        } else if ($city) {
            $smarty->assign('game_info', $city);
            $smarty->assign('game_count', city_count($_POST['search']));
        } else {
            $smarty->assign('game_count', "0");
        }
    } else {
        $smarty->assign('game_count', "0");
    }
    $namel="<img src='images/logo.png'>";
    $smarty->assign( 'name1',$namel);
    $smarty->display("mysearch.html");
}elseif ($act == "ajax_game") {
    $scat_id = $_GET['id'];
    //根据赛事自动查询城市
    __load("GameService");
    $game_obj = new GameService();
    $game_list = $game_obj->get_by_scat($scat_id);
    $json = new JSON();
    $data = $json->encode($game_list);
    print_r($data);
    exit;
}  elseif ($act == "ajax_game_city") {
    $game_id = $_GET['game_id'];
    //根据赛事自动查询城市
    __load("RegionService");
    $RegionService = new RegionService();
    $schedule_id = $RegionService->get_schedule($game_id);
    $json = new JSON();
    $data = $json->encode($schedule_id);
    print_r($data);
    exit;


} elseif ($act == "remove_cart_goods") {
    $goods_id = empty($_GET['goods_id']) ? 0 : $_GET['goods_id'];
    $game_id = empty($_GET['game_id']) ? 0 : $_GET['game_id'];
    remove_hotel_info($goods_id);
    $GLOBALS['db']->query("delete from " . $GLOBALS['ecs']->table('cart') . " where session_id='" . SESS_ID . "' and goods_id='" . $goods_id . "'");

    $url = 'game_search.php?game_id=' . $game_id;
    ecs_header("Location: $url\n");

    exit;
} elseif ($act == 'update_goods_number') {
    if($_SESSION['user_id'] == 0) {
        $goods_id = empty($_GET['goods_id']) ? 0 : $_GET['goods_id'];
        $game_id = empty($_GET['game_id']) ? 0 : $_GET['game_id'];
        $goods_number = empty($_GET['goods_number']) ? 0 : intval($_GET['goods_number']);
        if ($goods_number == 0) {
            remove_hotel_info($goods_id);
            $GLOBALS['db']->query("delete from " . $GLOBALS['ecs']->table('cart') . " where session_id='" . SESS_ID . "' and goods_id='" . $goods_id . "'");
        } elseif ($goods_number > 2) {

        } else {
            $GLOBALS['db']->query("update " . $GLOBALS['ecs']->table('cart') . " set goods_number='" . $goods_number . "' where session_id='" . SESS_ID . "' and goods_id='" . $goods_id . "'");
        }
        $url = 'lister.php?act=index&game_id=' . $game_id;
        ecs_header("Location: $url\n");
        exit;
    }else{
        $type_info = get_user($_SESSION['user_id']);
        $goods_id = empty($_GET['goods_id']) ? 0 : $_GET['goods_id'];
        $game_id = empty($_GET['game_id']) ? 0 : $_GET['game_id'];
        $goods_number = empty($_GET['goods_number']) ? 0 : intval($_GET['goods_number']);
        if ($goods_number == 0 && $type_info == 0) {
            remove_hotel_info($goods_id);
            $GLOBALS['db']->query("delete from " . $GLOBALS['ecs']->table('cart') . " where session_id='" . SESS_ID . "' and goods_id='" . $goods_id . "'");
        } elseif ($goods_number > 2 && $type_info == 0) {

        } else {
            $GLOBALS['db']->query("update " . $GLOBALS['ecs']->table('cart') . " set goods_number='" . $goods_number . "' where session_id='" . SESS_ID . "' and goods_id='" . $goods_id . "'");
        }
        $url = 'lister.php?act=index&game_id=' . $game_id;
        ecs_header("Location: $url\n");
        exit;
    }
}elseif ($act == 'update_goods_number_mobile') {
    if($_SESSION['user_id'] == 0) {
        $goods_id = empty($_GET['goods_id']) ? 0 : $_GET['goods_id'];
        $game_id = empty($_GET['game_id']) ? 0 : $_GET['game_id'];
        $goods_number = empty($_GET['goods_number']) ? 0 : intval($_GET['goods_number']);
        if ($goods_number == 0) {
            remove_hotel_info($goods_id);
            $GLOBALS['db']->query("delete from " . $GLOBALS['ecs']->table('cart') . " where session_id='" . SESS_ID . "' and goods_id='" . $goods_id . "'");
        } elseif ($goods_number > 4) {

        } else {
            $GLOBALS['db']->query("update " . $GLOBALS['ecs']->table('cart') . " set goods_number='" . $goods_number . "' where session_id='" . SESS_ID . "' and goods_id='" . $goods_id . "'");
        }
        $url = 'lister.php?act=index&game_id=' . $game_id;
        ecs_header("Location: $url\n");
        exit;
    }else{
        $type_info = get_user($_SESSION['user_id']);
        $goods_id = empty($_GET['goods_id']) ? 0 : $_GET['goods_id'];
        $game_id = empty($_GET['game_id']) ? 0 : $_GET['game_id'];
        $goods_number = empty($_GET['goods_number']) ? 0 : intval($_GET['goods_number']);
        if ($goods_number == 0 && $type_info == 0) {
            remove_hotel_info($goods_id);
            $GLOBALS['db']->query("delete from " . $GLOBALS['ecs']->table('cart') . " where session_id='" . SESS_ID . "' and goods_id='" . $goods_id . "'");
        } elseif ($goods_number > 4 && $type_info == 0) {

        } else {
            $GLOBALS['db']->query("update " . $GLOBALS['ecs']->table('cart') . " set goods_number='" . $goods_number . "' where session_id='" . SESS_ID . "' and goods_id='" . $goods_id . "'");
        }
        $url = 'lister.php?act=index&game_id=' . $game_id;
        ecs_header("Location: $url\n");
        exit;
    }
}


function get_relateds($type, $grand, $parent)
{
    $arr = array();
    if ($type == 1) {


        foreach ($sche_list as $key => $val) {
            $arr[$key]['related_id'] = $val['id'];
            $arr[$key]['related_name'] = $val['sche_name'];
        }
    }
    if ($type == 2) {
        /* 获取地区列表  */
        __load("RegionService");
        $region_obj = new RegionService();
        $region_list = $region_obj->region_list($parent);
        foreach ($res as $key => $val) {
            $arr[$key]['related_id'] = $val['id'];
            $arr[$key]['related_name'] = $val['num_name'] . "--" . $val['pitch_name'];
            __log($arr[$key]['related_id']);
        }
    }
    return $arr;
}



function get_cart_info($session_id, $game_id)
{
    $sql = "SELECT c.*,n.*,g.*,cm.color_value,c.goods_number as cart_goods_number FROM " . $GLOBALS['ecs']->table('cart') . "AS c," . $GLOBALS['ecs']->table('color_manage') . "AS cm," . $GLOBALS['ecs']->table('goods') . "AS g," . $GLOBALS['ecs']->table('number') . "AS n WHERE c.session_id= '$session_id' AND g.game_id={$game_id} AND c.goods_id=g.goods_id AND g.number_id=n.id AND n.color_id=cm.color_id ";
    return $GLOBALS['db']->getAll($sql);
}

function search_game($condition)
{
    return $GLOBALS['db']->getAll("SELECT * FROM " . $GLOBALS['ecs']->table('game') . "WHERE game_name LIKE\"%$condition%\" AND is_type=1");
}

function game_count($condition)
{
    return $GLOBALS['db']->getOne("SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('game') . "WHERE game_name LIKE\"%$condition%\" AND is_type=1");
}

function search_teams($condition)
{
    return $GLOBALS['db']->getAll("SELECT g.* FROM " . $GLOBALS['ecs']->table('game') . "AS g," . $GLOBALS['ecs']->table('schedule') . "AS s," . $GLOBALS['ecs']->table('number') . "AS n," . $GLOBALS['ecs']->table('num_team') . "AS nt," . $GLOBALS['ecs']->table('teams') . "AS t " . "WHERE g.id=s.game_id AND s.id=n.sche_id  AND n.id=nt.num_id AND nt.team_id=t.id  AND is_type=1 AND t.team_name LIKE\"%$condition%\" GROUP BY g.id");
}

function teams_count($condition)
{
    $sql = "SELECT COUNT(*) FROM (SELECT g.* FROM " . $GLOBALS['ecs']->table('game') . "AS g," . $GLOBALS['ecs']->table('schedule') . "AS s," . $GLOBALS['ecs']->table('number') . "AS n," . $GLOBALS['ecs']->table('num_team') . "AS nt," . $GLOBALS['ecs']->table('teams') . "AS t " . "WHERE g.id=s.game_id AND s.id=n.sche_id  AND n.id=nt.num_id AND nt.team_id=t.id AND t.team_name LIKE\"%$condition%\" GROUP BY g.id) aa";
    return $GLOBALS['db']->getOne($sql);
}

function search_city($condition)
{
    $sql = "SELECT g.* FROM " . $GLOBALS['ecs']->table('game') . "AS g," . $GLOBALS['ecs']->table('schedule') . "AS s," . $GLOBALS['ecs']->table('number') . "AS n," . $GLOBALS['ecs']->table('pitch') . "AS p," . $GLOBALS['ecs']->table('region') . "AS r " . "WHERE g.id=s.game_id AND s.id=n.sche_id  AND n.pitch_id=p.id AND p.region_id=r.region_id AND r.region_name LIKE\"%$condition%\"  AND r.region_type=2 GROUP BY g.id";
    return $GLOBALS['db']->getAll($sql);
}

function city_count($condition)
{
    $sql = "SELECT COUNT(*) FROM (SELECT g.* FROM " . $GLOBALS['ecs']->table('game') . "AS g," . $GLOBALS['ecs']->table('schedule') . "AS s," . $GLOBALS['ecs']->table('number') . "AS n," . $GLOBALS['ecs']->table('pitch') . "AS p," . $GLOBALS['ecs']->table('region') . "AS r " . "WHERE g.id=s.game_id AND s.id=n.sche_id  AND n.pitch_id=p.id AND p.region_id=r.region_id AND r.region_name LIKE\"%$condition%\"  AND r.region_type=2 GROUP BY g.id) aa";
    return $GLOBALS['db']->getOne($sql);
}

function remove_hotel_info($goods_id)
{
    $sql = "select rec_id from " . $GLOBALS['ecs']->table('cart') . " where goods_id = '" . $goods_id . "' and session_id='" . SESS_ID . "'";

    $result = $GLOBALS['db']->getAll($sql);
    foreach ($result as $key => $value) {
        $sql = "delete from " . $GLOBALS['ecs']->table('cart') . " where parent_id = " . $value['rec_id'] . " and goods_type='hotel' ";

        $GLOBALS['db']->query($sql);
    }

}
//获取用户的属性是否为代理商
function get_user($user_id){
    $sql = "SELECT type FROM sk_users WHERE user_id=$user_id";
    return $GLOBALS['db']->getOne($sql);
}
?>

